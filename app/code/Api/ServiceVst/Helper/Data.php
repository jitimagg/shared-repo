<?php

namespace Api\ServiceVst\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Filesystem\Io\File;

class Data extends AbstractHelper
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    protected $file;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        File $file
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->file = $file;

        parent::__construct($context);
    }

    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field, ScopeInterface::SCOPE_STORE, $storeId
        );
    }

    protected function _setCurl($url, $method, $params, $header)
    {
        $this->_log($url.print_r($params, true).print_r($header, true));

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_HTTPHEADER => $header
        ]);

        $response = curl_exec($curl);

        $error_msg = '';
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
        }

        curl_close($curl);

        $this->_log(print_r($response, true).'---'.print_r($error_msg, true));

        return json_decode($response, true);
    }

    public function auth()
    {
        $url = $this->getConfigValue('vst/Authen/url');

        $params = [
            'grant_type' => $this->getConfigValue('vst/Authen/grant_type'),
            'client_id' => $this->getConfigValue('vst/Authen/client_id'),
            'client_secret' => $this->getConfigValue('vst/Authen/client_secret')
        ];

        $header = [
            "cache-control: no-cache"
        ];

        return $this->_setCurl($url, 'POST', $params, $header);
    }

    public function customerList($auth, $params = [])
    {
        if (empty($auth['access_token'])) {
            return false;
        }

        $url = $this->getConfigValue('vst/customer_details/url');

        $header = [
            "cache-control: no-cache",
            "Validate: {$auth['token_type']} {$auth['access_token']}"
        ];

        return $this->_setCurl($url, 'POST', $params, $header);
    }

    public function customerCreate($auth, $params = [])
    {
        if (empty($auth['access_token'])) {
            return false;
        }

        $url = $this->getConfigValue('vst/customer_create/url');

        $header = [
            "cache-control: no-cache",
            "Validate: {$auth['token_type']} {$auth['access_token']}"
        ];

        return $this->_setCurl($url, 'POST', $params, $header);
    }

    public function customerUpdate($auth, $params = [])
    {
        if (empty($auth['access_token'])) {
            return false;
        }

        $url = $this->getConfigValue('vst/customer_update/url');

        $header = [
            "cache-control: no-cache",
            "Validate: {$auth['token_type']} {$auth['access_token']}"
        ];

        return $this->_setCurl($url, 'POST', $params, $header);
    }

    public function orderCreate($auth, $params = [])
    {
        if (empty($auth['access_token'])) {
            return false;
        }

        $url = $this->getConfigValue('vst/order_create/url');

        $header = [
            "cache-control: no-cache",
            "Validate: {$auth['token_type']} {$auth['access_token']}"
        ];

        return $this->_setCurl($url, 'POST', $params, $header);
    }

    public function edsPlans($auth, $params = [])
    {
        if (empty($auth['access_token'])) {
            return false;
        }

        $url = $this->getConfigValue('vst/product_plan/url');

        $header = [
            "cache-control: no-cache",
            "Validate: {$auth['token_type']} {$auth['access_token']}"
        ];

        return $this->_setCurl($url, 'POST', $params, $header);
    }

    private function _log($msg)
    {
        $this->generalLog('/var/log/service_vst.log', $msg);
    }

    public function generalLog($filePath, $msg = '')
    {
        // Create file and write content
        $this->file->write(BP . $filePath, $msg, 0644);
    }
}
