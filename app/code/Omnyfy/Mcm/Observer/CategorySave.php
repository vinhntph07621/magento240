<?php

namespace Omnyfy\Mcm\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class CategorySave
 * @package Omnyfy\Mcm\Observer
 */
class CategorySave implements ObserverInterface
{
    /*
     * @var \Omnyfy\Mcm\Model\CategoryCommissionReport 
     */
    protected $categoryCommissionReport;

    /**
     * @param \Omnyfy\Mcm\Model\CategoryCommissionReport $categoryCommissionReport 
     */
    public function __construct(\Omnyfy\Mcm\Model\CategoryCommissionReport $categoryCommissionReport)
    {
        $this->categoryCommissionReport = $categoryCommissionReport;
    }
    
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $category = $observer->getCategory(); 
        $collections = $this->categoryCommissionReport->getCollection()->addFieldToFilter('category_id', $category->getId());
                    $commissionData = $collections->getData();
                    if (!empty($commissionData)) {
                        foreach ($collections as $collection) {
                            $collection->setCategoryName($category->getName());
                            $collection->setCategoryCommissionPercentage($category->getCategoryCommissionPercentage());
                        }
                        $collections->save();
                    }
        
        return $this;
    }
}
