<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 7/8/17
 * Time: 1:56 PM
 */
namespace Omnyfy\Vendor\Block\Vendor\View;

use Magento\Framework\View\Element\Template;

class Policy extends \Magento\Framework\View\Element\Template
{
    protected $_coreRegistry;

    protected $vendorFactory;

    protected $vendorCollectionFactory;

    protected $vendorMetadataService;

    protected $searchCriteriaBuilder;

    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        Template\Context $context,
        \Omnyfy\Vendor\Model\VendorFactory $vendorFactory,
        \Omnyfy\Vendor\Model\Resource\Vendor\CollectionFactory $vendorCollectionFactory,
        \Omnyfy\Vendor\Api\VendorAttributeRepositoryInterface $vendorMetadataService,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        array $data = [])
    {
        $this->_coreRegistry = $coreRegistry;
        $this->vendorFactory = $vendorFactory;
        $this->vendorCollectionFactory = $vendorCollectionFactory;
        $this->vendorMetadataService = $vendorMetadataService;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        parent::__construct($context, $data);
    }

    public function getPolicyContents()
    {
        $vendor = $this->_coreRegistry->registry('vendor');

        if (empty($vendor)) {
            return false;
        }

        return $vendor->getPolicyContents();
    }

    public function getVendor()
    {
        $vendor = $this->_coreRegistry->registry('vendor');

        if (empty($vendor)) {
            return false;
        }

        return $vendor;
    }

    public function loadVendorAttributes()
    {
        $vendor = $this->_coreRegistry->registry('vendor');

        if (empty($vendor)) {
            return false;
        }

        $collection = $this->vendorCollectionFactory->create()
            ->addFieldToFilter('entity_id', $vendor->getEntityId());

        // @TODO - only get the policies

        foreach ($this->vendorMetadataService->getList($this->searchCriteriaBuilder->create())->getItems() as $metadata) {
            if ($metadata['is_visible_on_front']) {
                $collection->addFieldToSelect($metadata->getAttributeCode());
            }
        }

        return $collection->getFirstItem();
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
}