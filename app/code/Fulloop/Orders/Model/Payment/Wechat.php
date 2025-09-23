<?php

namespace Fulloop\Orders\Model\Payment;

class Wechat extends \Magento\Payment\Model\Method\AbstractMethod
{

    protected $_code = "wechat";
    protected $_isOffline = true;

    public function isAvailable(
        \Magento\Quote\Api\Data\CartInterface $quote = null
    ) {
        return parent::isAvailable($quote);
    }
}

