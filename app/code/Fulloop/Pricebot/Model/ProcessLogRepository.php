<?php
declare(strict_types=1);

namespace Fulloop\Pricebot\Model;

use Fulloop\Pricebot\Api\Data\ProcessLogInterfaceFactory;
use Fulloop\Pricebot\Api\Data\ProcessLogSearchResultsInterfaceFactory;
use Fulloop\Pricebot\Api\ProcessLogRepositoryInterface;
use Fulloop\Pricebot\Model\ResourceModel\ProcessLog as ResourceProcessLog;
use Fulloop\Pricebot\Model\ResourceModel\ProcessLog\CollectionFactory as ProcessLogCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

class ProcessLogRepository implements ProcessLogRepositoryInterface
{

    protected $resource;

    protected $processLogFactory;

    protected $processLogCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataProcessLogFactory;

    protected $extensionAttributesJoinProcessor;

    private $storeManager;

    private $collectionProcessor;

    protected $extensibleDataObjectConverter;

    /**
     * @param ResourceProcessLog $resource
     * @param ProcessLogFactory $processLogFactory
     * @param ProcessLogInterfaceFactory $dataProcessLogFactory
     * @param ProcessLogCollectionFactory $processLogCollectionFactory
     * @param ProcessLogSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceProcessLog $resource,
        ProcessLogFactory $processLogFactory,
        ProcessLogInterfaceFactory $dataProcessLogFactory,
        ProcessLogCollectionFactory $processLogCollectionFactory,
        ProcessLogSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->processLogFactory = $processLogFactory;
        $this->processLogCollectionFactory = $processLogCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataProcessLogFactory = $dataProcessLogFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Fulloop\Pricebot\Api\Data\ProcessLogInterface $processLog
    ) {
        /* if (empty($processLog->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $processLog->setStoreId($storeId);
        } */
        
        $processLogData = $this->extensibleDataObjectConverter->toNestedArray(
            $processLog,
            [],
            \Fulloop\Pricebot\Api\Data\ProcessLogInterface::class
        );
        
        $processLogModel = $this->processLogFactory->create()->setData($processLogData);
        
        try {
            $this->resource->save($processLogModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the processLog: %1',
                $exception->getMessage()
            ));
        }
        return $processLogModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($processLogId)
    {
        $processLog = $this->processLogFactory->create();
        $this->resource->load($processLog, $processLogId);
        if (!$processLog->getId()) {
            throw new NoSuchEntityException(__('Process_Log with id "%1" does not exist.', $processLogId));
        }
        return $processLog->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->processLogCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Fulloop\Pricebot\Api\Data\ProcessLogInterface::class
        );
        
        $this->collectionProcessor->process($criteria, $collection);
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }
        
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Fulloop\Pricebot\Api\Data\ProcessLogInterface $processLog
    ) {
        try {
            $processLogModel = $this->processLogFactory->create();
            $this->resource->load($processLogModel, $processLog->getProcessLogId());
            $this->resource->delete($processLogModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Process_Log: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($processLogId)
    {
        return $this->delete($this->get($processLogId));
    }
}

