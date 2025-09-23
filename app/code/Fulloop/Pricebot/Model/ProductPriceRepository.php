<?php
declare(strict_types=1);

namespace Fulloop\Pricebot\Model;

use Fulloop\Pricebot\Api\Data\ProductPriceInterfaceFactory;
use Fulloop\Pricebot\Api\Data\ProductPriceSearchResultsInterfaceFactory;
use Fulloop\Pricebot\Api\ProductPriceRepositoryInterface;
use Fulloop\Pricebot\Model\ResourceModel\ProductPrice as ResourceProductPrice;
use Fulloop\Pricebot\Model\ResourceModel\ProductPrice\CollectionFactory as ProductPriceCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

class ProductPriceRepository implements ProductPriceRepositoryInterface
{

    protected $resource;

    protected $dataObjectHelper;

    protected $productPriceFactory;

    protected $extensibleDataObjectConverter;
    protected $dataProductPriceFactory;

    private $storeManager;

    protected $dataObjectProcessor;

    protected $searchResultsFactory;

    private $collectionProcessor;

    protected $productPriceCollectionFactory;

    protected $extensionAttributesJoinProcessor;


    /**
     * @param ResourceProductPrice $resource
     * @param ProductPriceFactory $productPriceFactory
     * @param ProductPriceInterfaceFactory $dataProductPriceFactory
     * @param ProductPriceCollectionFactory $productPriceCollectionFactory
     * @param ProductPriceSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceProductPrice $resource,
        ProductPriceFactory $productPriceFactory,
        ProductPriceInterfaceFactory $dataProductPriceFactory,
        ProductPriceCollectionFactory $productPriceCollectionFactory,
        ProductPriceSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->productPriceFactory = $productPriceFactory;
        $this->productPriceCollectionFactory = $productPriceCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataProductPriceFactory = $dataProductPriceFactory;
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
        \Fulloop\Pricebot\Api\Data\ProductPriceInterface $productPrice
    ) {
        /* if (empty($productPrice->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $productPrice->setStoreId($storeId);
        } */
        
        $productPriceData = $this->extensibleDataObjectConverter->toNestedArray(
            $productPrice,
            [],
            \Fulloop\Pricebot\Api\Data\ProductPriceInterface::class
        );
        
        $productPriceModel = $this->productPriceFactory->create()->setData($productPriceData);
        
        try {
            $this->resource->save($productPriceModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the productPrice: %1',
                $exception->getMessage()
            ));
        }
        return $productPriceModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($productPriceId)
    {
        $productPrice = $this->productPriceFactory->create();
        $this->resource->load($productPrice, $productPriceId);
        if (!$productPrice->getId()) {
            throw new NoSuchEntityException(__('Product_Price with id "%1" does not exist.', $productPriceId));
        }
        return $productPrice->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->productPriceCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Fulloop\Pricebot\Api\Data\ProductPriceInterface::class
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
        \Fulloop\Pricebot\Api\Data\ProductPriceInterface $productPrice
    ) {
        try {
            $productPriceModel = $this->productPriceFactory->create();
            $this->resource->load($productPriceModel, $productPrice->getProductPriceId());
            $this->resource->delete($productPriceModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Product_Price: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($productPriceId)
    {
        return $this->delete($this->get($productPriceId));
    }
}

