<?php
declare(strict_types=1);

namespace Fulloop\Pricebot\Api\Data;

interface ProductPriceSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Product_Price list.
     * @return \Fulloop\Pricebot\Api\Data\ProductPriceInterface[]
     */
    public function getItems();

    /**
     * Set product_title list.
     * @param \Fulloop\Pricebot\Api\Data\ProductPriceInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

