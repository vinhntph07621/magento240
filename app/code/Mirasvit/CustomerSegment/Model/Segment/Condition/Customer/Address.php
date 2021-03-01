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

use Magento\Customer\Model\AddressRegistry;
use Magento\Customer\Model\ResourceModel\Address\Collection as AddressCollection;
use Magento\Customer\Model\ResourceModel\Address\CollectionFactory as AddressCollectionFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Rule\Model\Condition\Context;
use Magento\Sales\Api\OrderAddressRepositoryInterface;

/**
 * @method \Magento\Rule\Model\Condition\AbstractCondition[] getConditionOptions() - Added through the di.xml
 */
class Address extends AbstractAddress
{
    /**
     * @var AddressRegistry
     */
    private $addressRegistry;

    /**
     * @var AddressCollectionFactory
     */
    private $addressCollectionFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var OrderAddressRepositoryInterface
     */
    private $orderAddressRepository;

    /**
     * Address constructor.
     * @param OrderAddressRepositoryInterface $orderAddressRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param AddressCollectionFactory $addressCollectionFactory
     * @param AddressRegistry $addressRegistry
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        OrderAddressRepositoryInterface $orderAddressRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        AddressCollectionFactory $addressCollectionFactory,
        AddressRegistry $addressRegistry,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->setData('type', __CLASS__);
        $this->setData('value', \Magento\Sales\Model\Order\Address::TYPE_BILLING);
        $this->addressRegistry          = $addressRegistry;
        $this->addressCollectionFactory = $addressCollectionFactory;
        $this->searchCriteriaBuilder    = $searchCriteriaBuilder;
        $this->orderAddressRepository   = $orderAddressRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function getConditionLabel()
    {
        return 'Customer';
    }

    /**
     * Init list of available values.
     * @return Address
     */
    public function loadValueOptions()
    {
        $this->setData('value_option', [
            \Magento\Sales\Model\Order\Address::TYPE_BILLING  => __('Billing*'),
            \Magento\Sales\Model\Order\Address::TYPE_SHIPPING => __('Shipping*'),
            self::TYPE_ANY                                    => __('Any'),
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        if (!$this->getConditions()) {
            return true;
        }

        \Magento\Framework\Profiler::start(__METHOD__);
        $result = false;
        if ($this->getValue() != self::TYPE_ANY) { // Validate specific address type - Default Shipping/Billing
            $addressId = $model->getData('default_' . $this->getValue());
            if ($addressId) {
                $result = parent::validate($this->addressRegistry->retrieve($addressId));
            }
        } else { // Validate ANY customers' address
            if ($model->getData('customer_id')) { // If Registered customer - validate only saved addresses
                /** @var AddressCollection|\Magento\Sales\Model\Order\Address[] $addresses */
                $addresses = $this->addressCollectionFactory->create()
                    ->addFieldToFilter('parent_id', $model->getData('customer_id'));
            } else { // Otherwise validate ANY addresses used in orders
                $searchCriteria = $this->searchCriteriaBuilder->addFilter('email', $model->getData('email'))->create();
                /** @var AddressCollection|\Magento\Sales\Model\Order\Address[] $addresses */
                $addresses      = $this->orderAddressRepository->getList($searchCriteria)->getItems();
            }

            foreach ($addresses as $address) {
                if (parent::validate($address)) {
                    $result = true;
                }
            }
        }
        \Magento\Framework\Profiler::stop(__METHOD__);

        return $result;
    }
}
