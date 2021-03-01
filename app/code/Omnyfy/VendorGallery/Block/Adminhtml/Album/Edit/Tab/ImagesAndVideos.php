<?php
namespace Omnyfy\VendorGallery\Block\Adminhtml\Album\Edit\Tab;

use \Magento\Backend\Block\Widget\Tab\TabInterface;

class ImagesAndVideos extends \Magento\Backend\Block\Widget\Form\Generic implements TabInterface {
    /**
     * {@inheritdoc}
     */
    public function getTabLabel() {
        return 'Images';
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle() {
        return 'Images';
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab() {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden() {
        return false;
    }

    protected function _toHtml()
    {
        return parent::_toHtml() . $this->getChildBlock('gallery')->toHtml();
    }
}
