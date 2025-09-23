<?php
declare(strict_types=1);

namespace Fulloop\Pricebot\Model;

use Fulloop\Pricebot\Api\CompetitorRepositoryInterface;
use Fulloop\Pricebot\Api\Data\CompetitorInterfaceFactory;
use Fulloop\Pricebot\Api\Data\CompetitorSearchResultsInterfaceFactory;
use Fulloop\Pricebot\Model\ResourceModel\Competitor as ResourceCompetitor;
use Fulloop\Pricebot\Model\ResourceModel\Competitor\CollectionFactory as CompetitorCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

class CompetitorRepository implements CompetitorRepositoryInterface
{

    protected $resource;

    protected $competitorCollectionFactory;

    protected $dataObjectHelper;

    protected $dataCompetitorFactory;

    protected $extensibleDataObjectConverter;
    protected $competitorFactory;

    private $storeManager;

    protected $dataObjectProcessor;

    protected $searchResultsFactory;

    private $collectionProcessor;

    protected $extensionAttributesJoinProcessor;


    /**
     * @param ResourceCompetitor $resource
     * @param CompetitorFactory $competitorFactory
     * @param CompetitorInterfaceFactory $dataCompetitorFactory
     * @param CompetitorCollectionFactory $competitorCollectionFactory
     * @param CompetitorSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceCompetitor $resource,
        CompetitorFactory $competitorFactory,
        CompetitorInterfaceFactory $dataCompetitorFactory,
        CompetitorCollectionFactory $competitorCollectionFactory,
        CompetitorSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->competitorFactory = $competitorFactory;
        $this->competitorCollectionFactory = $competitorCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataCompetitorFactory = $dataCompetitorFactory;
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
        \Fulloop\Pricebot\Api\Data\CompetitorInterface $competitor
    ) {
        /* if (empty($competitor->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $competitor->setStoreId($storeId);
        } */
        
        $competitorData = $this->extensibleDataObjectConverter->toNestedArray(
            $competitor,
            [],
            \Fulloop\Pricebot\Api\Data\CompetitorInterface::class
        );
        
        $competitorModel = $this->competitorFactory->create()->setData($competitorData);
        
        try {
            $this->resource->save($competitorModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the competitor: %1',
                $exception->getMessage()
            ));
        }
        return $competitorModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($competitorId)
    {
        $competitor = $this->competitorFactory->create();
        $this->resource->load($competitor, $competitorId);
        if (!$competitor->getId()) {
            throw new NoSuchEntityException(__('Competitor with id "%1" does not exist.', $competitorId));
        }
        return $competitor->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->competitorCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Fulloop\Pricebot\Api\Data\CompetitorInterface::class
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
        \Fulloop\Pricebot\Api\Data\CompetitorInterface $competitor
    ) {
        try {
            $competitorModel = $this->competitorFactory->create();
            $this->resource->load($competitorModel, $competitor->getCompetitorId());
            $this->resource->delete($competitorModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Competitor: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($competitorId)
    {
        return $this->delete($this->get($competitorId));
    }
}

