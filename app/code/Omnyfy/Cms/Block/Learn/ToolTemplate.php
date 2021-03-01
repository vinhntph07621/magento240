<?php
namespace Omnyfy\Cms\Block\Learn;
use Magento\Framework\View\Element\Template;
class ToolTemplate extends Template
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
     * @var \Omnyfy\Cms\Model\ToolTemplateFactory
     */
	protected $_toolTemplateModelFactory;
	
	/** @var \Omnyfy\Cms\Helper\Data */
    protected $_dataHelper;

	protected $_filesystem ;
	protected $_imageFactory;
	
    public function __construct(
        Template\Context $context,
        \Omnyfy\Cms\Model\ResourceModel\Category\CollectionFactory $categorymodelFactory,
        \Omnyfy\Cms\Model\ResourceModel\ToolTemplate\CollectionFactory $toolTemplateModelFactory,
		\Omnyfy\Cms\Model\CategoryFactory $categoryFactory,
		\Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
		\Magento\Framework\Stdlib\DateTime\DateTime $date,
		\Omnyfy\Cms\Helper\Data $dataHelper,
        array $data = []
    ) {

        parent::__construct($context, $data);
        $this->_categorymodelFactory = $categorymodelFactory;
        $this->_toolTemplateModelFactory = $toolTemplateModelFactory;
		$this->customerSession = $customerSession;
		$this->_filesystem = $context->getFilesystem();
		$this->_imageFactory = $imageFactory;
		$this->_date = $date;
		$this->_dataHelper = $dataHelper;
        $this->_isScopePrivate = true;
    }
	
    public function _getCollection(){
        $collection = $this->_toolTemplateModelFactory->create();
        return $collection;     
    }
	
    public function getCollection($type)
    {
		$categoryId = $this->getRequest()->getParam('id');
		$collection = $this->_getCollection()->addFieldToSelect('*')
								->join(
									array('tool_article_mapping' => 'omnyfy_cms_article_tool_template'),
									'main_table.id = tool_article_mapping.tool_template_id',
									array('article_id' => 'article_id')
								)
								->join(
									array('article_data' => 'omnyfy_cms_article'),
									'tool_article_mapping.article_id = article_data.article_id',
									array('is_active' => 'is_active','publish_time' => 'publish_time')
								)
								->join(
									array('category_mapping' => 'omnyfy_cms_article_category'),
									'tool_article_mapping.article_id = category_mapping.article_id',
									array('category_id' => 'category_id')
								);
		$collection->addFieldToFilter('category_id',$categoryId);	
		$collection->addFieldToFilter('type',$type);	
		$collection->addFieldToFilter('status','1');	
		$collection->addFieldToFilter('is_active', '1');
		$collection->addFieldToFilter('publish_time', ['lteq' => $this->_date->gmtDate()]);
		$collection->getSelect()->group('tool_template_id');
		
        return $collection;
    }
	
	public function getLogoUrl($templateLogo)
    {
        if (empty($templateLogo)) {
            return false;
        }
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $templateLogo;
    }
	
    public function getTemplateUrl($template)
    {
        if (empty($template)) {
            return false;
        }
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $template;
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
	
    public function getToolUrl($tool)
    {
        if (empty($tool)) {
            return false;
        }
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $tool;
    }
}