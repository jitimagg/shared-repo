<?php
declare(strict_types=1);

namespace Fulloop\Pricebot\Api\Data;

interface ProductPriceInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const URL = 'url';
    const PRICE = 'price';
    const PRODUCT_TITLE = 'product_title';
    const COMPETITOR_ID = 'competitor_id';
    const VIEWS_COUNT = 'views_count';
    const PRODUCT_PRICE_ID = 'product_price_id';

    /**
     * Get product_price_id
     * @return string|null
     */
    public function getProductPriceId();

    /**
     * Set product_price_id
     * @param string $productPriceId
     * @return \Fulloop\Pricebot\Api\Data\ProductPriceInterface
     */
    public function setProductPriceId($productPriceId);

    /**
     * Get product_title
     * @return string|null
     */
    public function getProductTitle();

    /**
     * Set product_title
     * @param string $productTitle
     * @return \Fulloop\Pricebot\Api\Data\ProductPriceInterface
     */
    public function setProductTitle($productTitle);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Fulloop\Pricebot\Api\Data\ProductPriceExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Fulloop\Pricebot\Api\Data\ProductPriceExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Fulloop\Pricebot\Api\Data\ProductPriceExtensionInterface $extensionAttributes
    );

    /**
     * Get competitor_id
     * @return string|null
     */
    public function getCompetitorId();

    /**
     * Set competitor_id
     * @param string $competitorId
     * @return \Fulloop\Pricebot\Api\Data\ProductPriceInterface
     */
    public function setCompetitorId($competitorId);

    /**
     * Get price
     * @return string|null
     */
    public function getPrice();

    /**
     * Set price
     * @param string $price
     * @return \Fulloop\Pricebot\Api\Data\ProductPriceInterface
     */
    public function setPrice($price);

    /**
     * Get views_count
     * @return string|null
     */
    public function getViewsCount();

    /**
     * Set views_count
     * @param string $viewsCount
     * @return \Fulloop\Pricebot\Api\Data\ProductPriceInterface
     */
    public function setViewsCount($viewsCount);

    /**
     * Get url
     * @return string|null
     */
    public function getUrl();

    /**
     * Set url
     * @param string $url
     * @return \Fulloop\Pricebot\Api\Data\ProductPriceInterface
     */
    public function setUrl($url);
}

