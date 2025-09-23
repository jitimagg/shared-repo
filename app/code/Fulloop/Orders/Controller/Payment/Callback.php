<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Fulloop\Orders\Controller\Payment;

use Api\ServiceVst\Controller\Product\Create;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

class Callback extends \Magento\Framework\App\Action\Action implements \Magento\Framework\App\CsrfAwareActionInterface
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
        \Magento\Sales\Api\Data\OrderInterfaceFactory $orderFactory
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;

        $this->_checkoutSession = $_checkoutSession;

        $this->helperData = $helperData;

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
//        $to = ['teerachai.jc@gmail.com'];
//        $email = new \Zend_Mail();
//        $email->setSubject("Feedback email");
//        $email->setBodyText('text');
//        $email->setFrom('teerachai.jc@gmail.com', 'TJ');
//        $email->addTo($to);
//        $email->send();
//        echo 555;
//        exit;

        $this->_log('callback'.print_r($_POST, true));
//        echo '<pre>';

        $response = $this->checkPaymentStatus($_POST);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $orderInterface = $objectManager->create('Magento\Sales\Api\Data\OrderInterface');
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $order = $orderInterface->loadByIncrementId($response['reference_order']);

        if (!$order->getId()) {
            echo 'order not found';
        }

        $amount = (float)$order->getGrandTotal();
        $baseUrl = $storeManager->getStore()->getBaseUrl();

        $license = array();
        if ($response['transaction_state'] == 'Authorized'
            && $response['status'] == 'success'
            && $amount == $response['amount'] || true) {
            $this->auth = $this->helperData->auth();
            $tokenType = !empty($this->auth['token_type']) ? $this->auth['token_type'] : '';
            $accessToken = !empty($this->auth['access_token']) ? $this->auth['access_token'] : '';
            $license = $this->createOrder($response['reference_order'], $tokenType, $accessToken);
        }

        //re direct to success page
        header( "location: '.$baseUrl.'checkout/onepage/success/" );
        exit;
    }

    private  function checkPaymentStatus($resposeData)
    {
        //skey_test_20686cJpK9TmObAC2KOUWqJguxQUaSpmdAGYE
        $secretkey = $this->helperData->getConfigValue('payment/creditcard/secret_key'); // secret key ที่ได้จาก ธนาคาร
        //'https://dev-kpaymentgateway-services.kasikornbank.com/card/v2/charge';
        $urlPayment = $this->helperData->getConfigValue('payment/creditcard/payment_charge_url');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Cache-Control:no-cache',
                'x-api-key: '.$secretkey // ใส่ secretkey
            )
        );

        curl_setopt($ch, CURLOPT_URL, $urlPayment."/".$resposeData['objectId']); // ใส่ chrg id (ต้องตรงกับ object_id ที่รับมา) ที่ได้รับตอน step2.php
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);

        $data = curl_exec($ch);
        $response = json_decode($data);

        if (curl_errno($ch)){
            echo  curl_error($ch);
        }

        curl_close ($ch);

        $response = json_decode(json_encode($response), True);

        return $response;
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
        // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/callback.log');
        // $logger = new \Zend\Log\Logger();
        // $logger->addWriter($writer);
        // $logger->info($msg);

        $this->helperData->generalLog('/var/log/callback.log', $msg);
    }
}
