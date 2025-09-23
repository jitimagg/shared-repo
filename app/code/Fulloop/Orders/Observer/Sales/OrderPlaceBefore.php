<?php

namespace Fulloop\Orders\Observer\Sales;

class OrderPlaceBefore implements \Magento\Framework\Event\ObserverInterface
{
    protected $auth = [];
    protected $helperData;
    protected $context;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Api\ServiceVst\Helper\Data $helperData
    )
    {
        $this->helperData = $helperData;

        $this->context = $context;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws \Exception
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cartObj = $objectManager->get('\Magento\Checkout\Model\Cart');

        $quote = $cartObj->getQuote();
        $itemsArray = $quote->getAllVisibleItems();
        $paymentMethod = $quote->getPayment()->getMethod();
        if ($paymentMethod == 'creditcard_installment') {
            try {
                if (empty($itemsArray)) {
                    throw new \Exception(__('This PO Number is used already.'));
                }
                foreach($itemsArray as $item) {
                    $product = $item->getProduct();
                    if (!$product->getIsInstallment()) {
                        throw new \Exception(__('This PO Number is used already.'));
                    }
                }
            } catch (Exception $e) {
                $this->_log($e->getMessage());
                throw new \Exception($e->getMessage());
            }
        }
    }

    private function _log($msg)
    {
        // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/orderplace_before.log');
        // $logger = new \Zend\Log\Logger();
        // $logger->addWriter($writer);
        // $logger->info($msg);

         $this->helperData->generalLog('/var/log/orderplace_before.log', $msg);
    }
}
