<?php

namespace Fulloop\Orders\Model;

use Api\ServiceVst\Controller\Product\Create;

class ResponseManagement implements \Fulloop\Orders\Api\ResponseManagementInterface
{

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    protected $_checkoutSession;

    protected $helperData;

    protected $auth;

    protected $context;

    protected $orderFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Checkout\Model\Session $_checkoutSession,
        \Api\ServiceVst\Helper\Data $helperData,
        \Magento\Sales\Api\Data\OrderInterfaceFactory $orderFactory
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;

        $this->_checkoutSession = $_checkoutSession;

        $this->helperData = $helperData;


        $this->context = $context;

        $this->orderFactory = $orderFactory;

    }

    public function postResponse()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $baseUrl = $storeManager->getStore()->getBaseUrl();

        $resposeData = file_get_contents("php://input");
        $param = json_decode($resposeData, true);
        $adminUserId = 0;
        $this->_log('resp'.print_r($param, true));
        if (!empty($param['is_ajax'])) {
            $incrementId = $param['increment_id'];
            $paymentMethod = 'other';
            $this->auth['token_type'] = $param['token_type'];
            $this->auth['access_token'] = $param['access_token'];
            $adminUserId = !empty($param['admin_userid']) ? $param['admin_userid'] : 0;
        } else {
//            $incrementId = $param['RETURNINV'];
            $this->_log(print_r($param, true));
            $order = $this->_checkoutSession->getLastRealOrder();
            $incrementId = $order->getIncrementId();
            $this->_log('session increment id:'.$incrementId);

            $paymentMethod = 'credit_card';
            $this->auth = $this->helperData->auth();
        }

        if (empty($this->auth)) {
            $this->_log('no auth');
            return;
        }

        $this->_savePaymentResponse($incrementId, $paymentMethod, $resposeData);

//        if (!empty($_POST['order_increment_id'])) {
//            $incrementId = $_POST['order_increment_id'];
//        }

        $order = $this->orderFactory->create()->loadByIncrementId($incrementId);
        $orderItems = $order->getAllVisibleItems();

        if (isset($param['HOSTRESP'])) {
            if ($param['HOSTRESP'] != '00') {
                $this->_log('bank response incorrect');
                return;
            }
        }
        //HOSTRESP == 00
        //RETURNINV == $incrementId
        //AMOUNT

        $isEsdOrder = 0;

        foreach ($orderItems as $item) {
            $product = $item->getProduct();
            if ($product->getIsMicrosoftLicense()) {
                $isEsdOrder++;
            }
        }

        if ($isEsdOrder > 0) {
            $this->_log('create order');
            $developmentMode = $this->helperData->getConfigValue('vst/general/development_mode');
            if (empty($developmentMode)) {
                $this->_createEsdOrder($incrementId, $adminUserId);
            }
        }

        if (!empty($param['is_ajax'])) {
            return $incrementId;
        }

        //re direct to success page
        header( "location: '.$baseUrl.'checkout/onepage/success/" );
        exit;
    }

    private function _savePaymentResponse($incrementId, $paymentMethod, $respose_data)
    {
        $resources = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magento\Framework\App\ResourceConnection');
        $connection= $resources->getConnection();

        try {
            $sql = "INSERT INTO orders_payment_response(
                                                        increment_id, payment_method, respose_data
                                                        )
                    VALUES (:increment_id, :payment_method, :respose_data)";
            $bind = [
                'increment_id' => $incrementId,
                'payment_method' => $paymentMethod,
                'respose_data' => $respose_data
            ];

            $connection->query($sql, $bind);
        } catch (Exception $e) {
            $this->_log($e->getMessage());
        }
    }

    private function _createEsdOrder($incrementId, $adminUserId = 0)
    {
        // curl create product
        $_POST = [
            'increment_id' => $incrementId,
            'esd' => $this->auth,
            'admin_userid' => $adminUserId
        ];
        $productCreate = new Create($this->context, $this->resultJsonFactory, $this->helperData, $this->orderFactory);
        $productCreate->execute();
    }

    private function _log($msg)
    {
        // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/create_vst_backend_api.log');
        // $logger = new \Zend\Log\Logger();
        // $logger->addWriter($writer);
        // $logger->info($msg);

        $this->helperData->generalLog('/var/log/create_vst_backend_api.log', $msg);
    }
}
