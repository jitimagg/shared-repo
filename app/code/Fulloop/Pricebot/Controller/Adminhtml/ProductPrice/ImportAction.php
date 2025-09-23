<?php
declare(strict_types=1);

namespace Fulloop\Pricebot\Controller\Adminhtml\ProductPrice;

class ImportAction extends \Fulloop\Pricebot\Controller\Adminhtml\ProductPrice
{

    protected $resultForwardFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context, $resultPageFactory);
    }

    /**
     * New action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        echo "Hello!";
//        /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
//        $resultForward = $this->resultForwardFactory->create();
//        return $resultForward->forward('edit');
    }
}

