<?php

namespace Fulloop\Customers\Block\Adminhtml\Edit\Tab;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Ui\Component\Layout\Tabs\TabInterface;

class MicrosoftCustomer  extends \Magento\Framework\View\Element\Template implements TabInterface
{
    protected $_template = 'tab/microsoftcustomer.phtml';//your template file path

    /**
     * @var \Api\ServiceVst\Helper\Data
     */
    protected $helperData;

    protected $auth = [];

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Api\ServiceVst\Helper\Data $helperData,
        \Magento\Framework\UrlInterface $urlInterface,
        array $data = []
    ) {
        $this->helperData = $helperData;

        $this->auth = $helperData->auth();

        $this->_coreRegistry = $registry;

        parent::__construct($context, $data);
    }

    public function getAtuh()
    {
        return $this->auth;
    }

    public function getCustomerId()
    {
        return $this->_coreRegistry->registry(\Magento\Customer\Controller\RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    public function getCustomerData()
    {
        $customerId = $this->getCustomerId();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $collection = $objectManager->create('Magento\Customer\Model\Customer')->load($customerId);

        $customer = [];
        $customer['data'] = $collection->getData();

        foreach($collection->getAddresses() as $address){
            $customer['address'][$address->getId()] = $address->getData();
        }

        return $customer;
    }

    public function getFormField()
    {
        $customer = $this->getCustomerData();

        if (empty($customer['data'])) {
            return [];
        }

//        echo '<pre>';
//        print_r($customer);
//        exit;
        $fields = [];

        if (!empty($customer['data']['microsoft_customer_id'])) {
            //check has account
            $msCustomerData = $this->_getMicrosoftCustomer($customer['data']['microsoft_customer_id']);

            if (empty($msCustomerData['Customers'])) {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $objCustomer = $objectManager->create('Magento\Customer\Model\Customer')->load($this->getCustomerId());
                $objCustomer->setMicrosoftCustomerId(0);
                $objCustomer->save();
            } else {
                $fields = $this->_prepareUpdateForm($msCustomerData['Customers'][0]);
            }

        } else {
            $fields = $this->_prepareCreatForm($customer);
        }

        return $fields;
    }

    protected function _prepareUpdateForm($customer = [])
    {
        $fields['personal_detail_form'] = [
            'label' => 'Personal Detail',
            'fields' => [
                [
                    'input_label' => 'Username',
                    'input_type' => 'text',
                    'input_name' => 'username',
                    'input_value' => $customer['username'],
                ],
                [
                    'input_label' => 'Change Password',
                    'input_type' => 'password',
                    'input_name' => 'password',
                    'input_value' => '',
                ],
                [
                    'input_label' => 'Firstname',
                    'input_type' => 'text',
                    'input_name' => 'firstname',
                    'input_value' => $customer['firstname'],
                ],
                [
                    'input_label' => 'Lastname',
                    'input_type' => 'text',
                    'input_name' => 'lastname',
                    'input_value' => $customer['lastname'],
                ],
                [
                    'input_label' => 'Email',
                    'input_type' => 'text',
                    'input_name' => 'email',
                    'input_value' => $customer['email'],
                ],
            ]
        ];

        $fields['company_detail_form'] = [
            'label' => 'Company Detail',
            'fields' => [
                [
                    'input_label' => 'Company Name',
                    'input_type' => 'text',
                    'input_name' => 'companyname',
                    'input_value' => $customer['companyname'],
                ],
                [
                    'input_label' => 'Website',
                    'input_type' => 'text',
                    'input_name' => 'website',
                    'input_value' => '',
                ],
                [
                    'input_label' => 'Number of Employee',
                    'input_type' => 'text',
                    'input_name' => 'noofemployee',
                    'input_value' => '',
                ],
                [
                    'input_label' => 'Company\'s age',
                    'input_type' => 'text',
                    'input_name' => 'companyage',
                    'input_value' => '',
                ],
            ]
        ];

        $fields['billing_detail_form'] = [
            'label' => 'Billing Address Detail',
            'fields' => [
                [
                    'input_label' => 'Address 1',
                    'input_type' => 'text',
                    'input_name' => 'add1',
                    'input_value' => $customer['address1'],
                ],
                [
                    'input_label' => 'Address 2',
                    'input_type' => 'text',
                    'input_name' => 'address2',
                    'input_value' => $customer['address2'],
                ],
                [
                    'input_label' => 'Country',
                    'input_type' => 'text',
                    'input_name' => 'countryid',
                    'input_value' => '',
                ],
                [
                    'input_label' => 'State',
                    'input_type' => 'text',
                    'input_name' => 'state',
                    'input_value' => $customer['state'],
                ],
                [
                    'input_label' => 'City',
                    'input_type' => 'text',
                    'input_name' => 'city',
                    'input_value' => $customer['city'],
                ],
                [
                    'input_label' => 'Zipcode',
                    'input_type' => 'text',
                    'input_name' => 'pcode',
                    'input_value' => '',
                ],
            ]
        ];

        $fields['mailing_detail_form'] = [
            'label' => 'mailing Address Detail',
            'fields' => [
                [
                    'input_label' => 'Address 1',
                    'input_type' => 'text',
                    'input_name' => 'madd1',
                    'input_value' => $customer['address1'],
                ],
                [
                    'input_label' => 'Address 2',
                    'input_type' => 'text',
                    'input_name' => 'madd2',
                    'input_value' => $customer['address2'],
                ],
                [
                    'input_label' => 'Country',
                    'input_type' => 'text',
                    'input_name' => 'mcountryid',
                    'input_value' => '',
                ],
                [
                    'input_label' => 'State',
                    'input_type' => 'text',
                    'input_name' => 'mstate',
                    'input_value' => $customer['state'],
                ],
                [
                    'input_label' => 'City',
                    'input_type' => 'text',
                    'input_name' => 'mcity',
                    'input_value' => $customer['city'],
                ],
                [
                    'input_label' => 'Zipcode',
                    'input_type' => 'text',
                    'input_name' => 'mpcode',
                    'input_value' => '',
                ],
            ]
        ];

        $fields['contact_detail_form'] = [
            'label' => 'Contact Detail',
            'fields' => [
                [
                    'input_label' => 'Address 1',
                    'input_type' => 'text',
                    'input_name' => 'madd1',
                    'input_value' => $customer['address1'],
                ],
                [
                    'input_label' => 'Address 2',
                    'input_type' => 'text',
                    'input_name' => 'madd2',
                    'input_value' => $customer['address2'],
                ],
            ]
        ];

        $fields['security_detail_form'] = [
            'label' => 'Security Detail',
            'fields' => [
                [
                    'input_label' => 'Security Question',
                    'input_type' => 'text',
                    'input_name' => 'sanswer',
                    'input_value' => '',
                ],
                [
                    'input_label' => 'Security Answer',
                    'input_type' => 'text',
                    'input_name' => 'sques',
                    'input_value' => '',
                ],
            ]
        ];

        $fields['other_detail_form'] = [
            'label' => 'Other Detail',
            'fields' => [
                [
                    'input_label' => 'Customer\'s Status',
                    'input_type' => 'text',
                    'input_name' => 'status',
                    'input_value' => $customer['customer_status'],
                ],
                [
                    'input_label' => 'Language',
                    'input_type' => 'text',
                    'input_name' => 'language',
                    'input_value' => '',
                ],
                [
                    'input_label' => 'Customer Group',
                    'input_type' => 'text',
                    'input_name' => 'client_group_id',
                    'input_value' => '',
                ],
                [
                    'input_label' => 'Testing Account or not',
                    'input_type' => 'text',
                    'input_name' => 'testing_account_name',
                    'input_value' => '',
                ],
                [
                    'input_label' => 'Service renewal anniversary date',
                    'input_type' => 'text',
                    'input_name' => 'anniversary_date',
                    'input_value' => '',
                ],
                [
                    'input_label' => 'SMS Notifications',
                    'input_type' => 'text',
                    'input_name' => 'recievesms',
                    'input_value' => '',
                ],
                [
                    'input_label' => 'Overdue notices',
                    'input_type' => 'text',
                    'input_name' => 'overnotice',
                    'input_value' => '',
                ],
                [
                    'input_label' => 'Late fees',
                    'input_type' => 'text',
                    'input_name' => 'latefee',
                    'input_value' => '',
                ],
                [
                    'input_label' => 'Tax Exempt',
                    'input_type' => 'text',
                    'input_name' => 'taxexempt',
                    'input_value' => '',
                ],
                [
                    'input_label' => 'PAN Number',
                    'input_type' => 'text',
                    'input_name' => 'pan_no',
                    'input_value' => '',
                ],
                [
                    'input_label' => 'Invoice due days',
                    'input_type' => 'text',
                    'input_name' => 'inv_due_days',
                    'input_value' => '',
                ],
                [
                    'input_label' => 'Admin Note',
                    'input_type' => 'text',
                    'input_name' => 'adminnotes',
                    'input_value' => '',
                ],
                [
                    'input_label' => 'Custom fields',
                    'input_type' => 'text',
                    'input_name' => 'custom_field',
                    'input_value' => '',
                ],
            ]
        ];
        return $fields;
    }

    protected function _prepareCreatForm($customer = [])
    {
        $fields['personal_detail_form'] = [
            'label' => 'Create Microsoft Account',
            'fields' => [
                [
                    'input_label' => 'Username',
                    'input_type' => 'text',
                    'input_name' => 'username',
                    'input_value' => '',
                ],
                [
                    'input_label' => 'Password',
                    'input_type' => 'password',
                    'input_name' => 'password',
                    'input_value' => '',
                ],
                [
                    'input_label' => 'Confirm Password',
                    'input_type' => 'password',
                    'input_name' => 'con_password',
                    'input_value' => '',
                ],
                [
                    'input_label' => 'Firstname',
                    'input_type' => 'text',
                    'input_name' => 'firstname',
                    'input_value' => $customer['data']['firstname'],
                ],
                [
                    'input_label' => 'Lastname',
                    'input_type' => 'text',
                    'input_name' => 'lastname',
                    'input_value' => $customer['data']['lastname'],
                ],
                [
                    'input_label' => 'Email',
                    'input_type' => 'text',
                    'input_name' => 'email',
                    'input_value' => $customer['data']['email'],
                ],
                [
                    'input_label' => 'Venture ID',
                    'input_type' => 'text',
                    'input_name' => 'venture_id',
                    'input_value' => '',
                ],
                [
                    'input_label' => 'Company name',
                    'input_type' => 'text',
                    'input_name' => 'companyname',
                    'input_value' => '',
                ],
                [
                    'input_label' => 'Website',
                    'input_type' => 'text',
                    'input_name' => 'website',
                    'input_value' => '',
                ],
                [
                    'input_label' => 'Mobile Number',
                    'input_type' => 'text',
                    'input_name' => 'contact_no',
                    'input_value' => '',
                ],
                [
                    'input_label' => 'Phone Number',
                    'input_type' => 'text',
                    'input_name' => 'phonenumber',
                    'input_value' => '',
                ],
                [
                    'input_label' => 'Currency',
                    'input_type' => 'text',
                    'input_name' => 'currency',
                    'input_value' => '',
                ],
                [
                    'input_label' => 'Status',
                    'input_type' => 'text',
                    'input_name' => 'status',
                    'input_value' => '',
                ],
                [
                    'input_label' => 'SMS Notification',
                    'input_type' => 'text',
                    'input_name' => 'smsnoti',
                    'input_value' => '',
                ],
                [
                    'input_label' => 'Admin Note',
                    'input_type' => 'text',
                    'input_name' => 'adminnote',
                    'input_value' => '',
                ],
                [
                    'input_label' => 'Partner ID',
                    'input_type' => 'text',
                    'input_name' => 'pid',
                    'input_value' => '',
                ],

            ]
        ];

        $address = [];
        if (!empty($customer['data']['default_billing'])) {
            if (!empty($customer['address'][$customer['data']['default_billing']])) {
                $address = $customer['address'][$customer['data']['default_billing']];
            }
        }

        $fields['address_detail_form'] = [
            'label' => 'Address Information',
            'fields' => [
                [
                    'input_label' => 'Address 1',
                    'input_type' => 'text',
                    'input_name' => 'address1',
                    'input_value' => !empty($address['street']) ? $address['street'] : '',
                ],
                [
                    'input_label' => 'Address 2',
                    'input_type' => 'text',
                    'input_name' => 'address2',
                    'input_value' => '',
                ],
                [
                    'input_label' => 'Country',
                    'input_type' => 'text',
                    'input_name' => 'country',
                    'input_value' => (!empty($address['country_id']) && $address['country_id'] == 'TH') ? 'Thailand' : '',
                ],
                [
                    'input_label' => 'State',
                    'input_type' => 'text',
                    'input_name' => 'state',
                    'input_value' => !empty($address['city']) ? $address['city'] : '',
                ],
                [
                    'input_label' => 'City',
                    'input_type' => 'text',
                    'input_name' => 'city',
                    'input_value' => !empty($address['city']) ? $address['city'] : '',
                ],
                [
                    'input_label' => 'Zipcode',
                    'input_type' => 'text',
                    'input_name' => 'zipcode',
                    'input_value' => !empty($address['postcode']) ? $address['postcode'] : '',
                ],
            ]
        ];

        $maddress = [];
        if (!empty($customer['data']['default_billing'])) {//default_shipping
            if (!empty($customer['address'][$customer['data']['default_billing']])) {
                $maddress = $customer['address'][$customer['data']['default_billing']];
            }
        }

        $fields['maddress_detail_form'] = [
            'label' => 'Mailing Address Information',
            'fields' => [
                [
                    'input_label' => 'Address 1',
                    'input_type' => 'text',
                    'input_name' => 'madd1',
                    'input_value' => !empty($maddress['street']) ? $maddress['street'] : '',
                ],
                [
                    'input_label' => 'Address 2',
                    'input_type' => 'text',
                    'input_name' => 'madd2',
                    'input_value' => '',
                ],
                [
                    'input_label' => 'Country',
                    'input_type' => 'text',
                    'input_name' => 'mcountry',
                    'input_value' => (!empty($maddress['country_id']) && $maddress['country_id'] == 'TH') ? 'Thailand' : '',
                ],
                [
                    'input_label' => 'State',
                    'input_type' => 'text',
                    'input_name' => 'mstate',
                    'input_value' => !empty($maddress['city']) ? $maddress['city'] : '',
                ],
                [
                    'input_label' => 'City',
                    'input_type' => 'text',
                    'input_name' => 'mcity',
                    'input_value' => !empty($maddress['city']) ? $maddress['city'] : '',
                ],
                [
                    'input_label' => 'Zipcode',
                    'input_type' => 'text',
                    'input_name' => 'mpcode',
                    'input_value' => !empty($maddress['postcode']) ? $maddress['postcode'] : '',
                ],
            ]
        ];
        return $fields;
    }

    public function checkInputRequired($inputName = '')
    {
        $requireFields = [
            'username', 'password', 'con_password', 'firstname', 'email', 'companyname',
            'contact_no', 'phonenumber', 'currency', 'status', 'address1', 'country',
            'state', 'city', 'zipcode', 'mcountry', 'mstate'
        ];

        return in_array($inputName, $requireFields);
    }

    public function getTabLabel()
    {
        return __('Microsoft Customer');
    }

    public function getTabTitle()
    {
        return __('Microsoft Customer');
    }

    public function canShowTab()
    {
        if ($this->getCustomerId()) {
            return true;
        }
        return false;
    }

    public function isHidden()
    {
        if ($this->getCustomerId()) {
            return false;
        }
        return true;
    }

    public function getTabClass()
    {
        return '';
    }

    public function getTabUrl()
    {
        return '';
    }

    public function isAjaxLoaded()
    {
        return false;
    }

    public function getVstUrl($path = '')
    {
        return $this->_storeManager->getStore()->getBaseUrl().$path;
    }

    private function _getMicrosoftCustomer($microsoftCustomerId = 0)
    {
        $params = [
            'customer_id' => $microsoftCustomerId
        ];

        return $this->helperData->customerList($this->auth, $params);
    }
}
