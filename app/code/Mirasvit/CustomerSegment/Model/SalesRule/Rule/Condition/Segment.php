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



namespace Mirasvit\CustomerSegment\Model\SalesRule\Rule\Condition;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Context;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\CustomerSegment\Api\Data\Segment\CustomerInterface;
use Mirasvit\CustomerSegment\Api\Data\SegmentInterface;
use Mirasvit\CustomerSegment\Api\Repository\Segment\CustomerRepositoryInterface;
use Mirasvit\CustomerSegment\Api\Repository\SegmentRepositoryInterface;

class Segment extends AbstractCondition
{
    /**
     * @var SegmentRepositoryInterface
     */
    private $segmentRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @inheritdoc
     */
    protected $_inputType = 'grid';

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Segment constructor.
     * @param StoreManagerInterface $storeManager
     * @param CustomerRepositoryInterface $customerRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SegmentRepositoryInterface $segmentRepository
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        CustomerRepositoryInterface $customerRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SegmentRepositoryInterface $segmentRepository,
        Context $context,
        array $data = []
    ) {
        $this->storeManager          = $storeManager;
        $this->customerRepository    = $customerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->segmentRepository     = $segmentRepository;
        parent::__construct($context, $data);
    }

    /**
     * Method implemented for compatibility with Mirasvit Email conditions
     * @see \Mirasvit\Email\Model\Rule\Condition\AbstractCondition
     * @return string
     */
    public function getConditionGroup()
    {
        return __('Customer');
    }

    /**
     * Method implemented for compatibility with Mirasvit Email conditions
     * @see \Mirasvit\Email\Model\Rule\Condition\AbstractCondition
     * @inheritdoc
     */
    public function loadAttributeOptions()
    {
        $this->setData('attribute_option', ['segment_id' => __('Customer Segment')]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function loadValueOptions()
    {
        $options = [];
        $segments = $this->segmentRepository->getCollection();
        foreach ($segments as $segment) {
            $options[$segment->getId()] = $segment->getTitle();
        }

        $this->setData('value_option', $options);

        return $this;
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
     * Get HTML of condition string
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . __('Customer Segment %1 %2', $this->getOperatorElementHtml(), $this->getValueElementHtml())
            . $this->getRemoveLinkHtml();
    }

    /**
     * Only existing segment customers are validated.
     * @TODO validate customer even if it does not exist in any segment. For this:
     *       - validate customer and move it to segment
     *       - validate customer's segment in this condition
     *
     * @param \Magento\Framework\Model\AbstractModel|\Magento\Quote\Model\Quote\Address $model
     *
     * @inheritDoc
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        if ($model->getQuote()) {
            $email     = $model->getQuote()->getCustomerEmail();
            $websiteId = $model->getQuote()->getStore()->getWebsiteId();
        } else {
            $email     = $model->getData('customer_email');
            $websiteId = $this->storeManager->getStore($model->getData('store_id'))->getWebsiteId();
        }

        $customerSegments = $this->getCustomerSegmentIds($email, $websiteId);
        if (!$customerSegments) {
            return false;
        }

        return $this->validateAttribute($customerSegments);
    }

    /**
     * Get segment IDs to which customer belongs.
     *
     * @param string $email
     *
     * @param null $websiteId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getCustomerSegmentIds($email, $websiteId = null)
    {
        $segments = [];
        $this->searchCriteriaBuilder->addFilter(CustomerInterface::EMAIL, $email)
            ->addFilter(SegmentInterface::WEBSITE_ID, $websiteId);

        $items = $this->customerRepository->getList($this->searchCriteriaBuilder->create())->getItems();
        foreach ($items as $item) {
            $segments[] = $item->getSegmentId();
        }

        return $segments;
    }
}
