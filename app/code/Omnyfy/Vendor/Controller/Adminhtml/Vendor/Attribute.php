<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-08
 * Time: 15:18
 */
namespace Omnyfy\Vendor\Controller\Adminhtml\Vendor;

abstract class Attribute extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Omnyfy_Vendor::vendor_attributes';

    /**
     * @var \Magento\Framework\Cache\FrontendInterface
     */
    protected $_attributeLabelCache;

    /**
     * @var string
     */
    protected $_entityTypeId;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Cache\FrontendInterface $attributeLabelCache
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Cache\FrontendInterface $attributeLabelCache,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_attributeLabelCache = $attributeLabelCache;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Dispatch request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $this->_entityTypeId = $this->_objectManager->create(
            'Magento\Eav\Model\Entity'
        )->setType(
            \Omnyfy\Vendor\Model\Vendor::ENTITY
        )->getTypeId();
        return parent::dispatch($request);
    }

    /**
     * @param \Magento\Framework\Phrase|null $title
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function createActionPage($title = null)
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        if ($this->getRequest()->getParam('popup')) {
            if ($this->getRequest()->getParam('vendor_tab') === 'variations') {
                $resultPage->addHandle(['popup', 'omnyfy_vendor_attribute_edit_vendor_tab_variations_popup']);
            } else {
                $resultPage->addHandle(['popup', 'omnyfy_vendor_attribute_edit_popup']);
            }
            $pageConfig = $resultPage->getConfig();
            $pageConfig->addBodyClass('attribute-popup');
        } else {
            $resultPage->addBreadcrumb(__('Marketplace'), __('Marketplace'))
                ->addBreadcrumb(__('Manage Vendor Attributes'), __('Manage Vendor Attributes'))
                ->setActiveMenu('Omnyfy_Vendor::vendor_vendor_attributes');
            if (!empty($title)) {
                $resultPage->addBreadcrumb($title, $title);
            }
        }
        $resultPage->getConfig()->getTitle()->prepend(__('Vendor Attributes'));
        return $resultPage;
    }

    /**
     * Generate code from label
     *
     * @param string $label
     * @return string
     */
    protected function generateCode($label)
    {
        $code = substr(
            preg_replace(
                '/[^a-z_0-9]/',
                '_',
                strtolower($label)
            ),
            0,
            30
        );
        $validatorAttrCode = new \Zend_Validate_Regex(['pattern' => '/^[a-z][a-z_0-9]{0,29}[a-z0-9]$/']);
        if (!$validatorAttrCode->isValid($code)) {
            $code = 'attr_' . ($code ?: substr(md5(time()), 0, 8));
        }
        return $code;
    }
}