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
 * @package   mirasvit/module-email-designer
 * @version   1.1.45
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\EmailDesigner\Model\Config\Source;

use Magento\Framework\App\TemplateTypesInterface;
use Magento\Framework\Option\ArrayInterface;

class TemplateType implements ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $result = [
            [
                'label' => 'HTML',
                'value' => TemplateTypesInterface::TYPE_HTML,
            ],
            [
                'label' => 'Text',
                'value' => TemplateTypesInterface::TYPE_TEXT,
            ],
        ];

        return $result;
    }
}
