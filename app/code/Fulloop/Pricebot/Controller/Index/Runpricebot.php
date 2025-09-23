<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Fulloop\Pricebot\Controller\Index;

use Fulloop\Pricebot\Cron\PriceImport;

class Runpricebot extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Api\ServiceVst\Helper\Data
     */
    protected $helperData;

    protected $auth;

    protected $_productRepository;

    protected $_storeManager;

    protected $logger;
    protected $competitorFactory;
    protected $productPriceFactory;
    /**
    * Constructor.
    *
    * @param Magento\Framework\HTTP\Client\Curl $curl
    */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Api\ServiceVst\Helper\Data $helperData,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger,
        \Fulloop\Pricebot\Model\CompetitorFactory $competitorFactory,
        \Fulloop\Pricebot\Model\ProductPriceFactory $productPriceFactory
    )
    {
//        if (empty($_GET['debug']) || $_GET['debug'] != 'eyJ0eXAiOiJKV1QiLCJhbGciOiJTSEE') {
//            header( "location: http://google.com" );
//            exit(0);
//        }

        $this->_productRepository = $productRepository;

        $this->resultJsonFactory = $resultJsonFactory;

        $this->helperData = $helperData;

        $this->_storeManager = $storeManager;

        $this->logger = $logger;
        $this->competitorFactory = $competitorFactory;
        $this->productPriceFactory = $productPriceFactory;

        parent::__construct($context);
    }


    /**
     * View  page action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $pi = new PriceImport($this->logger, $this->competitorFactory, $this->productPriceFactory);
        $pi->execute();
    }


}
