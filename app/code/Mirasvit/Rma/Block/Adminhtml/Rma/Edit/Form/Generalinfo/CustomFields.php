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



namespace Mirasvit\Rma\Block\Adminhtml\Rma\Edit\Form\Generalinfo;

use Magento\Framework\Exception\NoSuchEntityException;
use Mirasvit\Rma\Api\Data\RmaInterface;
use Magento\Framework\Data\Form\Element\Fieldset;
use Mirasvit\Rma\Api\Service\Field\FieldManagementInterface;
use Mirasvit\Rma\Api\Service\Rma\RmaManagementInterface;
use Mirasvit\Rma\Api\Config\RmaConfigInterface;
use Mirasvit\Rma\Model\ResourceModel\Address\Collection;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Mirasvit\Rma\Helper\Customer\Url;
use Magento\Framework\Data\FormFactory;
use Magento\Backend\Block\Widget\Context;

class CustomFields extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Sales\Api\CreditmemoRepositoryInterface
     */
    private $creditMemoRepository;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var \Mirasvit\Rma\Api\Service\Rma\RmaManagementInterface
     */
    private $rmaManagement;

    /**
     * @var \Mirasvit\Rma\Helper\Customer\Url
     */
    private $rmaCustomerUrl;

    /**
     * @var \Mirasvit\Rma\Api\Service\Field\FieldManagementInterface
     */
    private $rmaField;

    /**
     * @var \Magento\Framework\Data\FormFactory
     */
    private $formFactory;

    /**
     * @var \Mirasvit\Rma\Model\ResourceModel\Address\Collection
     */
    private $addressCollection;

    /**
     * @var \Mirasvit\Rma\Api\Config\RmaConfigInterface
     */
    private $rmaConfig;

    public function __construct(
        FieldManagementInterface $rmaField,
        RmaManagementInterface $rmaManagement,
        RmaConfigInterface $rmaConfig,
        Collection $addressCollection,
        OrderRepositoryInterface $orderRepository,
        CreditmemoRepositoryInterface $creditMemoRepository,
        Url $rmaCustomerUrl,
        FormFactory $formFactory,
        Context $context,
        array $data = []
    ) {
        $this->rmaField             = $rmaField;
        $this->rmaManagement        = $rmaManagement;
        $this->rmaConfig            = $rmaConfig;
        $this->addressCollection    = $addressCollection;
        $this->orderRepository      = $orderRepository;
        $this->creditMemoRepository = $creditMemoRepository;
        $this->rmaCustomerUrl       = $rmaCustomerUrl;
        $this->formFactory          = $formFactory;

        parent::__construct($context, $data);
    }

    /**
     * @param Fieldset     $fieldset
     * @param RmaInterface $rma
     *
     * @return void
     */
    public function getReturnAddress($fieldset, $rma)
    {
        $defaultAddress = $this->rmaConfig->getReturnAddress($rma->getStoreId());
        $options        = $this->addressCollection->toOptionArray(true, $defaultAddress);

        foreach ($options as $k => $option) {
            $options[$k]['value'] = $this->escapeHtml($option['value']);
            $options[$k]['label'] = $this->escapeHtml($option['label']);
        }

        $fieldset->addField('return_address', 'select', [
            'label'  => __('Return Address'),
            'name'   => 'return_address',
            'value'  => $this->rmaManagement->getReturnAddressHtml($rma),
            'values' => $options,
        ]);
        $fieldset->addField('return_address_text', 'note', [
            'label' => '',
            'name'  => 'return_address_text',
            'text'  => nl2br($this->rmaManagement->getReturnAddressHtml($rma)),
        ]);
    }

    /**
     * @param RmaInterface $rma
     *
     * @return bool|\Magento\Framework\Data\Form
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getFieldForm(RmaInterface $rma)
    {
        $form       = $this->formFactory->create();
        $fieldset   = $form->addFieldset('field_fieldset', []);
        $collection = $this->rmaField->getStaffCollection();

        if (!$collection) {
            return false;
        }

        foreach ($collection as $field) {
            $fieldset->addField(
                $field->getCode(),
                $this->escapeHtml($field->getType()),
                $this->rmaField->getInputParams($field, true, $rma)
            );
        }

        return $form;
    }


    /**
     * @param Fieldset     $fieldset
     * @param RmaInterface $rma
     *
     * @return void
     */
    public function addCustomerLink($fieldset, RmaInterface $rma)
    {
        if ($rma->getCustomerId()) {
            $fieldset->addField('customer', 'link', [
                'label' => __('Customer'),
                'name'  => 'customer',
                'value' => $this->rmaManagement->getFullName($rma),
                'href'  => $this->rmaCustomerUrl->getBackendUrl($rma->getCustomerId()),
            ]);
        } else {
            $fieldset->addField('customer', 'label', [
                'label' => __('Customer'),
                'name'  => 'customer',
                'value' => $this->rmaManagement->getFullName($rma)
                    . ($rma->getIsGift() ? ' ' . __('(received a gift)') : ''),
            ]);
        }
    }

    /**
     * @param Fieldset     $fieldset
     * @param RmaInterface $rma
     *
     * @return void
     */
    public function addExchangeOrders($fieldset, RmaInterface $rma)
    {
        if ($rma->getExchangeOrderIds()) {
            $links = [];
            foreach ($rma->getExchangeOrderIds() as $id) {
                try {
                    $exchangeOrder = $this->orderRepository->get($id);
                    $links[]       = "<a href='" . $this->getUrl(
                            'sales/order/view',
                            ['order_id' => $id]
                        ) . "' target=\"_blank\">#" . $exchangeOrder->getIncrementId() . '</a>';
                } catch (NoSuchEntityException $e) {
                    // exchange order was removed
                    $links[] = empty($rma->getExchangeOrderIncrements()[$id]) ? $id :
                        $rma->getExchangeOrderIncrements()[$id];
                }
            }

            $fieldset->addField('exchangeorder', 'note', [
                'label' => __('Exchange Order'),
                'text'  => implode(', ', $links),
            ]);
        }
    }

    /**
     * @param Fieldset     $fieldset
     * @param RmaInterface $rma
     *
     * @return void
     */
    public function addReplacementOrders($fieldset, RmaInterface $rma)
    {
        if ($rma->getReplacementOrderIds()) {
            $links = [];
            foreach ($rma->getReplacementOrderIds() as $id) {
                try {
                    $replacementOrder = $this->orderRepository->get($id);
                    $url              = $this->getUrl('sales/order/view', ['order_id' => $id]);
                    $links[]          = "<a href='" . $url . "' target=\"_blank\">#" . $replacementOrder->getIncrementId() . '</a>';
                } catch (NoSuchEntityException $e) {
                    $links[] = empty($rma->getReplacementOrderIncrements()[$id]) ? $id :
                        $rma->getReplacementOrderIncrements()[$id];
                }
            }

            $fieldset->addField('replacementorder', 'note', [
                'label' => __('Replacement Order'),
                'text'  => implode(', ', $links),
            ]);
        }
    }

    /**
     * @param Fieldset     $fieldset
     * @param RmaInterface $rma
     *
     * @return void
     */
    public function addCreditmemos($fieldset, RmaInterface $rma)
    {
        if ($rma->getCreditMemoIds()) {
            $links = [];
            foreach ($rma->getCreditMemoIds() as $id) {
                $creditmemo = $this->creditMemoRepository->get($id);
                $links[]    = "<a href='" . $this->getUrl(
                        'sales/creditmemo/view',
                        ['creditmemo_id' => $id]
                    ) . "' target=\"_blank\">#" . $creditmemo->getIncrementId() . '</a>';
            }

            $fieldset->addField('credit_memo_id', 'note', [
                'label' => __('Credit Memo'),
                'text'  => implode(', ', $links),
            ]);
        }
    }
}