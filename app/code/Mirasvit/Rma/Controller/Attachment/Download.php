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



namespace Mirasvit\Rma\Controller\Attachment;

use Magento\Framework\App\Filesystem\DirectoryList;

class Download extends \Magento\Framework\App\Action\Action
{
    private $attachmentRepository;

    private $fileFactory;

    private $resultForwardFactory;

    public function __construct(
        \Mirasvit\Rma\Api\Repository\AttachmentRepositoryInterface $attachmentRepository,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->attachmentRepository = $attachmentRepository;
        $this->fileFactory          = $fileFactory;
        $this->resultForwardFactory = $resultForwardFactory;

        parent::__construct($context);
    }


    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $uid = $this->getRequest()->getParam('uid');
        try {
            $attachment = $this->attachmentRepository->getByUid($uid);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }

        return $this->fileFactory->create(
            $attachment->getName(),
            $attachment->getBody(),
            DirectoryList::VAR_DIR,
            $attachment->getType()
        );
    }
}
