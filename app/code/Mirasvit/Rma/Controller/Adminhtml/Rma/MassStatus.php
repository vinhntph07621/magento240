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



namespace Mirasvit\Rma\Controller\Adminhtml\Rma;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Mirasvit\Rma\Api\Service\Performer\PerformerFactoryInterface;
use Mirasvit\Rma\Model\ResourceModel\Rma\CollectionFactory;
use Mirasvit\Rma\Model\RmaFactory;

class MassStatus extends \Mirasvit\Rma\Controller\Adminhtml\Rma
{
    protected $filter;

    private $collectionFactory;

    private $eventManager;

    private $performer;

    private $rmaFactory;

    public function __construct(
        PerformerFactoryInterface $performer,
        RmaFactory $rmaFactory,
        Context $context,
        CollectionFactory $collectionFactory,
        Filter $filter
    )
    {
        $this->performer    = $performer;
        $this->rmaFactory   = $rmaFactory;
        $this->filter       = $filter;
        $this->eventManager = $context->getEventManager();

        $this->collectionFactory = $collectionFactory;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $collection     = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();

        $performer = $this->performer->create(PerformerFactoryInterface::USER, $this->_auth->getUser());

        foreach ($collection as $rma) {
            $rma->setStatusId($this->getRequest()->getParam('status_id'));
            $rma->save();
            $this->eventManager->dispatch('rma_update_rma_after', ['rma' => $rma, 'performer' => $performer]);
        }
        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been changed.', $collectionSize));

        return $resultRedirect->setPath('*/*/index');
    }
}
