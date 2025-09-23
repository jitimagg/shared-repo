<?php

namespace Fulloop\Products\Plugin;

class PluginBtnProductsView
{
    protected $authSession;

    protected $_backendUrl;

    protected $helperData;

    protected $auth;

    public function __construct(
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\UrlInterface $urlBiulder,
        \Api\ServiceVst\Helper\Data $helperData
    ) {
        $this->authSession = $authSession;

        $this->_backendUrl = $urlBiulder;

        $this->helperData = $helperData;

        $this->auth = $helperData->auth();
    }

    public function beforeSetLayout( \Magento\Sales\Block\Adminhtml\Order\View $subject )
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');

        $subject->addButton(
            'createvstorder',
            [
                'label' => __('Import Product'),
                'onclick' => "
                    ",
            ]
        );

        return null;
    }

    public function ajaxCreateVstOrder($order)
    {
        return '

        ';
    }

}
