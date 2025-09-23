<?php

namespace Fulloop\Products\Controller\Update;

class Vst extends \Magento\Framework\App\Action\Action
{

    protected $logger;

    protected $_productRepository;

    /**
     * @var \Api\ServiceVst\Helper\Data
     */
    protected $helperData;

    protected $auth;
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

        $this->auth = $helperData->auth();

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
            $result = $this->helperData->edsPlans($this->auth);

            $data = [];
            if (!empty($result['data'])) {
                $data = $this->_save($result['data']);
            }

            $_log = !empty($data) ? implode(',', $data):'no data';
            $this->_log($_log);
        } catch (\Exception $e) {
            $this->_log($e->getMessage());
        }

    }

    private function _save($data)
    {
        if (empty($data)) {
            return [];
        }
        $resources = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magento\Framework\App\ResourceConnection');
        $connection= $resources->getConnection();

        $sql = "SELECT `e`.* FROM `catalog_product_entity` AS `e`";
        $pData = $connection->fetchAll($sql);

        $added = [];
        foreach ($pData as $v) {
            $added[$v['sku']] = $v;
        }

        $id = [];

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        foreach ($data as $k => $v) {
            $sku = trim($v['skuid']);
            $name = trim($v['planname']);
            $price = trim($v['pricing']['THB']['onetime_price']);
            if (!empty($added[$sku])) {
                $product = $this->_productRepository->get($sku);
                $product->setStoreId(0);
//                $product->setPrice($price);
                $product->setStockData(
                    array(
                        'use_config_manage_stock' => 0,
                        'manage_stock' => 1,
                        'is_in_stock' => 1,
                        'qty' => 99
                    )
                );
            } else {
                $product = $objectManager->create('\Magento\Catalog\Model\Product');
                $product->setSku($sku); // Set your sku here
                $product->setName($name); // Name of Product
                $product->setAttributeSetId(4); // Attribute set id
                $product->setStatus(1); // Status on product enabled/ disabled 1/0
//                $product->setWeight(10); // weight of product
                $product->setVisibility(4); // visibilty of product (catalog / search / catalog, search / Not visible individually)
                $product->setTaxClassId(0); // Tax class id
                $product->setTypeId('simple'); // type of product (simple/virtual/downloadable/configurable)
                $product->setPrice($price); // price of product
                $product->setReferenceId($v['planid']);
                $product->setReferenceSku($sku);
                $product->setIsMicrosoftLicense(true);
                $product->setWebsiteIds([1]);
                $product->setUseDefault(['status' => 1]);
//                $product->setCategoryIds([3]);
                $product->setStockData(
                    array(
                        'use_config_manage_stock' => 0,
                        'manage_stock' => 1,
                        'is_in_stock' => 1,
                        'qty' => 99
                    )
                );
            }
            $product->save();

            $id[] = $product->getId();
        }

        return $id;
    }

    private function _log($msg)
    {
        // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/vst_cron.log');
        // $logger = new \Zend\Log\Logger();
        // $logger->addWriter($writer);
        // $logger->info($msg);

        $this->helperData->generalLog('/var/log/vst_cron.log', $msg);
    }
}
