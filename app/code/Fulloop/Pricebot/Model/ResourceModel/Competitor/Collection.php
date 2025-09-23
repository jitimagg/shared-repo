<?php
declare(strict_types=1);

namespace Fulloop\Pricebot\Model\ResourceModel\Competitor;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'competitor_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Fulloop\Pricebot\Model\Competitor::class,
            \Fulloop\Pricebot\Model\ResourceModel\Competitor::class
        );
    }
}

