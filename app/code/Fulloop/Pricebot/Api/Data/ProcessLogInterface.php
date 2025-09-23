<?php
declare(strict_types=1);

namespace Fulloop\Pricebot\Api\Data;

interface ProcessLogInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const MESSAGE = 'message';
    const PROCESS_LOG_ID = 'process_log_id';

    /**
     * Get process_log_id
     * @return string|null
     */
    public function getProcessLogId();

    /**
     * Set process_log_id
     * @param string $processLogId
     * @return \Fulloop\Pricebot\Api\Data\ProcessLogInterface
     */
    public function setProcessLogId($processLogId);

    /**
     * Get message
     * @return string|null
     */
    public function getMessage();

    /**
     * Set message
     * @param string $message
     * @return \Fulloop\Pricebot\Api\Data\ProcessLogInterface
     */
    public function setMessage($message);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Fulloop\Pricebot\Api\Data\ProcessLogExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Fulloop\Pricebot\Api\Data\ProcessLogExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Fulloop\Pricebot\Api\Data\ProcessLogExtensionInterface $extensionAttributes
    );
}

