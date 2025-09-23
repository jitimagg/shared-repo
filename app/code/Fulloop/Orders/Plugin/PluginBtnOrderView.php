<?php

namespace Fulloop\Orders\Plugin;

class PluginBtnOrderView
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

        $url = $storeManager->getStore()->getBaseUrl().'rest/V1/fulloop-orders/response';
        $adminUserId = $this->authSession->getUser()->getId() ?: 0;
        $order = $subject->getOrder();
        $incrementId = $order->getIncrementId();
        $tokenType = $this->auth['token_type'];
        $accessToken = $this->auth['access_token'];

        $isEsdOrder = 0;
        $orderItems = $order->getAllVisibleItems();
        foreach ($orderItems as $item) {
            $product = $item->getProduct();
            if ($product->getIsMicrosoftLicense()) {
                $isEsdOrder++;
            }
        }

        if ($isEsdOrder > 0) {
            $subject->addButton(
                'createvstorder',
                [
                    'label' => __('Create Vst Order'),
                    'onclick' => "
                    if(confirm('Are you sure?')) {
                        createVstOrder('{$url}', '{$incrementId}', '{$tokenType}', '{$accessToken}', {$adminUserId})
                    }
                    ",
                ]
            );
        }

        return null;
    }

    public function ajaxCreateVstOrder($order)
    {
        return '

        ';
    }

}
