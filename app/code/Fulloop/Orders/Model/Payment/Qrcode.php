<?php

namespace Fulloop\Orders\Model\Payment;

class Qrcode extends \Magento\Payment\Model\Method\AbstractMethod
{

    protected $_code = "qrcode";
    protected $_isOffline = true;

    public function isAvailable(
        \Magento\Quote\Api\Data\CartInterface $quote = null
    ) {
        return parent::isAvailable($quote);
    }
}

