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



namespace Mirasvit\Rma\Controller\Adminhtml;

abstract class Field extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;
    /**
     * @var \Magento\Backend\App\Action\Context
     */
    private $context;
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $localeDate;
    /**
     * @var \Mirasvit\Rma\Model\FieldFactory
     */
    protected $fieldFactory;
    /**
     * @var \Mirasvit\Rma\Service\Field\FieldManagement
     */
    protected $fieldManagement;
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;

    /**
     * Field constructor.
     * @param \Mirasvit\Rma\Service\Field\FieldManagement $fieldManagement
     * @param \Mirasvit\Rma\Model\FieldFactory $fieldFactory
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Mirasvit\Rma\Service\Field\FieldManagement $fieldManagement,
        \Mirasvit\Rma\Model\FieldFactory $fieldFactory,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->fieldManagement = $fieldManagement;
        $this->fieldFactory    = $fieldFactory;
        $this->escaper         = $escaper;
        $this->localeDate      = $localeDate;
        $this->registry        = $registry;
        $this->context         = $context;
        $this->backendSession  = $context->getSession();
        $this->resultFactory   = $context->getResultFactory();

        parent::__construct($context);
    }

    /**
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Magento_Sales::sales_operation');
        $resultPage->getConfig()->getTitle()->prepend(__('RMA'));
        return $resultPage;
    }

    /**
     * {@inheritdoc}
     */
    public function _initField()
    {
        $field = $this->fieldFactory->create();
        if ($this->getRequest()->getParam('id')) {
            $field->load($this->getRequest()->getParam('id'));
            if ($storeId = (int) $this->getRequest()->getParam('store')) {
                $field->setStoreId($storeId);
            }
        }

        $this->registry->register('current_field', $field);

        return $field;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_Rma::rma_dictionary_field');
    }

    /************************/
}