<?php
/**
 * Copyright © 2016 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Omnyfy\Cms\Model\Config\Source;

/**
 * Used in recent article widget
 *
 */
class CountryList implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Omnyfy\Cms\Model\ResourceModel\Country\CollectionFactory
     */
    protected $countryCollectionFactory;

    /**
     * @var array
     */
    protected $options;

    /**
     * Initialize dependencies.
     *
     * @param \Omnyfy\Cms\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory
     * @param void
     */
    public function __construct(
        \Omnyfy\Cms\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory
    ) {
        $this->countryCollectionFactory = $countryCollectionFactory;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = [];
            $collection = $this->countryCollectionFactory->create()->addFieldToFilter('status', 1);
			$collection->getSelect()->order('country_name','ASC');
			
            foreach ($collection as $item) {
                $this->options[] = [
                    'label' => $item->getCountryName(),
                    'value' => $item->getId(),
                ];
            }
        }

        return $this->options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $array = [];
        foreach ($this->toOptionArray() as $item) {
            $array[$item['value']] = $item['label'];
        }
        return $array;
    }
}
