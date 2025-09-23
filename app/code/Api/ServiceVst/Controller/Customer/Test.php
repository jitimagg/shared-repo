<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Api\ServiceVst\Controller\Customer;
use \Magento\Framework\DataObject;

class Test extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Api\ServiceVst\Helper\Data
     */
    protected $helperData;

    protected $auth;

    protected $_productRepository;

    protected $_storeManager;

    protected $_urlbiulder;
    /**
    * Constructor.
    *
    * @param Magento\Framework\HTTP\Client\Curl $curl
    */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Api\ServiceVst\Helper\Data $helperData,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlBiulder
    )
    {
//        if (empty($_GET['debug']) || $_GET['debug'] != 'eyJ0eXAiOiJKV1QiLCJhbGciOiJTSEE') {
//            header( "location: http://google.com" );
//            exit(0);
//        }

        $this->_urlbiulder = $urlBiulder;

        $this->_productRepository = $productRepository;

        $this->resultJsonFactory = $resultJsonFactory;

        $this->helperData = $helperData;

        $this->_storeManager = $storeManager;

//        $this->auth = $helperData->auth();

        parent::__construct($context);
    }

    /**
     * View  page action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        echo '<form method="POST" action="/checkout">
<script type="text/javascript"
src="https://dev-kpaymentgateway.kasikornbank.com/ui/v2/kpayment.min.js"
data-apikey="pkey_prod_75677dushd74774gdgdgd77d7dhsgfhfghfhgdh"
data-amount="74.00"
data-currency="THB"
data-payment-methods="card"
data-name="Your Shop Name"
data-mid="401001001001001"
>
</script>
</form>';
        exit;

        echo $this->_urlbiulder->getUrl('vst/customer/test');
        echo '<form name="sendform" method="POST" action="'.$this->_urlbiulder->getUrl('vst/customer/test').'">
<input type="text" name="test"/>
<input type="submit" />
        <form>';
        print_r($_POST);
        exit;

        echo '<pre>';

        $form = [
            'MERCHANT2' => $this->helperData->getConfigValue('payment/creditcard/merchant_id'),
            'TERM2' => $this->helperData->getConfigValue('payment/creditcard/term_id'),
            'AMOUNT2' => '000000000100',
            'URL2' => $this->helperData->getConfigValue('payment/creditcard/callback_url'),
            'RESPURL' => $this->helperData->getConfigValue('payment/creditcard/resp_url'),
            'IPCUST2' => '127.0.0.1',
            'DETAIL2' => 'Test Payment',
            'INVMERCHANT' => '000000998105',
            'FILLSPACE' => 'Y',
            'SHOPID' => 'TH',
//            'PAYTERM2' => '03'
        ];

        echo implode('', array_values($form)).$this->helperData->getConfigValue('payment/creditcard/md5_checksum').'<br/>';
        $form['CHECKSUM'] = md5(implode('', array_values($form)).$this->helperData->getConfigValue('payment/creditcard/md5_checksum'));

        print_r(array_values($form));
        //prepare form
        $action = $this->helperData->getConfigValue('payment/creditcard/payment_url_desktop');
        echo '
                <!--<form name=sendform method=post action="https://rt05.kasikornbank.com/pggroup/payment.aspx">-->
                <!-- url for mobile site https://uatkpgw.kasikornbank.com/pgpayment/payment.aspx-->
                <!--<form name=sendform method=post action="https://rt05.kasikornbank.com/mobilepay/payment.aspx">-->
                <!-- normal url for visa,master -->
                <form name="sendform" method="POST" action="'.$action.'">';
        foreach ($form as $k => $v) {
            echo '<input type="text" name="' . $k . '" value="' . $v . '"/><br/>';
        }
        echo '</form>
                    <button onclick="document.sendform.submit();">Submit</button><!--<script>document.sendform.submit();</script>-->';

        exit;

//        $xmlString = file_get_contents('http://209.97.140.145/dev-itsolution/Inventory.xml');
//        $xml = simplexml_load_string($xmlString, "SimpleXMLElement", LIBXML_NOCDATA);
//        $json = json_encode($xml);
//        $product = json_decode($json,TRUE);
//
//        if (!empty($product['row'])) {
//            $this->_save($product['row']);
//        }


        exit;

//        return $this->resultJsonFactory->create()->setData($this->auth);
    }

    private function _save($data)
    {

//        $product = $this->_productRepository->get('TZE-231');
//        $product->setPrice(trim(600));
//        $product->setStockData(
//            array(
//                'use_config_manage_stock' => 0,
//                'manage_stock' => 1,
//                'is_in_stock' => ((!empty(55)) ? 1 : 0),
//                'qty' => 55
//            )
//        );
//        $product->save();
//        print_r($product->getName());exit;
//        exit;
        echo '<pre>';

        $resources = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magento\Framework\App\ResourceConnection');
        $connection= $resources->getConnection();

        $sql = "SELECT `e`.* FROM `catalog_product_entity` AS `e`";
        $customerData = $connection->fetchAll($sql);

        $added = [];
        foreach ($customerData as $v) {
            $added[$v['sku']] = $v;
        }

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        foreach ($data as $k => $v) {

            if ($k > 10) {
                continue;
            }

            if (!empty($added[trim($v['ProductCode'])])) {
                $product = $this->_productRepository->get(trim($v['ProductCode']));
                $product->setPrice(trim(500));
                $product->setStockData(
                    array(
                        'use_config_manage_stock' => 0,
                        'manage_stock' => 1,
                        'is_in_stock' => ((!empty(33)) ? 1 : 0),
                        'qty' => 33
                    )
                );
            } else {
                //            echo $v['ProductCode'].'<br/>';
//            echo $v['ProductName'].'<hr/>';continue;
                // instance of object manager
                $product = $objectManager->create('\Magento\Catalog\Model\Product');
                $product->setSku(trim($v['ProductCode'])); // Set your sku here
                $product->setName(trim($v['ProductName']).' '.trim($v['ProductCode'])); // Name of Product
                $product->setAttributeSetId(4); // Attribute set id
                $product->setStatus(0); // Status on product enabled/ disabled 1/0
                $product->setWeight(10); // weight of product
                $product->setVisibility(4); // visibilty of product (catalog / search / catalog, search / Not visible individually)
                $product->setTaxClassId(0); // Tax class id
                $product->setTypeId('simple'); // type of product (simple/virtual/downloadable/configurable)
                $product->setPrice(trim($v['Price'])); // price of product
//            $product->setReferenceId($v['planid']);
                $product->setReferenceSku(trim($v['ProductCode']));
//            $product->setIsMicrosoftLicense(true);
                $product->setWebsiteIds([1]);
//            $product->setCategoryIds([3]);
                $product->setStockData(
                    array(
                        'use_config_manage_stock' => 0,
                        'manage_stock' => 1,
                        'is_in_stock' => ((!empty($v['QtyBalance'])) ? 1 : 0),
                        'qty' => $v['QtyBalance']
                    )
                );
            }

            $product->save();

            echo $product->getId().'<br/>';
        }

    }

}
