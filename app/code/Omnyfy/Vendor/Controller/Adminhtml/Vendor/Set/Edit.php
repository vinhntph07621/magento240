<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-16
 * Time: 16:12
 */
namespace Omnyfy\Vendor\Controller\Adminhtml\Vendor\Set;

class Edit extends \Omnyfy\Vendor\Controller\Adminhtml\Vendor\Set
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context, $coreRegistry);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $this->_setTypeId();
        $attributeSet = $this->_objectManager->create('Magento\Eav\Model\Entity\Attribute\Set')
            ->load($this->getRequest()->getParam('id'));

        if (!$attributeSet->getId()) {
            return $this->resultRedirectFactory->create()->setPath('omnyfy_vendor/*/index');
        }

        $this->_coreRegistry->register('current_attribute_set', $attributeSet);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Omnyfy_Vendor::vendor_sets');
        $resultPage->getConfig()->getTitle()->prepend(__('Attribute Sets'));
        $resultPage->getConfig()->getTitle()->prepend(
            $attributeSet->getId() ? $attributeSet->getAttributeSetName() : __('New Set')
        );
        $resultPage->addBreadcrumb(__('Marketplace'), __('Marketplace'));
        $resultPage->addBreadcrumb(__('Manage Vendor Sets'), __('Manage Vendor Sets'));
        return $resultPage;
    }
}
 