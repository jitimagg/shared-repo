<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Fulloop\Orders\Controller\Payment;

use Magento\Framework\App\Bootstrap;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Filesystem;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Model\Product\Gallery\Processor as GalleryProcessor;
use Magento\Catalog\Model\ResourceModel\Product\Gallery as ResourceModelGallery;

class ImportProductRemove extends \Magento\Framework\App\Action\Action
{

    protected $csv;
    protected $directoryList;
    protected $productRepository;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\File\Csv $csv,
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    )
    {
        $this->csv = $csv;
        $this->directoryList = $directoryList;
        $this->productRepository = $productRepository;

        parent::__construct($context);
    }

    /**
     * View  page action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
echo date('Y-m-d H:i:s') .'<br/>';
        $bootstrap = Bootstrap::create(BP, $_SERVER);
        $obj = $bootstrap->getObjectManager();

// Set area code
        $appState = $obj->get(\Magento\Framework\App\State::class);
        $appState->setAreaCode('adminhtml');

// Dependencies
        $productRepository = $obj->get(ProductRepositoryInterface::class);
        $galleryProcessor = $obj->get(GalleryProcessor::class);
        $productGallery = $obj->get(ResourceModelGallery::class);

        $folder = $this->directoryList->getPath('pub').'/media/import/custom_import/';

        $csvPath = $folder . 'csv';

        $fileCsvs = scandir($csvPath);

        $imageType = ['image', 'small_image', 'thumbnail'];
        foreach ($fileCsvs as $fileCsv) {
            $filePath = $csvPath . '/' . $fileCsv;
            if ($fileCsv !== '.' && $fileCsv !== '..' && is_file($filePath)) {
                if (file_exists($filePath)) {
                    $products = [];
                    // Returns array of rows from CSV
                    $data = $this->csv->getData($filePath);
                    foreach ($data as $i => $v) {
                        if ($i == 0 || !$v[0]) {
                            continue;
                        }

                        $sku = $v[0];
                         
                        try {
                            $productRepo = $this->productRepository->get($sku);
                        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                            echo ' -- ' . $sku .' -- no data<br/>';
                            continue;
                        }
                    

                        $mediaGalleryEntries = $productRepo->getMediaGalleryEntries();
                        foreach ($mediaGalleryEntries as $entry) {
                            $productGallery->deleteGallery($entry->getValueId());
                            $galleryProcessor->removeImage($productRepo, $entry->getFile());
                             
                            echo  $entry->getFile().'<br/>';
                        }


                        $productRepo->setImage('no_selection');
                        $productRepo->setSmallImage('no_selection');
                        $productRepo->setThumbnail('no_selection');

                        $productRepo->setStoreId(0);
                        $productRepository->save($productRepo);
                    }
                }
            }
        }

echo date('Y-m-d H:i:s').'<br/>';
        echo $i;
        exit;
    }

}
