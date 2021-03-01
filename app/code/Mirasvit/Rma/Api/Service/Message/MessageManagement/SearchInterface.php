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



namespace Mirasvit\Rma\Api\Service\Message\MessageManagement;


interface SearchInterface
{
    /**
     * @param \Mirasvit\Rma\Api\Data\RmaInterface $rma
     * @return \Mirasvit\Rma\Api\Data\MessageInterface[]
     */
    public function getVisibleInFront(\Mirasvit\Rma\Api\Data\RmaInterface $rma);
}