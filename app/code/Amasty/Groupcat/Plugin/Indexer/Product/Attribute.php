<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Groupcat
 */

namespace Amasty\Groupcat\Plugin\Indexer\Product;

use Amasty\Groupcat\Model\Indexer\Rule\RuleProductProcessor;
use Amasty\Groupcat\Model\ResourceModel\Rule\CollectionFactory as RuleCollectionFactory;
use Amasty\Groupcat\Model\Rule;
use Magento\CatalogRule\Model\Rule\Condition\Combine;
use Magento\Framework\Message\ManagerInterface;
use Magento\Rule\Model\Condition\Product\AbstractProduct;

class Attribute
{
    /**
     * @var RuleCollectionFactory
     */
    protected $ruleCollectionFactory;

    /**
     * @var RuleProductProcessor
     */
    protected $ruleIndexProcessor;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @param RuleCollectionFactory $ruleCollectionFactory
     * @param RuleProductProcessor $ruleIndexProcessor
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        RuleCollectionFactory $ruleCollectionFactory,
        RuleProductProcessor $ruleIndexProcessor,
        ManagerInterface $messageManager
    ) {
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->ruleIndexProcessor    = $ruleIndexProcessor;
        $this->messageManager        = $messageManager;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $subject
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute
     * @return \Magento\Catalog\Model\ResourceModel\Eav\Attribute
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave(
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $subject,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute
    ) {
        if ($attribute->dataHasChangedFor('is_used_for_promo_rules') && !$attribute->getIsUsedForPromoRules()) {
            $this->checkRulesAvailability($attribute->getAttributeCode());
        }
        return $attribute;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $subject
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute
     * @return \Magento\Catalog\Model\ResourceModel\Eav\Attribute
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDelete(
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $subject,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute
    ) {
        if ($attribute->getIsUsedForPromoRules()) {
            $this->checkRulesAvailability($attribute->getAttributeCode());
        }
        return $attribute;
    }

    /**
     * Check rules that contains affected attribute
     * If rules were found they will be set to inactive and notice will be add to admin session
     *
     * @param string $attributeCode
     * @return $this
     */
    protected function checkRulesAvailability($attributeCode)
    {
        /** @var $collection \Amasty\Groupcat\Model\ResourceModel\Rule\Collection */
        $collection = $this->ruleCollectionFactory->create()->addAttributeInConditionFilter($attributeCode);

        $disabledRulesCount = 0;
        foreach ($collection as $rule) {
            /** @var $rule Rule */
            $rule->setIsActive(0);
            /** @var $rule->getConditions() Combine */
            $this->removeAttributeFromConditions($rule->getConditions(), $attributeCode);
            $rule->save();

            $disabledRulesCount++;
        }

        if ($disabledRulesCount) {
            $this->ruleIndexProcessor->markIndexerAsInvalid();
            $this->messageManager->addWarningMessage(
                __(
                    'You disabled %1 Amasty Customer Group Catalog Rules based on "%2" attribute.',
                    $disabledRulesCount,
                    $attributeCode
                )
            );
        }

        return $this;
    }

    /**
     * Remove catalog attribute condition by attribute code from rule conditions
     *
     * @param Combine $combine
     * @param string $attributeCode
     * @return void
     */
    protected function removeAttributeFromConditions(Combine $combine, $attributeCode)
    {
        $conditions = $combine->getConditions();
        foreach ($conditions as $conditionId => $condition) {
            if ($condition instanceof Combine) {
                $this->removeAttributeFromConditions($condition, $attributeCode);
            }
            if ($condition instanceof AbstractProduct) {
                if ($condition->getAttribute() == $attributeCode) {
                    unset($conditions[$conditionId]);
                }
            }
        }
        $combine->setConditions($conditions);
    }
}
