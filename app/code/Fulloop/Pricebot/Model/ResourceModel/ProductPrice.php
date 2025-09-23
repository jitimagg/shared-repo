<?php
declare(strict_types=1);

namespace Fulloop\Pricebot\Model\ResourceModel;

class ProductPrice extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('fulloop_pricebot_product_price', 'product_price_id');
    }
}

