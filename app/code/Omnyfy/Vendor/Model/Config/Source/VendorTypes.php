<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-05-10
 * Time: 17:09
 */
namespace Omnyfy\Vendor\Model\Config\Source;

class VendorTypes implements \Magento\Framework\Option\ArrayInterface
{
    protected $_vendorTypeCollectionFactory;

    protected $_options;

    public function __construct(
        \Omnyfy\Vendor\Model\Resource\VendorType\CollectionFactory $vendorTypeCollectionFactory
    )
    {
        $this->_vendorTypeCollectionFactory = $vendorTypeCollectionFactory;
    }

    public function toOptionArray()
    {
        if (null == $this->_options) {
            $result = [];
            $collection = $this->_vendorTypeCollectionFactory->create();
            //Only show active vendor types
            $collection->addFieldToFilter('status', 1);
            foreach($collection as $vendorType)
            {
                $result[] = [
                    "value" => $vendorType->getId(),
                    "label" => $vendorType->getTypeName()
                ];
            }
            $this->_options = $result;
        }

        return $this->_options;
    }

    public function toValuesArray()
    {
        $options = $this->toOptionArray();
        $result = [];
        foreach($options as $option) {
            $result[$option['value']] = $option['label'];
        }
        return $result;
    }
}
 