<?php
namespace Omnyfy\Cms\Block\Learn;
use Magento\Framework\View\Element\Template;
class TopicSearch extends Template
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
        \Magento\Framework\Image\AdapterFactory $imageFactory,
		\Magento\Framework\Stdlib\DateTime\DateTime $date,
		\Omnyfy\Cms\Helper\Data $dataHelper,
        array $data = []
    ) {

        parent::__construct($context, $data);
        $this->_categorymodelFactory = $categorymodelFactory;
        $this->_articlemodelFactory = $articlemodelFactory;
		$this->_filesystem = $context->getFilesystem();
		$this->_imageFactory = $imageFactory;
		$this->_date = $date;
		$this->_dataHelper = $dataHelper;
        $this->_isScopePrivate = true;
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
    public function getArticleCollection()
    {
		$categoryId = $this->getRequest()->getParam('category_id');
        if (is_null($this->_articleCollection)) {
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
		}	
        return $this->_articleCollection;
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
}