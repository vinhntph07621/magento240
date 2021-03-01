<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\VendorReview\Block\Adminhtml\Add;

/**
 * Adminhtml add vendor review form
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Review data
     *
     * @var \Omnyfy\VendorReview\Helper\Data
     */
    protected $_reviewData = null;

    /**
     * Core system store model
     *
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Omnyfy\VendorReview\Helper\Data $reviewData
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Omnyfy\VendorReview\Helper\Data $reviewData,
        array $data = []
    ) {
        $this->_reviewData = $reviewData;
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare add review form
     *
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset('add_review_form', ['legend' => __('Review Details')]);

        $fieldset->addField('vendor_name', 'note', ['label' => __('Vendor'), 'text' => 'vendor_name']);

        $fieldset->addField(
            'detailed-rating',
            'note',
            [
                'label' => __('Vendor Rating'),
                'required' => true,
                'text' => '<div id="rating_detail">' . $this->getLayout()->createBlock(
                    'Omnyfy\VendorReview\Block\Adminhtml\Rating\Detailed'
                )->toHtml() . '</div>'
            ]
        );

        $fieldset->addField(
            'status_id',
            'select',
            [
                'label' => __('Status'),
                'required' => true,
                'name' => 'status_id',
                'values' => $this->_reviewData->getReviewStatusesOptionArray()
            ]
        );

        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'select_stores',
                'multiselect',
                [
                    'label' => __('Visibility'),
                    'required' => true,
                    'name' => 'select_stores[]',
                    'values' => $this->_systemStore->getStoreValuesForForm()
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        }

        $fieldset->addField(
            'nickname',
            'text',
            [
                'name' => 'nickname',
                'title' => __('Nickname'),
                'label' => __('Nickname'),
                'maxlength' => '50',
                'required' => true
            ]
        );

        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'title' => __('Summary of Review'),
                'label' => __('Summary of Review'),
                'maxlength' => '255',
                'required' => true
            ]
        );

        $fieldset->addField(
            'detail',
            'textarea',
            [
                'name' => 'detail',
                'title' => __('Review'),
                'label' => __('Review'),
                'required' => true
            ]
        );

        $fieldset->addField('vendor_id', 'hidden', ['name' => 'vendor_id']);

        /*$gridFieldset = $form->addFieldset('add_review_grid', array('legend' => __('Please select a vendor')));
          $gridFieldset->addField('vendors_grid', 'note', array(
          'text' => $this->getLayout()->createBlock('Omnyfy\VendorReview\Block\Adminhtml\Vendor\Grid')->toHtml(),
          ));*/

        $form->setMethod('post');
        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setAction($this->getUrl('vendorreview/vendor/post'));

        $this->setForm($form);
    }
}
