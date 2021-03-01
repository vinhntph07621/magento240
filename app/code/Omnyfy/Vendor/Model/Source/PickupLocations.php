<?php
/**
 * Created by PhpStorm.
 * User: Sanjaya-offline
 * Date: 14/02/2019
 * Time: 5:47 PM
 */

namespace Omnyfy\Vendor\Model\Source;


use Omnyfy\Vendor\Model\Location;
use Omnyfy\Vendor\Model\Resource\Location\Collection;

class PickupLocations implements \Magento\Framework\Option\ArrayInterface
{
    protected $_locationCollectionFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    public function __construct(
        \Omnyfy\Vendor\Model\Resource\Location\CollectionFactory $locationCollectionFactory,
        \Magento\Framework\Registry $registry
    )
    {
        $this->_locationCollectionFactory = $locationCollectionFactory;
        $this->_coreRegistry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->getLocationsArray();
    }

    /**
     * @return array
     */
    public function getLocationsArray() {
        /** @var \Omnyfy\Vendor\Model\Resource\Location\Collection $locationCollection */
        $locationCollection = $this->_locationCollectionFactory->create();
        $locationCollection->load();
        $options = [];

        /** @var \Omnyfy\Vendor\Model\Location $location */
        foreach($locationCollection as $location){
            $options[] = ["value" => $location->getId(), "label" => $location->getName()];
        }

        return $options;
    }
}