<?php
/**
 * Copyright © 2016 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Omnyfy\Cms\Block\Category;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;

/**
 * Cms category view
 */
class View extends \Omnyfy\Cms\Block\Article\ArticleList
{
	
	/**
     * @var \Omnyfy\Cms\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * Initialize dependencies.
     *
     * @param \Omnyfy\Cms\Model\CategoryFactory $categoryFactory
     * @param void
     */
	 public function __construct(
        \Omnyfy\Cms\Model\ResourceModel\Category\CollectionFactory $categoryFactory,
		\Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
		\Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Omnyfy\Cms\Model\ResourceModel\Article\CollectionFactory $articleCollectionFactory,
        \Omnyfy\Cms\Model\Url $url,
        array $data = []
    ) {
        parent::__construct($context, $coreRegistry, $filterProvider, $articleCollectionFactory, $url, $data);
		$this->_coreRegistry = $coreRegistry;
        $this->_filterProvider = $filterProvider;
        $this->_articleCollectionFactory = $articleCollectionFactory;
		$this->_date = $date;
        $this->_url = $url;
        $this->categoryFactory = $categoryFactory;
        $this->_isScopePrivate = true;
    }
	
    /**
     * Prepare articles collection
     *
     * @return void
     */
    protected function _prepareArticleCollection()
    {
        parent::_prepareArticleCollection();
        if ($category = $this->getCategory()) {
           // $categories = $category->getChildrenIds();
            $categories[] = $category->getId();
            $this->_articleCollection->addCategoryFilter($categories);
        }
    }

    /**
     * Retrieve category instance
     *
     * @return \Omnyfy\Cms\Model\Category
     */
    public function getCategory()
    {
        return $this->_coreRegistry->registry('current_cms_category');
    }

    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $category = $this->getCategory();
        if ($category) {
            $this->_addBreadcrumbs($category);
            $this->pageConfig->addBodyClass('cms-category-' . $category->getIdentifier());
            $this->pageConfig->getTitle()->set($category->getMetaTitle());
            $this->pageConfig->setKeywords($category->getMetaKeywords());
            $this->pageConfig->setDescription($category->getMetaDescription());
            $this->pageConfig->addRemotePageAsset(
                $category->getCategoryUrl(),
                'canonical',
                ['attributes' => ['rel' => 'canonical']]
            );
        }

        return parent::_prepareLayout();
    }

    /**
     * Prepare breadcrumbs
     *
     * @param \Omnyfy\Cms\Model\Category $category
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function _addBreadcrumbs($category)
    {
        if ($this->_scopeConfig->getValue('web/default/show_cms_breadcrumbs', ScopeInterface::SCOPE_STORE)
            && ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs'))
        ) {
            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
            );

             $breadcrumbsBlock->addCrumb(
                'cms',
                [
                    'label' => $this->_scopeConfig->getValue('mfcms/index_page/title', ScopeInterface::SCOPE_STORE),
                    'title' => __('Go to Cms Home Page'),
                    'link' => $this->_url->getBaseUrl()
                ]
            );

            $_category = $category;
            $parentCategories = [];
            while ($parentCategory = $_category->getParentCategory()) {
                $parentCategories[] = $_category = $parentCategory;
            }

            for ($i = count($parentCategories) - 1; $i >= 0; $i--) {
                $_category = $parentCategories[$i];
                $breadcrumbsBlock->addCrumb('cms_parent_category_'.$_category->getId(), [
                    'label' => $_category->getTitle(),
                    'title' => $_category->getTitle()
                    //'link'  => $_category->getCategoryUrl()
                ]);
            }

            $breadcrumbsBlock->addCrumb('cms_category',[
                'label' => $category->getTitle(),
                'title' => $category->getTitle()
            ]);
        }
    }
	
	public function getChildIds(){
		$category = $this->getCategory();
		$categories = $category->getChildrenIds();
		
		return $categories;
	}

	public function getChildCategory(){
		$collection = $this->categoryFactory->create()
										->addFieldToSelect('*')
										->join(
											array('country' => 'omnyfy_cms_country'),
											'main_table.country_id = country.id',
											array('country_name' => 'country_name')
										);
		$collection->addFieldToFilter('is_learn','1');
		$collection->addFieldToFilter('is_specific_country','1');
		$collection->getSelect()->order('country.country_name','ASC');
		#$collection->getSelect()->group('main_table.country_id');
		
		$countryIds = array();
		foreach($collection as $country){
			$article = $this->getArticleCollections($country->getCategoryId());
			if($country->getParentId()==$this->getRequest()->getParam('id') && !in_array($country->getCountryId(),$countryIds) && $article->getSize()>0){
				$countryIds[$country->getCountryId()] = $country->getCountryName();
			}	
		}
		return $countryIds;	
	}	
	
	/**
     * Retrieve prepared Category collection
     *
     * @return Omnyfy_Cms_Model_Resource_Category_Collection
     */
    public function getArticleCollections($categoryId)
    {
		$articleCollection = $this->_articleCollectionFactory->create()->addFieldToSelect('*')
								->join(
									array('category_mapping' => 'omnyfy_cms_article_category'),
									'main_table.article_id = category_mapping.article_id',
									array('category_id' => 'category_id','positioncategory' => 'category_mapping.position')
								);
		$articleCollection->addFieldToFilter('category_id',$categoryId);	
		$articleCollection->addFieldToFilter('is_active', '1'); 
		$articleCollection->setOrder('category_mapping.position', 'ASC'); 
		$articleCollection->addFieldToFilter('publish_time', ['lteq' => $this->_date->gmtDate()]);			
		
        return $articleCollection;
    }
	
	public function getProviderUrl($locationId){
        return $this->getUrl('omnyfy_vendor/index/location/id', array('id' => $locationId));
    }	
	
	/**
     * Retrieve prepared Category collection
     *
     * @return Omnyfy_Cms_Model_Resource_Category_Collection
     */
    public function getArticleUsertypeCollection($categoryId)
    {
		$collection = $this->_articleCollectionFactory->create()->addFieldToSelect('*')
								->join(
									array('category_mapping' => 'omnyfy_cms_article_category'),
									'main_table.article_id = category_mapping.article_id',
									array('category_id' => 'category_id','positioncategory' => 'category_mapping.position')
								)
								->join(
									array('user_type' => 'omnyfy_cms_article_user_type'),
									'main_table.article_id = user_type.article_id',
									array('user_type_id' => 'user_type_id')
								);
		$collection->addFieldToFilter('category_id',$categoryId);	
		$collection->addFieldToFilter('user_type_id',$this->getRequest()->getParam('usertype'));	
		$collection->addFieldToFilter('is_active', '1'); 
		$collection->setOrder('category_mapping.position', 'ASC'); 
		$collection->addFieldToFilter('publish_time', ['lteq' => $this->_date->gmtDate()]);		
		
        return $collection;
    }
}
