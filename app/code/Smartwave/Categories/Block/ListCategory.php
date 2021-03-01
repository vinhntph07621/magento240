<?php
namespace Smartwave\Categories\Block;

class ListCategory extends \Magento\Framework\View\Element\Template
{
    protected $_categoryFactory;

    public function __construct(
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\View\Element\Template\Context $context
    ) {
        $this->_categoryFactory = $categoryFactory;
        parent::__construct($context);
    }

    public function getCategoryName($categoryId)
    {
        $category = $this->_categoryFactory->create()->load($categoryId);
        $categoryName = $category->getName();
        return $categoryName;
    }

    public function getCategoryUrl($categoryId)
    {
        $category = $this->_categoryFactory->create()->load($categoryId);
        $categoryUrl = $category->getUrl();
        return $categoryUrl;
    }

    public function getCategoryImageUrl($categoryId)
    {
        $category = $this->_categoryFactory->create()->load($categoryId);
        $categoryImageUrl = $category->getImageUrl();
        return $categoryImageUrl;
    }
}