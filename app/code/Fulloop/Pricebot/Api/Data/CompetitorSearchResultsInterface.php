<?php
declare(strict_types=1);

namespace Fulloop\Pricebot\Api\Data;

interface CompetitorSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Competitor list.
     * @return \Fulloop\Pricebot\Api\Data\CompetitorInterface[]
     */
    public function getItems();

    /**
     * Set name list.
     * @param \Fulloop\Pricebot\Api\Data\CompetitorInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

