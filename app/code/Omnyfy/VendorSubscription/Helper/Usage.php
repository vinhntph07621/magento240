<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-08-16
 * Time: 11:12
 */
namespace Omnyfy\VendorSubscription\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Omnyfy\VendorSubscription\Model\Source\UsageType;

class Usage extends AbstractHelper
{
    protected $usageCollectionFactory;

    protected $usageResource;

    protected $usageType;

    public function __construct(
        Context $context,
        \Omnyfy\VendorSubscription\Model\Resource\Usage\CollectionFactory $usageCollectionFactory,
        \Omnyfy\VendorSubscription\Model\Resource\Usage $usageResource,
        UsageType $usageType
    ) {
        $this->usageCollectionFactory = $usageCollectionFactory;
        $this->usageResource = $usageResource;
        $this->usageType = $usageType;
        parent::__construct($context);
    }

    public function loadUsageByVendorId($vendorId, $mostAvailableFirst = false)
    {
        if (empty($vendorId)) {
            return false;
        }

        /**
         * @var \Omnyfy\VendorSubscription\Model\Resource\Usage\Collection $collection
         */
        $collection = $this->usageCollectionFactory->create();

        $collection->addVendorFilter($vendorId)
            ->addNowFilter()
            ->addRemainOrder($mostAvailableFirst);

        $result = [];
        foreach($collection as $usage) {
            if (!array_key_exists($usage->getUsageTypeId(), $result)) {
                $result[$usage->getUsageTypeId()] = [];
            }

            $result[$usage->getUsageTypeId()][] = $usage;
        }
        if (empty($result)) {
            return false;
        }

        return $result;
    }

    public function loadUsageWithFlag($vendorId, $isOneOff, $filterByNow = false)
    {
        if (empty($vendorId)) {
            return false;
        }

        /**
         * @var \Omnyfy\VendorSubscription\Model\Resource\Usage\Collection $collection
         */
        $collection = $this->usageCollectionFactory->create();
        $collection->addVendorFilter($vendorId)
            ->addOneOffFilter($isOneOff);

        if ($filterByNow) {
            $collection->addNowFilter();
        }

        $result = [];
        foreach($collection as $usage) {
            if (!array_key_exists($usage->getUsageTypeId(), $result)) {
                $result[$usage->getUsageTypeId()] = [];
            }
            $result[$usage->getUsageTypeId()][] = $usage;
        }
        if (empty($result)) {
            return false;
        }

        return $result;
    }

