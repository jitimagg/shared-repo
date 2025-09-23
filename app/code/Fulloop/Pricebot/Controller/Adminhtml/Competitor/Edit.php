<?php
declare(strict_types=1);

namespace Fulloop\Pricebot\Controller\Adminhtml\Competitor;

class Edit extends \Fulloop\Pricebot\Controller\Adminhtml\Competitor
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
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('competitor_id');
        $model = $this->_objectManager->create(\Fulloop\Pricebot\Model\Competitor::class);
        
        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Competitor no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->_coreRegistry->register('fulloop_pricebot_competitor', $model);
        
        // 3. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Competitor') : __('New Competitor'),
            $id ? __('Edit Competitor') : __('New Competitor')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Competitors'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? __('Edit Competitor %1', $model->getId()) : __('New Competitor'));
        return $resultPage;
    }
}

