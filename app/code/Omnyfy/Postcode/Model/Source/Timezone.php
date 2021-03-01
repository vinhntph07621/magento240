<?php

namespace Omnyfy\Postcode\Model\Source;

/**
 * @see \Magento\Config\Model\Config\Source\Locale\Timezone
 */
class Timezone extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    /**
     * Timezones that works incorrect with php_intl extension
     */
    protected $ignoredTimezones = [
        'Antarctica/Troll',
        'Asia/Chita',
        'Asia/Srednekolymsk',
        'Pacific/Bougainville'
    ];

    /**
     * @var \Magento\Framework\Locale\ListsInterface
     */
    protected $_localeLists;

    /**
     * @param \Magento\Framework\Locale\ListsInterface $localeLists
     */
    public function __construct(\Magento\Framework\Locale\ListsInterface $localeLists)
    {
        $this->_localeLists = $localeLists;
    }

    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $timezones = $this->_localeLists->getOptionTimezones();
            $this->_options = array_filter($timezones, function ($value) {
                if (in_array($value['value'], $this->ignoredTimezones)) {
                    return false;
                }
                return true;
            });
        }

        return $this->_options;
    }

}
