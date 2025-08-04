<?php
/**
 * Copyright © Megnor, Inc. All rights reserved.
 */

namespace Megnor\ShopByBrand\Model;

class ProductFactory extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Megnor\ShopByBrand\Model\ProductFactory');
    }
}