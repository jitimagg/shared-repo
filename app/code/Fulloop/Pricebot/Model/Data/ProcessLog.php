<?php
declare(strict_types=1);

namespace Fulloop\Pricebot\Model\Data;

use Fulloop\Pricebot\Api\Data\ProcessLogInterface;

class ProcessLog extends \Magento\Framework\Api\AbstractExtensibleObject implements ProcessLogInterface
{

    /**
     * Get process_log_id
     * @return string|null
     */
    public function getProcessLogId()
    {
        return $this->_get(self::PROCESS_LOG_ID);
    }

    /**
     * Set process_log_id
     * @param string $processLogId
     * @return \Fulloop\Pricebot\Api\Data\ProcessLogInterface
     */
    public function setProcessLogId($processLogId)
    {
        return $this->setData(self::PROCESS_LOG_ID, $processLogId);
    }

    /**
     * Get message
     * @return string|null
     */
    public function getMessage()
    {
        return $this->_get(self::MESSAGE);
    }

    /**
     * Set message
     * @param string $message
     * @return \Fulloop\Pricebot\Api\Data\ProcessLogInterface
     */
    public function setMessage($message)
    {
        return $this->setData(self::MESSAGE, $message);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Fulloop\Pricebot\Api\Data\ProcessLogExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Fulloop\Pricebot\Api\Data\ProcessLogExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Fulloop\Pricebot\Api\Data\ProcessLogExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}

