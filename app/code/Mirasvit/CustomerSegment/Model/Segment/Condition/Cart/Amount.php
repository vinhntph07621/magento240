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

/**
 * Class adds ability to validate current active shopping cart amount values
 */
class Amount extends AbstractCondition
{
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
     * {@inheritdoc}
     */
    protected $_inputType = 'numeric';

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
            'subtotal' => __('Subtotal'),
            'grand_total' => __('Grand Total'),
            'tax' => __('Tax'),
            'shipping' => __('Shipping'),
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . __('Shopping Cart %2 Amount %3 %4',
                $this->getData('label'),
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
        return __('Shopping Cart %1 Amount %2 %3',
            $this->getAttributeName(), $this->getOperatorName(), $this->getValueName()
        );
    }

    /**
     * {@inheritDoc}
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        $adapter = $this->resourceConnection->getConnection();
        $select = $adapter->select();

        $joinAddress = false;
        $operator = $this->operatorConverter->getSqlOperator($this->getData('operator'));
        switch($this->getData('attribute')) {
            case 'grand_total':
                $attribute = 'quote.base_grand_total';
                break;
            case 'subtotal':
                $attribute = 'quote.base_subtotal';
                break;
            case 'tax':
                $joinAddress = true;
                $attribute = 'address.base_tax_amount';
                break;
            case 'shipping':
                $joinAddress = true;
                $attribute = 'address.base_shipping_amount';
                break;
            default:
                throw new \UnexpectedValueException('Unknown Shopping Cart Amount type specified.');
        }


        $result = $adapter->getCheckSql("SUM({$attribute}) {$operator} {$this->getValue()}", 1, 0);
        $whereField = $model->getData('customer_id') ? 'quote.customer_id' : 'quote.customer_email';
        $whereValue = $model->getData('customer_id') ? $model->getData('customer_id') : $model->getData('email');

        $select->from(['quote' => $this->resourceConnection->getTableName('quote')],
                [new \Zend_Db_Expr($result)/*, new \Zend_Db_Expr("SUM({$attribute})")*/]
            )
            ->where('quote.is_active = 1')
            ->where('quote.store_id = ?', $model->getData('store_id'))
            ->where("{$whereField} = ?", $whereValue);

        if ($joinAddress) {
            $select->joinLeft(['address' => $this->resourceConnection->getTableName('quote_address')],
                'quote.entity_id = address.quote_id',
                []
            );
        }

        return (bool) $adapter->fetchOne($select);
    }
}