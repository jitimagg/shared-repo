<?php
declare(strict_types=1);

namespace Fulloop\Pricebot\Model\Data;

use Fulloop\Pricebot\Api\Data\CompetitorInterface;

class Competitor extends \Magento\Framework\Api\AbstractExtensibleObject implements CompetitorInterface
{

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
     * @return \Fulloop\Pricebot\Api\Data\CompetitorInterface
     */
    public function setCompetitorId($competitorId)
    {
        return $this->setData(self::COMPETITOR_ID, $competitorId);
    }

    /**
     * Get name
     * @return string|null
     */
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * Set name
     * @param string $name
     * @return \Fulloop\Pricebot\Api\Data\CompetitorInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Fulloop\Pricebot\Api\Data\CompetitorExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Fulloop\Pricebot\Api\Data\CompetitorExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Fulloop\Pricebot\Api\Data\CompetitorExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get scraper_class
     * @return string|null
     */
    public function getScraperClass()
    {
        return $this->_get(self::SCRAPER_CLASS);
    }

    /**
     * Set scraper_class
     * @param string $scraperClass
     * @return \Fulloop\Pricebot\Api\Data\CompetitorInterface
     */
    public function setScraperClass($scraperClass)
    {
        return $this->setData(self::SCRAPER_CLASS, $scraperClass);
    }

    /**
     * Get targets
     * @return string|null
     */
    public function getTargets()
    {
        return $this->_get(self::TARGETS);
    }

    /**
     * Set targets
     * @param string $targets
     * @return \Fulloop\Pricebot\Api\Data\CompetitorInterface
     */
    public function setTargets($targets)
    {
        return $this->setData(self::TARGETS, $targets);
    }

    /**
     * Get status
     * @return string|null
     */
    public function getStatus()
    {
        return $this->_get(self::STATUS);
    }

    /**
     * Set status
     * @param string $status
     * @return \Fulloop\Pricebot\Api\Data\CompetitorInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }
}

