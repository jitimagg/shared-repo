<?php
namespace Sm\BrandPage\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Sm\BrandPage\Helper\Data as BrandHelper;

class View extends Action
{
    protected $resultPageFactory;
    protected $brandHelper;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        BrandHelper $brandHelper
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->brandHelper = $brandHelper;
        parent::__construct($context);
    }

    public function execute()
    {  
        // 1. รับค่าแบรนด์จาก URL เช่น /brand/nike
        $brandLabel = $this->getRequest()->getParam('brand'); // "nike"

         if (!$brandLabel || !is_string($brandLabel)) {
            return $this->resultRedirectFactory->create()->setPath('noroute');
        }

        // 2. แปลง label เป็น option_id
        $attributeCode = 'manufacturer';
        $optionId = $this->brandHelper->getOptionIdByLabel($attributeCode, $brandLabel);

        if (!$optionId) {
            // ไม่เจอแบรนด์ → redirect ไปหน้า 404
            $this->messageManager->addErrorMessage(__('Brand not found.'));
            return $this->resultRedirectFactory->create()->setPath('noroute');
        }

        // 3. สร้างหน้า Page และส่งข้อมูลไปยัง template
        $page = $this->resultPageFactory->create();

        $block = $page->getLayout()->getBlock('brand.listing');

        // ส่งทั้ง option_id และ brandLabel ไปให้ template
        if ($block) {
            $block->setData('brand_label', $brandLabel)
                ->setData('brand_option_id', $optionId);
        }

        return $page;
    }
}
