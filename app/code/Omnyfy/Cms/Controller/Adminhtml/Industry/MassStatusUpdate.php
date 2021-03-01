<?php
/**
 * Project: CMS Industry M2.
 * User: abhay
 * Date: 01/05/17
 * Time: 2:30 PM
 */
namespace Omnyfy\Cms\Controller\Adminhtml\Industry;

use Magento\Backend\App\Action\Context;
use Omnyfy\Cms\Model\ResourceModel\Industry\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class MassStatus
 */
class MassStatusUpdate extends \Magento\Backend\App\Action {

    /**
     * @var \Magento\Ui\Component\MassAction\Filter $filter
     */
    protected $filter;

    /**
     * @var Omnyfy\Cms\Model\ResourceModel\Industry\CollectionFactory;
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
        $this->_coreRegistry = $coreRegistry;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Execute controller
     * @return Magento\Framework\Controller\ResultFactor
     */
    public function execute() {

        $collection = $this->filter->getCollection($this->collectionFactory->create());

        $industryStatus = $this->getRequest()->getParam('status');

        $industrysUpdated = 0;
        foreach ($collection as $industry) {
            $industry->setData(
                    'status', $industryStatus
            );
            $industry->save();
            $industrysUpdated++;
        }

        if ($industrysUpdated) {
            $this->messageManager->addSuccess(__('A total of %1 record(s) were updated.', $industrysUpdated));
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('cms/industry/index');
        return $resultRedirect;
    }

}
