<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Fulloop\Orders\Controller\Payment;

class Paymentpage extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    protected $_checkoutSession;

    protected $helperData;

    /**
     * Constructor.
     *
     * @param Magento\Framework\HTTP\Client\Curl $curl
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Checkout\Model\Session $_checkoutSession,
        \Api\ServiceVst\Helper\Data $helperData
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;

        $this->_checkoutSession = $_checkoutSession;

        $this->helperData = $helperData;

        parent::__construct($context);
    }

    /**
     * View  page action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $order = $this->_checkoutSession->getLastRealOrder();
        $incrementId = $order->getIncrementId();

        $this->_log('payment page => order id:'.$order->getIncrementId().'|| total:'.$order->getGrandTotal());

        if(!empty($_POST['token'])){
            $token = $_POST['token']; // รับค่า TOKEN จากระบบธนาคาร

            $datasend = array(  // ค่าที่ต้องส่ง อันนี้อาจต้องดูในคู่มือว่าตัวไหนจำเป็นต้องใส่บ้าง
                "amount"=> $order->getGrandTotal(),
                "currency"=> "THB",
                "description" => 'Order: '."{$incrementId}",
                "source_type" => "card",
                "mode"=> "token",
                "token"=> $token,
                "savecard" => false,
                "reference_order"=> $incrementId,
            );

            $product_desc = !empty($_POST['product_desc']) ? $_POST['product_desc'] : '';

            $secretkey = '';
            $urlPayment = '';
            switch ($product_desc) {
                case 'dcc':
                    //'skey_test_20686cJpK9TmObAC2KOUWqJguxQUaSpmdAGYE';
                    $secretkey = $this->helperData->getConfigValue('payment/creditcard/secret_key');
                    //'https://dev-kpaymentgateway-services.kasikornbank.com/card/v2/charge';
                    $urlPayment = $this->helperData->getConfigValue('payment/creditcard/payment_charge_url');
                    $mid = $this->helperData->getConfigValue('payment/creditcard/merchant_id');
                    $tid = $this->helperData->getConfigValue('payment/creditcard/term_id');
                    $datasend['dcc_data'] = array(
                        "dcc_currency" => "THB"
                    );
                    $datasend['additional_data'] = array(
                        "mid" => $mid,
                        "tid" => $tid
                    );
                    break;
                case 'mcc';
                    //'skey_test_20686cJpK9TmObAC2KOUWqJguxQUaSpmdAGYE';
                    $secretkey = $this->helperData->getConfigValue('payment/creditcard_installment/secret_key');
                    //'https://dev-kpaymentgateway-services.kasikornbank.com/card/v2/charge';
                    $urlPayment = $this->helperData->getConfigValue('payment/creditcard_installment/payment_charge_url');
                    $mid = $this->helperData->getConfigValue('payment/creditcard_installment/merchant_id');
                    $tid = $this->helperData->getConfigValue('payment/creditcard_installment/term_id');
                    $datasend['additional_data'] = array(
                        "mid" => $mid,
                        "tid" => $tid,
                        "smartpay_id" => $_POST['smartpayId'],
                        "term" => $_POST['term']
                    );
                    break;
                default:
                    break;
            }


            $ch = curl_init();
            $post_string = json_encode($datasend);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Cache-Control:no-cache',
                    'x-api-key: '.$secretkey // ใส่ Secret Key
                )
            );

            curl_setopt($ch, CURLOPT_URL, $urlPayment);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
            curl_setopt($ch, CURLOPT_SSLVERSION, 0 );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_VERBOSE, true);

            $data = curl_exec($ch);
            $response = json_decode($data);

            curl_close ($ch);

            $response = json_decode(json_encode($response), True);   // ค่าที่ได้รับกลับมาจะอยู่ในตัวแปรนี้จะมีค่าที่สำคัญคือ chrg id เอาไว้ยืนยันตอนสุดท้ายในขั้นตอนที่ 4

            if (empty($response['redirect_url'])) {
                return 'emty data';
            }

            if(ob_get_length() > 0) {
                ob_clean();
            }
            header('Location: '.$response['redirect_url']); // ทำการ Redirect ไปหน้ายื่นยันตัวตนลูกค้า
        }

        exit;
    }

    private function _log($msg)
    {
        // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/payment_page.log');
        // $logger = new \Zend\Log\Logger();
        // $logger->addWriter($writer);
        // $logger->info($msg);

         $this->helperData->generalLog('/var/log/payment_page.log', $msg);
    }

}
