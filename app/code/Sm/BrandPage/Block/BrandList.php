<?php
namespace Sm\BrandPage\Block;

use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Block\Product\ImageBuilder;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;

class BrandList extends Template
{
    protected $productCollectionFactory;
    protected $imageBuilder;
    protected $priceHelper;

    public function __construct(
        Template\Context $context,
        CollectionFactory $productCollectionFactory,
        ImageBuilder $imageBuilder,
        PriceHelper $priceHelper,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->imageBuilder = $imageBuilder;
        $this->priceHelper = $priceHelper;
        parent::__construct($context, $data);
    }

    public function getBrandLabel()
    {
        return $this->getData('brand_label');
    }

    public function getImage($product, $imageId = 'product_thumbnail_image')
    {
        return $this->imageBuilder->create($product, $imageId);
    }

    public function getPriceHtml($product)
    {
        return $this->priceHelper->currency($product->getFinalPrice(), true, false);
    }

    public function getProductCollection()
    {
        $optionId = $this->getData('brand_option_id');
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*')
                   ->addAttributeToFilter('manufacturer', ['eq' => $optionId])
                   ->addAttributeToFilter('visibility', ['neq' => 1]) // ไม่แสดง Not Visible
                   ->addAttributeToFilter('status', 1); // เฉพาะ Enabled

        // กรองสินค้าให้อยู่ใน category_id = 10 เท่านั้น (หมวดสินค้าปกติ)
        // $collection->addCategoriesFilter(['in' => 283]);

        return $collection;
    }
}
