<?php

namespace Fulloop\Orders\Model\Payment;

class Alipay extends \Magento\Payment\Model\Method\AbstractMethod
{

    protected $_code = "alipay";
    protected $_isOffline = true;

    public function isAvailable(
        \Magento\Quote\Api\Data\CartInterface $quote = null
    ) {
        return parent::isAvailable($quote);
    }
}

