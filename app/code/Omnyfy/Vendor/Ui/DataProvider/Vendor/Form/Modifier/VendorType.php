<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-05-22
 * Time: 16:39
 */
namespace Omnyfy\Vendor\Ui\DataProvider\Vendor\Form\Modifier;

use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Form\Field;
use Omnyfy\Vendor\Api\Data\VendorAttributeInterface;
use Omnyfy\Vendor\Model\Locator\LocatorInterface;

use Omnyfy\Vendor\Model\Resource\VendorType\CollectionFactory as TypeCollectionFactory;

class VendorType extends AbstractModifier
{
    /**
     * Sort order of "Attribute Set" field inside of fieldset
     */
    const TYPE_ID_FIELD_ORDER = 30;

    /**
     * Set collection factory
     *
     * @var CollectionFactory
     */
    protected $attributeSetCollectionFactory;

    protected $typeCollectionFactory;
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @param LocatorInterface $locator
     * @param CollectionFactory $attributeSetCollectionFactory
     * @param TypeCollectionFactory $typeCollectionFactory
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        LocatorInterface $locator,
        CollectionFactory $attributeSetCollectionFactory,
        TypeCollectionFactory $typeCollectionFactory,
        UrlInterface $urlBuilder
    ) {
        $this->locator = $locator;
        $this->attributeSetCollectionFactory = $attributeSetCollectionFactory;
        $this->typeCollectionFactory = $typeCollectionFactory;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Return options for select
     *
     * @return array
     */
    public function getOptions()
    {
        $collection = $this->typeCollectionFactory->create();
        $collection->addFieldToFilter('status', \Omnyfy\Vendor\Api\Data\VendorInterface::STATUS_ENABLED);
        $result = [];
        foreach($collection as $type) {
            $result[] = [
                'value' => $type->getTypeId(),
                'label' => $type->getTypeName()
            ];
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        if ($name = $this->getGeneralPanelName($meta)) {
            $meta[$name]['children']['type_id']['arguments']['data']['config']  = [
                'component' => 'Magento_Catalog/js/components/attribute-set-select',
                'disableLabel' => true,
                'filterOptions' => true,
                'elementTmpl' => 'ui/grid/filters/elements/ui-select',
                'formElement' => 'select',
                'componentType' => Field::NAME,
                'options' => $this->getOptions(),
                'visible' => 1,
                'required' => 1,
                'label' => __('Vendor Type'),
                'source' => $name,
                'dataScope' => 'type_id',
                'filterUrl' => $this->urlBuilder->getUrl('omnyfy_vendor/vendor_store/suggestTypes', ['isAjax' => 'true']),
                'sortOrder' => $this->getNextAttributeSortOrder(
                    $meta,
                    [VendorAttributeInterface::CODE_STATUS],
                    self::TYPE_ID_FIELD_ORDER
                ),
                'multiple' => false,
            ];
        }

        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return array_replace_recursive($data, [
            $this->locator->getVendor()->getId() => [
                self::DATA_SOURCE_DEFAULT => [
                    'attribute_set_id' => $this->locator->getVendor()->getTypeId()
                ],
            ]
        ]);
    }
}