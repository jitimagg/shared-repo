<?php
declare(strict_types=1);

namespace Fulloop\Pricebot\Api\Data;

interface CompetitorInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const NAME = 'name';
    const TARGETS = 'targets';
    const COMPETITOR_ID = 'competitor_id';
    const SCRAPER_CLASS = 'scraper_class';
    const STATUS = 'status';

    /**
     * Get competitor_id
     * @return string|null
     */
    public function getCompetitorId();

    /**
     * Set competitor_id
     * @param string $competitorId
     * @return \Fulloop\Pricebot\Api\Data\CompetitorInterface
     */
    public function setCompetitorId($competitorId);

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \Fulloop\Pricebot\Api\Data\CompetitorInterface
     */
    public function setName($name);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Fulloop\Pricebot\Api\Data\CompetitorExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Fulloop\Pricebot\Api\Data\CompetitorExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Fulloop\Pricebot\Api\Data\CompetitorExtensionInterface $extensionAttributes
    );

    /**
     * Get scraper_class
     * @return string|null
     */
    public function getScraperClass();

    /**
     * Set scraper_class
     * @param string $scraperClass
     * @return \Fulloop\Pricebot\Api\Data\CompetitorInterface
     */
    public function setScraperClass($scraperClass);

    /**
     * Get targets
     * @return string|null
     */
    public function getTargets();

    /**
     * Set targets
     * @param string $targets
     * @return \Fulloop\Pricebot\Api\Data\CompetitorInterface
     */
    public function setTargets($targets);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \Fulloop\Pricebot\Api\Data\CompetitorInterface
     */
    public function setStatus($status);
}

