<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Fulloop\Orders\Controller\Payment;

use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

class WechatNotify extends \Magento\Framework\App\Action\Action implements \Magento\Framework\App\CsrfAwareActionInterface
{

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $logger;

    protected $resultJsonFactory;

    protected $_checkoutSession;

    protected $helperData;

    protected $auth;

    protected $context;

    protected $orderFactory;

    protected $_productRepository;

    /**
    * Constructor.
    *
    * @param Magento\Framework\HTTP\Client\Curl $curl
    */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Checkout\Model\Session $_checkoutSession,
        \Api\ServiceVst\Helper\Data $helperData,
        \Magento\Sales\Api\Data\OrderInterfaceFactory $orderFactory,
        \Magento\Catalog\Model\ProductRepository $productRepository
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;

        $this->_checkoutSession = $_checkoutSession;

        $this->helperData = $helperData;

        $this->_productRepository = $productRepository;

//        $this->auth = $helperData->auth();

        $this->context = $context;

        $this->orderFactory = $orderFactory;

        parent::__construct($context);
    }

    /**
     * View  page action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $input = file_get_contents("php://input");
            $resposeData = json_decode($input, true);
//            $this->_log('notify'.print_r($_POST, true));
            $this->_log('notify'.print_r($resposeData, true));
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $orderInterface = $objectManager->create('Magento\Sales\Api\Data\OrderInterface');
            $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
            $order = $orderInterface->loadByIncrementId($resposeData['reference_order']);

            if (!$order->getId()) {
                $this->_log('notify order not found: '.$resposeData['reference_order']);
                echo 'order not found';
            }

            $amount = (float)$order->getGrandTotal();
            $baseUrl = $storeManager->getStore()->getBaseUrl();

            $license = array();
            if ($resposeData['transaction_state'] == 'Authorized'
                && $resposeData['status'] == 'success'
                && $amount == $resposeData['amount'] || true) {
                $this->auth = $this->helperData->auth();
                $tokenType = !empty($this->auth['token_type']) ? $this->auth['token_type'] : '';
                $accessToken = !empty($this->auth['access_token']) ? $this->auth['access_token'] : '';
                $license = $this->createOrder($resposeData['reference_order'], $tokenType, $accessToken);
                $this->_log('notify: '.print_r($$license, true));
            }

            //re direct to success page
            header( "location: '.$baseUrl.'checkout/onepage/success/" );
            exit;
        } catch (\Exception $e) {
            $this->_log($e->getMessage());
        }

    }

    private function createOrder ($incrementId, $tokenType, $accessToken)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $storeManager->getStore()->getBaseUrl().'rest/V1/fulloop-orders/response',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                "increment_id": "'.$incrementId.'",
                "token_type": "'.$tokenType.'",
                "access_token": "'.$accessToken.'",
                "is_ajax": 1
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            )
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    /**
     * @param RequestInterface $request
     *
     * @return bool|null
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    /**
     * @param RequestInterface $request
     *
     * @return InvalidRequestException|null
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    private function _log($msg)
    {
        // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/wechat_notify.log');
        // $logger = new \Zend\Log\Logger();
        // $logger->addWriter($writer);
        // $logger->info($msg);

        $this->helperData->generalLog('/var/log/wechat_notify.log', $msg);
    }
}
