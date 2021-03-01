<?php
/**
 * Created by PhpStorm.
 * User: Sanjaya-offline
 * Date: 14/02/2019
 * Time: 5:47 PM
 */

namespace Omnyfy\Vendor\Model\Config\Source;


use Omnyfy\Vendor\Model\Location;
use Omnyfy\Vendor\Model\Resource\Location\Collection;

class Vendors implements \Magento\Framework\Option\ArrayInterface
{
    protected $_vendorCollectionFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    public function __construct(
        \Omnyfy\Vendor\Model\Resource\Vendor\CollectionFactory $vendorCollectionFactory,
        \Magento\Framework\Registry $registry
    )
    {
        $this->_vendorCollectionFactory = $vendorCollectionFactory;
        $this->_coreRegistry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->getVendorsArray();
    }

    /**
     * @return array
     */
    public function getVendorsArray($activeOnly = true) {
        /** @var \Omnyfy\Vendor\Model\Resource\Vendor\Collection $vendorCollection */
        $vendorCollection = $this->_vendorCollectionFactory->create();
        if ($activeOnly) {
            $vendorCollection->addFieldToFilter('status', \Omnyfy\Vendor\Model\Source\Status::STATUS_ACTIVE);
        }
        $vendorCollection->load();
        $options = [];

        /** @var \Omnyfy\Vendor\Model\Vendor $vendor */
        foreach($vendorCollection as $vendor){
            $options[] = ["value" => $vendor->getEntityId(), "label" => $vendor->getName()];
        }

        return $options;

    }
}