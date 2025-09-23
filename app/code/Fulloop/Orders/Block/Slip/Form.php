<?php
namespace Fulloop\Orders\Block\Slip;

class Form extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);
    }
    public function getFormAction()
    {
        return $this->getUrl('fulloop_orders/slip/upload', ['_secure' => true]);
    }
}
