<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Api\ServiceVst\Controller\Customer;
use Magento\Framework\HTTP\Client\Curl;

class Update extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Api\ServiceVst\Helper\Data
     */
    protected $helperData;

    protected $auth;

    /**
     * Constructor.
     *
     * @param Magento\Framework\HTTP\Client\Curl $curl
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Api\ServiceVst\Helper\Data $helperData
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;

        $this->helperData = $helperData;

        $this->auth = !empty($_POST['esd']) ? $_POST['esd'] : [];

        parent::__construct($context);
    }

    /**
     * View  page action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result = $this->_updateMicrosoftCustomer();
        $error = 0;
        if (!empty($result['status']) && $result['status'] == 'Success') {
            $message = $result['message'];
        } else {
            $error = 1;
            $message = $result['errorMessage'];
        }

        $return = [
            'error' => $error,
            'msg' => $message
        ];

        $this->_log('result:'.print_r($return, true));

        return $this->resultJsonFactory
            ->create()
            ->setData($return);
    }

    private function _updateMicrosoftCustomer()
    {
        $formType = !empty($_POST['form_type']) ? $_POST['form_type'] : '';
        $params = !empty($_POST[$formType.'_microsoft_account']) ? $_POST[$formType.'_microsoft_account'] : [];

        return $this->helperData->customerUpdate($this->auth, $params);
    }

    private function _log($msg)
    {
        // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/account_edited.log');
        // $logger = new \Zend\Log\Logger();
        // $logger->addWriter($writer);
        // $logger->info($msg);

        $this->helperData->generalLog('/var/log/account_edited.log', $msg);
    }

}
