<?php
declare(strict_types=1);

namespace Fulloop\Pricebot\Model\Data;

use Fulloop\Pricebot\Api\Data\ProductPriceInterface;

class ProductPrice extends \Magento\Framework\Api\AbstractExtensibleObject implements ProductPriceInterface
{

    /**
     * Get product_price_id
     * @return string|null
     */
    public function getProductPriceId()
    {
        return $this->_get(self::PRODUCT_PRICE_ID);
    }

    /**
     * Set product_price_id
     * @param string $productPriceId
     * @return \Fulloop\Pricebot\Api\Data\ProductPriceInterface
     */
    public function setProductPriceId($productPriceId)
    {
        return $this->setData(self::PRODUCT_PRICE_ID, $productPriceId);
    }

    /**
     * Get product_title
     * @return string|null
     */
    public function getProductTitle()
    {
        return $this->_get(self::PRODUCT_TITLE);
    }

    /**
     * Set product_title
     * @param string $productTitle
     * @return \Fulloop\Pricebot\Api\Data\ProductPriceInterface
     */
    public function setProductTitle($productTitle)
    {
        return $this->setData(self::PRODUCT_TITLE, $productTitle);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Fulloop\Pricebot\Api\Data\ProductPriceExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Fulloop\Pricebot\Api\Data\ProductPriceExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Fulloop\Pricebot\Api\Data\ProductPriceExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get competitor_id
     * @return string|null
     */
    public function getCompetitorId()
    {
        return $this->_get(self::COMPETITOR_ID);
    }

    /**
     * Set competitor_id
     * @param string $competitorId
     * @return \Fulloop\Pricebot\Api\Data\ProductPriceInterface
     */
    public function setCompetitorId($competitorId)
    {
        return $this->setData(self::COMPETITOR_ID, $competitorId);
    }

    /**
     * Get price
     * @return string|null
     */
    public function getPrice()
    {
        return $this->_get(self::PRICE);
    }

    /**
     * Set price
     * @param string $price
     * @return \Fulloop\Pricebot\Api\Data\ProductPriceInterface
     */
    public function setPrice($price)
    {
        return $this->setData(self::PRICE, $price);
    }

    /**
     * Get views_count
     * @return string|null
     */
    public function getViewsCount()
    {
        return $this->_get(self::VIEWS_COUNT);
    }

    /**
     * Set views_count
     * @param string $viewsCount
     * @return \Fulloop\Pricebot\Api\Data\ProductPriceInterface
     */
    public function setViewsCount($viewsCount)
    {
        return $this->setData(self::VIEWS_COUNT, $viewsCount);
    }

    /**
     * Get url
     * @return string|null
     */
    public function getUrl()
    {
        return $this->_get(self::URL);
    }

    /**
     * Set url
     * @param string $url
     * @return \Fulloop\Pricebot\Api\Data\ProductPriceInterface
     */
    public function setUrl($url)
    {
        return $this->setData(self::URL, $url);
    }
}

