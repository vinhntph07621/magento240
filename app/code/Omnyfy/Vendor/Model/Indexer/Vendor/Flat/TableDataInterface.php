<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-26
 * Time: 17:21
 */
namespace Omnyfy\Vendor\Model\Indexer\Vendor\Flat;

/**
 * Interface TableDataInterface
 */
interface TableDataInterface
{
    /**
     * Move data from temporary tables to flat
     *
     * @param string $flatTable
     * @param string $flatDropName
     * @param string $temporaryFlatTableName
     * @return void
     */
    public function move($flatTable, $flatDropName, $temporaryFlatTableName);
}
 