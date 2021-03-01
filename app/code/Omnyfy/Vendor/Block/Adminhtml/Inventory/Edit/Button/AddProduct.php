<?php
/**
 * Project: Vendor.
 * User: jing
 * Date: 30/1/18
 * Time: 10:00 AM
 */
namespace Omnyfy\Vendor\Block\Adminhtml\Inventory\Edit\Button;

class AddProduct implements \Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface
{
    public function getButtonData()
    {
        return [
            'label' => __('Add Product'),
            'class' => 'action-secondary',
            'data_attribute' => [
                'mage-init' => [
                    'Magento_Ui/js/form/button-adapter' => [
                        'actions' => [
                            [
                                'targetName' => 'omnyfy_vendor_inventory_form.omnyfy_vendor_inventory_form.add_product_modal',
                                'actionName' => 'toggleModal'
                            ],
                            [
                                'targetName' => 'omnyfy_vendor_inventory_form.omnyfy_vendor_inventory_form.add_product_modal.omnyfy_vendor_inventory_product_listing',
                                'actionName' => 'render'
                            ]
                        ]
                    ]
                ]
            ],
            'on_click' => '',
            'sort_order' => 20
        ];
    }
}