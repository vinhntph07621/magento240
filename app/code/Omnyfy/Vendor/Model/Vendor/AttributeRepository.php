<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 11/9/17
 * Time: 12:16 PM
 */
namespace Omnyfy\Vendor\Model\Vendor;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Eav\Api\Data\AttributeInterface;

class AttributeRepository implements \Omnyfy\Vendor\Api\VendorAttributeRepositoryInterface
{
    protected $eavAttributeRepository;

    protected $searchCriteriaBuilder;

    protected $eavConfig;

    protected $inputtypeValidatorFactory;

    protected $attributeResource;

    protected $vendorHelper;

    public function __construct(
        \Magento\Eav\Api\AttributeRepositoryInterface $eavAttributeRepository,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\ValidatorFactory $validatorFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Omnyfy\Vendor\Model\Resource\Vendor\Attribute $attributeResource,
        \Omnyfy\Vendor\Helper\Vendor $vendorHelper
    )
    {
        $this->eavAttributeRepository = $eavAttributeRepository;
        $this->eavConfig = $eavConfig;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->attributeResource = $attributeResource;
        $this->vendorHelper = $vendorHelper;
        $this->inputtypeValidatorFactory = $validatorFactory;
    }

    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        return $this->eavAttributeRepository->getList(
            \Omnyfy\Vendor\Api\Data\VendorAttributeInterface::ENTITY_TYPE_CODE,
            $searchCriteria
        );
    }

    public function get($attributeCode)
    {
        return $this->eavAttributeRepository->get(
            \Omnyfy\Vendor\Api\Data\VendorAttributeInterface::ENTITY_TYPE_CODE,
            $attributeCode
        );
    }

    public function getCustomAttributesMetadata($dataObjectClassName = null)
    {
        return $this->getList($this->searchCriteriaBuilder->create())->getItems();
    }

    public function save(\Omnyfy\Vendor\Api\Data\VendorAttributeInterface $attribute)
    {
        if ($attribute->getAttributeId()) {
            $existingModel = $this->get($attribute->getAttributeCode());

            if (!$existingModel->getAttributeId()) {
                throw NoSuchEntityException::singleField('attribute_code', $existingModel->getAttributeCode());
            }

            // Attribute code must not be changed after attribute creation
            $attribute->setAttributeCode($existingModel->getAttributeCode());
            $attribute->setAttributeId($existingModel->getAttributeId());
            $attribute->setIsUserDefined($existingModel->getIsUserDefined());
            $attribute->setFrontendInput($existingModel->getFrontendInput());

            if (is_array($attribute->getFrontendLabels())) {
                $defaultFrontendLabel = $attribute->getDefaultFrontendLabel();
                $frontendLabel[0] = !empty($defaultFrontendLabel)
                    ? $defaultFrontendLabel
                    : $existingModel->getDefaultFrontendLabel();
                foreach ($attribute->getFrontendLabels() as $item) {
                    $frontendLabel[$item->getStoreId()] = $item->getLabel();
                }
                $attribute->setDefaultFrontendLabel($frontendLabel);
            }
        } else {
            $attribute->setAttributeId(null);

            if (!$attribute->getFrontendLabels() && !$attribute->getDefaultFrontendLabel()) {
                throw InputException::requiredField('frontend_label');
            }

            $frontendLabels = [];
            if ($attribute->getDefaultFrontendLabel()) {
                $frontendLabels[0] = $attribute->getDefaultFrontendLabel();
            }
            if ($attribute->getFrontendLabels() && is_array($attribute->getFrontendLabels())) {
                foreach ($attribute->getFrontendLabels() as $label) {
                    $frontendLabels[$label->getStoreId()] = $label->getLabel();
                }
                if (!isset($frontendLabels[0]) || !$frontendLabels[0]) {
                    throw InputException::invalidFieldValue('frontend_label', null);
                }

                $attribute->setDefaultFrontendLabel($frontendLabels);
            }
            $attribute->setAttributeCode(
                $attribute->getAttributeCode() ?: $this->generateCode($frontendLabels[0])
            );
            $this->validateCode($attribute->getAttributeCode());
            $this->validateFrontendInput($attribute->getFrontendInput());

            $attribute->setBackendType(
                $attribute->getBackendTypeByInput($attribute->getFrontendInput())
            );
            $attribute->setSourceModel(
                $this->vendorHelper->getAttributeSourceModelByInputType($attribute->getFrontendInput())
            );
            $attribute->setBackendModel(
                $this->vendorHelper->getAttributeBackendModelByInputType($attribute->getFrontendInput())
            );
            $attribute->setEntityTypeId(
                $this->eavConfig
                    ->getEntityType(\Omnyfy\Vendor\Api\Data\VendorAttributeInterface::ENTITY_TYPE_CODE)
                    ->getId()
            );
            $attribute->setIsUserDefined(1);
        }
        if (!empty($attribute->getData(AttributeInterface::OPTIONS))) {
            $options = [];
            $sortOrder = 0;
            $default = [];
            $optionIndex = 0;
            foreach ($attribute->getOptions() as $option) {
                $optionIndex++;
                $optionId = $option->getValue() ?: 'option_' . $optionIndex;
                $options['value'][$optionId][0] = $option->getLabel();
                $options['order'][$optionId] = $option->getSortOrder() ?: $sortOrder++;
                if (is_array($option->getStoreLabels())) {
                    foreach ($option->getStoreLabels() as $label) {
                        $options['value'][$optionId][$label->getStoreId()] = $label->getLabel();
                    }
                }
                if ($option->getIsDefault()) {
                    $default[] = $optionId;
                }
            }
            $attribute->setDefault($default);
            if (count($options)) {
                $attribute->setOption($options);
            }
        }
        $this->attributeResource->save($attribute);
        return $this->get($attribute->getAttributeCode());
    }

    public function delete(\Omnyfy\Vendor\Api\Data\VendorAttributeInterface $attribute)
    {
        $this->attributeResource->delete($attribute);
        return true;
    }

    public function deleteById($attributeCode)
    {
        $this->delete(
            $this->get($attributeCode)
        );
        return true;
    }

    protected function validateCode($code)
    {
        $validatorAttrCode = new \Zend_Validate_Regex(['pattern' => '/^[a-z][a-z_0-9]{0,30}$/']);
        if (!$validatorAttrCode->isValid($code)) {
            throw InputException::invalidFieldValue('attribute_code', $code);
        }
    }

    protected function validateFrontendInput($frontendInput)
    {
        /** @var \Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\Validator $validator */
        $validator = $this->inputtypeValidatorFactory->create();
        if (!$validator->isValid($frontendInput)) {
            throw InputException::invalidFieldValue('frontend_input', $frontendInput);
        }
    }
}