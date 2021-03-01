<?php
/**
 * Project: CMS M2.
 * User: abhay
 * Date: 1/06/18
 * Time: 1:30 PM
 */
namespace Omnyfy\Cms\Block\ToolTemplate;

use Magento\Framework\View\Element\Template;

class Templates extends \Magento\Framework\View\Element\Template
{
    protected $coreRegistry;
    protected $_toolTemplateModelFactory;
    protected $_date;
	
    public function __construct(
        Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
		\Magento\Customer\Model\Session $customerSession,
        \Omnyfy\Cms\Model\ResourceModel\ToolTemplate\CollectionFactory $toolTemplateModelFactory,
        array $data = [])
    {
        $this->coreRegistry = $coreRegistry;
		$this->customerSession = $customerSession;
		$this->_toolTemplateModelFactory = $toolTemplateModelFactory;
        parent::__construct($context, $data);
    }
	
	public function _getCollection(){
        $collection = $this->_toolTemplateModelFactory->create();
        return $collection;     
    }
	
	public function getCollection(){
		$collection = $this->_getCollection()->addFieldToSelect('*');
		$collection->addFieldToFilter('type', '2');
		
		$collection->setOrder('position','asc');
		$collection->setOrder('id','desc');
		$collection->addFieldToFilter('status', '1'); 
		
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
}