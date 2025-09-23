<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Api\ServiceVst\Controller\Product;

class UpdatePlans extends \Magento\Framework\App\Action\Action
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

    /**
     * Constructor.
     *
     * @param Magento\Framework\HTTP\Client\Curl $curl
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Api\ServiceVst\Helper\Data $helperData
    )
    {
        if (empty($_GET['debug']) || $_GET['debug'] != 'eyJ0eXAiOiJKV1QiLCJhbGciOiJTSEE') {
            header( "location: http://google.com" );
            exit(0);
        }

        $this->resultJsonFactory = $resultJsonFactory;

        $this->helperData = $helperData;

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
        $result = $this->helperData->edsPlans($this->auth);

        if (empty($result['data'])) {
            echo 'no data';
            exit;
        }

        $this->_save($result['data']);
    }

    private function _save($data)
    {
        if (empty($data)) {
            return [];
        }
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        foreach ($data as $v) {
             // instance of object manager
            $product = $objectManager->create('\Magento\Catalog\Model\Product');
            $product->setSku($v['skuid']); // Set your sku here
            $product->setName($v['planname']); // Name of Product
            $product->setAttributeSetId(4); // Attribute set id
            $product->setStatus(1); // Status on product enabled/ disabled 1/0
            $product->setWeight(10); // weight of product
            $product->setVisibility(4); // visibilty of product (catalog / search / catalog, search / Not visible individually)
            $product->setTaxClassId(0); // Tax class id
            $product->setTypeId('simple'); // type of product (simple/virtual/downloadable/configurable)
            $product->setPrice($v['pricing']['THB']['onetime_price']); // price of product
            $product->setReferenceId($v['planid']);
            $product->setReferenceSku($v['skuid']);
            $product->setIsMicrosoftLicense(true);
            $product->setWebsiteIds([1]);
            $product->setCategoryIds([3]);
            $product->setStockData(
                array(
                    'use_config_manage_stock' => 0,
                    'manage_stock' => 1,
                    'is_in_stock' => 1,
                    'qty' => 999
                )
            );
            $product->save();

            echo $product->getId().'<br/>';
        }

    }

}
