<?php
/**
 * Project: CMS M2.
 * User: abhay
 * Date: 3/05/18
 * Time: 11:30 AM
 */
namespace Omnyfy\Cms\Block;

use Magento\Framework\View\Element\Template;

class ToolTemplate extends \Magento\Framework\View\Element\Template
{
    protected $coreRegistry;
    protected $_toolTemplateModelFactory;
    protected $_date;
	
    public function __construct(
        Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Omnyfy\Cms\Model\ResourceModel\ToolTemplate\CollectionFactory $toolTemplateModelFactory,
        array $data = [])
    {
        $this->coreRegistry = $coreRegistry;
		$this->_toolTemplateModelFactory = $toolTemplateModelFactory;
        parent::__construct($context, $data);
    }
	
	/**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {

		$this->pageConfig->addBodyClass('cms-tooltemplate-view');
		$this->pageConfig->getTitle()->set('Tools and Templates');
        return parent::_prepareLayout();
    }
	
	public function getToolTemplate(){
		return $this->coreRegistry->registry('current_tooltemplate');
	}

    public function getLogoUrl($vendorLogo)
    {
        if (empty($vendorLogo)) {
            return false;
        }
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $vendorLogo;
    }
	
	public function isToolTemplate(){
		$collection = $this->_toolTemplateModelFactory->create()->addFieldToSelect('*');
		$collection->setOrder('position','asc');
		$collection->setOrder('id','desc');
		$collection->addFieldToFilter('status', '1'); 
		
		if($collection->getSize()>0){
			return false;
		} else{
			return true;
		}
	}
}