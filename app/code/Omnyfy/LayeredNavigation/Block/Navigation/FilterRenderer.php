<?php

namespace Omnyfy\LayeredNavigation\Block\Navigation;

class FilterRenderer extends \Magento\Framework\View\Element\Template implements FilterRendererInterface
{

    public function render($filter)
    {
        $this->assign('filter', $filter);
        $html = $this->_toHtml();
        $this->assign('filter', null);
        return $html;
    }

}
