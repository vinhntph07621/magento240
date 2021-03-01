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
 * @package   mirasvit/module-email-report
 * @version   2.0.11
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\EmailReport\Model;

use Magento\Framework\Model\AbstractModel;
use Mirasvit\EmailReport\Api\Data\EmailInterface;

class Email extends AbstractModel implements EmailInterface
{
    use ReportProperties;

    /**
     * {@inheritDoc}
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Email::class);
    }
}
