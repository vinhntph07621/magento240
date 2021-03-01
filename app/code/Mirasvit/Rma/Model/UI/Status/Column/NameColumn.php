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
 * @package   mirasvit/module-rma
 * @version   2.1.18
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Rma\Model\UI\Status\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Mirasvit\Core\Service\SerializeService as Serializer;

class NameColumn extends Column
{
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     * @throws \Zend_Json_Exception
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');

                if ($serialized = Serializer::decode($item[$name])) {
                    $item[$name] = array_values($serialized)[0];
                }

            }
        }

        return $dataSource;
    }
}
