<?php

namespace Fulloop\Orders\Api;

interface ResponseManagementInterface
{

    /**
     * POST for response api
     * @param string $param
     * @return string
     */
    public function postResponse();
}
