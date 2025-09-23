<?php
declare(strict_types=1);

namespace Fulloop\Pricebot\Model;

use Fulloop\Pricebot\Api\Data\ProcessLogInterface;
use Fulloop\Pricebot\Api\Data\ProcessLogInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class ProcessLog extends \Magento\Framework\Model\AbstractModel
{

    protected $process_logDataFactory;

    protected $dataObjectHelper;

    protected $_eventPrefix = 'fulloop_pricebot_process_log';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ProcessLogInterfaceFactory $process_logDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Fulloop\Pricebot\Model\ResourceModel\ProcessLog $resource
     * @param \Fulloop\Pricebot\Model\ResourceModel\ProcessLog\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ProcessLogInterfaceFactory $process_logDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Fulloop\Pricebot\Model\ResourceModel\ProcessLog $resource,
        \Fulloop\Pricebot\Model\ResourceModel\ProcessLog\Collection $resourceCollection,
        array $data = []
    ) {
        $this->process_logDataFactory = $process_logDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve process_log model with process_log data
     * @return ProcessLogInterface
     */
    public function getDataModel()
    {
        $process_logData = $this->getData();
        
        $process_logDataObject = $this->process_logDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $process_logDataObject,
            $process_logData,
            ProcessLogInterface::class
        );
        
        return $process_logDataObject;
    }
}

