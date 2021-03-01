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



namespace Mirasvit\Rma\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\User\Model\ResourceModel\User\CollectionFactory as UserCollectionFactory;
use Mirasvit\Rma\Api\Config\HelpdeskConfigInterface;
use Mirasvit\Rma\Api\Data\RmaInterface;
use Mirasvit\Rma\Api\Service\Message\MessageManagement\AddInterface;
use Mirasvit\Rma\Api\Service\Performer\PerformerFactoryInterface;
use Mirasvit\Rma\Model\ResourceModel\Rma\CollectionFactory as RmaCollectionFactory;

class Process
{
    private $context;

    private $helpdeskConfig;

    private $messageAddManagement;

    private $performer;

    private $registry;

    private $rmaCollectionFactory;

    private $userCollectionFactory;

    public function __construct(
        HelpdeskConfigInterface $helpdeskConfig,
        PerformerFactoryInterface $performer,
        AddInterface $messageAddManagement,
        RmaCollectionFactory $rmaCollectionFactory,
        UserCollectionFactory $userCollectionFactory,
        Registry $registry,
        Context $context
    ) {
        $this->helpdeskConfig        = $helpdeskConfig;
        $this->performer             = $performer;
        $this->messageAddManagement  = $messageAddManagement;
        $this->rmaCollectionFactory  = $rmaCollectionFactory;
        $this->userCollectionFactory = $userCollectionFactory;
        $this->registry              = $registry;
        $this->context               = $context;
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Email $email
     * @param string                         $code
     *
     * @return bool|\Mirasvit\Helpdesk\Model\Ticket
     * @throws \Exception
     */
    public function processEmail($email, $code)
    {
        if (!$this->helpdeskConfig->isHelpdeskActive()) {
            return false;
        }
        $performerType = PerformerFactoryInterface::GUEST;

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        //try to find RMA for this email
        $guestId       = str_replace(RmaInterface::MESSAGE_CODE, '', $code);
        $rmaCollection = $this->rmaCollectionFactory->create()->addFieldToFilter('guest_id', $guestId);

        if (!$rmaCollection->count()) {// echo 'Can\'t find a RMA by guest id '.$guestId;
            return false;
        }

        $rma = $rmaCollection->getFirstItem();

        //try to find staff user for this email
        $userCollection = $this->userCollectionFactory->create()
            ->addFieldToFilter('email', $email->getFromEmail());

        if ($userCollection->count()) {
            $performer     = $userCollection->getFirstItem();
            $performerType = PerformerFactoryInterface::USER;

            $rma->setUserId($performer->getId());
            $rma->save();
        } else {
            $performer = $objectManager->create('\Mirasvit\Helpdesk\Helper\Customer')->getCustomerByEmail($email);

            if ($performer->getId()) {
                $performerType = PerformerFactoryInterface::CUSTOMER;
            }
        }

        $performer = $this->performer->create($performerType, $performer);

        //add message to rma
        $body = $objectManager->create('\Mirasvit\Helpdesk\Helper\StringUtil')
            ->parseBody($email->getBody(), $email->getFormat());

        $ticket = $objectManager->create(\Mirasvit\Helpdesk\Model\Ticket::class)->load($rma->getTicketId());
        $ticket->setRmaId($rma->getId());

        $this->registry->unregister('current_rma');
        $this->registry->register('current_rma', $rma);

        try {
            $this->context->getEventManager()->dispatch(
                'helpdesk_process_email',
                ['body' => $body, 'performer' => $performer, 'ticket' => $ticket, 'email' => $email]
            );
        } catch (LocalizedException $e) {
            // ignore exception of empty message for an email
            if ($e->getCode() != 400) {
                throw $e;
            }
        }

        return $rma;
    }
}
