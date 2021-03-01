<?php
/**
 * Project: CMS M2.
 * User: abhay
 * Date: 7/5/17
 * Time: 3:30 PM
 */
namespace Omnyfy\Cms\Block\Country\Market;

use Magento\Framework\View\Element\Template;

class Industry extends \Magento\Framework\View\Element\Template
{
    protected $coreRegistry;
    protected $industryCollectionFactory;
    protected $articleFactory;
    protected $categoryFactory;
    protected $dataHelper;
    protected $_currencyFactory;
	
	/**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;
	
    public function __construct(
        Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
		\Magento\Customer\Model\Session $customerSession,
		\Omnyfy\Cms\Model\ResourceModel\Article\CollectionFactory $articlemodelFactory,
        \Omnyfy\Cms\Helper\Data $dataHelper,
        \Omnyfy\Cms\Model\ResourceModel\Industry\CollectionFactory $industryCollectionFactory,
		\Magento\Framework\Stdlib\DateTime\DateTime $date,
		\Magento\Directory\Model\CurrencyFactory $_currencyFactory,
        \Omnyfy\Cms\Model\CategoryFactory $categoryFactory,
        array $data = [])
    {
        $this->coreRegistry = $coreRegistry;
		$this->customerSession = $customerSession;
        $this->industryCollectionFactory = $industryCollectionFactory;
		$this->articleFactory = $articlemodelFactory;
        $this->categoryFactory = $categoryFactory;
		$this->_date = $date;
		$this->_currencyFactory = $_currencyFactory;
		$this->dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }
	
    /**
     * Return URL for resized CMS Item image
     * 
     * @param Omnyfy_Cms_Model_Article $item
     * @param integer $width
     * @return string|false
     */
    public function getBannerUrl($banner, $width, $height)
    {
        return $this->dataHelper->imageResize($banner, $width, $height);
    }

    public function getLogoUrl($vendorLogo)
    {
        if (empty($vendorLogo)) {
            return false;
        }
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $vendorLogo;
    }
	
	public function getIndustryCollection(){
		return $this->industryCollectionFactory->create()
											->addFieldToSelect('*')
											->addFieldToFilter('status','1')
											->setOrder('industry_name', 'ASC'); 
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
	
	public function getChildCategories($categoryId){
		$industryCategory = $this->categoryFactory->create()->load($categoryId)->getChildrenIds();
		return array_slice($industryCategory, 0, 5, true);
	}
	
	public function getCategory($categoryId){
		return $this->categoryFactory->create()->load($categoryId);
	}
	
	public function getArticleCollection($categoryId){
		$collection = $this->articleFactory->create()->addFieldToSelect('*')
										->join(
											array('category_mapping' => 'omnyfy_cms_article_category'),
											'main_table.article_id = category_mapping.article_id',
											array('category_id' => 'category_id')
										);
		$collection->addFieldToFilter('category_id',$categoryId);								
		$collection->addFieldToFilter('is_active', '1');
		$collection->addFieldToFilter('publish_time', ['lteq' => $this->_date->gmtDate()]);		
		
		if($collection->getSize()>0){
			return true;
		}
		
		return false;
	}
}