<?php
namespace Omnyfy\Cms\Block\Learn;
use Magento\Framework\View\Element\Template;
class TopicSearchCountry extends Template
{
    /**
     * Article collection
     *
     * @var \Omnyfy\Cms\Model\ResourceModel\Category\Collection
     */
    protected $_categoryCollection = null;
	
    /**
     * Article collection
     *
     * @var \Omnyfy\Cms\Model\ResourceModel\Article\Collection
     */
    protected $_articleCollection = null;
    protected $_articleCollection1 = null;
	
	/**
     * Article factory
     *
     * @var \Omnyfy\Cms\Model\CategoryFactory
     */
	 
	protected $_categorymodelFactory;
	/**
     * Article factory
     *
     * @var \Omnyfy\Cms\Model\ArticleFactory
     */
	protected $_articlemodelFactory;
	
	/** @var \Omnyfy\Cms\Helper\Data */
    protected $_dataHelper;

	protected $_filesystem ;
	protected $_imageFactory;
	
	/**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;
	
	private $_itemPerPage = 8;
    private $_pageFrame = 8;
    private $_curPage = 1;
	
    public function __construct(
        Template\Context $context,
        \Omnyfy\Cms\Model\ResourceModel\Category\CollectionFactory $categorymodelFactory,
        \Omnyfy\Cms\Model\ResourceModel\Article\CollectionFactory $articlemodelFactory,
		\Omnyfy\Cms\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
		\Magento\Framework\Stdlib\DateTime\DateTime $date,
		\Omnyfy\Cms\Helper\Data $dataHelper,
        array $data = []
    ) {

        parent::__construct($context, $data);
        $this->_categorymodelFactory = $categorymodelFactory;
        $this->_articlemodelFactory = $articlemodelFactory;
		$this->categoryFactory = $categoryFactory;
		$this->_imageFactory = $imageFactory;
		$this->_date = $date;
		$this->_dataHelper = $dataHelper;
        $this->_isScopePrivate = true;
        $this->_filesystem = $context->getFilesystem();
    }
	
    public function _getCollection(){
        $collection = $this->_articlemodelFactory->create();
        return $collection;     
    }
	
	/**
     * Retrieve prepared Category collection
     *
     * @return Omnyfy_Cms_Model_Resource_Category_Collection
     */
    public function getArticleCollection($categoryId)
    {
        #if (is_null($this->_articleCollection)) {
		$this->_articleCollection = $this->_getCollection()->addFieldToSelect('*')
								->join(
									array('category_mapping' => 'omnyfy_cms_article_category'),
									'main_table.article_id = category_mapping.article_id',
									array('category_id' => 'category_id','positioncategory' => 'category_mapping.position')
								);
		$this->_articleCollection->addFieldToFilter('category_id',$categoryId);	
		$this->_articleCollection->addFieldToFilter('is_active', '1'); 
		$this->_articleCollection->setOrder('category_mapping.position', 'ASC'); 
		$this->_articleCollection->addFieldToFilter('publish_time', ['lteq' => $this->_date->gmtDate()]);			
		#}	
        return $this->_articleCollection;
    }
	
