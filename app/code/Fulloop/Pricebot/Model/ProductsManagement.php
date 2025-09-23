<?php
declare(strict_types=1);

namespace Fulloop\Pricebot\Model;

class ProductsManagement implements \Fulloop\Pricebot\Api\ProductsManagementInterface
{

    /**
     * {@inheritdoc}
     */
    public function postProducts($param)
    {
        return 'hello api POST return the $param ' . $param;
    }

    /**
     * {@inheritdoc}
     */
    public function getProducts($param)
    {
        return 'hello api GET return the $param ' . $param;
    }
}

