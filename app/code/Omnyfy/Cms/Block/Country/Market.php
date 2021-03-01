<?php
/**
 * Project: CMS M2.
 * User: abhay
 * Date: 26/4/18
 * Time: 03:00 PM
 */
namespace Omnyfy\Cms\Block\Country;

use Magento\Framework\View\Element\Template;

class Market extends \Magento\Framework\View\Element\Template
{
    protected $coreRegistry;
    protected $countryCollectionFactory;
    protected $industryCollectionFactory;
    protected $categoryFactory;
    protected $dataHelper;
    protected $_currencyFactory;
	
    public function __construct(
        Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
		\Magento\Customer\Model\Session $customerSession,
        \Omnyfy\Cms\Helper\Data $dataHelper,
		\Omnyfy\Cms\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
		\Omnyfy\Cms\Model\ResourceModel\Industry\CollectionFactory $industryCollectionFactory,
		\Magento\Directory\Model\CurrencyFactory $_currencyFactory,
        \Omnyfy\Cms\Model\CategoryFactory $categoryFactory,
        array $data = [])
    {
        $this->coreRegistry = $coreRegistry;
		$this->customerSession = $customerSession;
		$this->industryCollectionFactory = $industryCollectionFactory;
        $this->countryCollectionFactory = $countryCollectionFactory;
        $this->categoryFactory = $categoryFactory;
		$this->_currencyFactory = $_currencyFactory;
		$this->dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }
	
	/**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {

		$this->pageConfig->addBodyClass('cms-country-market');
		$this->pageConfig->getTitle()->set('Export markets finder');
		#$this->pageConfig->setKeywords($category->getMetaKeywords());
		#$this->pageConfig->setDescription($category->getMetaDescription());
		/* $this->pageConfig->addRemotePageAsset(
			$category->getCategoryUrl(),
			'canonical',
			['attributes' => ['rel' => 'canonical']]
		); */

        return parent::_prepareLayout();
    }
	
    /**
     * Return URL for resized CMS Item image
     * 
     * @param integer $width
     * @return string|false
     */
    public function getResizedImage($banner, $width = null, $height = null)
    {
        return $this->dataHelper->imageResize($banner, $width, $height);
    }
	
	public function getIndustryCollection(){
		return $this->industryCollectionFactory->create()
											->addFieldToSelect('*')
											->addFieldToFilter('status','1')
											->setOrder('industry_name', 'ASC'); 
	}

    public function getLogoUrl($logo)
    {
        if (empty($logo)) {
            return false;
        }
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $logo;
    }
	
	/**
     * Return login url for guest users with referer url
     *
     * @return string
     */
    public function getLoginUrl() {
        $url = $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
        $login_url = $this->getUrl('customer/account/login', array('referer' => base64_encode($url)));
        return $login_url;
    }
	
	public function isLoggedIn() {
        return $this->customerSession->isLoggedIn();
    }
	
	/* public function getChildCategories(){
		return $this->categoryFactory->create()->load($this->getCountry()->getIndustryInfoCategory());
	} */
	
	public function getCategory($categoryId){
		return $this->categoryFactory->create()->load($categoryId);
	}
	
	public function getCurrentUrl() {
        $url = $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
        return $url;
    }
	
	public function getCountryCollection(){
		return $this->countryCollectionFactory->create()
											->addFieldToSelect('*')
											->addFieldToFilter('status','1')
											->setOrder('country_name', 'ASC'); 
	}
	
	public function getCountryUrl($countryId){
		 return $this->getUrl('cms/country/view/id', array('id' => $countryId));
	}
	
	
	public function getIndustryCategories($industryId){
		$industryCategory = $this->categoryFactory->create()->load($industryId)->getChildrenIds();
		return array_slice($industryCategory, 0, 5, true);
	}
	
	public function getMapPosition($value,$position){
		$finalVal = $value/$position*100;
		return number_format((float)$finalVal, 2, '.', '').'%';
	}
}