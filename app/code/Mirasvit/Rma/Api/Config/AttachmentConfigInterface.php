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



namespace Mirasvit\Rma\Api\Config;


interface AttachmentConfigInterface
{
    const ATTACHMENT_ITEM_MESSAGE = 'message';
    const ATTACHMENT_ITEM_RETURN_LABEL = 'return_label';

    /**
     * @param null $store
     * @return array
     */
    public function getFileAllowedExtensions($store = null);

    /**
     * @param null $store
     * @return array
     */
    public function getShippingLabelsAllowedExtensions($store = null);

    /**
     * @param null|int $store
     * @return int
     */
    public function getFileSizeLimit($store = null);
}