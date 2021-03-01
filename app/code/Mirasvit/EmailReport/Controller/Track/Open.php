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



namespace Mirasvit\EmailReport\Controller\Track;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Session\SessionManagerInterface;
use Mirasvit\EmailReport\Api\Repository\OpenRepositoryInterface;
use Mirasvit\EmailReport\Api\Service\StorageServiceInterface;

class Open extends Action
{
    /**
     * @var SessionManagerInterface
     */
    private $sessionManager;

    /**
     * @var OpenRepositoryInterface
     */
    private $openRepository;

    /**
     * @var StorageServiceInterface
     */
    private $storageService;

    /**
     * Open constructor.
     * @param OpenRepositoryInterface $openRepository
     * @param SessionManagerInterface $sessionManager
     * @param StorageServiceInterface $storageService
     * @param Context $context
     */
    public function __construct(
        OpenRepositoryInterface $openRepository,
        SessionManagerInterface $sessionManager,
        StorageServiceInterface $storageService,
        Context $context
    ) {
        $this->openRepository = $openRepository;
        $this->sessionManager = $sessionManager;
        $this->storageService = $storageService;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Raw $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        if ($uniqueHash = $this->getRequest()->getParam(StorageServiceInterface::QUEUE_PARAM_NAME)) {
            $queue = $this->storageService->retrieveQueue($uniqueHash);

            if ($queue) {
                $open = $this->openRepository->create()
                    ->setTriggerId($queue->getTriggerId())
                    ->setQueueId($queue->getId())
                    ->setSessionId($this->sessionManager->getSessionId());

                $this->openRepository->ensure($open);
                $this->storageService->persistQueueHash($queue->getUniqHash());
            }
        }

        $pixel = base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw==');

        return $resultPage
            ->setHeader('Content-Type', 'image/gif')
            ->setHeader('Content-Length', strlen($pixel))
            ->setContents($pixel);
    }
}
