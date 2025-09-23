<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Api\ServiceVst\Controller\Customer;

class Create extends \Magento\Framework\App\Action\Action
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
        //check has account
        $msCustomerData = $this->_getMicrosoftCustomer();

        $error = false;
        if (empty($msCustomerData)) {
            $result = $this->_createMicrosoftCustomer();
            if (!empty($result['status']) && $result['status'] == 'Success') {
                //get customer list
                $msCustomerData = $this->_getMicrosoftCustomer();
                if (empty($msCustomerData)) {
                    $error = true;
                    $message = $msCustomerData.'-1';
                    if (!empty($result['message'])) {
                        $message .= $result['message'];
                    }
                }
            } else {
                $error = true;
                $message = !empty($result['errorMessage']) ? $result['errorMessage'].'-2' : '' ;
            }
        }

        if (!empty($msCustomerData)) {
            $this->_save($msCustomerData);
            $message = 'success sync data ID:'.$msCustomerData['id'];
        }

        $return = [
                    'error' => $error,
                    'msg' => $message
                ];

        return $this->resultJsonFactory
                    ->create()
                    ->setData($return);
    }

    private function _getMicrosoftCustomer()
    {
        $msFields = !empty($_POST['microsoft_account']) ? $_POST['microsoft_account'] : [];
        $params = [
            'username' => !empty($msFields['username']) ? $msFields['username'] : ''
        ];

        $list =  $this->helperData->customerList($this->auth, $params);

        $data = [];
        if (!empty($list['Customers'])) {
            foreach ($list['Customers'] as $v) {
                if ($params['username'] == $v['username']) {
                    $data = $v;
                }
            }
        }

        return $data;
    }

    private function _createMicrosoftCustomer()
    {
        $params = !empty($_POST['microsoft_account']) ? $_POST['microsoft_account'] : [];

        return $this->helperData->customerCreate($this->auth, $params);
    }

    private function _save($data)
    {
        if (empty($data) || empty($_POST['mage_customer_id'])) {
            return [];
        }
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $product = $objectManager->create('Magento\Customer\Model\Customer')->load($_POST['mage_customer_id']);
        $product->setMicrosoftCustomerId($data['id']);
        $product->save();
    }

}
