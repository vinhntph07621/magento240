<?php
/**
 * Project: RMA per vendor
 * Author: seth
 * Date: 21/2/20
 * Time: 12:25 pm
 **/

namespace Omnyfy\Rma\Block\Adminhtml\Rma\Edit\Form;


class GeneralInfo extends \Mirasvit\Rma\Block\Adminhtml\Rma\Edit\Form\GeneralInfo
{
    /**
     * @var \Omnyfy\Rma\Helper\Data '
     */
    protected $helper;

    protected $customFields;

    protected $rmaManagement;

    protected $rmaUserHtml;

    protected $rmaUrl;

    protected $rmaOption;

    protected $storeHelper;

    protected $formFactory;

    protected $convertDataObject;

    public function __construct(
        \Mirasvit\Rma\Block\Adminhtml\Rma\Edit\Form\Generalinfo\CustomFields $customFields,
        \Mirasvit\Rma\Api\Config\RmaNumberConfigInterface $rmaNumberConfig,
        \Mirasvit\Rma\Api\Service\Rma\RmaManagementInterface $rmaManagement,
        \Mirasvit\Rma\Api\Service\Rma\RmaOrderInterface $rmaOrderService,
        \Mirasvit\Rma\Api\Service\Status\StatusManagementInterface $statusManagement,
        \Mirasvit\Rma\Helper\User\Html $rmaUserHtml,
        \Mirasvit\Rma\Helper\Rma\Url $rmaUrl,
        \Mirasvit\Rma\Helper\Rma\Option $rmaOption,
        \Mirasvit\Rma\Helper\Store $storeHelper,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Convert\DataObject $convertDataObject,
        \Magento\Backend\Block\Widget\Context $context,
        \Omnyfy\Rma\Helper\Data $helper,
        array $data = []
    ) {
        $this->customFields         = $customFields;
        $this->rmaManagement        = $rmaManagement;
        $this->rmaUserHtml          = $rmaUserHtml;
        $this->rmaUrl               = $rmaUrl;
        $this->rmaOption            = $rmaOption;
        $this->storeHelper          = $storeHelper;
        $this->formFactory          = $formFactory;
        $this->convertDataObject    = $convertDataObject;
        $this->helper              = $helper;

        parent::__construct($customFields,
            $rmaNumberConfig,
            $rmaManagement,
            $rmaOrderService,
            $statusManagement,
            $rmaUserHtml,
            $rmaUrl,
            $rmaOption,
            $storeHelper,
            $formFactory,
            $convertDataObject,
            $context,
            $data
        );
    }

    /**
     * General information form
     *
     * @param \Mirasvit\Rma\Api\Data\RmaInterface $rma
     *
     * @return string
     */
    public function getGeneralInfoFormHtml(\Mirasvit\Rma\Api\Data\RmaInterface $rma)
    {
        $form = $this->formFactory->create();
        /** @var \Magento\Framework\Data\Form\Element\Fieldset $fieldset */
        $fieldset = $form->addFieldset('edit_fieldset', ['legend' => __('General Information')]);

        if ($rma->getId()) {
            $fieldset->addField('rma_id', 'hidden', [
                'name'  => 'rma_id',
                'value' => $rma->getId(),
            ]);
        }

        $element = $fieldset->addField('increment_id', 'text', [
            'label' => __('RMA #'),
            'name'  => 'increment_id',
            'value' => $rma->getIncrementId(),
        ]);

        if (!$rma->getId()) {
            $element->setNote('will be generated automatically, if empty');
        }

        $this->customFields->addCustomerLink($fieldset, $rma);

        $order = $this->rmaManagement->getOrder($rma);
        if (!$order) {
            $customerId = (int)$this->getRequest()->getParam('customer_id');
            $fieldset->addField('receipt_number', 'text', [
                'name'   => 'receipt_number',
                'label'  => __('Order (Offline) #'),
            ]);
            $fieldset->addField('is_offline', 'hidden', [
                'name'   => 'is_offline',
                'value'  => 1,
            ]);
            $fieldset->addField('customer_id', 'hidden', [
                'name'   => 'customer_id',
                'value'  => $customerId,
            ]);
            $fieldset->addField('store_id', 'select', [
                'name'   => 'store_id',
                'value'  => 1,
                'label'  => __('Store View'),
                'values' => $this->storeHelper->getCoreStoreOptionArray(),
            ]);
        } elseif ($order->getIsOffline()) {
            $fieldset->addField('order_id', 'label', [
                'name'   => 'order_id',
                'label'  => __('Order (Offline) #'),
                'value'  => $this->escapeHtml($order->getReceiptNumber()),
            ]);
        } else {
            $fieldset->addField('order_id', 'link', [
                'name'   => 'order_id',
                'label'  => __('Order #'),
                'value'  => '#' . $order->getIncrementId(),
                'href'   => $this->getUrl('sales/order/view', ['order_id' => $rma->getOrderId()]),
            ]);
        }

        if ($rma->getTicketId()) {
            $fieldset->addField('ticket_id', 'hidden', [
                'name'  => 'ticket_id',
                'value' => $rma->getTicketId(),
            ]);
            $ticket = $this->rmaManagement->getTicket($rma);
            $fieldset->addField('ticket_link', 'link', [
                'label'  => __('Created From Ticket'),
                'name'   => 'ticket_link',
                'value'  => '#' . $ticket->getCode(),
                'href'   => $ticket->getBackendUrl(),
                'target' => '_blank',
            ]);
        }

        $user = $this->helper->getUser($rma->getUserId());
        if ($this->helper->getVendorId()) {
            $fieldset->addField('user_id', 'select', [
                'label'  => __('RMA Owner'),
                'name'   => 'user_id',
                'value'  => !empty($user) ? $user->getId() : '',
                'values' => $this->rmaUserHtml->toAdminUserOptionArray(true),
                'readonly' => true,
            ]);
        }else {
            $fieldset->addField('user_id', 'select', [
                'label'  => __('RMA Owner'),
                'name'   => 'user_id',
                'value'  => !empty($user) ? $user->getId() : '',
                'values' => $this->rmaUserHtml->toAdminUserOptionArray(true),
            ]);
        }

        $fieldset->addField('status_id', 'select', [
            'label'  => __('Status'),
            'name'   => 'status_id',
            'value'  => $rma->getStatusId(),
            'values' => $this->convertDataObject->toOptionArray($this->rmaOption->getStatusList(), "id", "name")
        ]);

        $fieldset->addField('return_label', 'Mirasvit\Rma\Block\Adminhtml\Rma\Edit\Form\Element\File', [
            'label'      => __('Upload Return Label'),
            'name'       => 'return_label',
            'attachment' => $this->rmaManagement->getReturnLabel($rma),
        ]);

        if ($rma->getId()) {
            $fieldset->addField('guest_link', 'link', [
                'label'  => __('External Link'),
                'name'   => 'guest_link',
                'class'  => 'guest-link',
                'value'  => __('open'),
                'href'   => $this->rmaUrl->getGuestUrl($rma),
                'target' => '_blank',
            ]);
        }


        $this->customFields->addExchangeOrders($fieldset, $rma);
        $this->customFields->addCreditmemos($fieldset, $rma);

        $this->customFields->getReturnAddress($fieldset, $rma);

        return $form->toHtml();
    }
}