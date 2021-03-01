<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-customer-segment
 * @version   1.0.51
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\CustomerSegment\Api\Data\Segment;


interface OptionSourceInterface extends \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @param bool $all - add additional option or not
     *
     * @return array Format: array('<value>' => '<label>', ...)
     */
    public function toOptionHash($all = false);

    /**
     * Return array of options as value-label pairs
     *
     * @param bool $all - add additional option or not
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray($all = false);

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function getAllOptions();
}
