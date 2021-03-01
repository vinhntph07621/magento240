<?php
/**
 * Created by PhpStorm.
 * User: Sanjaya-offline
 * Date: 5/12/2019
 * Time: 2:53 PM
 */

namespace Omnyfy\VendorFeatured\Model\Location\Source;


class Location implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Omnyfy\Vendor\Model\Resource\Location\CollectionFactory
     */
    protected $_locationCollectionFactory;

    public function __construct(
        \Omnyfy\Vendor\Model\Resource\Location\CollectionFactory $locationCollectionFactory
    )
    {
        $this->_locationCollectionFactory = $locationCollectionFactory;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = [
            'label' => "Select a location",
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

        /** @var \Omnyfy\Vendor\Model\Resource\Location\Collection $locationCollection */
        $locationCollection = $this->_locationCollectionFactory->create();
        $locationCollection->load();

        if($locationCollection->count() > 0) {
            foreach ($locationCollection as $location){
                $labelArray[$location->getId()] = $location->getName();
            }
        }
        return $labelArray;
    }
}