<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Fulloop\Products\Controller\Update;

class Inventory extends \Magento\Framework\App\Action\Action
{

    protected $logger;

    protected $_productRepository;

    protected $helperData;
    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Api\ServiceVst\Helper\Data $helperData
    )
    {
        $this->_productRepository = $productRepository;

        $this->logger = $logger;

        $this->helperData = $helperData;

        parent::__construct($context);
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        try {
            $url = $this->helperData->getConfigValue('inventory/connection/url');
            $this->_log($url);
            $xmlString = file_get_contents($url);
            $xml = simplexml_load_string($xmlString, "SimpleXMLElement", LIBXML_NOCDATA);
            $json = json_encode($xml);
            $product = json_decode($json,TRUE);

            $data = [];
            if (!empty($product['row'])) {
                $data = $this->_save($product['row']);
            }

            $_log = !empty($data) ? implode(',', $data):'no data';
            $this->_log($_log);
            $this->logger->addInfo("Cronjob InventoryUpdate is executed.");
        } catch (\Exception $e) {
            $this->_log($e->getMessage());
        }

    }

    private function _save($data)
    {
        $resources = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magento\Framework\App\ResourceConnection');
        $connection= $resources->getConnection();

        $sql = "SELECT `e`.* FROM `catalog_product_entity` AS `e`";
        $pData = $connection->fetchAll($sql);

        $added = [];
        foreach ($pData as $v) {
            $added[md5($v['sku'])] = $v;
        }

        $id = [];

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $code = [];
        foreach ($data as $k => $v) {
            $productCode = trim($v['ProductCode']);
            $productName = trim($v['ProductName']);
            if (!empty($added[md5($productCode)])) {
                $code['update'][] = $productCode;

                try {
                    $product = $this->_productRepository->get($productCode);
                    $product->setStoreId(0);
//                    $product->setPrice(trim($v['Price']));
                    $product->setStockData(
                        array(
                            'use_config_manage_stock' => 0,
                            'manage_stock' => 1,
                            'is_in_stock' => ((!empty($v['QtyBalance'])) ? 1 : 0),
                            'qty' => trim($v['QtyBalance'])
                        )
                    );
                    $product->save();
                } catch(\Exception $e) {
                    $this->_log($e->getMessage().':'.$productCode);
                }
            } else {
                $code['insert'][] = $productCode;
                $product = $objectManager->create('\Magento\Catalog\Model\Product');
                try {
                    $product->setSku($productCode);
                    $product->setName($productName.' '.$productCode);
                    $product->setAttributeSetId(4);
                    $product->setStatus(0);
                    $product->setVisibility(4);
                    $product->setTaxClassId(0);
                    $product->setTypeId('simple');
                    $product->setPrice(trim($v['Price']));
                    $product->setReferenceSku($productCode);
                    $product->setWebsiteIds([1]);
                    $product->setUseDefault(['status' => 1]);
                    $product->setStockData(
                        array(
                            'use_config_manage_stock' => 0,
                            'manage_stock' => 1,
                            'is_in_stock' => ((!empty($v['QtyBalance'])) ? 1 : 0),
                            'qty' => $v['QtyBalance']
                        )
                    );
                    $product->save();
                } catch(\Exception $e) {
                    $this->_log($e->getMessage().':'.$productCode);
                }
            }

            $id[] = $product->getId();
        }

        if (!empty($code['update'])) {
            $this->_log(implode(',', $code['update']));
        }

        if (!empty($code['insert'])) {
            $this->_log(implode(',', $code['insert']));
        }

        return $id;
    }

    private function _log($msg)
    {
        // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/inventory_cron.log');
        // $logger = new \Zend\Log\Logger();
        // $logger->addWriter($writer);
        // $logger->info($msg);

         $this->helperData->generalLog('/var/log/inventory_cron.log', $msg);
    }
}
