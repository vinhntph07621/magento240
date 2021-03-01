<?php

/**
 * Copyright © 2016 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Omnyfy\Cms\Model\Config\Source;

/**
 * Used in edit article form
 *
 */
class CategoryTree implements \Magento\Framework\Option\ArrayInterface {

    /**
     * @var \Omnyfy\Cms\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_categoryCollectionFactory;

    /**
     * @var array
     */
    protected $_options;

    /**
     * @var array
     */
    protected $_childs;

    /**
     * Initialize dependencies.
     *
     * @param \Omnyfy\Cms\Model\ResourceModel\Category\CollectionFactory $authorCollectionFactory
     * @param void
     */
    public function __construct(
    \Omnyfy\Cms\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory, \Magento\Backend\Model\UrlInterface $urlBuilder, \Magento\Framework\App\Request\Http $request
    ) {
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->urlBuilder = $urlBuilder;
        $this->request = $request;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        if ($this->_options === null) {
            $this->_options = $this->_getOptions();
        }
        return $this->_options;
    }

    protected function _getOptions($itemId = 0) {
        $childs = $this->_getChilds();
        $options = [];

        if (isset($childs[$itemId])) {
            foreach ($childs[$itemId] as $item) {
                $data = [
                    'label' => $item->getTitle() .
                    ($item->getIsActive() ? '' : ' (' . __('Disabled') . ')'),
                    'value' => $item->getId(),
                ];
                if (isset($childs[$item->getId()])) {
                    $data['optgroup'] = $this->_getOptions($item->getId());
                }

                $options[] = $data;
            }
        }

        return $options;
    }

    protected function _getChilds() {
        if ($this->_childs === null) {
            $this->_childs = $this->_categoryCollectionFactory->create()
                    ->getGroupedChilds();
        }
        return $this->_childs;
    }

    public function getTree($itemId = 0) {
        $childs = $this->_getChilds();
        $options = [];
        $html = '';
        $id = $this->request->getParam('id');
        $dataJstree = "";
        if (isset($childs[$itemId])) {
            foreach ($childs[$itemId] as $item) {
                $html .= '<li id="node_' . $item->getId() . '"><a href="' . $this->urlBuilder->getUrl('*/*/edit/', ['id' => $item->getId()]) . '" onclick="document.location=this.href">' . $item->getTitle() . ($item->getIsActive() ? '' : ' (' . __('Disabled') . ')') . '</a>';
                if (isset($childs[$item->getId()])) {
                    $html .= "<ul>" . $this->getTree($item->getId()) . '</ul>';
                }
                $html .= '</li>';
            }
        }
        return $html;
    }

}
