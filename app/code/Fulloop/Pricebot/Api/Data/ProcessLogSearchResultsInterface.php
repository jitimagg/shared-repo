<?php
declare(strict_types=1);

namespace Fulloop\Pricebot\Api\Data;

interface ProcessLogSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Process_Log list.
     * @return \Fulloop\Pricebot\Api\Data\ProcessLogInterface[]
     */
    public function getItems();

    /**
     * Set message list.
     * @param \Fulloop\Pricebot\Api\Data\ProcessLogInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

