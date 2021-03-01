<?php
/**
 * Copyright © 2015 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Omnyfy\Cms\Block\Learn;

use Magento\Framework\View\Element\Template;

class LearnTopic extends Template
{
	/**
     * Cms collection
     *
     * @var \Omnyfy\Cms\Model\ResourceModel\Category\Collection
     */
    protected $_categoryCollection = null;
	
	/**
     * Cms collection
     *
     * @var \Omnyfy\Cms\Model\ResourceModel\Article\Collection
     */
    protected $_articleCollection = null;
	
	/**
     * Cms factory
     *
     * @var \Omnyfy\Cms\Model\CategoryFactory
     */
	protected $_categorymodelFactory;
	/**
     * Cms factory
     *
     * @var \Omnyfy\Cms\Model\CategoryFactory
     */
	protected $_articlemodelFactory;
	
	/** @var \Omnyfy\Cms\Helper\Data */
    protected $_dataHelper;
	
	/**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;
	
	protected $_filesystem ;
	protected $_imageFactory;
	protected $_cmsPage;
	
    public function __construct(
        Template\Context $context,
        \Omnyfy\Cms\Model\ResourceModel\Category\CollectionFactory $categorymodelFactory,
        \Omnyfy\Cms\Model\ResourceModel\Article\CollectionFactory $articlemodelFactory,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
		\Magento\Framework\Stdlib\DateTime\DateTime $date,
		\Omnyfy\Cms\Helper\Data $dataHelper,
		\Magento\Cms\Model\Page $cmsPage,
        array $data = []
    ) {

        parent::__construct($context, $data);
		$this->_cmsPage = $cmsPage;
        $this->_categorymodelFactory = $categorymodelFactory;
        $this->_articlemodelFactory = $articlemodelFactory;
		$this->_imageFactory = $imageFactory;
		$this->_filesystem = $context->getFilesystem();
		$this->_date = $date;
		$this->_dataHelper = $dataHelper;
        $this->_isScopePrivate = true;
    }
	
	 public function _getCollection(){
        $collection = $this->_categorymodelFactory->create();
        return $collection;     
    }
	
	/**
     * Retrieve prepared Article collection
     *
     * @return Omnyfy_Cms_Model_Resource_Category_Collection
     */
    public function getCollection()
    {
		$userType = $this->getData('user_type');
		 //get values of current page
        $page=($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
		//get values of current limit
        $pageSize=($this->getRequest()->getParam('limit'))? $this->getRequest()->getParam('limit') : 100;
		
		$topicId = $this->_dataHelper->getConfig('mfcms/topic_category/topic_category_id');
		
       	$this->_articleCollection = $this->_getCollection()->addFieldToSelect('*')
										->join(
											array('article_category_mapping' => 'omnyfy_cms_article_category'),
											'main_table.category_id = article_category_mapping.category_id',
											array('category_id' => 'article_category_mapping.category_id')
										)->join(
											array('user_type' => 'omnyfy_cms_article_user_type'),
											'article_category_mapping.article_id = user_type.article_id',
											array('user_type_id' => 'user_type.user_type_id')
										)->join(
											array('user_type_data' => 'omnyfy_cms_user_type'),
											'user_type_data.id = user_type.user_type_id',
											array('user_is_active' => 'user_type_data.status')
										)->join(
											array('artcle_data' => 'omnyfy_cms_article'),
											'article_category_mapping.article_id = artcle_data.article_id',
											array('article_is_active' => 'artcle_data.is_active')
										);
		$this->_articleCollection->addFieldToFilter('path', $topicId);
		$this->_articleCollection->addFieldToFilter('is_learn', '1');
		if($userType){
			$this->_articleCollection->addFieldToFilter('user_type_id',$userType);
		}	
		$this->_articleCollection->getSelect()->group('article_category_mapping.category_id');
		$this->_articleCollection->setOrder('position','asc');
		$this->_articleCollection->addFieldToFilter('main_table.is_active', '1'); 
		$this->_articleCollection->addFieldToFilter('status', '1'); 
		$this->_articleCollection->addFieldToFilter('artcle_data.is_active', '1'); 
		$this->_articleCollection->addFieldToFilter('publish_time', ['lteq' => $this->_date->gmtDate()]);
		/* $this->_articleCollection->addFieldToFilter('path', $topicId);
		$this->_articleCollection->addFieldToFilter('is_learn', '1'); */
		/*$this->_categoryCollection->addFieldToFilter('end_date', ['gteq' => $this->timezone->date()->format('Y-m-d')]);
		$this->_categoryCollection->setOrder('start_date','asc'); */
		$this->_articleCollection->setCurPage($page);
		$this->_articleCollection->setPageSize($pageSize);
        return $this->_articleCollection;
    }
    	
	/**
     * Return URL to item's view page
     *
     * @param Omnyfy_Cms_Model_Article $articleItem
     * @return string
     */
	
	/**
     * Return URL for resized Article Item image
     *
     * @param Omnyfy_Cms_Model_Article $item
     * @param integer $width
     * @return string|false
     */
    public function getImageUrl($item, $width=null, $height=null)
    {
        return $this->_dataHelper->imageResize($item, $width, $height);
    } 
	
	protected function _prepareLayout()
	{
		$collection = $this->getCollection();

		parent::_prepareLayout();
		/* if ($collection) {
			// create pager block for collection
			$pager = $this->getLayout()->createBlock('Magento\Theme\Block\Html\Pager','events.pager');
			// assign collection to pager
			$pager->setLimit(9)->setCollection($collection);
			$pager->setAvailableLimit([9=>9,18=>18,27=>27,81=>81]);
			$this->setChild('pager', $pager);// set pager block in layout
		} */
		return $this;
	}

	/**
	 * @return string
	 */
	// method for get pager html
	public function getPagerHtml()
	{
		return $this->getChildHtml('pager');
	}
	
	public function getArticleCount($categoryId,$userTypeId){
		$articleCount = $this->_articlemodelFactory->create()->addFieldToSelect('*')
										->join(
											array('category_mapping' => 'omnyfy_cms_article_category'),
											'main_table.article_id = category_mapping.article_id',
											array('category_id' => 'category_id')
										)
										->join(
											array('category_data' => 'omnyfy_cms_category'),
											'category_mapping.category_id = category_data.category_id',
											array('category_is_active' => 'category_data.is_active')
										)
										->join(
											array('user_type' => 'omnyfy_cms_article_user_type'),
											'main_table.article_id = user_type.article_id',
											array('user_type_id' => 'user_type_id')
										)
										->join(
											array('user_type_data' => 'omnyfy_cms_user_type'),
											'id = user_type.user_type_id',
											array('status' => 'status')
										);
		$articleCount->addFieldToFilter('category_mapping.category_id',$categoryId);								
		$articleCount->addFieldToFilter('user_type_id',$userTypeId);
		$articleCount->addFieldToFilter('publish_time', ['lteq' => $this->_date->gmtDate()]);
		$articleCount->addFieldToFilter('main_table.is_active', '1');								
		$articleCount->addFieldToFilter('category_data.is_active', '1');								
		$articleCount->addFieldToFilter('user_type_data.status', '1');								
										
		if(count($articleCount)>1){
			return count($articleCount).' articles';
		}else if(count($articleCount)=='1'){
			return count($articleCount).' article';
		}else{ 
			return '0 article';
		}						
	}
	
	/* public function getRelatedArticles()
    {
       	$this->_articleCollection = $this->_getCollection()
										->addFieldToSelect('*')
										->join(
											array('category_mapping' => 'omnyfy_cms_article_category'),
											'main_table.article_id = category_mapping.article_id',
											array('category_id' => 'category_id')
										)
										->join(
											array('category_data' => 'omnyfy_cms_category'),
											'category_mapping.category_id = category_data.category_id',
											array('is_learn' => 'is_learn')
										);
		$this->_articleCollection->addFieldToFilter('is_learn','1');
		$this->_articleCollection->setCurPage($page);
		$this->_articleCollection->setPageSize($pageSize);
        return $this->_articleCollection;
    } */
	
	public function getCurrentPageId(){
		return $this->_cmsPage->getId();
	}
}
