<?php
declare(strict_types=1);

namespace Fulloop\Pricebot\Controller\Adminhtml\ProductPrice;

class Delete extends \Fulloop\Pricebot\Controller\Adminhtml\ProductPrice
{

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('product_price_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create(\Fulloop\Pricebot\Model\ProductPrice::class);
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Product Price.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['product_price_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Product Price to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}

