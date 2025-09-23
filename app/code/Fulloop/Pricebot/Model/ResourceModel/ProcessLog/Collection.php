<?php
declare(strict_types=1);

namespace Fulloop\Pricebot\Model\ResourceModel\ProcessLog;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'process_log_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Fulloop\Pricebot\Model\ProcessLog::class,
            \Fulloop\Pricebot\Model\ResourceModel\ProcessLog::class
        );
    }
}

