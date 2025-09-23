<?php
namespace Fulloop\Pages\Plugin;

class Topmenu
{

    protected $Session;
    /**
     * @param Context                                   $context
     * @param array                                     $data
     */
    public function __construct(
        \Magento\Customer\Model\Session $session
    ) {
        $this->Session = $session;
    }


    public function afterGetHtml(\Magento\Theme\Block\Html\Topmenu $topmenu, $html)
    {
//        $swappartyUrl = $topmenu->getUrl('fulloop_orders/slip/form');//here you can set link
//        $html .= "<li class=\"level0 nav-4 level-top ui-menu-item\">";
//        $html .= "<a href=\"" . $swappartyUrl . "\" class=\"level-top ui-corner-all\"><span class=\"ui-menu-icon ui-icon ui-icon-carat-1-e\"></span><span>" . __("แจ้งชำระเงิน") . "</span></a>";
//        $html .= "</li>";
        return $html;
    }
}
