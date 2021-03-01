<?php
/**
 * Created by PhpStorm.
 * User: Sanjaya-offline
 * Date: 7/10/2019
 * Time: 11:24 AM
 */

namespace Omnyfy\VendorFeatured\Model\Vendor\Source;


class Tags implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Omnyfy\VendorFeatured\Model\ResourceModel\VendorTag\CollectionFactory
     */
    protected $_vendorTagCollectionFactory;

    /**
     * Tags constructor.
     * @param \Omnyfy\VendorFeatured\Model\ResourceModel\VendorTag\CollectionFactory $vendorTagCollectionFactory
     */
    public function __construct(
        \Omnyfy\VendorFeatured\Model\ResourceModel\VendorTag\CollectionFactory $vendorTagCollectionFactory
    )
    {
        $this->_vendorTagCollectionFactory = $vendorTagCollectionFactory;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = $this->getOptionArray();
        $options = array();
        
        if ($availableOptions && count($availableOptions) > 0) {
            foreach ($availableOptions as $key => $value) {
                $options[] = [
                    'label' => $value,
                    'value' => $key,
                ];
            }
        }
        return $options;
    }

    /**
     * @return array
     */
    public function getOptionArray()
    {
        $vendorArray = [];
        $vendorArray[0] = 'All';
        /** @var \Omnyfy\VendorFeatured\Model\ResourceModel\VendorTag\Collection $vendorCollection */
        $vendorTagCollection = $this->_vendorTagCollectionFactory->create();
        $vendorTagCollection->load();
        $labelArray = array();

        if($vendorTagCollection->count() > 0) {
            /** @var \Omnyfy\Vendor\Model\Vendor $vendor */
            foreach ($vendorTagCollection as $vendor){
                $labelArray[$vendor->getId()] = $vendor->getName();
            }
        }
        return $labelArray;
    }
}