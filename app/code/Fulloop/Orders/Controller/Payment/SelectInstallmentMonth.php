<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Fulloop\Orders\Controller\Payment;

class SelectInstallmentMonth extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    protected $_checkoutSession;

    protected $helperData;

    protected $quoteFactory;

    protected $quoteRepository;

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
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Catalog\Model\ProductRepository $productRepository
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;

        $this->_checkoutSession = $_checkoutSession;

        $this->helperData = $helperData;

        $this->quoteFactory = $quoteFactory;

        $this->quoteRepository = $quoteRepository;

        $this->_productRepository = $productRepository;

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
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');

//        $baseUrl = $storeManager->getStore()->getBaseUrl();
        $baseUrl = $this->helperData->getConfigValue('web/secure/base_url');
        $order = $this->_checkoutSession->getLastRealOrder();
        $quoteId = $order->getQuoteId();
        $quote = $this->quoteFactory->create()->load($quoteId);

        if (!$order->getId()) {
            header("Location: " . $baseUrl);
            exit;
        }

        $quoteItems = $quote->getAllVisibleItems();
        $months = [];
        if (!empty($quoteItems)) {
            foreach ($quoteItems as $item) {
                $product = $item->getProduct();

                $p = $this->_productRepository->getById($product->getId());

                if (!$product->getIsInstallment()) {
                    echo 'มีสินค้าที่ไม่สามารถผ่อนชำระได้ <b><u><a href="'.$baseUrl.'">กลับหน้าหลัก</a></u></b>';
                    exit;
                } else {
                    $installmentMonth = $p->getInstallmentMonth() ? $p->getInstallmentMonth() : 3;
                    array_push($months, $installmentMonth);
                }
            }
        }

        $orderItems = $order->getAllVisibleItems();

        $limitMonth = !empty($months) ? min($months) : 3;

        $installmentOptions = '<option value=""></option>';
        if ($limitMonth == 3) {
            $installmentOptions .= '<option value="3">ผ่อนชำระ ดอกเบี้ย 0%, 3 เดือน</option>';
        }

        if ($limitMonth == 6) {
            $installmentOptions .= '<option value="3">ผ่อนชำระ ดอกเบี้ย 0%, 3 เดือน</option>';
            $installmentOptions .= '<option value="6">ผ่อนชำระ ดอกเบี้ย 0%, 6 เดือน</option>';
        }

        if ($limitMonth == 10) {
            $installmentOptions .= '<option value="3">ผ่อนชำระ ดอกเบี้ย 0%, 3 เดือน</option>';
            $installmentOptions .= '<option value="6">ผ่อนชำระ ดอกเบี้ย 0%, 6 เดือน</option>';
            $installmentOptions .= '<option value="10">ผ่อนชำระ ดอกเบี้ย 0%, 10 เดือน</option>';
        }

        $this->_log('Credit card - Pre => order id:'.$order->getIncrementId().'|| total:'.$order->getGrandTotal());


        $tableItem = '';
        if (!empty($orderItems)) {
            foreach ($orderItems as $item) {
                $product = $item->getProduct();

                $helperImport = $objectManager->get('\Magento\Catalog\Helper\Image');

                $imageUrl = $helperImport->init($product, 'product_page_image_small')
                    ->setImageFile($product->getSmallImage()) // image,small_image,thumbnail
                    ->resize(380)
                    ->getUrl();

                $tableItem .= '<tr>
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

        echo '
        <div class="container mt-5">
        <div><img width="200" src="' . $baseUrl . 'asset/Logo-ITSC.png" title="" alt=""></div>

            <div class="card">
            <div class="card-header" style="background: none;">
            <h3>Order Summary</h3>
        </div>
            <div class="card-body">
              <table class="table">
              ' . $tableItem . '
              <tr><td><strong class="fs-16">ราคารวม</strong></td><td></td><td align="center"><strong class="fs-16">' . number_format($order->getGrandTotal(), 2) . ' ฿</strong></td></tr>
              </table>
              <div class="row">
                  <div class="col-md-6">
                    <label class="form-label">เลือกจำนวนเดือนที่ต้องการผ่อนชำระ <span class="text-danger">*</span>: </label>
                    <select name="payment[installment_plan]" id="select-install-month" class="form-select" onchange="selecteInstallmentPlan(this);">
                        ' . $installmentOptions . '
                    </select>
                  </div>
              </div>
            </div>

            <div class="card-footer" style="background: none; text-align: center;">
              <input id="submit-installment" type="button" onclick="continueInstallment()" class="btn btn-large btn-success" value="Continue" />
            </div>
            </div>
            <div class="mt-5 text-center">
                <img width="30" src="' . $baseUrl . 'asset/bank_logo/kbank.png" style="display: inline-block">
                <img width="30" src="' . $baseUrl . 'asset/bank_logo/ktc.jpeg" style="display: inline-block">
                <img width="30" src="' . $baseUrl . 'asset/bank_logo/scb.png" style="display: inline-block">
                <img width="30" src="' . $baseUrl . 'asset/bank_logo/bay.png" style="display: inline-block">
                <img width="30" src="' . $baseUrl . 'asset/bank_logo/aeon.png" style="display: inline-block"><br/>
            <p style="font-size: 12px;">*สำหรับบัตรกรุงศรีสามารถชำระด้วยบัตร Krungsri, Central, Tesco Lotus VISA</p>
</div>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
        <script type="text/javascript">
                    function selecteInstallmentPlan(e) {
                        $("#submit-installment").hide();
                        let val = 3;
                        if ($(e).val()) {
                            val = $(e).val();
                        }

                        if (!val) {
                            return;
                        }

                        let myHeaders = new Headers();
                        myHeaders.append("Content-Type", "application/json");
                        let raw = JSON.stringify({"installment_period": val, "quote_id": "' . $quoteId . '"});

                        let requestOptions = {
                            method: "POST",
                            headers: myHeaders,
                            body: raw,
                            redirect: "follow"
                        };

                        fetch("'.$baseUrl.'rest/V1/fulloop-orders/set-quote", requestOptions)
                        .then(
                            response => {
                               $("#submit-installment").show();
                            }
                        ).then(
                            result => {
                                $("#submit-installment").show();
                            }
                        ).catch(
                            error => {
                                $("#submit-installment").show();
                            }
                        );
                    }

                    function continueInstallment() {
                        let select = $("#select-install-month").val();

                        if (!select) {
                            alert("กรุณาเลือกเดือนที่ต้องการผ่อนชำระ");
                            return;
                        } else {
                            window.location.href = "'.$baseUrl.'fulloop_orders/payment/creditcardinstallment/";
                        }
                    }
        </script>
        ';

        exit;
    }

    private function _log($msg)
    {
        // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/select_installment_month_controller.log');
        // $logger = new \Zend\Log\Logger();
        // $logger->addWriter($writer);
        // $logger->info($msg);

        $this->helperData->generalLog('/var/log/select_installment_month_controller.log', $msg);
    }

}
