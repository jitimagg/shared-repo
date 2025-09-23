<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Fulloop\Orders\Controller\Payment;

class Qrcode extends \Magento\Framework\App\Action\Action
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
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');

//        $baseUrl = $storeManager->getStore()->getBaseUrl();
        $baseUrl = $this->helperData->getConfigValue('web/secure/base_url');
        $secretkey = $this->helperData->getConfigValue('payment/qrcode/secret_key');
        $publickey = $this->helperData->getConfigValue('payment/qrcode/public_key');  // public key ที่ได้จาก ธนาคาร
        $formJs = $this->helperData->getConfigValue('payment/qrcode/payment_js_url');
        $orderUrl = $this->helperData->getConfigValue('payment/qrcode/payment_order_url');


        $order = $this->_checkoutSession->getLastRealOrder();

        if (!$order->getId()) {
            header("Location: " . $baseUrl);
            exit;
        }

        $orderItems = $order->getAllVisibleItems();
        $table = '';
        $table .= '<div style="margin: auto; margin-top: 70px; width: 35%;">
<div><img width="200" src="' . $baseUrl . 'asset/Logo-ITSC.png" title="" alt=""></div>

<table cellpadding="10" border="0" cellspacing="0" style="padding: 25px; margin: auto; border: solid #c9dae1 1px; border-radius: 10px; width: 100%">';
        $table .= '<tr>
            <td colspan="3"><h3>Order Summary</h3></td>
        </tr>';
        if (!empty($orderItems)) {
            foreach ($orderItems as $item) {
                $product = $item->getProduct();

                $helperImport = $objectManager->get('\Magento\Catalog\Helper\Image');

                $imageUrl = $helperImport->init($product, 'product_page_image_small')
                    ->setImageFile($product->getSmallImage()) // image,small_image,thumbnail
                    ->resize(380)
                    ->getUrl();
                $table .= '<tr>
<td>
                                      <img width="100" src="'.$imageUrl.'" alt="' . $product->getName() . '" class="img-thumbnail">
                                    </td>
            <td>
                <p><b>' . $product->getName() . '</b></p>
                <p>Quantity: ' . (int)$item->getQtyOrdered() . '</p>
            </td>
            <td align="center">' . number_format($item->getRowTotalInclTax(), 2) . ' ฿</td>
        </tr>';
            }
        }

        $table .= '<tr>
            <td colspan="3">
                <hr/>
            </td>
        </tr>';

        $table .= '<tr>
<td></td><td>ราคารวม</td><td align="center">'.number_format($order->getGrandTotal(), 2).' ฿</td>
</tr>';

        $this->_log('Credit card - Pre => order id:'.$order->getIncrementId().'|| total:'.$order->getGrandTotal());

        $table .= '<tr>
            <td colspan="3">
                <hr/>
            </td>
        </tr>';
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
                "description": "'.$order->getIncrementId().'",
                "source_type": "qr",
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
        data-payment-methods="qr"
        data-order-id="'.$resp['id'].'"
        >
        </script>
        </form>
        </td></tr>
        </table>
            </div>';

        exit;
    }

    private function _log($msg)
    {
        // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/qrocode_controller.log');
        // $logger = new \Zend\Log\Logger();
        // $logger->addWriter($writer);
        // $logger->info($msg);

         $this->helperData->generalLog('/var/log/qrocode_controller.log', $msg);
    }

}
