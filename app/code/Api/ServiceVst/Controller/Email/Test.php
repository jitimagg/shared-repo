<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Api\ServiceVst\Controller\Email;

class Test extends \Magento\Framework\App\Action\Action
{
    protected $auth = [];

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_getAuthenToken();
        parent::__construct($context);
    }

    private function _getAuthenToken()
    {
        $url = 'https://managemp.labvstecscloud.net/api/v2/auth/token';
        $params = [
            'grant_type' => 'client_credentials',
            'client_id' => 'racknap-1266981365',
            'client_secret' => '5a263c8b9fe3ba572ef5c7d6f3780061c6a3b41a8e941aa1c4d12e99add0c9ad'
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $params,
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $result = json_decode($response, true);

        $this->_auth = empty($result['errorCode']) ? $result : [];
    }

    /**
     * View  page action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        $resources = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magento\Framework\App\ResourceConnection');
        $connection = $resources->getConnection();
        $sql = "SELECT * FROM order_microsoft_license";
        $rsl = $connection->fetchAll($sql);
        $t = '<div style="margin: auto; width: 900px;">
<p>To: teerachai.jaichuang@gmail.com</p>
<p>From: no-reply@itsolution.co.th</p>
<hr/>';
        $t .= '<image src="http://www.itsolution.co.th/skin/frontend/ultimo/default/images/ITsolutionShop03.png" />';
        $t .= '<h1>Thank you!</h1>';
//        $t .= '<table border="1">';
//        $t .= '<tr>
//                        <th>Plan Name</th>
//                        <th>Product Key</th>
//                        <th></th>
//                    </tr>';
//        foreach ($rsl as $v) {
//            $t .= '<tr>
//                        <td>'.$v['plan_name'].'</td>
//                        <td>'.$v['product_key'].'</td>
//                        <td><a href="'.$v['activation_link'].'?q='.$v['activation_link'].'">Activate Link</a></td>
//                    </tr>';
//        }
//        $t .= '</table>';

        foreach ($rsl as $v) {
            $t .= '<p><b>Plan Name:</b> '.$v['plan_name'].'</p>';
            $t .= '<p><b>Product Key:</b> '.$v['product_key'].'</p>';
            $t .= '<p><a href="'.$v['activation_link'].'?q='.$v['activation_link'].'">Activate Link</a></p><br/><br/>';
        }
        $t .= '<br/><a href="'.$v['racknap_customer_portal_url'].'"><button>Portal URL</button></a></div>';
        echo $t;
        exit;

        $customer_id = 2;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerData = $objectManager->create('Magento\Customer\Model\Customer')->load($customer_id);
        echo '<pre>';print_r($customerData->getData());

        $params = [
            'email' => 'teerachai.jc2@gmail.com'
        ];
        $result = $this->helperData->customerList($this->auth, $params);

        print_r($result['Customers'][0]);
    }

}
