<?php
declare(strict_types=1);

namespace Fulloop\Pricebot\Api;

interface ProductsManagementInterface
{

    /**
     * POST for products api
     * @param string $param
     * @return string
     */
    public function postProducts($param);

    /**
     * GET for products api
     * @param string $param
     * @return string
     */
    public function getProducts($param);
}

