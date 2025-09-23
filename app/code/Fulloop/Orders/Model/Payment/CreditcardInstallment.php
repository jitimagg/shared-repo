<?php

namespace Fulloop\Orders\Model\Payment;

class CreditcardInstallment extends \Magento\Payment\Model\Method\AbstractMethod
{

    protected $_code = "creditcard_installment";
    protected $_isOffline = true;

    public function isAvailable(
        \Magento\Quote\Api\Data\CartInterface $quote = null
    ) {
        $itemsArray = $quote->getAllVisibleItems();
        if (empty($itemsArray)) {
            return false;
        }

        foreach($itemsArray as $item) {
            $product = $item->getProduct();
            if (!$product->getIsInstallment()) {
                return false;
            }
        }

        return parent::isAvailable($quote);
    }
}

