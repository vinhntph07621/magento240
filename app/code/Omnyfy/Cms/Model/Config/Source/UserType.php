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
class UserType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Omnyfy\Cms\Model\ResourceModel\UserType\CollectionFactory
     */
    protected $userTypeCollectionFactory;

    /**
     * @var array
     */
    protected $options;

    /**
     * Initialize dependencies.
     *
     * @param \Omnyfy\Cms\Model\ResourceModel\UserType\CollectionFactory $userTypeCollectionFactory
     * @param void
     */
    public function __construct(
        \Omnyfy\Cms\Model\ResourceModel\UserType\CollectionFactory $userTypeCollectionFactory
    ) {
        $this->userTypeCollectionFactory = $userTypeCollectionFactory;
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
            $collection = $this->userTypeCollectionFactory->create();
            $collection
                    //->addFieldToFilter('status', 1)
                    ->setOrder('user_type');

            foreach ($collection as $item) {
                $this->options[] = [
                    'label' => $item->getUserType().
                        ($item->getStatus() ? '' : ' ('.__('Disabled').')'),
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
    
    /**
     * Generate spaces
     * @param  int $n
     * @return string
     */
    protected function _getSpaces($n)
    {
        $s = '';
        for ($i = 0; $i < $n; $i++) {
            $s .= '--- ';
        }

        return $s;
    }

}
