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



namespace Mirasvit\CustomerSegment\Model\Segment\Condition\Customer;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Model\AbstractModel;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Context;
use Mirasvit\CustomerSegment\Api\Service\Segment\AttributeServiceInterface;

class Newsletter extends AbstractCondition implements AttributeServiceInterface
{
    const SUBSCRIBED   = '1';
    const UNSUBSCRIBED = '0';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * Newsletter constructor.
     * @param ResourceConnection $resourceConnection
     * @param Context $context
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        Context $context
    ) {
        parent::__construct($context);

        $this->addData([
            'type'  => __CLASS__,
            'label' => __('Newsletter Subscription'),
        ]);

        $this->setValue(self::SUBSCRIBED);

        $this->resourceConnection = $resourceConnection;
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
     * Init list of available values
     * @return Newsletter
     */
    public function loadValueOptions()
    {
        $this->setValueOption([
            self::SUBSCRIBED   => __('subscribed'),
            self::UNSUBSCRIBED => __('not subscribed'),
        ]);

        return $this;
    }

    /**
     * Get HTML of condition string
     * @return string
     */
    public function asHtml()
    {
        $element = $this->getValueElementHtml();

        return $this->getTypeElementHtml()
            . __('Customer is %1 to newsletter.', $element)
            . $this->getRemoveLinkHtml();
    }

    /**
     * {@inheritdoc}
     */
    public function asString($format = '')
    {
        return __('Customer is %1 to newsletter.', $this->getValueName());
    }

    /**
     * Get type for value element.
     * @return string
     */
    public function getValueElementType()
    {
        return 'select';
    }

    /**
     * Implement this method because we change its HTML, and OperatorElementHtml is not rendered.
     * It's used for attribute value validation.
     * @return string
     */
    public function getOperator()
    {
        return '==';
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeValue(\Magento\Framework\DB\Adapter\AdapterInterface $adapter, AbstractModel $model)
    {
        $operator = $this->getValue() === self::SUBSCRIBED ? '=' : '<>';
        $select   = $adapter->select()
            ->from(['main' => $this->resourceConnection->getTableName('newsletter_subscriber')], new \Zend_Db_Expr('1'))
            ->where('main.store_id = ?', $model->getData('store_id'))
            ->where('main.subscriber_email = ?', $model->getData('email'))
            ->where("main.subscriber_status {$operator} ?", \Magento\Newsletter\Model\Subscriber::STATUS_SUBSCRIBED);

        return $adapter->fetchOne($select);
    }

    /**
     * {@inheritDoc}
     */
    public function validate(AbstractModel $model)
    {
        return $this->validateAttribute($this->getAttributeValue($this->resourceConnection->getConnection(), $model));
    }
}
