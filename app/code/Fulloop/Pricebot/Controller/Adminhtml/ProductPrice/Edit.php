<?php
declare(strict_types=1);

namespace Fulloop\Pricebot\Controller\Adminhtml\ProductPrice;

class Edit extends \Fulloop\Pricebot\Controller\Adminhtml\ProductPrice
{

    protected $resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context, $resultPageFactory);
    }

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('product_price_id');
        $model = $this->_objectManager->create(\Fulloop\Pricebot\Model\ProductPrice::class);

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Product Price no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->_coreRegistry->register('fulloop_pricebot_product_price', $model);

        // 3. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Product Price') : __('New Product Price'),
            $id ? __('Edit Product Price') : __('New Product Price')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Product Prices'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? __('Edit Product Price %1', $model->getId()) : __('New Product Price'));
        return $resultPage;
    }
}

