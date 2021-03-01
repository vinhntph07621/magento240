<?php
/**
 * Project: Vendor Options
 * Author: seth
 * Date: 21/2/20
 * Time: 5:06 pm
 **/

namespace Omnyfy\Rma\Model\Config\Source;


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
        $options[] = ["value" => 0, "label" => __('Marketplace Owner')];;
        /** @var \Omnyfy\Vendor\Model\Vendor $vendor */
        foreach($vendorCollection as $vendor){
            $options[] = ["value" => $vendor->getEntityId(), "label" => $vendor->getName()];
        }

        return $options;

    }
}