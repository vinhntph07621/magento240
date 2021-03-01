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



namespace Mirasvit\CustomerSegment\Model\Segment\Condition\Cart;


use Magento\Framework\App\ResourceConnection;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Context;
use Mirasvit\CustomerSegment\Api\Service\OperatorConversionInterface;

class Productsqty extends AbstractCondition
{
    const PRODUCT_QTY   = 'items_qty';
    const PRODUCT_COUNT = 'items_count';

    /**
     * @inheritdoc
     */
    protected $_inputType = 'numeric';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;
    /**
     * @var OperatorConversionInterface
     */
    private $operatorConverter;

    /**
     * Amount constructor.
     *
     * @param OperatorConversionInterface $operatorConverter
     * @param ResourceConnection          $resourceConnection
     * @param Context                     $context
     * @param array                       $data
     */
    public function __construct(
        OperatorConversionInterface $operatorConverter,
        ResourceConnection $resourceConnection,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->resourceConnection = $resourceConnection;
        $this->operatorConverter = $operatorConverter;
    }

    /**
     * Customize default operator input by type mapper for numeric type.
     *
     * {@inheritdoc}
     */
    public function getDefaultOperatorInputByType()
    {
        if (null === $this->_defaultOperatorInputByType) {
            parent::getDefaultOperatorInputByType();
            $this->_defaultOperatorInputByType['numeric'] = array('==', '!=', '>=', '>', '<=', '<');
        }

        return $this->_defaultOperatorInputByType;
    }

    /**
     * @inheritDoc
     */
    public function loadAttributeOptions()
    {
        $this->setData('attribute_option', [
            self::PRODUCT_COUNT => __('Count'),
            self::PRODUCT_QTY   => __('QTY'),
        ]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . __('Shopping Cart Products %1 %2 %3',
                $this->getAttributeElementHtml(),
                $this->getOperatorElementHtml(),
                $this->getValueElementHtml()
            )
            . $this->getRemoveLinkHtml();
    }

    /**
     * {@inheritdoc}
     */
    public function asString($format = '')
    {
        return __('Shopping Cart Products %1 %2 %3',
            $this->getAttributeName(), $this->getOperatorName(), $this->getValueName()
        );
    }

    /**
     * @inheritDoc
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        $adapter = $this->resourceConnection->getConnection();
        $select = $adapter->select();

        $res = $adapter->getCheckSql("SUM({$this->getAttribute()}) {$this->getOperator()} {$this->getValue()}", 1, 0);
        $whereField = $model->getData('customer_id') ? 'quote.customer_id' : 'quote.customer_email';
        $whereValue = $model->getData('customer_id') ? $model->getData('customer_id') : $model->getData('email');

        $select->from(['quote' => $this->resourceConnection->getTableName('quote')],
                [new \Zend_Db_Expr($res), new \Zend_Db_Expr("SUM({$this->getAttribute()})")]
            )
            ->where('quote.is_active = 1')
            ->where('quote.store_id = ?', $model->getData('store_id'))
            ->where("{$whereField} = ?", $whereValue);

        return (bool) $adapter->fetchOne($select);
    }
}