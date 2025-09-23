<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Fulloop\Orders\Controller\Payment;

class Wechat extends \Magento\Framework\App\Action\Action
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
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $FormKey = $objectManager->get('Magento\Framework\Data\Form\FormKey');

        $secretkey = $this->helperData->getConfigValue('payment/wechat/secret_key');
        $publickey = $this->helperData->getConfigValue('payment/wechat/public_key');  // public key ที่ได้จาก ธนาคาร
        $formJs = $this->helperData->getConfigValue('payment/wechat/payment_js_url');
        $orderUrl = $this->helperData->getConfigValue('payment/wechat/payment_order_url');

        $order = $this->_checkoutSession->getLastRealOrder();

        $orderItems = $order->getAllVisibleItems();
        $table = '';
        if (!empty($orderItems)) {
            $table .= '<table cellpadding="5" border="1" cellspacing="0" style="margin: auto;">';
            $table .= '<tr><th>ชื่อสินค้า</th><th>จำนวน</th><th>ราคา</th></tr>';
            foreach ($orderItems as $item) {
                $product = $item->getProduct();
                $table .= '<tr>
                                <td>' . $product->getName() . '</td>
                                <td align="center">' . $item->getQtyOrdered() . '</td>
                                <td align="center">' . number_format($item->getRowTotalInclTax(), 2) . '</td>
                            </tr>';
            }
        }
        $table .= '<tr><td align="right" colspan="2">ราคารวม</td><td align="center">'.number_format($order->getGrandTotal(), 2).'</td></tr>';

        $this->_log('Wechat - Pre => order id:'.$order->getIncrementId().'|| total:'.$order->getGrandTotal());

        $amount = number_format((float)$order->getGrandTotal(), 2, '.', '');

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $orderUrl,//'https://dev-kpaymentgateway-services.kasikornbank.com/qr/v2/order',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
                "amount": '.$amount.',
                "currency": "THB",
                "description": "'.$order->getIncrementId().'"
                "source_type": "wechat",
                "reference_order": "'.$order->getIncrementId().'"
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'x-api-key: '.$secretkey
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $resp = json_decode($response, true);

        echo $table . '
        <tr><td align="right" colspan="2"></td><td>
        <form method="POST" action="/checkout">
        <input name="form_key" type="hidden" value="'.$FormKey->getFormKey().'">
        <script type="text/javascript"
        src="'.$formJs.'"
        data-apikey="'.$publickey.'"
        data-amount="'.$amount.'"
        data-payment-methods="wechat"
        data-order-id="'.$resp['id'].'"
        >
        </script>
        </form>
        </td></tr>
        </table>';

        exit;
    }

    private function _log($msg)
    {
        // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/wechat_controller.log');
        // $logger = new \Zend\Log\Logger();
        // $logger->addWriter($writer);
        // $logger->info($msg);

        $this->helperData->generalLog('/var/log/wechat_controller.log', $msg);
    }

}
