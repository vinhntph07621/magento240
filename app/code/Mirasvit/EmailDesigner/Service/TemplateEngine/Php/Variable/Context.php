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


namespace Mirasvit\EmailDesigner\Service\TemplateEngine\Php\Variable;

use Magento\Framework\DataObject;

/**
 * @method $this getVariablePool();
 * @method $this setVariablePool($pool);
 */
class Context extends DataObject
{
    /**
     * {@inheritdoc}
     */
    public function __call($method, $args)
    {
        $result = parent::__call($method, $args);
        // If default getter does not return anything - check variable pool
        if (substr($method, 0, 3) == 'get'
            && $result === null
            && $this->getData('variable_pool')
        ) {
            $result = $this->getData('variable_pool')->resolve($method, $args);
        }

        return $result;
    }
}