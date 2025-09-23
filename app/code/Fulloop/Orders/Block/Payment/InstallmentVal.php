<?php
namespace Fulloop\Orders\Block\Payment;

class InstallmentVal extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);
    }
    public function getTest()
    {
        return 555;
    }
}
