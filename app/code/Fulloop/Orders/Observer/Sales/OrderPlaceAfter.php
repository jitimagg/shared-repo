<?php

namespace Fulloop\Orders\Observer\Sales;

class OrderPlaceAfter implements \Magento\Framework\Event\ObserverInterface
{
    protected $auth = [];

    protected $helperData;

    protected $context;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Api\ServiceVst\Helper\Data $helperData
    )
    {
        $this->helperData = $helperData;

        $this->context = $context;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
//        $this->_log('ttttttt');
        return '';
        $this->_getAuthenToken();

        $order = $observer->getEvent()->getOrder();
        $customerId = $order->getCustomerId();

        //get esd customer and validate
        $msCustomer = $this->_getMsCustomerData($customerId);

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

        //update customer mail

        $key = $order->getIncrementId();
        if (empty($key)) {
            $key = uniqid();
        }

        try {

            $orderItems = $order->getAllItems();
            foreach ($orderItems as $item) {
                for ($i = 0; $i < $item->getQtyOrdered(); $i++) {
                    $resp = $this->_esdCreate($msCustomer['id'], $item->getSku());
                    if (!empty($resp) && $resp['status'] == 'success') {
                        //insert license to database => use job schedule
                        $this->_addLicenseLog($customerId, $key, $item->getSku(), $resp);
                    } else {
                        $resp['product_key'] = $this->_mockKey(5).'-'.$this->_mockKey(5).'-'.$this->_mockKey(5).'-'.$this->_mockKey(5).'-'.$this->_mockKey(5);
                        $resp['client_trans_id'] = '9745942e-4c8d-6eaf-9bae-297a09096db0';
                        $resp['activation_link'] = 'https://managevst152.labvstecscloud.net/memberp/';
                        $resp['racknap_customer_portal_url'] = 'https://managevst152.labvstecscloud.net/memberp/';

                        //insert license to database => use job schedule
                        $this->_addLicenseLog($customerId, $key, $item->getSku(), $resp);
                    }
                }
            }
        } catch (Exception $e) {
            $this->_log($e->getMessage());
        }

        //update customer mail back

        //รวม license key and send mail


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
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://managemp.labvstecscloud.net/api/v2/esd/create",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "customer_id={$customerId}&skuid={$sku}",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "Validate: {$this->_auth['token_type']} {$this->_auth['access_token']}",
                "Content-Type: application/x-www-form-urlencoded"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $result = json_decode($response, true);

        $this->_log(print_r($result, true));
        return empty($result['errorCode']) ? $result : false;
    }

    private function _addLicenseLog($customerId, $orderId, $sku, $resp)
    {
        $plan = $this->_getEsdPlan($sku);
        $resources = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magento\Framework\App\ResourceConnection');
        $connection= $resources->getConnection();

        try {
            $sql = "INSERT INTO order_microsoft_license(
                                                        increment_id, customer_id, plan_id, plan_name, sku, product_key,
                                                        client_trans_id, activation_link, racknap_customer_portal_url
                                                        )
                    VALUES (:increment_id, :customer_id, :plan_id, :plan_name, :sku, :product_key,
                            :client_trans_id, :activation_link, :racknap_customer_portal_url)";
            $bind = [
                'increment_id' => $orderId,
                'customer_id' => $customerId,
                'plan_id' => !empty($plan['data'][0]['planid']) ? $plan['data'][0]['planid'] : 0,
                'plan_name' => !empty($plan['data'][0]['planname']) ? $plan['data'][0]['planname'] : 0,
                'sku' => $sku,
                'product_key' => $resp['product_key'],
                'client_trans_id' => $resp['client_trans_id'],
                'activation_link' => $resp['activation_link'],
                'racknap_customer_portal_url' => $resp['racknap_customer_portal_url']
            ];

            $connection->query($sql, $bind);
        } catch (Exception $e) {
            $this->_log($e->getMessage());
        }
    }

    private function _getAuthenToken()
    {
        $url = 'https://managemp.labvstecscloud.net/api/v2/auth/token';
        $params = [
            'grant_type' => 'client_credentials',
            'client_id' => 'racknap-1266981365',
            'client_secret' => '5a263c8b9fe3ba572ef5c7d6f3780061c6a3b41a8e941aa1c4d12e99add0c9ad'
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache"
            )
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $result = json_decode($response, true);

        $this->_log(print_r($result, true));
        $this->_auth = empty($result['errorCode']) ? $result : [];
    }

    private function _getEsdPlan($sku)
    {
        $url = 'https://managemp.labvstecscloud.net/api/v2/esd/plans';

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "skuid={$sku}",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "Validate: {$this->_auth['token_type']} {$this->_auth['access_token']}",
                "Content-Type: application/x-www-form-urlencoded"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $result = json_decode($response, true);

        $this->_log(print_r($result, true));
        return empty($result['errorCode']) ? $result : false;
    }

    private function _getMsCustomerData($customerId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $collection = $objectManager->create('Magento\Customer\Model\Customer')->load($customerId);
        $customerData = $collection->getData();

        if (empty($customerData['microsoft_customer_id'])) {
            return false;
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://managemp.labvstecscloud.net/api/v2/customer/list",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "customer_id={$customerData['microsoft_customer_id']}",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "Validate: {$this->_auth['token_type']} {$this->_auth['access_token']}",
                "Content-Type: application/x-www-form-urlencoded"
            ),
        ));

        $response = curl_exec($curl);

        $result = json_decode($response, true);

        $this->_log(print_r($result, true));
        return empty($result['errorCode']) ? $result : false;
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
