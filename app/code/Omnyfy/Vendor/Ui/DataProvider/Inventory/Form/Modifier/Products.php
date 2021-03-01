<?php
/**
 * Project: Multi Vendors.
 * User: jing
 * Date: 31/1/18
 * Time: 2:21 PM
 */
namespace Omnyfy\Vendor\Ui\DataProvider\Inventory\Form\Modifier;

class Products implements \Magento\Ui\DataProvider\Modifier\ModifierInterface
{
    protected $urlBuilder;

    protected $_backendSession;

    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Backend\Model\Session $backendSession
    )
    {
        $this->urlBuilder = $urlBuilder;
        $this->_backendSession = $backendSession;
    }

    public function modifyMeta(array $meta)
    {
        $locationId = $this->_backendSession->getCurrentLocationId();
        $listingTarget = 'omnyfy_vendor_inventory_product_listing';

        $meta['add_product_modal']=[
            'arguments' => [
                'data' => [
                    'config' => [
                        'isTemplate' => false,
                        'componentType' => \Magento\Ui\Component\Modal::NAME,
                        'dataScope' => '',
                        'provider' => 'omnyfy_vendor_inventory_form.omnyfy_vendor_inventory_form_data_source',
                        'options' => [
                            'title' => __('Add Product'),
                            'buttons' => [
                                [
                                    'text' => __('Cancel'),
                                    'actions' => [
                                        'closeModal'
                                    ]
                                ],
                                [
                                    'text' => __('Add Selected Products'),
                                    'class' => 'action-primary',
                                    'actions' => [
                                        [
                                            'targetName' => 'index = omnyfy_vendor_inventory_product_listing',
                                            'actionName' => 'save'
                                        ],
                                        'closeModal'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'children' => [
                'omnyfy_vendor_inventory_product_listing' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'component' => 'Omnyfy_Vendor/js/components/product-insert-listing',
                                'componentType' => \Magento\Ui\Component\Container::NAME,
                                'autoRender' => false,
                                'dataScope' => $listingTarget,
                                'externalProvider' => $listingTarget . '.' . $listingTarget . '_data_source',
                                'selectionsProvider' => $listingTarget . '.' . $listingTarget . '.omnyfy_vendor_inventory_product_columns.ids',
                                'ns' => $listingTarget,
                                'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                                'immediateUpdateBySelection' => true,
                                'behaviourType' => 'simple',
                                'externalFilterMode' => true,
                                'dataLinks' => [
                                    'imports' => false,
                                    'exports' => true
                                ],
                                'formProvider' => 'ns=${ $.namespace }, index = omnyfy_vendor_inventory_form',
                                'addProductUrl' => $this->urlBuilder->getUrl('omnyfy_vendor/inventory/addProductToLocation'),
                                'locationId' => $locationId,
                                'loading' => false,

                                'imports' => [
                                    'locationId' => '${ $.provider }:data.location.current_location_id'
                                ],
                                'exports' => [
                                    'locationId' => '${ $.externalProvider }:params.current_location_id'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        return $meta;
    }

    public function modifyData(array $data)
    {
        //$locationId = $this->_backendSession->getCurrentLocationId();
        //$data[$locationId]['current_location_id'] = $locationId;
        return $data;
    }
}