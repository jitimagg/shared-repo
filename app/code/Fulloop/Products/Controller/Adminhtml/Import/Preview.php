<?php
/**
 * Copyright © Megnor, Inc. All rights reserved.
 */

namespace Fulloop\Products\Controller\Adminhtml\Import;

use Magento\Framework\View\Result\Page;
use Magento\Framework\App\Filesystem\DirectoryList;

class Preview extends \Fulloop\Products\Controller\Adminhtml\Import
{

    const MENU_ID = 'Fulloop_Products::import_product';
    public $excelInfo;
    public $excelResult;
    protected $helperData;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Api\ServiceVst\Helper\Data $helperData
    ) {
        $this->helperData = $helperData;
    }

    /**
     * Load the page defined in view/adminhtml/layout/exampleadminnewpage_helloworld_index.xml
     *
     * @return Page
     */
    public function execute()
    {
        try {
            $this->getExcelInfo();
            $this->getExcelResult();
            $resultPage = $this->resultPageFactory->create();
            $resultPage->setActiveMenu(static::MENU_ID);
            $resultPage->getConfig()->getTitle()->prepend(__('Import Preview'));

            $block = $resultPage->getLayout()->getBlock('import.preview');
            $block->setData('excel_info', $this->excelInfo);
            $block->setData('excel_result', $this->excelResult);

            return $resultPage;
        } catch (\Exception $e) {
            $this->_log($e->getMessage());
            return $e->getMessage();
        }
    }

    public function getExcelInfo() {
        $uploader = $this->_objectManager->create(
            'Magento\MediaStorage\Model\File\Uploader',
            ['fileId' => 'import_product']
        );
        $uploader->setAllowedExtensions(['xlsx', 'xls']);
        /** @var \Magento\Framework\Image\Adapter\AdapterInterface $imageAdapter */
        $imageAdapter = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')->create();
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(true);
        /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
        $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
            ->getDirectoryRead(DirectoryList::MEDIA);
        $result = $uploader->save($mediaDirectory->getAbsolutePath('import_product'));
        $this->excelInfo = $result;
        $this->_log(json_encode($result));
    }

    public function getExcelResult() {
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        /**  Load $inputFileName to a Spreadsheet Object  **/
        $spreadsheet = $reader->load($this->excelInfo['path'].$this->excelInfo['file']);
        $worksheet = $spreadsheet->getActiveSheet();

        $lists = array();
        foreach ($worksheet->getRowIterator() as $key => $row) {
//            if ($key == 1) {
//                continue;
//            }
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

       $this->excelResult = $lists;
    }

    public function getFormKey() {
        return $this->formKey->getFormKey();
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
