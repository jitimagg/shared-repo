<?php
namespace Megnor\ShopByBrand\Block;
class Brandlist extends \Magento\Framework\View\Element\Template
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
    //     return $this->getUrl('pub/media',['_secure' => $this->getRequest()->isSecure()]);
    // }

    public function getMediaUrl()
        {
            return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        }

    /**
     * Prepare breadcrumbs
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    public function _addBreadcrumbs()
    {
       $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');

       if($breadcrumbsBlock){

            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
            );
            $breadcrumbsBlock->addCrumb(
                'brand',
                [
                    'label' => __('Brand'),
                    'title' => __(sprintf('Go to Brand Home Page'))
                ]
            );
        }
    }
    public function _prepareLayout()
    {
        $this->_addBreadcrumbs();
        return parent::_prepareLayout();
        
    }
}