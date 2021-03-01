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


namespace Mirasvit\Rma\Service\Report;

class Resolution extends AbstractReasons
{
    /**
     * {@inheritdoc}
     */
    public function getItemTable()
    {
        return 'mst_rma_item';
    }

    /**
     * {@inheritdoc}
     */
    public function getReasonsTable()
    {
        return 'mst_rma_resolution';
    }

    /**
     * {@inheritdoc}
     */
    public function getItemReasonsField()
    {
        return 'resolution_id';
    }

    /**
     * {@inheritdoc}
     */
    public function getReasonsField()
    {
        return 'resolution_id';
    }

}