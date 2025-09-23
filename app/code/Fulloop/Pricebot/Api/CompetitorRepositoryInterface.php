<?php
declare(strict_types=1);

namespace Fulloop\Pricebot\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface CompetitorRepositoryInterface
{

    /**
     * Save Competitor
     * @param \Fulloop\Pricebot\Api\Data\CompetitorInterface $competitor
     * @return \Fulloop\Pricebot\Api\Data\CompetitorInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Fulloop\Pricebot\Api\Data\CompetitorInterface $competitor
    );

    /**
     * Retrieve Competitor
     * @param string $competitorId
     * @return \Fulloop\Pricebot\Api\Data\CompetitorInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($competitorId);

    /**
     * Retrieve Competitor matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Fulloop\Pricebot\Api\Data\CompetitorSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Competitor
     * @param \Fulloop\Pricebot\Api\Data\CompetitorInterface $competitor
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Fulloop\Pricebot\Api\Data\CompetitorInterface $competitor
    );

    /**
     * Delete Competitor by ID
     * @param string $competitorId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($competitorId);
}

