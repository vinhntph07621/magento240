<?php

namespace Omnyfy\LayeredNavigation\Model\Layer\Filter;

class Item extends \Magento\Framework\DataObject
{

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * @var \Magento\Theme\Block\Html\Pager
     */
    protected $_htmlPagerBlock;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Theme\Block\Html\Pager $htmlPagerBlock
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\UrlInterface $url,
        \Magento\Theme\Block\Html\Pager $htmlPagerBlock,
        \Magento\Framework\App\RequestInterface $request,
        array $data = []
    ) {
        $this->_url = $url;
        $this->_htmlPagerBlock = $htmlPagerBlock;
        $this->_request = $request;

        parent::__construct($data);
    }

    /**
     * Get filter instance
     *
     * @return \Omnyfy\LayeredNavigation\Model\Layer\Filter\AbstractFilter
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getFilter()
    {
        $filter = $this->getData('filter');
        if (!is_object($filter)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('The filter must be an object. Please set the correct filter.')
            );
        }
        return $filter;
    }

    /**
     * Get item url
     *
     * @return string
     */
    public function getUrl()
    {
        if ($this->isSelected()) {
            return $this->getRemoveUrl();
        }

        return $this->getAddUrl();
    }

    /**
     * Get url to add item to filter
     *
     * @return string
     */
    public function getAddUrl()
    {
        $value = $this->getFilterValue();
        array_push($value, $this->getValue());

        return $this->_url->getUrl('*/*/*', [
            '_current'      => true,
            '_use_rewrite'  => true,
            '_escape'       => true,
            '_query'        => [
                $this->getFilter()->getRequestVar() => implode(',', $value),
                $this->_htmlPagerBlock->getPageVarName() => null,
            ]
        ]);
    }

    /**
     * Get url for remove item from filter
     *
     * @return string
     */
    public function getRemoveUrl()
    {
        $value = array_diff($this->getFilterValue(), [$this->getValue()]);

        return $this->_url->getUrl('*/*/*', [
            '_current'      => true,
            '_use_rewrite'  => true,
            '_escape'       => true,
            '_query'        => [
                $this->getFilter()->getRequestVar() => empty($value) ? $this->getFilter()->getResetValue() : implode(',', $value),
                $this->_htmlPagerBlock->getPageVarName() => null
            ]
        ]);
    }

    public function getSelectUrl()
    {
        $value = $this->getValue();

        return $this->_url->getUrl('*/*/*', [
            '_current'      => true,
            '_use_rewrite'  => true,
            '_escape'       => true,
            '_query'        => [
                $this->getFilter()->getRequestVar() => $value,
                $this->_htmlPagerBlock->getPageVarName() => null,
            ]
        ]);
    }

    /**
     * Is item selected?
     *
     * @return boolean
     */
    public function isSelected()
    {
        return in_array($this->getValue(), $this->getFilterValue());
    }

    /**
     * Get filter value
     *
     * @return array
     */
    public function getFilterValue()
    {
        $value = $this->_request->getParam($this->getFilter()->getRequestVar());
        if (is_null($value)) {
            return [];
        }

        return explode(',', $value);
    }

    /**
     * Get item filter name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getFilter()->getName();
    }

    /**
     * Get item value as string
     *
     * @return string
     */
    public function getValueString()
    {
        $value = $this->getValue();
        if (is_array($value)) {
            return implode(',', $value);
        }
        return $value;
    }

}
