<?php
declare(strict_types=1);

namespace Fulloop\Pricebot\Model\ResourceModel\ProductPrice;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'product_price_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Fulloop\Pricebot\Model\ProductPrice::class,
            \Fulloop\Pricebot\Model\ResourceModel\ProductPrice::class
        );
    }
}

