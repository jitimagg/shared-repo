<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Fulloop\Orders\Controller\Payment;

use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

class Notify extends \Magento\Framework\App\Action\Action implements \Magento\Framework\App\CsrfAwareActionInterface
{

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $logger;

    protected $resultJsonFactory;

    protected $_checkoutSession;

    protected $helperData;

    protected $auth;

    protected $context;

    protected $orderFactory;

    protected $_productRepository;

    /**
    * Constructor.
    *
    * @param Magento\Framework\HTTP\Client\Curl $curl
    */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Checkout\Model\Session $_checkoutSession,
        \Api\ServiceVst\Helper\Data $helperData,
        \Magento\Sales\Api\Data\OrderInterfaceFactory $orderFactory,
        \Magento\Catalog\Model\ProductRepository $productRepository
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;

        $this->_checkoutSession = $_checkoutSession;

        $this->helperData = $helperData;

        $this->_productRepository = $productRepository;

//        $this->auth = $helperData->auth();

        $this->context = $context;

        $this->orderFactory = $orderFactory;

        parent::__construct($context);
    }

    /**
     * View  page action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $resposeData = file_get_contents("php://input");
            $this->_log('notify'.print_r($_POST, true));
            $this->_log('notify'.print_r($resposeData, true));
        } catch (\Exception $e) {
            $this->_log($e->getMessage());
        }

    }

    /**
     * @param RequestInterface $request
     *
     * @return bool|null
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    /**
     * @param RequestInterface $request
     *
     * @return InvalidRequestException|null
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    private function _log($msg)
    {
        // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/notify.log');
        // $logger = new \Zend\Log\Logger();
        // $logger->addWriter($writer);
        // $logger->info($msg);

         $this->helperData->generalLog('/var/log/notify.log', $msg);
    }
}