    /**
     * @param int $vendorId  Vendor ID
     * @param int $typeId  Usage Type ID
     * @param int|null|\Zend_Db_Expr $packageId Package ID
     * @param int|null|\Zend_Db_Expr $planId Plan ID
     * @param int|bool $isOneOff Is One Off
     * @param int $count Count
     * @param string|null|\Zend_Db_Expr $startDate Start Date
     * @param string|null|\Zend_Db_Expr $endDate End Date
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addNewUsage($vendorId, $typeId, $packageId, $planId, $isOneOff, $count, $startDate, $endDate)
    {
        $data = [
            'vendor_id' => $vendorId,
            'usage_type_id' => $typeId,
            'package_id' => $packageId,
            'plan_id' => $planId,
            'is_one_off' => $isOneOff,
            'usage_limit' => $count,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];
        $this->usageResource->bulkSave(
            [$data]
        );
    }

    public function isRunOut($vendorId, $typeId)
    {
        $usages = $this->loadUsageByVendorId($vendorId);
        if (empty($usages)) {
            return true;
        }

        if (isset($usages[$typeId])) {
            $available = false;
            foreach($usages[$typeId] as $usage) {
                if ($usage->getUsageLimit() > $usage->getUsageCount()) {
                    $available = true;
                    break;
                }
            }
            if ($available) {
                return false;
            }
        }

        return true;
    }

    public function logUsage($vendorId, $typeId, $id)
    {
        $this->usageResource->logUsage($vendorId, $typeId, $id);

        // load all log total by vendorId typeId
        $total = $this->usageResource->getLogTotal($vendorId, $typeId);

        $usages = $this->loadUsageByVendorId($vendorId);
        if (isset($usages[$typeId])) {
            $toFill = $total;
            foreach($usages[$typeId] as $usage) {
                if ($toFill > $usage->getUsageLimit()) {

                    $this->usageResource->updateById('usage_count', $usage->getUsageLimit(), $usage->getId());
                    $toFill -= $usage->getUsageLimit();
                }
                else{
                    $this->usageResource->updateById('usage_count', $toFill, $usage->getId());
                    break;
                }
            }
        }
    }

    public function returnUsageCount($vendorId, $typeId, $id)
    {
        $usages = $this->loadUsageByVendorId($vendorId, true);
        if (empty($usages) || !isset($usages[$typeId])) {
            return false;
        }

        $did = false;

        foreach($usages[$typeId] as $usage) {
            //count > 1 then put the count back, otherwise try next
            if ($usage->getUsageCount() > 1) {
                $this->usageResource->updateById(
                    'usage_count',
                    $usage->getUsageCount() - 1,
                    $usage->getId()
                );

                $did = true;
                break;
            }
        }

        if ($did) {
            $this->usageResource->removeUsageLog($vendorId, $typeId, $id);
            return true;
        }

        return false;
    }

    /**
     * @param int $vendorId
     * @param \Omnyfy\VendorSubscription\Api\Data\PlanInterface $plan
     */
    public function assignInitUsage($vendorId, $plan)
    {
        $typeIds = array_keys($this->usageType->toValuesArray());

        $usages = $this->loadUsageWithFlag($vendorId, 1);
        $zendDbExprNull = new \Zend_Db_Expr('NULL');
        $zendDbExprNow = new \Zend_Db_Expr('NOW()');

        foreach($typeIds as $typeId) {
            if (empty($usages) || !isset($usages[$typeId])) {
                $count = 0;
                switch($typeId) {
                    case UsageType::PRODUCT:
                        $count = $plan->getProductLimit();
                        break;
                    case UsageType::KIT_STORE:
                        $count = $plan->getKitStoreLimit();
                        break;
                    case UsageType::ENQUIRY:
                        $count = $plan->getEnquiryLimit();
                        break;
                    case UsageType::REQUEST_FOR_QUOTE:
                        $count = $plan->getRequestForQuoteLimit();
                        break;
                }

                //not create record with 0 limit
                if ($count <= 0 ) {
                    continue;
                }

                $this->addNewUsage(
                    $vendorId,
                    $typeId,
                    $zendDbExprNull,
                    $plan->getId(),
                    1,
                    $count,
                    $zendDbExprNow,
                    $zendDbExprNull
                );
            }
        }
    }

    public function assignRepeatPlanUsage($vendorId, $plan, $startDate, $endDate)
    {
        if (empty($vendorId) || empty($plan->getId())) {
            return;
        }

        //load plan-usage relation for this plan
        $inPlan = $this->usageResource->loadPlanUsageRelation($plan->getId());

        if (empty($inPlan)) {
            return;
        }

        $byVendor = $this->loadUsageWithFlag($vendorId, 0, true);

        $zendDbExprNull = new \Zend_Db_Expr('NULL');
        foreach($inPlan as $usageTypeId => $limit) {
            //Avoid 0 limit record
            if ($limit <= 0) {
                continue;
            }

            if (!empty($byVendor) &&  array_key_exists($usageTypeId, $byVendor)) {
                foreach($byVendor[$usageTypeId] as $usage) {
                    //if a usage with same type_id, plan_id and almost same start date and end date, do not create
                    if ($usage->getPlanId() == $plan->getId()
                        && abs(strtotime($usage->getStartDate()) - strtotime($startDate) ) < 43200
                        && abs(strtotime($usage->getEndDate()) - strtotime($endDate)) < 43200
                        ) {
                        continue 2;
                    }
                }
            }

            $this->addNewUsage(
                $vendorId,
                $usageTypeId,
                $zendDbExprNull,
                $plan->getId(),
                0,
                $limit,
                $startDate,
                $endDate
            );
        }
    }

    public function assignPackageUsage($vendorId, $package, $startDate, $endDate)
    {

    }
}
 