<?php
declare(strict_types=1);

namespace Fulloop\Customers\Observer\Customer;

use Api\ServiceVst\Controller\Customer\Create;
use Magento\TestFramework\Inspection\Exception;

class RegisterSuccess implements \Magento\Framework\Event\ObserverInterface
{

    protected $context;

    protected $helperData;

    protected $countryFactory;

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
        \Api\ServiceVst\Helper\Data $helperData,
        \Magento\Directory\Model\CountryFactory $countryFactory
    )
    {
        $this->context = $context;

        $this->resultJsonFactory = $resultJsonFactory;

        $this->helperData = $helperData;

        $this->countryFactory = $countryFactory;

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

//        echo '<pre>';
//        $post =  $this->request->getPost();
//        $this->_log(print_r($post, true));
//        exit;

        $customer = $observer->getEvent()->getCustomer();
        $addresses = $customer->getAddresses();

        $countryId = 0;
        $zipcode = '';
        $company = '';
        $phone = '';
        $address = '';
        $regionId = 0;

        foreach ($addresses as $a) {
            if ($a->isDefaultBilling()) {
                $countryId = $a->getCountryId();
                $regionId = $a->getRegionId();
                $zipcode = $a->getPostcode();
                $company = $a->getCompany();
                $phone = $a->getTelephone();
                $address = $a->getStreet();
            }
        }


        $country = $this->countryFactory->create()->loadByCode($countryId);
        $countryName = $country->getName();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $region = $objectManager->create('Magento\Directory\Model\Region')
            ->load($regionId);

        $city = $region->getName();

        $_POST['esd'] = $this->auth;
        $_POST['mage_customer_id'] = $customer->getId();
        $_POST['microsoft_account'] = [
            'username' => $customer->getEmail(),
            'password' => $_POST['password'],
            'con_password' => $_POST['password'],
            'firstname' => $customer->getFirstname(),
            'lastname' => $customer->getLastname(),
            'email' => $customer->getEmail(),
            'venture_id' => $this->helperData->getConfigValue('vst/customer_create/venture_id'),
            'companyname' => $company,
            'contact_no' => $phone,
            'phonenumber' => $phone,
            'currency' => $this->helperData->getConfigValue('vst/customer_create/currency'),
            'status' => 'Active',
            'address1' => !empty($address) ? implode(' ', $address) : '',
            'country' => $countryName,
            'state' => $city,
            'city' => $city,
            'zipcode' => $zipcode,
            'mcountry' => $countryName,
            'mstate' => $city,
            'pid' => $this->helperData->getConfigValue('vst/customer_create/pid'),
        ];

        $create = new Create($this->context, $this->resultJsonFactory, $this->helperData);
        $create->execute();
    }

    private function _log($msg)
    {
        // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/customer_register.log');
        // $logger = new \Zend\Log\Logger();
        // $logger->addWriter($writer);
        // $logger->info($msg);

         $this->helperData->generalLog('/var/log/customer_register.log', $msg);
    }
}
