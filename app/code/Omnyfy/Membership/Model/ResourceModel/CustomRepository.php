<?php
namespace Omnyfy\Membership\Model\ResourceModel;

use \Magento\Eav\Model\Config;
use Magento\Customer\Model\CustomerRegistry;
class CustomRepository{

    /**
     * @var CustomerRegistry
     */
    protected $customerRegistry;

    /**
     * @var Config
     */
    protected $eavConfig;

    public function __construct(
        CustomerRegistry $customerRegistry,
        Config $eavConfig
    )
    {
        $this->customerRegistry = $customerRegistry;
        $this->eavConfig = $eavConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($customerId)
    {
        $customerModel = $this->customerRegistry->retrieve($customerId);
        return $customerModel->getDataModel();
    }

    public function getMembershipType($customerId)
    {
        $customerModel = $this->customerRegistry->retrieve($customerId);
        $attributeValue = $customerModel->getData('membership');
        $attribute = $this->eavConfig->getAttribute('customer', 'membership');
        return $attribute->getSource()->getOptionText($attributeValue);
    }
}

