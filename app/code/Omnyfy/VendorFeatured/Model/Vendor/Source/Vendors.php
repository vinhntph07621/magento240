<?php
/**
 * Created by PhpStorm.
 * User: Sanjaya-offline
 * Date: 7/10/2019
 * Time: 11:24 AM
 */

namespace Omnyfy\VendorFeatured\Model\Vendor\Source;


class Vendors implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Omnyfy\Vendor\Model\Resource\Vendor\CollectionFactory
     */
    protected $_vendorCollectionFactory;

    /**
     * Vendors constructor.
     * @param \Omnyfy\Vendor\Model\Resource\Vendor\CollectionFactory $vendorCollectionFactory
     */
    public function __construct(
        \Omnyfy\Vendor\Model\Resource\Vendor\CollectionFactory $vendorCollectionFactory
    )
    {
        $this->_vendorCollectionFactory = $vendorCollectionFactory;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = [
            'label' => "Select a vendor",
            'value' => null,
        ];
        $availableOptions = $this->getOptionArray();
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }

    /**
     * @return array
     */
    public function getOptionArray()
    {
        $labelArray = [];

        /** @var \Omnyfy\Vendor\Model\Resource\Vendor\Collection $vendorCollection */
        $vendorCollection = $this->_vendorCollectionFactory->create();
        $vendorCollection->load();

        if($vendorCollection->count() > 0) {
            foreach ($vendorCollection as $vendor){
                $labelArray[$vendor->getId()] = $vendor->getName();
            }
        }
        return $labelArray;
    }
}