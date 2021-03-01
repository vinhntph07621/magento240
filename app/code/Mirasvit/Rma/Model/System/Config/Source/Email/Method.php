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
 * @package   mirasvit/module-rma
 * @version   2.1.18
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Rma\Model\System\Config\Source\Email;

use Magento\Framework\Option\ArrayInterface;
use Mirasvit\Rma\Api\Config\NotificationConfigInterface;

class Method implements ArrayInterface
{
    /**
     * {@inheritDoc}
     */
    public function toOptionArray()
    {
        $options = [
            ['value' => NotificationConfigInterface::EMAIL_METHOD_BCC, 'label' => __('Bcc')],
            ['value' => NotificationConfigInterface::EMAIL_METHOD_COPY, 'label' => __('Separate Email')],
        ];

        return $options;
    }
}
