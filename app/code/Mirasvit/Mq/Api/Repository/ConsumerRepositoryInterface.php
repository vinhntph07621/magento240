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
 * @package   mirasvit/module-message-queue
 * @version   1.0.12
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Mq\Api\Repository;

use Mirasvit\Mq\Api\ConsumerInterface;

interface ConsumerRepositoryInterface
{
    /**
     * @param string $name
     * @return ConsumerInterface
     */
    public function get($name);

    /**
     * @param string $queueName
     * @return ConsumerInterface[]
     */
    public function getByQueueName($queueName);
}