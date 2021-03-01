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



namespace Mirasvit\Rma\Controller\Rma;

use Magento\Framework\Controller\ResultFactory;

class PrintAction extends \Mirasvit\Rma\Controller\Rma
{

    /**
     * @var \Mirasvit\Rma\Helper\Controller\Rma\GuestStrategy
     */
    private $guestStrategy;
    /**
     * @var \Mirasvit\Rma\Api\Repository\RmaRepositoryInterface
     */
    private $rmaRepository;
    /**
     * @var \Mirasvit\Rma\Api\Service\Rma\RmaManagement\SaveInterface
     */
    private $rmaSaveManagement;
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * PrintAction constructor.
     * @param \Mirasvit\Rma\Helper\Controller\Rma\GuestStrategy $guestStrategy
     * @param \Mirasvit\Rma\Api\Repository\RmaRepositoryInterface $rmaRepository
     * @param \Mirasvit\Rma\Api\Service\Rma\RmaManagement\SaveInterface $rmaSaveManagement
     * @param \Magento\Framework\Registry $registry
     * @param \Mirasvit\Rma\Helper\Controller\Rma\StrategyFactory $strategyFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Mirasvit\Rma\Helper\Controller\Rma\GuestStrategy $guestStrategy,
        \Mirasvit\Rma\Api\Repository\RmaRepositoryInterface $rmaRepository,
        \Mirasvit\Rma\Api\Service\Rma\RmaManagement\SaveInterface $rmaSaveManagement,
        \Magento\Framework\Registry $registry,
        \Mirasvit\Rma\Helper\Controller\Rma\StrategyFactory $strategyFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->guestStrategy     = $guestStrategy;
        $this->rmaRepository     = $rmaRepository;
        $this->rmaSaveManagement = $rmaSaveManagement;
        $this->registry          = $registry;

        parent::__construct($strategyFactory, $customerSession, $context);
    }


    /**
     * {@inheritdoc}
     */
    public function isRequireCustomerAutorization()
    {
        return $this->strategy->isRequireCustomerAutorization();
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        try {
            $rma = $this->guestStrategy->initRma($this->getRequest());
            $this->registry->register('current_rma', $rma);
            $this->rmaSaveManagement->markAsReadForCustomer($rma);
            /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
            $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
            $this->initPage($resultPage);

            return $resultPage;
        } catch(\Magento\Framework\Exception\NoSuchEntityException $e) {
            /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
            $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
            return $resultForward->forward('noroute');
        }
    }
}
