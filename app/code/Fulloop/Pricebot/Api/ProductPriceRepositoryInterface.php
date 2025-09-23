<?php
declare(strict_types=1);

namespace Fulloop\Pricebot\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ProductPriceRepositoryInterface
{

    /**
     * Save Product_Price
     * @param \Fulloop\Pricebot\Api\Data\ProductPriceInterface $productPrice
     * @return \Fulloop\Pricebot\Api\Data\ProductPriceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Fulloop\Pricebot\Api\Data\ProductPriceInterface $productPrice
    );

    /**
     * Retrieve Product_Price
     * @param string $productPriceId
     * @return \Fulloop\Pricebot\Api\Data\ProductPriceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($productPriceId);

    /**
     * Retrieve Product_Price matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Fulloop\Pricebot\Api\Data\ProductPriceSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Product_Price
     * @param \Fulloop\Pricebot\Api\Data\ProductPriceInterface $productPrice
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Fulloop\Pricebot\Api\Data\ProductPriceInterface $productPrice
    );

    /**
     * Delete Product_Price by ID
     * @param string $productPriceId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($productPriceId);
}

