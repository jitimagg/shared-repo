<?php
declare(strict_types=1);

namespace Fulloop\Customers\Observer\Customer;

use Api\ServiceVst\Controller\Customer\Update;

class AccountEdited implements \Magento\Framework\Event\ObserverInterface
{

    protected $context;

    protected $helperData;

    protected $auth;

    protected $resultJsonFactory;

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
        $this->context = $context;

        $this->resultJsonFactory = $resultJsonFactory;

        $this->helperData = $helperData;

        $this->auth = $helperData->auth();

    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {

        $customer = $observer->getEvent()->getCustomerDataObject();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $objCustomer = $objectManager->create('Magento\Customer\Model\Customer')->load($customer->getId());

        $this->_log(print_r($customer->getId().'||'.$customer->getFirstname().'||'.$customer->getLastname().'||'.$customer->getEmail().'||'.($objCustomer->getMicrosoftCustomerId() ? : 0), true));

        if (!$objCustomer->getMicrosoftCustomerId()) {
            return;
        }

        $this->_log('update ms account');

        $_POST['esd'] = $this->auth;
        $_POST['form_type'] = 'personal_detail_form';
        $_POST['personal_detail_form_microsoft_account'] = [
            'customer_id' => $objCustomer->getMicrosoftCustomerId(),
            'form_type' => 'personal_detail_form',
            'submit' => 'Update',
            'username' => $customer->getEmail(),
            'password' => '',
            'firstname' => $customer->getFirstname(),
            'lastname' => $customer->getLastname(),
            'email' => $customer->getEmail()
        ];

        $create = new Update($this->context, $this->resultJsonFactory, $this->helperData);
        $create->execute();

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
