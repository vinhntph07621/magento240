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



namespace Mirasvit\CustomerSegment\Model\Segment\Condition\Order;


use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Rule\Model\Condition\Context;
use Magento\Sales\Api\OrderAddressRepositoryInterface;
use Mirasvit\CustomerSegment\Model\Segment\Condition\Customer\AbstractAddress;

/**
 * @method \Magento\Rule\Model\Condition\AbstractCondition[] getConditionOptions() - Added through the di.xml
 */
class Address extends AbstractAddress
{
    /**
     * @var OrderAddressRepositoryInterface
     */
    private $orderAddressRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * Address constructor.
     *
     * @param SearchCriteriaBuilder           $searchCriteriaBuilder
     * @param OrderAddressRepositoryInterface $orderAddressRepository
     * @param Context                         $context
     * @param array                           $data
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        OrderAddressRepositoryInterface $orderAddressRepository,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->setType(__CLASS__);
        $this->setValue(\Magento\Sales\Model\Order\Address::TYPE_BILLING);
        $this->orderAddressRepository = $orderAddressRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritDoc}
     */
    public function getConditionLabel()
    {
        return 'Order';
    }

    /**
     * {@inheritDoc}
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        \Magento\Framework\Profiler::start(__METHOD__);
        if (!$this->getConditions()) {
            return true;
        }

        $isValid = false;

        $this->searchCriteriaBuilder->addFilter('email', $model->getData('email'));
        if ($this->getValue() != self::TYPE_ANY) {
            $this->searchCriteriaBuilder->addFilter('address_type', $this->getValue());
        }

        $addressList = $this->orderAddressRepository->getList($this->searchCriteriaBuilder->create());
        /** @var \Magento\Sales\Model\Order\Address $address */
        foreach ($addressList->getItems() as $address) {
            if(parent::validate($address)) {
                $isValid = true;
                break;
            }
        }
        \Magento\Framework\Profiler::stop(__METHOD__);

        return $isValid;
    }
}