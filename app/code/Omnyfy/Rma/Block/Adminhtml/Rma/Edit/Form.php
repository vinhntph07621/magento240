<?php
/**
 * Project: Overwrite to move template files to Omnyfy_Rma module
 * Author: seth
 * Date: 21/2/20
 * Time: 12:05 pm
 **/

namespace Omnyfy\Rma\Block\Adminhtml\Rma\Edit;


class Form extends \Mirasvit\Rma\Block\Adminhtml\Rma\Edit\Form
{
    public function __construct(
        \Mirasvit\Rma\Api\Service\Rma\RmaManagementInterface $rmaManagement,
        \Mirasvit\Rma\Block\Adminhtml\Rma\Edit\Form\GeneralInfo $generalInfo,
        \Mirasvit\Rma\Block\Adminhtml\Rma\Edit\Form\ShippingAddress $shippingAddressForm,
        \Mirasvit\Rma\Helper\Rma\Calculate $calculateHelper,
        \Mirasvit\Rma\Helper\Module $moduleHelper,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        $this->generalInfo         = $generalInfo;
        $this->shippingAddressForm = $shippingAddressForm;
        $this->rmaManagement       = $rmaManagement;
        $this->calculateHelper     = $calculateHelper;
        $this->moduleHelper        = $moduleHelper;
        $this->pricingHelper       = $pricingHelper;
        $this->registry            = $registry;
        $this->context             = $context;

        parent::__construct($rmaManagement, $generalInfo, $shippingAddressForm, $calculateHelper, $moduleHelper, $pricingHelper, $registry, $context);
    }

    /**
     * Items html
     *
     * @return string
     */
    public function getItemsHtml()
    {
        $rma = $this->getRma();
        $template = 'rma/edit/form/items.phtml';
        $order = $this->rmaManagement->getOrder($rma);
        if (!$order) {
            $template = 'rma/edit/form/offline/create_item.phtml';
        } elseif ($order->getIsOffline()) {
            $template = 'rma/edit/form/offline/items.phtml';
        }

        return $this->getLayout()->createBlock('\Omnyfy\Rma\Block\Adminhtml\Rma\Edit\Form\Items')
            ->setRma($rma)
            ->setTemplate($template)
            ->toHtml();
    }
}