<?php
namespace Omnyfy\Stripe\Plugin;

class VendorEarningDataProvider
{
    public function afterGetSearchResult(
        \Omnyfy\Mcm\Ui\DataProvider\VendorEarnings\Grid\VendorEarningDataProvider $subject,
        \Magento\Framework\Api\Search\SearchResultInterface $result)
    {
        $result->getSelect()->group('id')->order('payout_ref', 'desc');
        return $result;
    }
}
