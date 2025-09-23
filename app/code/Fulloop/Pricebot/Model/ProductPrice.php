<?php
declare(strict_types=1);

namespace Fulloop\Pricebot\Model;

use Fulloop\Pricebot\Api\Data\ProductPriceInterface;
use Fulloop\Pricebot\Api\Data\ProductPriceInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class ProductPrice extends \Magento\Framework\Model\AbstractModel
{

    protected $_eventPrefix = 'fulloop_pricebot_product_price';
    protected $dataObjectHelper;

    protected $product_priceDataFactory;


    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ProductPriceInterfaceFactory $product_priceDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Fulloop\Pricebot\Model\ResourceModel\ProductPrice $resource
     * @param \Fulloop\Pricebot\Model\ResourceModel\ProductPrice\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ProductPriceInterfaceFactory $product_priceDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Fulloop\Pricebot\Model\ResourceModel\ProductPrice $resource,
        \Fulloop\Pricebot\Model\ResourceModel\ProductPrice\Collection $resourceCollection,
        array $data = []
    ) {
        $this->product_priceDataFactory = $product_priceDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve product_price model with product_price data
     * @return ProductPriceInterface
     */
    public function getDataModel()
    {
        $product_priceData = $this->getData();
        
        $product_priceDataObject = $this->product_priceDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $product_priceDataObject,
            $product_priceData,
            ProductPriceInterface::class
        );
        
        return $product_priceDataObject;
    }
}

