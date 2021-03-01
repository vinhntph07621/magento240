<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-customer-segment
 * @version   1.0.51
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */




namespace Mirasvit\CustomerSegment\Model\Segment\Condition\FollowUpEmail;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Context;
use Mirasvit\CustomerSegment\Api\Service\Segment\AttributeServiceInterface;

class Subscription extends AbstractCondition implements AttributeServiceInterface
{
    const ALL = 0;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var ModuleManager
     */
    private $moduleManager;

    public function __construct(
        ResourceConnection $resourceConnection,
        Context $context,
        ModuleManager $moduleManager
    ) {
        parent::__construct($context);

        $this->resourceConnection = $resourceConnection;
        $this->moduleManager      = $moduleManager;

        $this->addData([
            'type'  => __CLASS__,
            'label' => __('Follow Up Email Subscription'),
        ]);

        $this->setValue(self::ALL);
    }

    /**
     * @inheritDoc
     */
    public function loadOperatorOptions()
    {
        parent::loadOperatorOptions();
        $this->setData('operator_option', [
            '()' => __('one of these'),
            '==' => __('all of these'),
        ]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getNewChildSelectOptions()
    {
        return [
            [
                'value' => $this->getData('type'),
                'label' => $this->getData('label'),
            ],
        ];
    }

    /**
     * Get HTML of condition string
     * @return string
     */
    public function asHtml()
    {
        if (!$this->isFollowUpEmailEnabled()) {
            return $this->getTypeElementHtml()
                . __('Follow Up Email condition can only be used if the Follow Up Email extension is installed and enabled')
                . $this->getRemoveLinkHtml();
        }

        $element = $this->getValueElementHtml();

        return $this->getTypeElementHtml()
            . __(
                'Customer is unsubscribed from %1 Follow Up Email triggers: %2.',
                $this->getOperatorElementHtml(),
                $element
            )
            . $this->getRemoveLinkHtml();
    }

    /**
     * {@inheritdoc}
     */
    public function asString($format = '')
    {
        if (!$this->isFollowUpEmailEnabled()) {
            return $this->getTypeElementHtml()
                . __('Follow Up Email condition can only be used if the Follow Up Email extension is installed and enabled');
        }

        return __(
            'Customer is unsubscribed from %1 Follow Up Email triggers: %2.',
            $this->getOperatorName(),
            $this->getValueName()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeValue(\Magento\Framework\DB\Adapter\AdapterInterface $adapter, AbstractModel $model)
    {
        $select = $adapter->select()
            ->from(['main' => $this->resourceConnection->getTableName('mst_email_unsubscription')], ['main.trigger_id'])
            ->where('main.email = "' . $model->getEmail() . '"');

        $result = $adapter->fetchAll($select);
        $result = array_map(function ($row) {
            return $row['trigger_id'];
        }, $result);

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function validate(AbstractModel $model)
    {
        if (!$this->isFollowUpEmailEnabled()) {
            return true;
        }
        $triggers = $this->getAttributeValue($this->resourceConnection->getConnection(), $model);
        $values   = $this->getValue();

        if (!count($triggers)) {
            return false;
        }

        switch ($this->getOperator()) {
            case '()':
                if (in_array(self::ALL, $values)) {
                    return true;
                }

                foreach ($triggers as $trigger) {
                    if (in_array($trigger, $values)) {
                        return true;
                    }
                }

                return false;

            case '==':
                if (in_array(self::ALL, $values)) {
                    return in_array(self::ALL, $triggers);
                }

                return !count(array_diff($values, $triggers));

            default:
                return true;
        }
    }

    /**
     * Get type for value element.
     * @return string
     */
    public function getValueElementType()
    {
        return 'multiselect';
    }

    /**
     * @return $this|AbstractCondition
     */
    public function loadValueOptions()
    {
        if (!$this->isFollowUpEmailEnabled()) {
            return $this;
        }

        if (!$this->resourceConnection) {
            $this->resourceConnection = ObjectManager::getInstance()->get(ResourceConnection::class);
        }

        $adapter = $this->resourceConnection->getConnection();
        $select  = $adapter->select();

        $select->from(
            ['main' => $this->resourceConnection->getTableName('mst_email_trigger')],
            ['trigger_id', 'title']
        );

        $result   = $adapter->fetchAll($select);
        $triggers = [self::ALL => __('All')];

        foreach ($result as $trigger) {
            $triggers[$trigger['trigger_id']] = __($trigger['title']);
        }

        $this->setValueOption($triggers);

        return $this;
    }

    /**
     * @return bool
     */
    private function isFollowUpEmailEnabled()
    {
        if (!$this->moduleManager) {
            $this->moduleManager = ObjectManager::getInstance()->get(ModuleManager::class);
        }
        return $this->moduleManager->isEnabled('Mirasvit_Email');
    }
}
