<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\VendorReview\Block\Adminhtml;

/**
 * Ratings grid
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Rating extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml';
        $this->_blockGroup = 'Omnyfy_VendorReview';
        $this->_headerText = __('Manage Ratings');
        $this->_addButtonLabel = __('Add New Rating');
        parent::_construct();
    }
}
