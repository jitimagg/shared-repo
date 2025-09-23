<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Fulloop\Products\Controller\Update;

class Invtest extends \Magento\Framework\App\Action\Action
{

    protected $logger;

    protected $_productRepository;

    protected $helperData;
    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Api\ServiceVst\Helper\Data $helperData
    )
    {
        $this->_productRepository = $productRepository;

        $this->logger = $logger;

        $this->helperData = $helperData;

        parent::__construct($context);
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        try {
//            $url = $this->helperData->getConfigValue('inventory/connection/url');
            $url = 'http://203.151.85.119:8000/Inventory.xml';
            $xmlString = file_get_contents($url);echo $xmlString;
            $xml = simplexml_load_string($xmlString, "SimpleXMLElement", LIBXML_NOCDATA);
            echo $xml;
        } catch (\Exception $e) {

        }

    }

}
