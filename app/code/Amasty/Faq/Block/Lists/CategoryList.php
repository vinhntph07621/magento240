<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Block\Lists;

use Amasty\Faq\Model\ConfigProvider;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\View\Element\Template;
use Amasty\Faq\Model\ResourceModel\Category\CollectionFactory;
use Amasty\Faq\Model\ResourceModel\Category\Collection;
use Amasty\Faq\Api\Data\CategoryInterface;
use Magento\Framework\Registry;
use Magento\Framework\DataObject\IdentityInterface;

class CategoryList extends \Amasty\Faq\Block\AbstractBlock implements IdentityInterface
{
    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var Collection
     */
    private $collection;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var HttpContext
     */
    private $httpContext;

    public function __construct(
        Template\Context $context,
        Registry $coreRegistry,
        CollectionFactory $collectionFactory,
        ConfigProvider $configProvider,
        HttpContext $httpContext,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->httpContext = $httpContext;
        $this->coreRegistry = $coreRegistry;
        $this->collection = $collectionFactory->create();
        $this->configProvider = $configProvider;
        $this->setData('cache_lifetime', 86400);
    }

    /**
     * @return int
     */
    public function getCurrentCategoryId()
    {
        return (int) $this->coreRegistry->registry('current_faq_category_id');
    }

    /**
     * @return \Amasty\Faq\Model\Category[]
     */
    public function getCategories()
    {
        $this->collection->addFrontendFilters(
            $this->_storeManager->getStore()->getId(),
            null,
            $this->httpContext->getValue(CustomerContext::CONTEXT_GROUP)
        );

        return $this->collection->getItems();
    }

    /**
     * @param CategoryInterface $category
     *
     * @return string
     */
    public function getCategoryUrl(CategoryInterface $category)
    {
        return $this->_urlBuilder->getUrl($this->configProvider->getUrlKey() . '/' . $category->getUrlKey());
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [Collection::CACHE_TAG];
    }

    /**
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return parent::getCacheKeyInfo() + ['cat_id' => $this->getCurrentCategoryId()];
    }
}
