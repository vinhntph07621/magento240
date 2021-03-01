<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-08
 * Time: 16:04
 */

namespace Omnyfy\Vendor\Ui\DataProvider\Vendor;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;

class TypeGrid extends DataProvider
{
    protected $searchBy;

    protected $viewMode;

    protected $status;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        \Omnyfy\Vendor\Model\Source\SearchBy $searchBy,
        \Omnyfy\Vendor\Model\Source\ViewMode $viewMode,
        \Omnyfy\Vendor\Model\Source\Status $status,
        array $meta = [],
        array $data = [])
    {
        $this->searchBy = $searchBy;
        $this->viewMode = $viewMode;
        $this->status = $status;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $reporting, $searchCriteriaBuilder, $request, $filterBuilder, $meta, $data);
    }

    protected function searchResultToOutput(\Magento\Framework\Api\Search\SearchResultInterface $searchResult) {
        $arrItems = [];
        $arrItems['totalRecords'] = $searchResult->getTotalCount();

        $arrItems['items'] = [];
        foreach ($searchResult->getItems() as $item) {
            //$item->setData('search_by_text', $this->getText('search_by', $item));
            //$item->setData('view_mode_text', $this->getText('view_mode', $item));
            //$item->setData('status_text', $this->getText('status', $item));
            $arrItems['items'][] = $item->getData();
        }

        return $arrItems;
    }

    protected function getText($type, $item)
    {
        $result = $item->getData($type);
        $value = $result;
        $source = null;
        switch($type) {
            case 'search_by':
                $source = $this->searchBy;
                break;
            case 'view_mode':
                $source = $this->viewMode;
                break;
            case 'status':
                $source = $this->status;
                break;
        }
        $arr = empty($source) ? [] : $source->toValuesArray();
        return array_key_exists($value, $arr) ? $arr[$value] : '';
    }
}