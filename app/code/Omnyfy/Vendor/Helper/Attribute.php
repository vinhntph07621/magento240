<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-08-01
 * Time: 12:45
 */
namespace Omnyfy\Vendor\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Attribute extends AbstractHelper
{
    protected $vendorTypeRepository;

    protected $vendorAttributeRepository;

    protected $locationAttributeRepository;

    protected $searchCriteriaBuilder;

    protected $allowedFields = [
        'is_global',
        'is_visible',
        'is_searchable',
        'is_filterable',
        'is_visible_on_front',
        'is_html_allowed_on_front',
        'is_filterable_in_search',
        'is_visible_in_advanced_search',
        'is_wysiwyg_enabled',
        'used_in_listing',
        'is_used_in_grid',
        'is_visible_in_grid',
        'is_filterable_in_grid',
        'used_for_sort_by',
        'used_in_form'
    ];

    public function __construct(
        Context $context,
        \Omnyfy\Vendor\Api\VendorTypeRepositoryInterface $vendorTypeRepository,
        \Omnyfy\Vendor\Api\VendorAttributeRepositoryInterface $vendorAttributeRepository,
        \Omnyfy\Vendor\Api\LocationAttributeRepositoryInterface $locationAttributeRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->vendorTypeRepository = $vendorTypeRepository;
        $this->vendorAttributeRepository = $vendorAttributeRepository;
        $this->locationAttributeRepository = $locationAttributeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        parent::__construct($context);
    }

    public function getVendorSignUpAttributes($vendorTypeId)
    {
        return $this->getVendorAttributesWithCondition($vendorTypeId,
            [
                'is_visible' => 1,
                'used_in_form' => 1
            ]
        );
    }

    public function getLocationFilterAttributes($vendorTypeId)
    {
        return $this->getLocationAttributesWithCondition($vendorTypeId,
            [
                'is_filterable' => 1,
                'is_filterable_in_search' => 1,
            ]
        );
    }

    public function getVendorAttributesWithCondition($vendorTypeId, $conditions)
    {
        $vendorType = $this->vendorTypeRepository->getById($vendorTypeId);

        $vendorAttributeSetId = $vendorType->getVendorAttributeSetId();

        $this->searchCriteriaBuilder->addFilter('attribute_set_id', $vendorAttributeSetId);
        foreach($conditions as $field => $value) {
            if (!in_array($field, $this->allowedFields)) {
                continue;
            }
            $this->searchCriteriaBuilder->addFilter($field, $value);
        }

        return $this->vendorAttributeRepository->getList($this->searchCriteriaBuilder->create());
    }

    public function getLocationAttributesWithCondition($vendorTypeId, $conditions)
    {
        $vendorType = $this->vendorTypeRepository->getById($vendorTypeId);

        $locationAttributeSetId = $vendorType->getLocationAttributeSetId();

        $this->searchCriteriaBuilder->addFilter('attribute_set_id', $locationAttributeSetId);
        foreach($conditions as $field => $value) {
            if (!in_array($field, $this->allowedFields)) {
                continue;
            }
            $this->searchCriteriaBuilder->addFilter($field, $value);
        }

        return $this->vendorAttributeRepository->getList($this->searchCriteriaBuilder->create());
    }
}