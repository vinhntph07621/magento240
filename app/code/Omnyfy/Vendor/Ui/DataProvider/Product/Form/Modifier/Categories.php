<?php
/**
 * Project: Hide New Category Button is vendor is logged in.
 * Author: seth
 * Date: 23/3/20
 * Time: 11:53 am
 **/

namespace Omnyfy\Vendor\Ui\DataProvider\Product\Form\Modifier;


use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\DB\Helper as DbHelper;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\UrlInterface;

class Categories extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\Categories
{
    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var CategoryCollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * Categories constructor.
     * @param LocatorInterface $locator
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param DbHelper $dbHelper
     * @param UrlInterface $urlBuilder
     * @param ArrayManager $arrayManager
     * @param \Magento\Backend\Model\Session $session
     */
    public function __construct(
        LocatorInterface $locator,
        CategoryCollectionFactory $categoryCollectionFactory,
        DbHelper $dbHelper,
        UrlInterface $urlBuilder,
        ArrayManager $arrayManager,
        \Magento\Backend\Model\Session $session
    ) {
        $this->locator = $locator;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->dbHelper = $dbHelper;
        $this->urlBuilder = $urlBuilder;
        $this->arrayManager = $arrayManager;
        $this->session = $session;
        parent::__construct($locator, $categoryCollectionFactory, $dbHelper, $urlBuilder, $arrayManager);
    }

    /**
     * Customize Categories field
     *
     * @param array $meta
     * @return array
     */
    protected function customizeCategoriesField(array $meta)
    {
        $fieldCode = 'category_ids';
        $elementPath = $this->arrayManager->findPath($fieldCode, $meta, null, 'children');
        $containerPath = $this->arrayManager->findPath(static::CONTAINER_PREFIX . $fieldCode, $meta, null, 'children');

        if (!$elementPath) {
            return $meta;
        }

        $vendorInfo = $this->session->getVendorInfo();
        if (!empty($vendorInfo) && isset($vendorInfo['is_vendor_admin'])) {
            // if vendor is logged in, hide the create_category_button
            $meta = $this->arrayManager->merge(
                $containerPath,
                $meta,
                [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Categories'),
                                'dataScope' => '',
                                'breakLine' => false,
                                'formElement' => 'container',
                                'componentType' => 'container',
                                'component' => 'Magento_Ui/js/form/components/group',
                                'scopeLabel' => __('[GLOBAL]'),
                            ],
                        ],
                    ],
                    'children' => [
                        $fieldCode => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'formElement' => 'select',
                                        'componentType' => 'field',
                                        'component' => 'Magento_Catalog/js/components/new-category',
                                        'filterOptions' => true,
                                        'chipsEnabled' => true,
                                        'disableLabel' => true,
                                        'levelsVisibility' => '1',
                                        'elementTmpl' => 'ui/grid/filters/elements/ui-select',
                                        'options' => $this->getCategoriesTree(),
                                        'listens' => [
                                            'index=create_category:responseData' => 'setParsed',
                                            'newOption' => 'toggleOptionSelected'
                                        ],
                                        'config' => [
                                            'dataScope' => $fieldCode,
                                            'sortOrder' => 10,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ]
                ]
            );
        }else {
            $meta = $this->arrayManager->merge(
                $containerPath,
                $meta,
                [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Categories'),
                                'dataScope' => '',
                                'breakLine' => false,
                                'formElement' => 'container',
                                'componentType' => 'container',
                                'component' => 'Magento_Ui/js/form/components/group',
                                'scopeLabel' => __('[GLOBAL]'),
                            ],
                        ],
                    ],
                    'children' => [
                        $fieldCode => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'formElement' => 'select',
                                        'componentType' => 'field',
                                        'component' => 'Magento_Catalog/js/components/new-category',
                                        'filterOptions' => true,
                                        'chipsEnabled' => true,
                                        'disableLabel' => true,
                                        'levelsVisibility' => '1',
                                        'elementTmpl' => 'ui/grid/filters/elements/ui-select',
                                        'options' => $this->getCategoriesTree(),
                                        'listens' => [
                                            'index=create_category:responseData' => 'setParsed',
                                            'newOption' => 'toggleOptionSelected'
                                        ],
                                        'config' => [
                                            'dataScope' => $fieldCode,
                                            'sortOrder' => 10,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'create_category_button' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'title' => __('New Category'),
                                        'formElement' => 'container',
                                        'additionalClasses' => 'admin__field-small',
                                        'componentType' => 'container',
                                        'component' => 'Magento_Ui/js/form/components/button',
                                        'template' => 'ui/form/components/button/container',
                                        'actions' => [
                                            [
                                                'targetName' => 'product_form.product_form.create_category_modal',
                                                'actionName' => 'toggleModal',
                                            ],
                                            [
                                                'targetName' =>
                                                    'product_form.product_form.create_category_modal.create_category',
                                                'actionName' => 'render'
                                            ],
                                            [
                                                'targetName' =>
                                                    'product_form.product_form.create_category_modal.create_category',
                                                'actionName' => 'resetForm'
                                            ]
                                        ],
                                        'additionalForGroup' => true,
                                        'provider' => false,
                                        'source' => 'product_details',
                                        'displayArea' => 'insideGroup',
                                        'sortOrder' => 20,
                                    ],
                                ],
                            ]
                        ]
                    ]
                ]
            );
        }

        return $meta;
    }
}