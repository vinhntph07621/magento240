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



namespace Mirasvit\Mq\Model;

use Mirasvit\Mq\Api\ConsumerInterface;

class Consumer implements ConsumerInterface
{
    /**
     * @var string
     */
    private $queueName;

    /**
     * @var array
     */
    private $callback;

    /**
     * Consumer constructor.
     * @param string $queueName
     * @param array $callback
     */
    public function __construct(
        $queueName,
        array $callback
    ) {
        $this->queueName = $queueName;
        $this->callback = $callback;
    }

    /**
     * @return string
     */
    public function getQueueName()
    {
        return $this->queueName;
    }

    /**
     * @return array
     */
    public function getCallback()
    {
        return $this->callback;
    }
}
