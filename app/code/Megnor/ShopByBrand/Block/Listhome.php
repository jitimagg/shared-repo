<?php
namespace Megnor\ShopByBrand\Block;
class Listhome extends \Magento\Framework\View\Element\Template
{

    protected $_brandFactory;
    protected $storeManager;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Megnor\ShopByBrand\Model\BrandFactory $brandFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) 
    {
    	$this->_brandFactory = $brandFactory;
        $this->storeManager = $storeManager;

        parent::__construct($context);
    }
    
    
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    
    public function getBrands(){
		$collection = $this->_brandFactory->create()->getCollection();
		$collection->addFieldToFilter('is_active' , \Megnor\ShopByBrand\Model\Status::STATUS_ENABLED);
		$collection->setOrder('name' , 'ASC');
		$charbrandArray = array();
		foreach($collection as $brand)
		{	
			$name = trim($brand->getName());
			$charbrandArray[strtoupper($name[0])][] = $brand;
		}
		
    	return $charbrandArray;
    }
    //  public function getImageMediaPath(){
    // 	return $this->getUrl('pub/media',['_secure' => $this->getRequest()->isSecure()]);
    // }

    public function getMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA); //http://localhost/itsolution/pub/media/
    }

    public function getFeaturedBrands(){


		$collection = $this->_brandFactory->create()->getCollection();
		$collection->addFieldToFilter('is_active' , \Megnor\ShopByBrand\Model\Status::STATUS_ENABLED);
		$collection->setOrder('sort_order' , 'ASC');
    	return $collection;
    }
    
}