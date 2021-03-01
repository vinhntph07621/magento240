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
 * @package   mirasvit/module-email
 * @version   2.1.44
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Email\Api\Service\Queue;

use Mirasvit\Email\Api\Data\QueueInterface;

interface MailModifierInterface
{
    /**
     * @param QueueInterface $queue
     * @param string $content
     * @return string
     */
    public function modifyContent(QueueInterface $queue, $content);
}
