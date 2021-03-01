<?php
namespace Omnyfy\VendorGallery\Block\Adminhtml\Album\Edit\Button;

class Add extends \Omnyfy\Vendor\Block\Adminhtml\Inventory\Edit\Button\Generic
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;

    /**
     * Add constructor.
     * @param \Magento\Framework\View\Element\UiComponent\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Model\Session $backendSession
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Model\Session $backendSession
    ) {
        $this->backendSession = $backendSession;
        parent::__construct($context, $registry);
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Add New Album'),
            'on_click' => sprintf("location.href = '%s';", $this->getUrl('*/*/add')),
            'class' => 'primary'
        ];
    }
}
