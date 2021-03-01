<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 7/8/17
 * Time: 10:38 AM
 */
namespace Omnyfy\Vendor\Block\Vendor;

use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;

class View extends \Magento\Framework\View\Element\Template
{
    protected $_coreRegistry;

    protected $vendorRepository;

    protected $_blockFilter;

    protected $_helper;

    protected $vendorFactory;

    protected $vendorCollectionFactory;

    protected $vendorMetadataService;

    protected $searchCriteriaBuilder;

    public function __construct(
        \Omnyfy\Vendor\Api\VendorRepositoryInterface $vendorRepository,
        \Magento\Framework\Registry $coreRegistry,
	\Magento\Cms\Model\Template\FilterProvider $filterProvider,
        Template\Context $context,
        \Magento\Cms\Model\Template\Filter $filter,
        \Omnyfy\Vendor\Helper\Media $helper,
        \Omnyfy\Vendor\Model\VendorFactory $vendorFactory,
        \Omnyfy\Vendor\Model\Resource\Vendor\CollectionFactory $vendorCollectionFactory,
        \Omnyfy\Vendor\Api\VendorAttributeRepositoryInterface $vendorMetadataService,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,

        array $data = [])
    {
        $this->vendorRepository = $vendorRepository;
	   $this->_filterProvider = $filterProvider;
        $this->_blockFilter = $filter;
        $this->_coreRegistry = $coreRegistry;
        $this->_helper = $helper;
        $this->vendorFactory = $vendorFactory;
        $this->vendorCollectionFactory = $vendorCollectionFactory;
        $this->vendorMetadataService = $vendorMetadataService;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        parent::__construct($context, $data);
    }

    public function getVendor()
    {
        $vendorId = (int)$this->getRequest()->getParam('id');
        if ($this->getVendorId() != $vendorId) {
            $this->setVendorId($vendorId);
        }
        if (!$this->_coreRegistry->registry('vendor') && $this->getVendorId()) {
            $vendor = $this->vendorRepository->getById($this->getVendorId());
            $this->_coreRegistry->register('vendor', $vendor);
        }
        return $this->_coreRegistry->registry('vendor');
    }

    public function getDescription()
    {
        return $this->_blockFilter->filter($this->getVendor()->getDescription());
    }

    public function getProductsCount()
    {
        //TODO: retrieve products count by vendor_id and website_id
        return 40;
    }

    public function getBannerUrl()
    {
        if (!$this->hasData('banner_url')) {
            $bannerUrl = $this->_helper->getVendorBannerUrl($this->getVendor());

            if (empty($bannerUrl)) {
                return false;
            }

            $this->setData('banner_url', $bannerUrl);
        }
        return $this->getData('banner_url');
    }

    public function getLogoUrl()
    {
        if (!$this->hasData('logo_url')) {
            $logo = $this->_helper->getVendorLogoUrl($this->getVendor());

            if (empty($logo)) {
                return false;
            }

            $this->setData('logo_url', $logo);
        }
        return $this->getData('logo_url');
    }

    public function getMerchantStoreUrl()
    {
        $vendor = $this->getVendor();
        $vendorId = $vendor->getId();
        return $this->getUrl('*/*/store', ['id' => $vendorId]);
    }

    public function getVendorAddress()
    {
        return  $this->getVendor()->getAddress();
    }

    public function getRatingPercent()
    {
        return 80;
    }

    public function getScore()
    {
        return '4.0';
    }
	
	/**
     * Retrieve description content
     *
     * @return string
     */
	public function getContent($description){
		return $this->_filterProvider->getPageFilter()->filter($description);
	}

    public function loadVendorAttributes()
    {
        $vendorId = (int)$this->getRequest()->getParam('id');
        $vendor = $this->vendorFactory->create();
        if ($vendorId) {
            try {
                $vendor->load($vendorId);
            }
            catch (\Exception $e) {
                $this->_logger->critical($e);
            }
        }

        $collection = $this->vendorCollectionFactory->create()
            ->addFieldToFilter('entity_id', $vendor->getEntityId());

        foreach ($this->vendorMetadataService->getList($this->searchCriteriaBuilder->create())->getItems() as $metadata) {
            if ($metadata['is_visible_on_front']) {
                $collection->addFieldToSelect($metadata->getAttributeCode());
            }
        }
        $collection->getFirstItem();
        return $collection->getFirstItem();
    }

    public function getColumnSplitValue($attributes)
    {
        // @TODO - put into configuration value
        $excludeFields = ['status', 'entity_id', 'name', 'email', 'attribute_set_id', 'type_id', 'description', 'shipping_policy', 'return_policy', 'payment_policy', 'marketing_policy'];
        $getColumnSplitValue = 0;
        foreach ($attributes as $attributeKey => $attributeValue) {
            if (!in_array($attributeKey, $excludeFields)) {
                if ($this->shouldDisplayAttribute($attributeKey)) {
                    $getColumnSplitValue++;
                }
            }
        }

        return $getColumnSplitValue;
    }

    public function getMultiSelectValues($attribute)
    {
        $vendor = $this->getVendor();

        return explode(",", $vendor->getResource()->getAttribute($attribute)->getFrontEnd()->getValue($vendor));
    }

    public function shouldDisplayAttribute($attribute)
    {
        if ($this->getVendor()->getResource()->getAttribute($attribute)) {
            if ($this->getVendor()->getResource()->getAttribute($attribute)->getFrontend()->getValue($this->getVendor())) {
                return true;
            }
        }

        return false;
    }

    public function getAttributeLabel($attribute)
    {
        if ($this->getVendor()->getResource()->getAttribute($attribute)) {
            $attributeData = $this->getVendor()->getResource()->getAttribute($attribute)->getData();

            if (isset($attributeData['frontend_label'])) {
                return $attributeData['frontend_label'];
            }
        }
        else {
            return '';
        }
    }

    public function getAttributeValue($attribute)
    {
        $vendor = $this->getVendor();

        return $vendor->getResource()->getAttribute($attribute)->getFrontEnd()->getValue($vendor);
    }

    public function isAttributeMultiselect($attribute)
    {
        $vendor = $this->getVendor();

        $attributeInfo = $vendor->getResource()->getAttribute($attribute);

        if (!empty($attributeInfo)) {
            $attributeInfo = $attributeInfo->getData();

            if(isset($attributeInfo['frontend_input'])) {
                if ($attributeInfo['frontend_input'] == 'multiselect') {
                    return true;
                }
            }
        }

        return false;
    }
}
