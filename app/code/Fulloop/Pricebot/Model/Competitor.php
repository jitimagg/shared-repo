<?php
declare(strict_types=1);

namespace Fulloop\Pricebot\Model;

use Fulloop\Pricebot\Api\Data\CompetitorInterface;
use Fulloop\Pricebot\Api\Data\CompetitorInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class Competitor extends \Magento\Framework\Model\AbstractModel
{

    protected $_eventPrefix = 'fulloop_pricebot_competitor';
    protected $dataObjectHelper;

    protected $competitorDataFactory;


    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param CompetitorInterfaceFactory $competitorDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Fulloop\Pricebot\Model\ResourceModel\Competitor $resource
     * @param \Fulloop\Pricebot\Model\ResourceModel\Competitor\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        CompetitorInterfaceFactory $competitorDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Fulloop\Pricebot\Model\ResourceModel\Competitor $resource,
        \Fulloop\Pricebot\Model\ResourceModel\Competitor\Collection $resourceCollection,
        array $data = []
    ) {
        $this->competitorDataFactory = $competitorDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve competitor model with competitor data
     * @return CompetitorInterface
     */
    public function getDataModel()
    {
        $competitorData = $this->getData();
        
        $competitorDataObject = $this->competitorDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $competitorDataObject,
            $competitorData,
            CompetitorInterface::class
        );
        
        return $competitorDataObject;
    }
}

