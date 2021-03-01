<?php
/**
 * Copyright © 2015 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Omnyfy\Cms\Block\Learn;

use Magento\Framework\View\Element\Template;

class Popular extends Template
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
        $collection = $this->_articlemodelFactory->create();
        return $collection;     
    }
	
	/**
     * Retrieve prepared Article collection
     *
     * @return Omnyfy_Cms_Model_Resource_Article_Collection
     */
    public function getCollection()
    {
       	$this->_articleCollection = $this->_getCollection()->addFieldToSelect('*');
		$this->_articleCollection->setOrder('article_counter','desc');
		$this->_articleCollection->addFieldToFilter('is_active', '1'); 
		$this->_articleCollection->addFieldToFilter('article_counter', ['neq' => '0']);
		$this->_articleCollection->addFieldToFilter('publish_time', ['lteq' => $this->_date->gmtDate()]);
		$this->_articleCollection->setPageSize(8); 

        return $this->_articleCollection;
    }
    	
	protected function _prepareLayout()
	{
		$collection = $this->getCollection();

		parent::_prepareLayout();

		return $this;
	}
}
