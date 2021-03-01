<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-09
 * Time: 12:13
 */
namespace Omnyfy\Vendor\Ui\DataProvider\Vendor\TypeForm\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class Basic implements ModifierInterface
{
    const VENDOR_ATTRIBUTE_SET_FIELD_ORDER = 30;

    const LOCATION_ATTRIBUTE_SET_FIELD_ORDER = 40;

    protected $locator;

    protected $attributeSetCollectionFactory;

    protected $urlBuilder;

    protected $vendorTypeId;

    protected $locationTypeId;

    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $attributeSetCollectionFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Omnyfy\Vendor\Model\Resource\Vendor $vendorResource,
        \Omnyfy\Vendor\Model\Resource\Location $locationResource,
        \Omnyfy\Vendor\Model\Vendor\Type\Locator\LocatorInterface $locator
    )
    {
        $this->attributeSetCollectionFactory = $attributeSetCollectionFactory;
        $this->urlBuilder = $urlBuilder;
        $this->vendorTypeId = $vendorResource->getTypeId();
        $this->locationTypeId = $locationResource->getTypeId();
        $this->locator = $locator;
    }

    public function modifyData(array $data)
    {
        $model = $this->locator->getVendorType();
        $modelId = $model->getId();
        if (!isset($data[$modelId])) {
            $data[$modelId] = $model->getData();
            $data[$modelId]['vendor_type'] = $model->getData();
            $data[$modelId]['id'] = $modelId;
            $data[$modelId]['vendor_type']['id'] = $modelId;
        }
        return $data;
    }

    public function modifyMeta(array $meta)
    {
        $name = 'general';
        $meta[$name]['children']['vendor_attribute_set_id']['arguments']['data']['config']  = [
            'component' => 'Magento_Catalog/js/components/attribute-set-select',
            'disableLabel' => true,
            'filterOptions' => true,
            'elementTmpl' => 'ui/grid/filters/elements/ui-select',
            'formElement' => 'select',
            'componentType' => \Magento\Ui\Component\Form\Field::NAME,
            'options' => $this->getVendorSetOptions(),
            'visible' => 1,
            'required' => 1,
            'label' => __('Vendor Attribute Set'),
            'source' => 'vendor_type',
            'dataScope' => 'vendor_attribute_set_id',
            'filterUrl' => $this->urlBuilder->getUrl('omnyfy_vendor/vendor/suggestAttributeSets', ['isAjax' => 'true']),
            'sortOrder' => self::VENDOR_ATTRIBUTE_SET_FIELD_ORDER,
            'multiple' => false,
            'validation' => ['required-entry' => true]
        ];
        $meta[$name]['children']['location_attribute_set_id']['arguments']['data']['config']  = [
            'component' => 'Magento_Catalog/js/components/attribute-set-select',
            'disableLabel' => true,
            'filterOptions' => true,
            'elementTmpl' => 'ui/grid/filters/elements/ui-select',
            'formElement' => 'select',
            'componentType' => \Magento\Ui\Component\Form\Field::NAME,
            'options' => $this->getLocationSetOptions(),
            'visible' => 1,
            'required' => 1,
            'label' => __('Location Attribute Set'),
            'source' => 'vendor_type',
            'dataScope' => 'location_attribute_set_id',
            'filterUrl' => $this->urlBuilder->getUrl('omnyfy_vendor/location/suggestAttributeSets', ['isAjax' => 'true']),
            'sortOrder' => self::LOCATION_ATTRIBUTE_SET_FIELD_ORDER,
            'multiple' => false,
            'validation' => ['required-entry' => true]
        ];

        return $meta;
    }

    public function getVendorSetOptions()
    {
        /** @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $collection */
        $collection = $this->attributeSetCollectionFactory->create();
        $collection->setEntityTypeFilter($this->vendorTypeId)
            ->addFieldToSelect('attribute_set_id', 'value')
            ->addFieldToSelect('attribute_set_name', 'label')
            ->setOrder(
                'attribute_set_name',
                \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection::SORT_ORDER_ASC
            );

        return $collection->getData();
    }

    public function getLocationSetOptions()
    {
        /** @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $collection */
        $collection = $this->attributeSetCollectionFactory->create();
        $collection->setEntityTypeFilter($this->locationTypeId)
            ->addFieldToSelect('attribute_set_id', 'value')
            ->addFieldToSelect('attribute_set_name', 'label')
            ->setOrder(
                'attribute_set_name',
                \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection::SORT_ORDER_ASC
            );

        return $collection->getData();
    }
}
 