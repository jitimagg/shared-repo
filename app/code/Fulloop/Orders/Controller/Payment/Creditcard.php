<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Fulloop\Orders\Controller\Payment;

class Creditcard extends \Magento\Framework\App\Action\Action
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
        //'pkey_test_20686FMK9QVNqry0EjYBc6m8UWCMJA3AM0vU2';
        $publickey = $this->helperData->getConfigValue('payment/creditcard/public_key');  // public key ที่ได้จาก ธนาคาร
        //'https://dev-kpaymentgateway.kasikornbank.com/ui/v2/kpayment.min.js';
        $formJs = $this->helperData->getConfigValue('payment/creditcard/payment_js_url');
        $mid = $this->helperData->getConfigValue('payment/creditcard/merchant_id');

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


        $table .= '<tr><td></td><td>ราคารวม</td><td align="center">'.number_format($order->getGrandTotal(), 2).' ฿</td></tr>';

        $this->_log('Credit card - Pre => order id:'.$order->getIncrementId().'|| total:'.$order->getGrandTotal());

        $amount = number_format((float)$order->getGrandTotal(), 2, '.', '');

        echo $table . '
        <tr><td align="right" colspan="2"></td><td>
            <form method="POST" action="'.$baseUrl.'fulloop_orders/payment/paymentpage/">
            <input name="form_key" type="hidden" value="'.$FormKey->getFormKey().'">
            <input name="product_desc" type="hidden" value="dcc">
            <script type="text/javascript"
            src="'.$formJs.'"
            data-apikey="'.$publickey.'"
            data-amount="'.$amount.'"
            data-currency="THB"
            data-payment-methods="card"
            data-name="ITSolution Online"
            data-mid="'.$mid.'"
            >
            </script>
            </form>
            </td></tr>
            </table>
            <div style="text-align: center; margin-top: 20px;">
                <img width="30" src="' . $baseUrl . 'asset/bank_logo/kbank.png" style="display: inline-block">
                <img width="30" src="' . $baseUrl . 'asset/bank_logo/ktc.jpeg" style="display: inline-block">
                <img width="30" src="' . $baseUrl . 'asset/bank_logo/scb.png" style="display: inline-block">
                <img width="30" src="' . $baseUrl . 'asset/bank_logo/bay.png" style="display: inline-block">
                <img width="30" src="' . $baseUrl . 'asset/bank_logo/aeon.png" style="display: inline-block"><br>
                <p style="font-size: 12px;">*สำหรับบัตรกรุงศรีสามารถชำระด้วยบัตร Krungsri, Central, Tesco Lotus VISA</p>
            </div>
            </div>';

        exit;
    }

    private function _log($msg)
    {
        // $writer = new \Laminas\Log\Writer\Stream(BP . '/var/log/creditcard_controller.log');
        // $logger = new \Laminas\Log\Logger();
        // $logger->addWriter($writer);
        // $logger->info($msg);

         $this->helperData->generalLog('/var/log/creditcard_controller.log', $msg);
    }

}
