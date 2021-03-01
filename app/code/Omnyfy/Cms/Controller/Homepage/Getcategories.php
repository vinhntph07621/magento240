<?php
/**
 * Project: Get Categories
 * Author: seth
 * Date: 22/5/20
 * Time: 12:32 pm
 **/

namespace Omnyfy\Cms\Controller\Homepage;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class Getcategories extends Action
{
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * Getcategories constructor.
     * @param Context $context
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->_categoryFactory = $categoryFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
    }

    public function execute()
    {
        $resultJson = $this->_resultJsonFactory->create();
        $response = ['success' => false, 'categories' => null];
        if ($this->getRequest()->isAjax()) {
            $categories = $this->_categoryFactory->create()->load($this->getRequest()->getParam('category_id'));
            $childrenCategories = $categories->getChildrenCategories($this->getRequest()->getParam('category_id'));
            if (!empty($childrenCategories)) {
                $subcategories[] = ['url' => '' , 'name' => 'Select Sub Category'];
                foreach ($childrenCategories as $category) {
                    $subcategories[] = ['url' => $category->getUrl() , 'name' => $category->getName()];
                }
                $response = [
                    'success' => true,
                    'categories'  => $subcategories
                ];
            }
        }
        return $resultJson->setData($response);
    }
}