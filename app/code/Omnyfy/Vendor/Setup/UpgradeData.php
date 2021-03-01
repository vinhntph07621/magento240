<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 8/9/17
 * Time: 11:14 AM
 */
namespace Omnyfy\Vendor\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\App\State;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;

class UpgradeData implements UpgradeDataInterface
{
    protected $vendorSetupFactory;

    private $eavSetupFactory;

    private $widgetFactory;

    protected $scopeConfigInterface;

    protected $storeManager;

    /**
     * @var State
     */
    private $appState;

    public function __construct(
        \Omnyfy\Vendor\Setup\VendorSetupFactory $vendorSetupFactory,
        \Magento\Widget\Model\Widget\InstanceFactory $widgetFactory,
        //State $appState,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        EavSetupFactory $eavSetupFactory
    )
    {
        $this->vendorSetupFactory = $vendorSetupFactory;
        $this->widgetFactory = $widgetFactory;
        //$this->appState = $appState;
        //$this->scopeConfigInterface = $scopeConfig;
        //$this->storeManager = $storeManager;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /*
        $this->appState->emulateAreaCode(
            Area::AREA_FRONTEND,
            [$setup, $context]
        );
        */

        $setup->startSetup();

        $version = $context->getVersion();
        if (version_compare($version, '1.0.3', '<')) {

            $vendorSetup = $this->vendorSetupFactory->create(['setup' => $setup]);

            $vendorSetup->installEntities();

            $locationEntity = \Omnyfy\Vendor\Model\Location::ENTITY;
            $vendorSetup->addAttribute(
                $locationEntity, 'region_id', [
                    'type'          => 'static',
                    'input'         => 'select',
                    'required'      => true,
                    'sort_order'    => 400,
                    'visible'       => true,
                    'system'        => false,
                    'searchable'    => true,
                    'used_in_listing' => true,
                ]
            );

            $vendorSetup->removeEntityType('omnyfy_vendor');
            $vendorSetup->removeEntityType('omnyfy_location');
        }

        if (version_compare($version, '1.0.4', '<')) {
            $vendorSetup = $this->vendorSetupFactory->create(['setup' => $setup]);
            $locationEntity = \Omnyfy\Vendor\Model\Location::ENTITY;

            $vendorSetup->addAttribute(
                $locationEntity,
                'latitude',
                [
                    'type'          => 'decimal',
                    'label'         => 'Latitude',
                    'input'         => 'text',
                    'required'      => false,
                    'sort_order'    => 650,
                    'visible'       => true,
                    'system'        => false,
                    'searchable'    => true,
                    'used_in_listing' => true,
                ]
            );

            $vendorSetup->addAttribute(
                $locationEntity,
                'longitude',
                [
                    'type'          => 'decimal',
                    'label'         => 'Longitude',
                    'input'         => 'text',
                    'required'      => false,
                    'sort_order'    => 700,
                    'visible'       => true,
                    'system'        => false,
                    'searchable'    => true,
                    'used_in_listing' => true,
                ]
            );

            $vendorSetup->addAttribute(
                $locationEntity,
                'region_id',
                [
                    'type'          => 'static',
                    'label'         => 'Region ID',
                    'input'         => 'text',
                    'required'      => true,
                    'sort_order'    => 500,
                    'visible'       => true,
                    'system'        => false,
                    'searchable'    => true,
                    'used_in_listing' => true,
                ]
            );

            $vendorSetup->updateAttribute(
                $locationEntity,
                'region_id',
                [
                    'frontend_input' => 'text',
                ]
            );
        }

        if (version_compare($version, '1.0.10', '<')) {
            $vendorSetup = $this->vendorSetupFactory->create(['setup' => $setup]);
            $locationEntity = \Omnyfy\Vendor\Model\Location::ENTITY;

            $vendorSetup->addAttribute(
                $locationEntity,
                'is_warehouse',
                [
                    'type'          => 'static',
                    'label'         => 'Is Warehouse',
                    'input'         => 'select',
                    'required'      => true,
                    'sort_order'    => 650,
                    'visible'       => true,
                    'system'        => false,
                    'searchable'    => true,
                    'used_in_listing' => true,
                    'source_model'  => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean'
                ]
            );
        }

        if (version_compare($version, '1.0.13', '<')) {
            $vendorSetup = $this->vendorSetupFactory->create(['setup' => $setup]);
            $vendorEntity = \Omnyfy\Vendor\Model\Vendor::ENTITY;
            $locationEntity = \Omnyfy\Vendor\Model\Location::ENTITY;

            $attributeModel = $vendorSetup->getEntityType($vendorEntity, 'attribute_model');
            if ('Omnyfy\\Vendor\\Model\\Resource\\Vendor\\Eav\\Attribute' !== $attributeModel ){
                $vendorSetup->updateEntityType(
                    $vendorEntity,
                    'attribute_model',
                    'Omnyfy\\Vendor\\Model\\Resource\\Vendor\\Eav\\Attribute'
                );
            }

            $attributeModel = $vendorSetup->getEntityType($locationEntity, 'attribute_model');
            if ('Omnyfy\\Vendor\\Model\\Resource\\Eav\\Attribute' !== $attributeModel ){
                $vendorSetup->updateEntityType(
                    $locationEntity,
                    'attribute_model',
                    'Omnyfy\\Vendor\\Model\\Resource\\Eav\\Attribute'
                );
            }

            $vendorSetup->updateAttribute(
                $vendorEntity,
                'status',
                [
                    'frontend_input' => 'select',
                    'frontend_label' => 'Status',
                    'source_model' => 'Omnyfy\\Vendor\\Model\\Source\\Status'
                ]
            );

            $vendorSetup->updateAttribute(
                $vendorEntity,
                'status',
                [
                    'frontend_input' => 'select',
                    'frontend_label' => 'Status',
                    'source_model' => 'Omnyfy\\Vendor\\Model\\Source\\Status'
                ]
            );
        }

        if (version_compare($version, '1.0.14', '<')) {
            $vendorSetup = $this->vendorSetupFactory->create(['setup' => $setup]);
            $locationEntity = \Omnyfy\Vendor\Model\Location::ENTITY;

            $vendorSetup->updateAttribute(
                $locationEntity,
                'is_warehouse',
                [
                    'source_model' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean'
                ]
            );

            $vendorSetup->addAttribute(
                $locationEntity,
                'lon',
                [
                    'type'          => 'static',
                    'label'         => 'Longitude',
                    'input'         => 'text',
                    'required'      => true,
                    'sort_order'    => 650,
                    'visible'       => false,
                    'system'        => true,
                    'searchable'    => false,
                    'used_in_listing' => false
                ]
            );
            $vendorSetup->addAttribute(
                $locationEntity,
                'lat',
                [
                    'type'          => 'static',
                    'label'         => 'Latitude',
                    'input'         => 'text',
                    'required'      => true,
                    'sort_order'    => 650,
                    'visible'       => false,
                    'system'        => true,
                    'searchable'    => false,
                    'used_in_listing' => false
                ]
            );

            $vendorSetup->addAttribute(
                $locationEntity,
                'rad_lon',
                [
                    'type'          => 'static',
                    'label'         => 'Radians of Longitude',
                    'input'         => 'text',
                    'required'      => true,
                    'sort_order'    => 650,
                    'visible'       => false,
                    'system'        => true,
                    'searchable'    => true,
                    'used_in_listing' => true
                ]
            );
            $vendorSetup->addAttribute(
                $locationEntity,
                'rad_lat',
                [
                    'type'          => 'static',
                    'label'         => 'Radians of Latitude',
                    'input'         => 'text',
                    'required'      => true,
                    'sort_order'    => 650,
                    'visible'       => false,
                    'system'        => true,
                    'searchable'    => true,
                    'used_in_listing' => true
                ]
            );
            $vendorSetup->addAttribute(
                $locationEntity,
                'cos_lat',
                [
                    'type'          => 'static',
                    'label'         => 'Cosine of Latitude',
                    'input'         => 'text',
                    'required'      => true,
                    'sort_order'    => 650,
                    'visible'       => false,
                    'system'        => true,
                    'searchable'    => true,
                    'used_in_listing' => true
                ]
            );
            $vendorSetup->addAttribute(
                $locationEntity,
                'sin_lat',
                [
                    'type'          => 'static',
                    'label'         => 'Sine of Latitude',
                    'input'         => 'text',
                    'required'      => true,
                    'sort_order'    => 650,
                    'visible'       => false,
                    'system'        => true,
                    'searchable'    => true,
                    'used_in_listing' => true
                ]
            );
        }

        if (version_compare($version, '1.0.15', '<')) {
            $vendorSetup = $this->vendorSetupFactory->create(['setup' => $setup]);
            $vendorEntity = \Omnyfy\Vendor\Model\Vendor::ENTITY;
            $locationEntity = \Omnyfy\Vendor\Model\Location::ENTITY;

            $toRemove = ['fax', 'social_media'];
            foreach($toRemove as $code) {
                $vendorSetup->removeAttribute($vendorEntity, $code);
            }

            $toVarchar = ['abn'];
            foreach($toVarchar as $code) {
                $vendorSetup->updateAttribute($vendorEntity, $code, 'frontend_input', 'text');
            }

            $toTextarea = ['description', 'shipping_policy', 'return_policy', 'payment_policy', 'marketing_policy'];
            foreach($toTextarea as $code) {
                $vendorSetup->updateAttribute($vendorEntity, $code, 'frontend_input', 'textarea');
            }

            $toImage = ['logo', 'banner'];
            foreach($toImage as $code) {
                $vendorSetup->updateAttribute($vendorEntity, $code, 'frontend_input', 'image');
                $vendorSetup->updateAttribute($vendorEntity, $code, 'backend_model',
                    'Omnyfy\Vendor\Model\Vendor\Attribute\Backend\Media');
            }

            $toLabel = [
                'name' => 'Vendor Name',
                'status' => 'Status',
                'email' => 'Email',
                'abn' => 'ABN',
                'logo' => 'Logo',
                'banner' => 'Banner',
                'shipping_policy' => 'Shipping Policy',
                'return_policy' => 'Return Policy',
                'payment_policy' => 'Payment Policy',
                'marketing_policy' => 'Marketing Policy',
                'address' => 'Address',
                'phone' => 'Phone',
                'description' => 'Description'
            ];

            foreach($toLabel as $code => $label) {
                $vendorSetup->updateAttribute($vendorEntity, $code, 'frontend_label', $label);
            }

            $toLabel = [
                'vendor_id' => 'Vendor Id',
                'priority' => 'Priority',
                'location_name' => 'Location name',
                'description' => 'Description',
                'address' => 'Address',
                'suburb' => 'Suburb',
                'region' => 'Region',
                'country' => 'Country',
                'postcode' => 'Postcode',
                'latitude' => 'Latitude',
                'longitude' => 'Longitude',
                'status' => 'Status'
            ];

            foreach($toLabel as $code => $label) {
                $vendorSetup->updateAttribute($locationEntity, $code, 'frontend_label', $label);
            }
        }

        if (version_compare($version, '1.0.16', '<')) {
            $vendorSetup = $this->vendorSetupFactory->create(['setup' => $setup]);
            $conn = $setup->getConnection();

            $vendorDefaultAttributeSetId = $vendorSetup->getEntityType(\Omnyfy\Vendor\Model\Vendor::ENTITY, 'default_attribute_set_id');
            $locationDefaultAttributeSetId = $vendorSetup->getEntityType(\Omnyfy\Vendor\Model\Location::ENTITY, 'default_attribute_set_id');

            $tableName = $conn->getTableName('omnyfy_vendor_vendor_type');

            $conn->update(
                $tableName,
                [
                    'vendor_attribute_set_id' => $vendorDefaultAttributeSetId,
                    'location_attribute_set_id' => $locationDefaultAttributeSetId
                ],
                'type_id=1'
            );
        }


        if (version_compare($version, '1.0.24', '<')) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            if(!$eavSetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, 'omnyfy_dimensions_length')) {
                $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY,
                    'omnyfy_dimensions_length',
                    [
                        'type' => 'text',
                        'backend' => '',
                        'frontend' => '',
                        'label' => 'Omnyfy Dimension Length',
                        'input' => 'text',
                        'class' => '',
                        'source' => '',
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                        'visible' => true,
                        'required' => false,
                        'user_defined' => false,
                        'default' => '',
                        'searchable' => false,
                        'filterable' => false,
                        'comparable' => false,
                        'visible_on_front' => false,
                        'used_in_product_listing' => true,
                        'unique' => false,
                        'apply_to' => ''
                    ]
                );
            }

            if(!$eavSetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, 'omnyfy_dimensions_width')) {
                $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY,
                    'omnyfy_dimensions_width',
                    [
                        'type' => 'text',
                        'backend' => '',
                        'frontend' => '',
                        'label' => 'Omnyfy Dimension Width',
                        'input' => 'text',
                        'class' => '',
                        'source' => '',
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                        'visible' => true,
                        'required' => false,
                        'user_defined' => false,
                        'default' => '',
                        'searchable' => false,
                        'filterable' => false,
                        'comparable' => false,
                        'visible_on_front' => false,
                        'used_in_product_listing' => true,
                        'unique' => false,
                        'apply_to' => ''
                    ]
                );
            }

            if(!$eavSetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, 'omnyfy_dimensions_height')) {
                $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY,
                    'omnyfy_dimensions_height',
                    [
                        'type' => 'text',
                        'backend' => '',
                        'frontend' => '',
                        'label' => 'Omnyfy Dimension Height',
                        'input' => 'text',
                        'class' => '',
                        'source' => '',
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                        'visible' => true,
                        'required' => false,
                        'user_defined' => false,
                        'default' => '',
                        'searchable' => false,
                        'filterable' => false,
                        'comparable' => false,
                        'visible_on_front' => false,
                        'used_in_product_listing' => true,
                        'unique' => false,
                        'apply_to' => ''
                    ]
                );
            }

            $vendorSetup = $this->vendorSetupFactory->create(['setup' => $setup]);
            $vendorEntity = \Omnyfy\Vendor\Model\Vendor::ENTITY;
            $locationEntity = \Omnyfy\Vendor\Model\Location::ENTITY;

            $toLabel = [
                'address' => 'Address',
                'phone' => 'Phone',
                'description' => 'Description'
            ];

            foreach($toLabel as $code => $label) {
                $vendorSetup->updateAttribute($vendorEntity, $code, 'frontend_label', $label);
            }

            $toLabel = [
                'vendor_id' => 'Vendor Id',
                'priority' => 'Priority',
                'location_name' => 'Location name',
                'description' => 'Description',
                'address' => 'Address',
                'suburb' => 'Suburb',
                'region' => 'Region',
                'country' => 'Country',
                'postcode' => 'Postcode',
                'latitude' => 'Latitude',
                'longitude' => 'Longitude',
                'status' => 'Status'
            ];

            foreach($toLabel as $code => $label) {
                $vendorSetup->updateAttribute($locationEntity, $code, 'frontend_label', $label);
            }
        }

        if (version_compare($version, '1.0.25', '<')){
            $vendorSetup = $this->vendorSetupFactory->create(['setup' => $setup]);
            $vendorEntity = \Omnyfy\Vendor\Model\Vendor::ENTITY;
            $locationEntity = \Omnyfy\Vendor\Model\Location::ENTITY;

            $toHideLocation = [
                'vendor_id',
                'priority',
                'location_name',
                'description',
                'address',
                'suburb',
                'region',
                'country',
                'postcode',
                'status',
                'region_id',
                'is_warehouse',
                'rad_lon',
                'rad_lat',
                'cos_lat',
                'sin_lat',
                'lon',
                'lat',
                'latitude',
                'longitude',
            ];

            foreach($toHideLocation as $code ) {
                $vendorSetup->updateAttribute($locationEntity, $code, 'is_visible', 0);
            }


            $toHideVendor = [
                'name',
                'status',
                'email',
                'abn'
            ];

            foreach($toHideVendor as $code) {
                $vendorSetup->updateAttribute($vendorEntity, $code, 'is_visible', 0);
            }
        }

        if (version_compare($version, '1.0.26', '<')) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            if(!$eavSetup->getAttributeId(\Omnyfy\Vendor\Model\Vendor::ENTITY, 'vfree_shipping_config')) {
                $eavSetup->addAttribute(
                    \Omnyfy\Vendor\Model\Vendor::ENTITY,
                    'vfree_shipping_config',
                    [
                        'type' => 'int',
                        'label' => 'Enable Free Shipping Message',
                        'input' => 'select',
                        'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                        'required' => true,
                        'default' => '0',
                        'sort_order' => 100,
                        'visible' => true,
                        'system' => false,
                        'searchable' => true,
                        'used_in_listing' => true
                    ]
                );
            }

            if(!$eavSetup->getAttributeId(\Omnyfy\Vendor\Model\Vendor::ENTITY, 'vfree_shipping_threshold')) {
                $eavSetup->addAttribute(
                    \Omnyfy\Vendor\Model\Vendor::ENTITY,
                    'vfree_shipping_threshold',
                    [
                        'type' => 'text',
                        'backend' => '',
                        'frontend' => '',
                        'label' => 'Free Shipping Threshold',
                        'input' => 'text',
                        'class' => '',
                        'source' => '',
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                        'visible' => true,
                        'required' => false,
                        'user_defined' => false,
                        'default' => '',
                        'searchable' => false,
                        'filterable' => false,
                        'comparable' => false,
                        'visible_on_front' => false,
                        'used_in_product_listing' => false,
                        'unique' => false,
                        'apply_to' => ''
                    ]
                );
            }

            if(!$eavSetup->getAttributeId(\Omnyfy\Vendor\Model\Vendor::ENTITY, 'vfree_shipping_message')) {
                $eavSetup->addAttribute(
                    \Omnyfy\Vendor\Model\Vendor::ENTITY,
                    'vfree_shipping_message',
                    [
                        'type' => 'text',
                        'backend' => '',
                        'frontend' => '',
                        'label' => 'Free Shipping Message',
                        'input' => 'text',
                        'class' => '',
                        'source' => '',
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                        'visible' => true,
                        'required' => false,
                        'user_defined' => false,
                        'default' => '',
                        'searchable' => false,
                        'filterable' => false,
                        'comparable' => false,
                        'visible_on_front' => false,
                        'used_in_product_listing' => true,
                        'unique' => false,
                        'apply_to' => ''
                    ]
                );
            }

        if (version_compare($version, '1.0.28', '<')) {
            $vendorSetup = $this->vendorSetupFactory->create(['setup' => $setup]);
            $locationEntity = \Omnyfy\Vendor\Model\Location::ENTITY;

            if(!$vendorSetup->getAttributeId(\Omnyfy\Vendor\Model\Location::ENTITY, 'location_contact_name')) {
                $vendorSetup->addAttribute(
                    $locationEntity,
                    'location_contact_name',
                    [
                        'type'          => 'static',
                        'label'         => 'Location Contact Name',
                        'input'         => 'text',
                        'required'      => true,
                        'sort_order'    => 700,
                        'visible'       => false,
                        'system'        => false,
                        'searchable'    => true,
                        'used_in_listing' => true,
                    ]
                );
            }

            if(!$vendorSetup->getAttributeId(\Omnyfy\Vendor\Model\Location::ENTITY, 'location_contact_phone')) {
                $vendorSetup->addAttribute(
                    $locationEntity,
                    'location_contact_phone',
                    [
                        'type'          => 'static',
                        'label'         => 'Location Contact Phone',
                        'input'         => 'text',
                        'required'      => true,
                        'sort_order'    => 710,
                        'visible'       => false,
                        'system'        => false,
                        'searchable'    => true,
                        'used_in_listing' => true,
                    ]
                );
            }

            if(!$vendorSetup->getAttributeId(\Omnyfy\Vendor\Model\Location::ENTITY, 'location_contact_email')) {
                $vendorSetup->addAttribute(
                    $locationEntity,
                    'location_contact_email',
                    [
                        'type'          => 'static',
                        'label'         => 'Location Contact Email',
                        'input'         => 'text',
                        'required'      => true,
                        'sort_order'    => 720,
                        'visible'       => false,
                        'system'        => false,
                        'searchable'    => true,
                        'used_in_listing' => true,
                    ]
                );
            }
            if(!$vendorSetup->getAttributeId(\Omnyfy\Vendor\Model\Location::ENTITY, 'location_company_name')) {
                $vendorSetup->addAttribute(
                    $locationEntity,
                    'location_company_name',
                    [
                        'type'          => 'static',
                        'label'         => 'Location Company Name',
                        'input'         => 'text',
                        'required'      => true,
                        'sort_order'    => 730,
                        'visible'       => false,
                        'system'        => false,
                        'searchable'    => true,
                        'used_in_listing' => true,
                    ]
                );
            }
        }

            /*
            if (version_compare($version, '1.0.27', '<')) {

                $themeId = $this->scopeConfigInterface->getValue(
                    \Magento\Framework\View\DesignInterface::XML_PATH_THEME_ID,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $this->storeManager->getStore()->getId()
                );

                $productVendorWidget = [
                    'instance_type' => '\Omnyfy\Vendor\Block\Widget\ProductVendor',
                    'theme_id' => $themeId,
                    'title' => 'PDP Vendor',
                    'store_ids' => '0',
                    'widget_parameters' => '{"title":"Merchant","by_text":"Sold by"}',
                    'sort_order' => 1,
                    'page_groups' => [[
                        'page_group' => 'all_products',
                        'all_products' => [
                            'page_id' => null,
                            //'group' => 'all_pages',
                            'layout_handle' => 'catalog_product_view',
                            'block' => 'pdp.vendor',
                            'for' => 'all',
                            'template' => 'widget/brand.phtml'
                        ]
                    ]]
                ];

                $this->widgetFactory->create()->setData($productVendorWidget)->save();
            }*/

            if (version_compare($version, '1.0.27', '<')) {
                $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
                if($eavSetup->getAttributeId(\Omnyfy\Vendor\Model\Vendor::ENTITY, 'vfree_shipping_message')) {
                    $eavSetup->removeAttribute(
                        \Omnyfy\Vendor\Model\Vendor::ENTITY,
                        'vfree_shipping_message'
                    );
                }
            }
        }
    }
}
