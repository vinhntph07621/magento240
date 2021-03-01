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
 * @package   mirasvit/module-customer-segment
 * @version   1.0.51
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\CustomerSegment\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Registry;
use Mirasvit\CustomerSegment\Api\Data\SegmentInterface;
use Mirasvit\CustomerSegment\Api\Repository\SegmentRepositoryInterface;

abstract class SegmentAbstract extends Action
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var SegmentRepositoryInterface
     */
    protected $segmentRepository;

    /**
     * @var Action\Context
     */
    protected $context;

    /**
     * SegmentAbstract constructor.
     * @param SegmentRepositoryInterface $segmentRepository
     * @param Registry $registry
     * @param ForwardFactory $resultForwardFactory
     * @param Action\Context $context
     */
    public function __construct(
        SegmentRepositoryInterface $segmentRepository,
        Registry $registry,
        ForwardFactory $resultForwardFactory,
        Action\Context $context
    ) {
        $this->segmentRepository    = $segmentRepository;
        $this->registry             = $registry;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->context              = $context;

        parent::__construct($context);
    }

    /**
     * Initiate current page: set active menu and add page titles
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Mirasvit_CustomerSegment::segment');

        $resultPage->getConfig()->getTitle()->prepend(__('Customer Segment'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Segments'));

        return $resultPage;
    }

    /**
     * @return false|SegmentInterface
     */
    protected function initModel()
    {
        $model = $this->segmentRepository->create();

        if ($this->getRequest()->getParam(SegmentInterface::ID)) {
            $model = $this->segmentRepository->get($this->getRequest()->getParam(SegmentInterface::ID));
        }

        $this->registry->register(SegmentInterface::class, $model);

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mirasvit_CustomerSegment::customersegment_segment');
    }
}
