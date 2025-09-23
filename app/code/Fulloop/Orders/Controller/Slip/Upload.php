<?php
namespace Fulloop\Orders\Controller\Slip;

use Magento\Framework\App\Filesystem\DirectoryList;

class Upload extends \Magento\Framework\App\Action\Action
{

    protected $request;

    protected $_filesystem;

    protected $_customerSession;

    protected $orderFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Filesystem $fileSystem,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Api\Data\OrderInterfaceFactory $orderFactory
    ) {
       $this->request = $request;

        $this->_filesystem = $fileSystem;

        $this->_customerSession = $customerSession;

        $this->orderFactory = $orderFactory;

        return parent::__construct($context);
    }

    public function execute()
    {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $baseUrl = $storeManager->getStore()->getBaseUrl();

        $customerSession = $objectManager->get('Magento\Customer\Model\Session');
        $customer = $customerSession->getCustomer();
        $customerData = $customer->getData();

        $post =  $this->request->getPost();

        if (empty($post['increment_id']) || empty($customerData)) {
            header('location: '.$baseUrl.'fulloop_orders/slip/form/');
            exit;
        }

        $order = $this->orderFactory->create()->loadByIncrementId($post['increment_id']);
        $mobile = $order->getShippingAddress()->getTelephone();
        $customerId = $order->getCustomerId();

        if ($customerId != $customerData['entity_id']) {
            header('location: '.$baseUrl.'fulloop_orders/slip/form/');
            exit;
        }

//        echo '<pre>';
//        print_r($customerData);
//        print_r($order->getShippingAddress()->getTelephone());
//        exit;
        $absPath = $this->uploadFile();

        $incrementId = $post['increment_id'];
        $firstName = !empty($customerData['firstname']) ? $customerData['firstname'] : '';
        $lastName = !empty($customerData['lastname']) ? $customerData['lastname'] : '';
        $email = !empty($customerData['email']) ? $customerData['email'] : '';
        $mobile = !empty($mobile) ? $mobile : '';
        $filePath = !empty($absPath['file']) ? $absPath['file'] : '';
        $adedBy = !empty($customerData['entity_id']) ? $customerData['entity_id'] : '';
        $addedType = 'customer';
        $amount = !empty($post['amount']) ? $post['amount'] : '';

        $resources = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magento\Framework\App\ResourceConnection');
        $connection= $resources->getConnection();

        try {
            $sql = "INSERT INTO order_payment_proof(
                                                        increment_id, firstname, lastname, email, mobile, file_path,
                                                        added_by, added_type, amount
                                                        )
                    VALUES (:increment_id, :firstname, :lastname, :email, :mobile,
                            :file_path, :added_by, :added_type, :amount)";
            $bind = [
                'increment_id' => $incrementId,
                'firstname' => $firstName,
                'lastname' => $lastName,
                'email' => $email,
                'mobile' => $mobile,
                'file_path' => $filePath,
                'added_by' => $adedBy,
                'added_type' => $addedType,
                'amount' => $amount
            ];

            $connection->query($sql, $bind);
        } catch (Exception $e) {
            $this->_log($e->getMessage());
        }

        header('location: '.$baseUrl.'sales/order/history/');
        exit;
    }

    private function uploadFile() {
        $result = array();
        if ($_FILES['slip']['name']) {
            try {
                // init uploader model.
                $uploader = $this->_objectManager->create(
                    'Magento\MediaStorage\Model\File\Uploader',
                    ['fileId' => 'slip']
                );
                $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(true);
                // get media directory
                $mediaDirectory = $this->_filesystem->getDirectoryRead('media');
                // save the image to media directory
                $result = $uploader->save($mediaDirectory->getAbsolutePath());
            } catch (Exception $e) {
                \Zend_Debug::dump($e->getMessage());
            }
        }

        return $result;
    }
}
