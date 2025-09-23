<?php

namespace Fulloop\Orders\Model;

class SetQuote implements \Fulloop\Orders\Api\SetQuoteInterface
{

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    protected $_checkoutSession;

    protected $helperData;

    protected $auth;

    protected $context;

    protected $orderFactory;

    protected $quoteFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Checkout\Model\Session $_checkoutSession,
        \Api\ServiceVst\Helper\Data $helperData,
        \Magento\Sales\Api\Data\OrderInterfaceFactory $orderFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;

        $this->_checkoutSession = $_checkoutSession;

        $this->helperData = $helperData;

        $this->quoteFactory = $quoteFactory;

        $this->context = $context;

        $this->orderFactory = $orderFactory;

    }

    public function postResponse()
    {
        try {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $cartObj = $objectManager->get('\Magento\Checkout\Model\Cart');

            $resposeData = file_get_contents("php://input");
            $param = json_decode($resposeData, true);

            $quoteId = !empty($param['quote_id']) ? $param['quote_id'] : '';
            if (!empty($quoteId)) {
                $quote = $this->quoteFactory->create()->load($quoteId);
            } else {
                $quote = $cartObj->getQuote();
            }

            $period = !empty($param['installment_period']) ? $param['installment_period'] : 3;

            $quote->setInstallmentPeriod($period);
            $quote->save();

            echo $quote->getInstallmentPeriod();

            $this->_log(print_r($param, true));
        } catch (Exception $e) {
            $this->_log($e->getMessage());
        }
    }

    private function _log($msg)
    {
        // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/api_set_quote.log');
        // $logger = new \Zend\Log\Logger();
        // $logger->addWriter($writer);
        // $logger->info($msg);

        $this->helperData->generalLog('/var/log/api_set_quote.log', $msg);
    }
}
