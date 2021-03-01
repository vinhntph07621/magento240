<?php
/**
 * Created by PhpStorm.
 * User: Sanjaya-offline
 * Date: 4/10/2018
 * Time: 12:59 AM
 */

namespace Omnyfy\Checklist\Model\Checklist\Source;


class Status implements \Magento\Framework\Data\OptionSourceInterface
{
    protected $_checklist;

    public function __construct(\Omnyfy\Checklist\Model\Checklist $checklist)
    {
        $this->_checklist = $checklist;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = ['label' => '', 'value' => ''];
        $availableOptions = $this->getOptionArray();
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }

    public static function getOptionArray()
    {
        return [1 => __('Enabled'), 0 => __('Disabled')];
    }
}