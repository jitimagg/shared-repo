<?php
declare(strict_types=1);

namespace Fulloop\Pricebot\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ProcessLogRepositoryInterface
{

    /**
     * Save Process_Log
     * @param \Fulloop\Pricebot\Api\Data\ProcessLogInterface $processLog
     * @return \Fulloop\Pricebot\Api\Data\ProcessLogInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Fulloop\Pricebot\Api\Data\ProcessLogInterface $processLog
    );

    /**
     * Retrieve Process_Log
     * @param string $processLogId
     * @return \Fulloop\Pricebot\Api\Data\ProcessLogInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($processLogId);

    /**
     * Retrieve Process_Log matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Fulloop\Pricebot\Api\Data\ProcessLogSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Process_Log
     * @param \Fulloop\Pricebot\Api\Data\ProcessLogInterface $processLog
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Fulloop\Pricebot\Api\Data\ProcessLogInterface $processLog
    );

    /**
     * Delete Process_Log by ID
     * @param string $processLogId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($processLogId);
}