	/**
     * Retrieve prepared Category collection
     *
     * @return Omnyfy_Cms_Model_Resource_Category_Collection
     */
    public function getArticleCollectionNew($countryId,$categoryId)
    {
        #if (is_null($this->_articleCollection)) {
		$this->_articleCollection1 = $this->_getCollection()->addFieldToSelect('*')
								->join(
									array('category_mapping' => 'omnyfy_cms_article_category'),
									'main_table.article_id = category_mapping.article_id',
									array('category_id' => 'category_id','positioncategory' => 'category_mapping.position')
								)
								->join(
									array('category_data' => 'omnyfy_cms_category'),
									'category_mapping.category_id = category_data.category_id',
									array('country_id' => 'country_id','is_learn' => 'is_learn','is_specific_country' => 'is_specific_country')
								);
		$this->_articleCollection1->addFieldToFilter('category_data.is_learn','1');
		$this->_articleCollection1->addFieldToFilter('category_data.is_specific_country','1');						
		$this->_articleCollection1->addFieldToFilter('category_mapping.category_id',$categoryId);	
		$this->_articleCollection1->addFieldToFilter('category_data.country_id', $countryId); 
		$this->_articleCollection1->addFieldToFilter('main_table.is_active', '1'); 
		#$this->_articleCollection1->setOrder('category_mapping.position', 'ASC'); 
		$this->_articleCollection1->addFieldToFilter('publish_time', ['lteq' => $this->_date->gmtDate()]);		
		$this->_articleCollection1->getSelect()->group('main_table.article_id')->order('category_data.country_id','ASC')->order('category_mapping.position ASC');		
		#}	
        return $this->_articleCollection1;
    }
    	
	public function getCollection($collection = 'null')
    {
		 //get values of current page
        $page=($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
		//get values of current limit
        $pageSize=($this->getRequest()->getParam('limit'))? $this->getRequest()->getParam('limit') : 100;

        if($collection != 'null'){
            $page = $this->getRequest()->getParam('p');
            if($page) $this->_curPage = $page;
            
            $collection->setCurPage($page);
            $collection->setPageSize($pageSize);
            return $collection;
        }
    }
	
	public function getCategory($categoryId)
	{
		return $this->categoryFactory->create()->load($categoryId);
	}
	
	public function getPositionCategory($categoryId)
	{
		return $this->_categorymodelFactory->create()->addFieldToFilter('category_id',$categoryId)->setOrder('position')->getTreeOrderedArray();
	}
	
	/**
     * Return URL for resized Events Item image
     * 
     * @param Omnyfy_Events_Model_Events $item
     * @param integer $width
     * @return string|false
     */
    public function getImageUrl($item, $width, $height)
    {
        return $this->_dataHelper->imageResize($item, $width, $height);
    } 
	
	public function getTopicTabTitle(){
		$dataKey = $this->getRequest()->getParam('data_keyword');
		if($dataKey == 'general'){
			return 'General Information';
		} else if($dataKey == 'country'){
			return 'For Specific Country';
		} else if($dataKey == 'tools'){
			return 'Tools & templates';
		} 
	}
	
	public function getTopicIdentifier(){
		return $this->getRequest()->getParam('data_keyword');
	}
	
	public function getCurrentCat()
	{
		return $this->getRequest()->getParam('id');
	}
	
	public function getProviderUrl($locationId){
        return $this->getUrl('omnyfy_vendor/index/location/id', array('id' => $locationId));
    }	
	
	public function isAnyCategory(){
		$childCategories = $this->getCategory($this->getCurrentCat())->getChildrenIds();
		$categoryTab = false;
		foreach($childCategories as $child):
			$category = null;
			$category = $this->getCategory($child);
			$articleCollection = $this->getArticleCollection($child);		
			if($category->getIsActive() && $category->getIsLearn() && $category->getIsSpecificCountry() && $articleCollection->getSize()>0){
				$categoryTab = true;
			}	
		endforeach;
		return $categoryTab;
	}
	
	public function getCountries(){
		$collection = $this->_categorymodelFactory->create()
										->addFieldToSelect('*')
										->join(
											array('country' => 'omnyfy_cms_country'),
											'main_table.country_id = country.id',
											array('country_name' => 'country_name')
										);
		$collection->addFieldToFilter('is_learn','1');
		$collection->addFieldToFilter('is_specific_country','1');
		
		$countryIds = array();
		foreach($collection as $country){
			if($country->getParentId()==$this->getRequest()->getParam('id') && !in_array($country->getCountryId(),$countryIds)){
				$countryIds[$country->getCategoryId()] = $country->getCountryId();
			}	
		}
		return $countryIds;
	}
}