<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-03
 * Time: 17:40
 */
namespace Omnyfy\Vendor\Model\Source;

class SearchBy extends \Omnyfy\Core\Model\Source\AbstractSource
{
    const SEARCH_BY_VENDOR = 0;

    const SEARCH_BY_LOCATION = 1;

    public function toValuesArray()
    {
        return [
            self::SEARCH_BY_VENDOR => __('Vendor'),
            self::SEARCH_BY_LOCATION => __('Location')
        ];
    }
}
 