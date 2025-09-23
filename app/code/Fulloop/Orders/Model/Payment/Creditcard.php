<?php

namespace Fulloop\Orders\Model\Payment;

class Creditcard extends \Magento\Payment\Model\Method\AbstractMethod
{

    protected $_code = "creditcard";
    protected $_isOffline = true;

    public function isAvailable(
        \Magento\Quote\Api\Data\CartInterface $quote = null
    ) {
        return parent::isAvailable($quote);
    }
}

