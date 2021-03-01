<?php

/**
 * Events data helper
 */

namespace Omnyfy\Cms\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\User\Model\ResourceModel\User\CollectionFactory as UserCollectionFactory;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    public $timezone;
    protected $_storeManager;
    protected $_filesystem;
    protected $_imageFactory;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * Article collection
     *
     * @var \Omnyfy\Cms\Model\ResourceModel\Article\Collection
     */
    protected $_articleCollection = null;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * Article factory
     *
     * @var \Omnyfy\Cms\Model\ArticleFactory
     */
    protected $_articlemodelFactory;

    /**
     * Article factory
     *
     * @var \Omnyfy\Cms\Model\ArticleFactory
     */
    protected $_categoryFactory;

    const XML_PATH = 'mfcms/';
    const ROOT_CATEGORY_ID = 'root_category/root_category_id';

    public function __construct(
    \Magento\Framework\App\Helper\Context $context, \Magento\Framework\Filesystem $filesystem, \Omnyfy\Cms\Model\ResourceModel\Article\CollectionFactory $articlemodelFactory, \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryFactory, \Magento\Framework\Image\AdapterFactory $imageFactory, \Magento\Cms\Model\Template\FilterProvider $filterProvider, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Stdlib\DateTime\DateTime $date, \Magento\Customer\Model\Session $customerSession, \Omnyfy\Cms\Model\ResourceModel\ToolTemplate\CollectionFactory $toolTemplateModelFactory, \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    ) {
        $this->_imageFactory = $imageFactory;
        $this->_filterProvider = $filterProvider;
        $this->_filesystem = $filesystem;
        $this->customerSession = $customerSession;
        $this->_articlemodelFactory = $articlemodelFactory;
        $this->_toolTemplateModelFactory = $toolTemplateModelFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_date = $date;
        $this->timezone = $timezone;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    // pass imagename, width and height
    public function imageResize($image, $width = null, $height = null) {
        $absolutePath = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath('/') . $image;
        if (!$image) {
            return false;
        }
        $imageResized = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath('resized/' . $width . '/') . $image;
        //create image factory...
        $imageResize = $this->_imageFactory->create();
        $imageResize->open($absolutePath);
        $imageResize->constrainOnly(TRUE);
        $imageResize->keepTransparency(TRUE);
        $imageResize->keepFrame(FALSE);
        $imageResize->resize($width, $height);
        //destination folder                
        $destination = $imageResized;
        //save image      
        $imageResize->save($destination);

        $resizedURL = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'resized/' . $width . '/' . $image;
        return $resizedURL;
    }

    public function getDateFormat($date) {
        return $this->timezone->date(new \DateTime($date))->format('d M Y');
    }

    public function getArticleList($articleKeyword, $userTypeId) {
        $result = [];
        $articleCollection = $this->_articlemodelFactory->create()->addFieldToSelect('*')->join(
                array('user_type' => 'omnyfy_cms_article_user_type'), 'main_table.article_id = user_type.article_id', array('user_type_id' => 'user_type.user_type_id')
        );
        $articleCollection->addFieldToFilter('title', ['like' => '%' . $articleKeyword . '%']);
        $articleCollection->addFieldToFilter('user_type_id', $userTypeId);
        $articleCollection->addFieldToFilter('is_active', '1');
        $articleCollection->addFieldToFilter('publish_time', ['lteq' => $this->_date->gmtDate()]);

        $output = '<ul class="list-unstyled">';
        foreach ($articleCollection as $article) {
            $output .= '<li><a href="' . $article->getArticleUrl() . '">' . $article->getTitle() . '</a></li>';
        }
        $output .= '</ul>';
        return $output;
    }

    public function getServiceProvider($articleId) {
        #if (is_null($this->_articleCollection)) {
        $_articleCollection = $this->_articlemodelFactory->create()->addFieldToSelect('meta_title')
                ->join(
                        array('location_mapping' => 'omnyfy_cms_article_vendor'), 'main_table.article_id = location_mapping.article_id', array('vendor_id' => 'vendor_id')
                )
                ->join(
					array('location_data' => 'omnyfy_vendor_location_flat_1'),
					'location_mapping.vendor_id = location_data.entity_id',
					array('promotion_messages_two' => 'promotion_messages_two','promotion_messages_one' => 'promotion_messages_one','promotion_messages_three' => 'promotion_messages_three','location_name' => 'location_name','location_id' => 'location_data.entity_id' ,'vendor_id' => 'location_data.vendor_id')
				)
                ->join(
                array('vendor_data' => 'omnyfy_vendor_vendor_entity'), 'location_data.vendor_id = vendor_data.entity_id', array('logo' => 'logo', 'name' => 'name')
        );
        $_articleCollection->addFieldToFilter('main_table.article_id', $articleId)->getSelect();
        #}	
        return $_articleCollection;
    }

    public function getBusinessTypes($articleId) {
        #$categoryFactory = $obj->create('Magento\Catalog\Model\ResourceModel\Category\CollectionFactory');
        $categories = $this->_categoryFactory->create();
        $joinConditions = 'e.entity_id = omnyfy_cms_article_service_category.catelog_category_id';
        $categories->addAttributeToSelect('*');
        $categories->getSelect()->join(
                        ['omnyfy_cms_article_service_category'], $joinConditions, []
                )->columns("omnyfy_cms_article_service_category.article_id")
                ->where("omnyfy_cms_article_service_category.article_id=" . $articleId);
        return $categories;
    }

    /**
     * Retrieve post content
     *
     * @return string
     */
    public function getContent($articleContent) {
        $cotent = $this->_filterProvider->getPageFilter()->filter(
                $articleContent
        );
        return $cotent;
    }

    public function getConfigValue($field, $storeId = null) {
        return $this->scopeConfig->getValue(
                        $field, ScopeInterface::SCOPE_STORE, $storeId
        );
    }

    public function getGeneralConfig($code, $storeId = null) {
        return $this->getConfigValue(self::XML_PATH . $code, $storeId);
    }

    public function getConfig($config_path) {
        return $this->scopeConfig->getValue(
                        $config_path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
	
	public function getToolTemplate($articleId){
		$collection = $this->_toolTemplateModelFactory->create()->addFieldToSelect('*');
		$collection->join(
                        array('article_mapping' => 'omnyfy_cms_article_tool_template'), 'main_table.id = article_mapping.tool_template_id', array('article_id' => 'article_id')
                );
		$collection->addFieldToFilter('article_id', $articleId); 
		$collection->addFieldToFilter('status', '1'); 
		
        return $collection;
	}
	
	public function getToolTemplateUrl($url)
    {
        if (empty($url)) {
            return false;
        }
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $url;
    }
	
	public function isLoggedIn() {
        return $this->customerSession->isLoggedIn();
    }
}
