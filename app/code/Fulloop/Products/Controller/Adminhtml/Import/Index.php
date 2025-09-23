<?php
/**
 * Copyright © Megnor, Inc. All rights reserved.
 */

namespace Fulloop\Products\Controller\Adminhtml\Import;

use Magento\Framework\View\Result\Page;

class Index extends \Magento\Backend\App\Action implements \Magento\Framework\App\Action\HttpGetActionInterface
{

    const MENU_ID = 'Fulloop_Products::import_product';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    protected $formKey;
    protected $helperData;
    /**
     * Index constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Api\ServiceVst\Helper\Data $helperData
    ) {
        parent::__construct($context);

        $this->helperData = $helperData;
        $this->resultPageFactory = $resultPageFactory;
        $this->formKey = $formKey;
    }

    /**
     * Load the page defined in view/adminhtml/layout/exampleadminnewpage_helloworld_index.xml
     *
     * @return Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(static::MENU_ID);
        $resultPage->getConfig()->getTitle()->prepend(__('Import'));

        return $resultPage;
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
