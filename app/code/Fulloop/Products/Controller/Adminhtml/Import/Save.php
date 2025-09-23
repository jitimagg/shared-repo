<?php
/**
 * Copyright © Megnor, Inc. All rights reserved.
 */

namespace Fulloop\Products\Controller\Adminhtml\Import;

use Magento\Framework\View\Result\Page;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Catalog\Model\Product\Attribute\Source\Status;

class Save extends \Fulloop\Products\Controller\Adminhtml\Import
{

    protected $_collectionFactory;

    protected $_storeManager;

     protected $helperData;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collecionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Api\ServiceVst\Helper\Data $helperData
    )
    {
        $this->helperData = $helperData;
        $this->_collectionFactory = $collecionFactory;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory, $formKey, $productRepository);
    }

    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            try {
                $post_value = $this->getRequest()->getPostValue();
                $ext = pathinfo($post_value['name'], PATHINFO_EXTENSION);
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader(ucfirst($ext));
                /**  Load $inputFileName to a Spreadsheet Object  **/
                $spreadsheet = $reader->load($post_value['path'].$post_value['file']);
                $worksheet = $spreadsheet->getActiveSheet();

                $lists = array();
                foreach ($worksheet->getRowIterator() as $key => $row) {
                    if ($key == 1) {
                        continue;
                    }
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(FALSE); // This loops through all cells,
                    $list_cell = array();
                    $add = 0;
                    foreach ($cellIterator as $col => $cell) {
                        $val = trim($cell->getValue());
                        $list_cell[] = $val;
                        if (!empty($val)) {
                            $add++;
                        }
                    }

                    if ($add > 0) {
                        $lists[] = $list_cell;
                    }
                }

                $this->_log(json_encode($lists));
                $data = [];
                if (!empty($lists)) {
                    $data = $this->_save($lists);
                }

                $_log = !empty($data) ? implode(',', $data):'no data';
                $this->_log($_log);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->_log($e->getMessage());
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->_log($e->getMessage());
                $this->messageManager->addError(
                    __('Something went wrong while saving the item data. Please review the error log.')
                );
            }
        }
        $this->_redirect('catalog/product/index/');
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
            $brand = trim($v[0]);
            $collection = $this->_collectionFactory
                ->create()
                ->addAttributeToFilter('name',$brand)
                ->setPageSize(1);
            if ($collection->getSize()) {
                $categoryId = $collection->getFirstItem()->getId();
            } else {
                $categoryId = 0;
                // insert category
            }
            $productCode = trim($v[1]);
            $productName = trim($v[2]);
            $price = trim($v[3]);
            $qty = trim($v[4]);
            if (!empty($added[md5($productCode)])) {
                $code['update'][] = $productCode;

                try {
                    $product = $this->_productRepository->get($productCode);
                    $product->setStoreId(0);
                    $product->setPrice($price);
                    $product->setCategoryIds([$categoryId]);
                    $product->setStockData(
                        array(
                            'use_config_manage_stock' => 0,
                            'manage_stock' => 1,
                            'is_in_stock' => ((!empty($qty)) ? 1 : 0),
                            'qty' => $qty
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
                    $product->setName($productName);
                    $product->setAttributeSetId(4);
                    $product->setStatus(0);
                    $product->setVisibility(4);
                    $product->setTaxClassId(0);
                    $product->setTypeId('simple');
                    $product->setPrice($price);
//                    $product->setReferenceSku($productCode);
                    $product->setWebsiteIds([1]);
                    $product->setCategoryIds([$categoryId]);
                    $product->setUseDefault(['status' => 1]);
                    $product->setStockData(
                        array(
                            'use_config_manage_stock' => 0,
                            'manage_stock' => 1,
                            'is_in_stock' => ((!empty($qty)) ? 1 : 0),
                            'qty' => $qty
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
        // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/import_product.log');
        // $logger = new \Zend\Log\Logger();
        // $logger->addWriter($writer);
        // $logger->info($msg);

         $this->helperData->generalLog('/var/log/import_product.log', $msg);
    }
}
