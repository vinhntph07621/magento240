<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 8/9/17
 * Time: 10:10 AM
 */
namespace Omnyfy\Vendor\Setup;

use Magento\Eav\Setup\EavSetup;

class VendorSetup extends EavSetup
{
    public function getDefaultEntities()
    {
        $locationEntity = \Omnyfy\Vendor\Model\Location::ENTITY;

        $vendorEntity = \Omnyfy\Vendor\Model\Vendor::ENTITY;

        $entities = [
            $locationEntity => [
                'entity_model' => 'Omnyfy\Vendor\Model\Resource\Location',
                'table' => $locationEntity. '_entity',
                'additional_attribute_table' => 'omnyfy_vendor_eav_attribute',
                'entity_attribute_collection' => 'Omnyfy\Vendor\Model\Resource\Location\Attribute\Collection',
                'attributes' => [
                    'vendor_id' => [
                        'type' => 'static',
                    ],
                    'priority' => [
                        'type' => 'static',
                    ],
                    'location_name' => [
                        'type' => 'static',
                    ],
                    'description' => [
                        'type' => 'static',
                    ],
                    'address' => [
                        'type' => 'static',
                    ],
                    'suburb' => [
                        'type' => 'static',
                    ],
                    'region' => [
                        'type' => 'static',
                    ],
                    'country' => [
                        'type' => 'static',
                    ],
                    'postcode' => [
                        'type' => 'static',
                    ],
                    'latitude' => [
                        'type' => 'static',
                    ],
                    'longitude' => [
                        'type' => 'static',
                    ],
                    'status' => [
                        'type' => 'static',
                    ]
                ]
            ],

            $vendorEntity => [
                'entity_model' => 'Omnyfy\Vendor\Model\Resource\Vendor',
                'table' => $vendorEntity. '_entity',
                'additional_attribute_table' => 'omnyfy_vendor_eav_attribute',
                'entity_attribute_collection' => 'Omnyfy\Vendor\Model\Resource\Vendor\Attribute\Collection',
                'attributes' => [
                    'name' => [
                        'type' => 'static',
                    ],
                    'status' => [
                        'type' => 'static',
                    ],
                    'address' => [
                        'type' => 'static',
                    ],
                    'phone' => [
                        'type' => 'static',
                    ],
                    'email' => [
                        'type' => 'static',
                    ],
                    'fax' => [
                        'type' => 'static',
                    ],
                    'social_media' => [
                        'type' => 'static',
                    ],
                    'description' => [
                        'type' => 'static',
                    ],
                    'abn' => [
                        'type' => 'static',
                    ],
                    'logo' => [
                        'type' => 'static',
                    ],
                    'banner' => [
                        'type' => 'static',
                    ],
                    'shipping_policy' => [
                        'type' => 'text',
                    ],
                    'return_policy' => [
                        'type' => 'text',
                    ],
                    'payment_policy' => [
                        'type' => 'text',
                    ],
                    'marketing_policy' => [
                        'type' => 'text',
                    ]
                ]
            ]

        ];

        return $entities;
    }
}