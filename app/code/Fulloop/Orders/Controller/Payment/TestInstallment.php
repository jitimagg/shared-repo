<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Fulloop\Orders\Controller\Payment;

class TestInstallment extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    protected $_checkoutSession;

    protected $helperData;

    protected $quoteFactory;

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
        \Magento\Quote\Model\QuoteFactory $quoteFactory
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;

        $this->_checkoutSession = $_checkoutSession;

        $this->helperData = $helperData;

        $this->quoteFactory = $quoteFactory;

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

        $baseUrl = $storeManager->getStore()->getBaseUrl();
        //'pkey_test_20686FMK9QVNqry0EjYBc6m8UWCMJA3AM0vU2';
        $publickey = $this->helperData->getConfigValue('payment/creditcard_installment/public_key');  // public key ที่ได้จาก ธนาคาร
        //'https://dev-kpaymentgateway.kasikornbank.com/ui/v2/kpayment.min.js';
        $formJs = $this->helperData->getConfigValue('payment/creditcard_installment/payment_js_url');
        $mid = $this->helperData->getConfigValue('payment/creditcard_installment/merchant_id');

        $order = $this->_checkoutSession->getLastRealOrder();
        $quoteId = $order->getQuoteId();
        $quote = $this->quoteFactory->create()->load($quoteId);
        $period = $quote->getInstallmentPeriod();
        if (empty($period)) {
            $period = 3;
        }

        $quoteItems = $quote->getAllVisibleItems();
        $months = [];
        if (!empty($quoteItems)) {
            foreach ($quoteItems as $item) {
                $product = $item->getProduct();
                echo 555;
                echo $product->getIsInstallment();
                echo $product->getName();
echo $product->getInstallmentMonth();
                if (!$product->getIsInstallment()) {
                    echo 'มีสินค้าที่ไม่สามารถผ่อนชำระได้ <b><u><a href="'.$baseUrl.'">กลับหน้าหลัก</a></u></b>';
                    exit;
                } else {
                    array_push($months, $product->getInstallmentMonth());
                }
            }
        }

        echo '<pre>';print_r($months, 1);

        $months = [10];

        $orderItems = $order->getAllVisibleItems();
        $table = '';
        $table .= '<div style="margin: auto; margin-top: 70px; width: 35%;">
<div><img width="200" src="https://itsolution.co.th/pub/media/logo/stores/1/New-Logo-ITSC-2021.png" title="" alt=""></div>
<table cellpadding="10" border="0" cellspacing="0" style="padding: 25px; margin: auto; border: solid #c9dae1 1px; border-radius: 10px; width: 100%">';
        $table .= '<tr>
            <td colspan="2"><h3>Order Summary</h3></td>
        </tr>';
        if (!empty($orderItems)) {
//            $table .= '<tr><th>ชื่อสินค้า</th><th>จำนวน</th><th>ราคา</th></tr>';
            foreach ($orderItems as $item) {
                $product = $item->getProduct();
//                $table .= '<tr>
//                                <td>' . $product->getName() . '</td>
//                                <td align="center">' . (int)$item->getQtyOrdered() . '</td>
//                                <td align="center">' . number_format($item->getRowTotalInclTax(), 2) . '</td>
//                            </tr>';
                $table .= '<tr>
            <td>
                <p><b>' . $product->getName() . '</b></p>
                <p>Quantity: ' . (int)$item->getQtyOrdered() . '</p>
            </td>
            <td align="center">' . number_format($item->getRowTotalInclTax(), 2) . ' ฿</td>
        </tr>';
            }
        }
        $table .= '<tr>
            <td colspan="2">
                <hr/>
            </td>
        </tr>';


        $table .= '<tr><td>ราคารวม</td><td align="center">'.number_format($order->getGrandTotal(), 2).' ฿</td></tr>';

        $table .= '<tr>
            <td colspan="2">
                <hr/>
            </td>
        </tr>';
        $limitMonth = min($months);

        $installmentOptions = '';
        if ($limitMonth == 3) {
            $installmentOptions .= '<option value="3">3 เดือน</option>';
        }

        if ($limitMonth == 6) {
            $installmentOptions .= '<option value="3">3 เดือน</option>';
            $installmentOptions .= '<option value="6">6 เดือน</option>';
        }

        if ($limitMonth == 10) {
            $installmentOptions .= '<option value="3">3 เดือน</option>';
            $installmentOptions .= '<option value="6">6 เดือน</option>';
            $installmentOptions .= '<option value="10">10 เดือน</option>';
        }

        $table .= '<tr>
            <td colspan="2">
                ผ่อนชำระ ดอกเบี้ย 0%,

                <select name="payment[installment_plan]" onchange="selecteInstallmentPlan(this);">
                    '.$installmentOptions.'
                </select>
            </td>
        </tr>';

        $this->_log('Credit card - Pre => order id:'.$order->getIncrementId().'|| total:'.$order->getGrandTotal());

        $amount = number_format((float)$order->getGrandTotal(), 2, '.', '');

        $table .= '<tr>
            <td colspan="2">
                <hr/>
            </td>
        </tr>';
        echo $table . '
        <tr><td colspan="2" align="center">
            <form method="POST" action="'.$baseUrl.'fulloop_orders/payment/paymentpage/">
            <input name="form_key" type="hidden" value="'.$FormKey->getFormKey().'">
            <input name="product_desc" type="hidden" value="mcc">
            <script id="script-kbank" type="text/javascript"
            src="'.$formJs.'"
            data-apikey="'.$publickey.'"
            data-amount="'.$amount.'"
            data-currency="THB"
            data-payment-methods="card"
            data-name="ITSolution Online"
            data-mid="'.$mid.'"
            data-smartpay-id="0001"
            data-term=""
            >
            </script>
            </form>
            </td></tr>
            </table>
            </div>
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
            <script type="text/javascript">
            function selecteInstallmentPlan(e) {console.log($(e).val())


                let val = 3;
                if ($(e).val()) {
                    val = $(e).val();
                }

                $("#script-kbank").data("term", val)
console.log($("#script-kbank").data("term"))
                let myHeaders = new Headers();
                myHeaders.append("Content-Type", "application/json");
                let raw = JSON.stringify({"installment_period": val});

                let requestOptions = {
                    method: "POST",
                    headers: myHeaders,
                    body: raw,
                    redirect: "follow"
                };

                fetch("/rest/V1/fulloop-orders/set-quote", requestOptions)
                .then(
                    response => {
                        response.text();
                    }
                ).then(
                    result => {
                    }
                ).catch(
                    error => {
                    }
                );
            }
</script>';

        exit;
    }

    private function _log($msg)
    {
        // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/creditcard_installment_controller.log');
        // $logger = new \Zend\Log\Logger();
        // $logger->addWriter($writer);
        // $logger->info($msg);

        $this->helperData->generalLog('/var/log/creditcard_installment_controller.log', $msg);
    }

}
