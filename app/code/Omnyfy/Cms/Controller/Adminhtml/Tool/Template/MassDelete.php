<?php

namespace Omnyfy\Cms\Controller\Adminhtml\Tool\Template;

use Magento\Backend\App\Action\Context;
use Omnyfy\Cms\Model\ResourceModel\ToolTemplate\CollectionFactory;
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
     * @var Omnyfy\Cms\Model\ResourceModel\ToolTemplate\CollectionFactory;
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
        $toolTemplatesUpdated = 0;
        foreach ($collection as $toolTemplate) {
            $toolTemplate->delete();
            $toolTemplatesUpdated++;
        }

        if ($toolTemplatesUpdated) {
            $this->messageManager->addSuccess(__('A total of %1 record(s) were deleted.', $toolTemplatesUpdated));
        }

        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('cms/tool_template/index');
        return $resultRedirect;
    }

}
