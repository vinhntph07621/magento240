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



namespace Mirasvit\Rma\Observer;

use Magento\Framework\Event\ObserverInterface;
use Mirasvit\Rma\Api\Service\Message\MessageManagement\AddInterface;
use Mirasvit\Rma\Model\RmaFactory;

class HelpdeskProcessEmail implements ObserverInterface
{
    private $messageAddManagement;

    private $rmaFactory;

    public function __construct(
        RmaFactory $rmaFactory,
        AddInterface $messageAddManagement
    ) {
        $this->rmaFactory           = $rmaFactory;
        $this->messageAddManagement = $messageAddManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $event     = $observer->getEvent();
        $ticket    = $event->getTicket();
        $performer = $event->getPerformer();
        $email     = $event->getEmail() ?: false;
        $text      = $event->getBody();

        if (!$rmaId = $ticket->getRmaId()) {
            return;
        }

        $rma = $this->rmaFactory->create()->load($rmaId);
        if (!$rma->getId()) {
            return;
        }

        $params = [
            'isNotified'    => true,
            'isVisible'     => true,
            'isNotifyAdmin' => true,
            'helpdeskEmail' => $email,
        ];

        $this->messageAddManagement->addMessage($performer, $rma, $text, $params);
    }
}
