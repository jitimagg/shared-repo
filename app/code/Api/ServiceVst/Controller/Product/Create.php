<?php

namespace Api\ServiceVst\Controller\Product;

class Create extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Api\ServiceVst\Helper\Data
     */
    protected $helperData;

    protected $auth;

    protected $orderFactory;

    /**
     * Constructor.
     *
     * @param Magento\Framework\HTTP\Client\Curl $curl
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Api\ServiceVst\Helper\Data $helperData,
        \Magento\Sales\Api\Data\OrderInterfaceFactory $orderFactory
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;

        $this->helperData = $helperData;

        $this->orderFactory = $orderFactory;

        $this->auth = !empty($_POST['esd']) ? $_POST['esd'] : [];

        parent::__construct($context);
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute() {

        $order = $this->orderFactory->create()->loadByIncrementId($_POST['increment_id']);
        $customerId = $order->getCustomerId();

        $rsl = $this->_getLicenseByIncrementId($order->getIncrementId());
        if (!empty($rsl)) {
            return '';
        }

        $this->_log('orderrr');

        //get esd customer and validate
        $msCustomer = $this->_getMicrosoftCustomer($customerId);

        if (empty($msCustomer['Customers'])) {
            $this->_log('no Microsoft Account');
            return '';
        }

        $msCustomer = $msCustomer['Customers'][0];
        if (!empty($msCustomer['customer_status']) && strtolower($msCustomer['customer_status']) != 'active') {
            $this->_log('Microsoft Account customer_status not "Active"');
            return '';
        }

        $oldCustomerEmail = $msCustomer['email'];
        $customerEmail = $msCustomer['email'];

        //update customer mail
        $configEmail = $this->helperData->getConfigValue('vst/general/email_key');//'teerachai.jc@gmail.com';
        $this->_updateTempEmail($msCustomer, $configEmail);

        $incrementId = $order->getIncrementId();
        if (empty($incrementId)) {
            $incrementId = uniqid();
        }

        try {

            $orderItems = $order->getAllVisibleItems();
            foreach ($orderItems as $item) {
                $product = $item->getProduct();
                if (!$product->getIsMicrosoftLicense()) {
                    continue;
                }

                for ($i = 0; $i < $item->getQtyOrdered(); $i++) {
                    $resp = $this->_esdCreate($msCustomer['id'], $item->getSku());
                    if (!empty($resp) && $resp['status'] == 'Success') {
                        //insert license to database => use job schedule
                        $this->_addLicenseLog($customerId, $incrementId, $item->getSku(), $resp);
                    }
                }
            }
        } catch (Exception $e) {
            $this->_log($e->getMessage());
        }

        //update customer mail back
        $this->_updateTempEmail($msCustomer, $oldCustomerEmail);

        //รวม license key and send mail
        $this->_sendEmail($customerEmail, $incrementId);

        return $incrementId;
    }

    private function _updateTempEmail($customer, $email)
    {
        $params = [
            'customer_id' => $customer['id'],
            'form_type' => 'personal_detail_form',
            'submit' => 'Update',
            'username' => $customer['username'],
            'email' => $email,
            'firstname' => $customer['firstname'],
            'lastname' => $customer['lastname']
        ];

        return $this->helperData->customerUpdate($this->auth, $params);
    }

    private function _mockKey($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return strtoupper($randomString);
    }

    private function _esdCreate($customerId, $sku)
    {
        $params = [
            'customer_id' => $customerId,
            'skuid' => $sku
        ];
        return $this->helperData->orderCreate($this->auth, $params);
    }

    private function _addLicenseLog($customerId, $incrementId, $sku, $resp)
    {
        $plan = $this->_getEsdPlan($sku);
        $resources = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magento\Framework\App\ResourceConnection');
        $connection= $resources->getConnection();

        $created_by = $customerId;
        $created_user_role = 'customer';
        if (!empty($_POST['admin_userid'])) {
            $created_by = $_POST['admin_userid'];
            $created_user_role = 'admin';
        }

        try {
            $sql = "INSERT INTO order_microsoft_license(
                                                        increment_id, customer_id, plan_id, plan_name, sku, product_key,
                                                        client_trans_id, activation_link, racknap_customer_portal_url,
                                                        created_by, created_user_role
                                                        )
                    VALUES (:increment_id, :customer_id, :plan_id, :plan_name, :sku, :product_key,
                            :client_trans_id, :activation_link, :racknap_customer_portal_url,
                            :created_by, :created_user_role)";
            $bind = [
                'increment_id' => $incrementId,
                'customer_id' => $customerId,
                'plan_id' => !empty($plan['data'][0]['planid']) ? $plan['data'][0]['planid'] : 0,
                'plan_name' => !empty($plan['data'][0]['planname']) ? $plan['data'][0]['planname'] : 0,
                'sku' => $sku,
                'product_key' => $resp['product_key'],
                'client_trans_id' => $resp['client_trans_id'],
                'activation_link' => $resp['activation_link'],
                'racknap_customer_portal_url' => $resp['racknap_customer_portal_url'],
                'created_by' => $created_by,
                'created_user_role' => $created_user_role
            ];

            $connection->query($sql, $bind);
        } catch (Exception $e) {
            $this->_log($e->getMessage());
        }
    }

    private function _getEsdPlan($sku)
    {
        $params = [
            'skuid' => $sku
        ];

        return $this->helperData->edsPlans($this->auth, $params);
    }

    private function _getMicrosoftCustomer($customerId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $collection = $objectManager->create('Magento\Customer\Model\Customer')->load($customerId);
        $customerData = $collection->getData();

        if (empty($customerData['microsoft_customer_id'])) {
            return false;
        }

        $params = [
            'customer_id' => $customerData['microsoft_customer_id']
        ];

        return $this->helperData->customerList($this->auth, $params);
    }

    private function _sendEmail($email, $incrementId)
    {
        $rsl = $this->_getLicenseByIncrementId($incrementId);

        $this->_log('email:'.$email);
        $t = '';
        $text = '';
        if (!empty($rsl)) {
            foreach ($rsl as $v) {
                $t .= '<p><b>Plan Name:</b> '.$v['plan_name'].'</p>';
                $t .= '<p><b>Product Key:</b> '.$v['product_key'].'</p>';
                $t .= '<p><a href="'.$v['activation_link'].'?q='.$v['activation_link'].'">Activate Link</a></p><br/><br/>';

                $text .= 'Plan Name: '.$v['plan_name']."\n";
                $text .= 'Product Key: '.$v['product_key']."\n";
                $text .= 'Link:'.$v['activation_link'].'?q='.$v['activation_link']."\n"."\n"."\n";

                $this->_log($v['plan_name'].'||'.$v['product_key']);
            }
            //sendmail
            $this->_sendmail($email, $text);
        }

    }

    private function _sendmail($to, $msg)
    {
        $config = array(
            'host' => 'smtp.gmail.com',
            'port' => 587,
            'ssl' => 'tls',
            'auth' => 'login',
            'username' => 'teerachai.jaichuang@gmail.com',
            'password' => 'dvfmaitknjoizseo',
        );

        $transport = new \Zend_Mail_Transport_Smtp('smtp.gmail.com', $config);

        $configEmail = $this->helperData->getConfigValue('vst/general/email_key');
        $email = new \Zend_Mail();
        $email->setSubject("Test");
        $email->setBodyText($msg);
        $email->setFrom($configEmail, 'ITS');
        $email->addTo($to);
        $email->send();
    }

    private function _getLicenseByIncrementId($incrementId)
    {
        $resources = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magento\Framework\App\ResourceConnection');
        $connection = $resources->getConnection();
        $sql = "SELECT *
                FROM order_microsoft_license
                WHERE increment_id='{$incrementId}'";
        $rsl = $connection->fetchAll($sql);

        return $rsl;
    }

    private function _log($msg)
    {
        // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/esd_create.log');
        // $logger = new \Zend\Log\Logger();
        // $logger->addWriter($writer);
        // $logger->info($msg);

         $this->helperData->generalLog('/var/log/esd_create.log', $msg);
    }
}
