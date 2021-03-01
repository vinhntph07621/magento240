<?php

namespace Omnyfy\Cms\Controller\Adminhtml\User\Type;

use Magento\Backend\App\Action\Context;
use Omnyfy\Cms\Model\ResourceModel\UserType\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class MassDelete
 */
class MassDelete extends \Magento\Backend\App\Action {

    /**
     * @var \Magento\Ui\Component\MassAction\Filter $filter
     */
    protected $filter;

    /**
     * @var Omnyfy\Cms\Model\ResourceModel\UserType\CollectionFactory;
     */
    protected $collectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
    \Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry, \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory, \Magento\Framework\View\Result\PageFactory $resultPageFactory, Filter $filter, CollectionFactory $collectionFactory
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * Execute controller
     * @return Magento\Framework\Controller\ResultFactor
     */
    public function execute() {
        
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $userTypesUpdated = 0;
        foreach ($collection as $userType) {
            $userType->delete();
            $userTypesUpdated++;
        }

        if ($userTypesUpdated) {
            $this->messageManager->addSuccess(__('A total of %1 record(s) were deleted.', $userTypesUpdated));
        }

        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('cms/user_type/index');
        return $resultRedirect;
    }

}
