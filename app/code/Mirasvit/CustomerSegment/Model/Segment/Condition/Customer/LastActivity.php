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
use Magento\Rule\Model\Condition\Context;
use Mirasvit\CustomerSegment\Model\Segment\Condition\AbstractCondition;

class LastActivity extends AbstractCondition
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * LastActivity constructor.
     * @param ResourceConnection $resource
     * @param Context $context
     * @param array $data
     * @throws \Exception
     */
    public function __construct(
        ResourceConnection $resource,
        Context $context,
        array $data = []
    ) {
        $this->resource = $resource;

        $data['label'] = __('Last Activity');

        parent::__construct($context, $data);
    }

    /**
     * @return array|mixed
     */
    public function getAttributeOption()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . __(
                'Last Activity was %1 %2 days ago',
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
        return __(
            'Last Activity was %1 %2 days ago',
            $this->getOperatorName(),
            $this->getValueName()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function validate(AbstractModel $model)
    {
        /** @var \Mirasvit\CustomerSegment\Model\Candidate $model */
        $customerId = $model->getCustomerId();

        if (!$customerId) {
            return false;
        }

        $connection = $this->resource->getConnection();

        //customer_log
        $selectA = $connection->select()
            ->from(
                $this->resource->getTableName('customer_log'),
                ['date' => new \Zend_Db_Expr('MAX(last_login_at)')]
            )->where('customer_id = ?', $customerId);

        //customer_visitor
        $selectB = $connection->select()
            ->from(
                $this->resource->getTableName('customer_visitor'),
                ['date' => new \Zend_Db_Expr('MAX(last_visit_at)')]
            )->where('customer_id = ?', $customerId);

        //sales_order
        $selectC = $connection->select()
            ->from(
                $this->resource->getTableName('sales_order'),
                ['date' => new \Zend_Db_Expr('MAX(created_at)')]
            )->where('customer_id = ?', $customerId);

        //customer_entity
        $selectD = $connection->select()
            ->from(
                $this->resource->getTableName('customer_entity'),
                ['date' => new \Zend_Db_Expr('MAX(created_at)')]
            )->where('entity_id = ?', $customerId);

        $unitedSelect = $this->resource->getConnection()->select()
            ->from(
                $connection->select()->union([$selectA, $selectB, $selectC, $selectD]),
                ['date' => new \Zend_Db_Expr('MAX(date)')]
            );

        $date = $connection->fetchOne($unitedSelect);

        if (!$date) {
            return false;
        }

        $daysAgo = round((time() - strtotime($date)) / (60 * 60 * 24));

        return $this->validateAttribute($daysAgo);
    }
}
