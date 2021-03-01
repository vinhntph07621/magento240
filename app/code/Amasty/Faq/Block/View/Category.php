<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Block\View;

use Amasty\Faq\Api\Data\CategoryInterface;
use Amasty\Faq\Model\ConfigProvider;
use Amasty\Faq\Model\Hreflang;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;

class Category extends Template implements \Magento\Framework\DataObject\IdentityInterface
{
    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    private $httpContext;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var FilterProvider
     */
    private $filterProvider;

    /**
     * @var Hreflang
     */
    private $hreflang;

    public function __construct(
        Template\Context $context,
        Registry $coreRegistry,
        \Magento\Framework\App\Http\Context $httpContext,
        ConfigProvider $configProvider,
        FilterProvider $filterProvider,
        Hreflang $hreflang,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->coreRegistry = $coreRegistry;
        $this->httpContext = $httpContext;
        $this->setData('cache_lifetime', 86400);
        $this->configProvider = $configProvider;
        $this->filterProvider = $filterProvider;
        $this->hreflang = $hreflang;
    }

    /**
     * @return int
     */
    public function getShortAnswerBehavior()
    {
        return (int)$this->configProvider->getFaqPageShortAnswerBehavior();
    }

    /**
     * @return \Amasty\Faq\Api\Data\CategoryInterface
     */
    public function getCurrentCategory()
    {
        return $this->coreRegistry->registry('current_faq_category');
    }

    /**
     * @return bool
     */
    public function isShowQuestionForm()
    {
        return $this->configProvider->isShowAskQuestionOnAnswerPage();
    }

    /**
     * @return int
     */
    public function getCurrentCategoryId()
    {
        return (int)$this->httpContext->getValue(\Amasty\Faq\Model\Context::CONTEXT_CATEGORY);
    }

    /**
     * Add metadata to page header
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        /** @var \Amasty\Faq\Api\Data\CategoryInterface $category */
        $category = $this->getCurrentCategory();
        if ($category) {
            $this->pageConfig->getTitle()->set($category->getMetaTitle() ? : $category->getTitle());
            if ($description = $category->getMetaDescription()) {
                $this->pageConfig->setDescription($description);
            }

            $categoryStores = array_filter(explode(',', $category->getStores()));
            $this->hreflang->addHreflang($category->getRelativeUrl(), $categoryStores);

            /** @var \Magento\Theme\Block\Html\Title $headingBlock */
            if ($headingBlock = $this->getLayout()->getBlock('page.main.title')) {
                $headingBlock->setPageTitle($category->getTitle());
            }

            if ($this->configProvider->isCanonicalUrlEnabled()) {
                $this->pageConfig->addRemotePageAsset(
                    $this->getCanonicalUrl($category),
                    'canonical',
                    ['attributes' => ['rel' => 'canonical']]
                );
            }

            if ($category->isNoindex() || $category->isNofollow()) {
                if ($category->isNoindex() && $category->isNofollow()) {
                    $this->pageConfig->setRobots('NOINDEX,NOFOLLOW');
                } elseif ($category->isNofollow()) {
                    $this->pageConfig->setRobots('NOFOLLOW');
                } else {
                    $this->pageConfig->setRobots('NOINDEX');
                }
            }
        }

        return parent::_prepareLayout();
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities = [\Amasty\Faq\Model\Category::CACHE_TAG . '_' . $this->getCurrentCategoryId()];
        /** @var \Amasty\Faq\Block\Lists\QuestionsList $listBlock */
        $listBlock = $this->getChildBlock('amasty_faq_questions');
        if ($listBlock) {
            $identities = array_merge($identities, $listBlock->getIdentities());
        }

        return $identities;
    }

    /**
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $params = $this->getRequest()->getParams();
        ksort($params);

        return parent::getCacheKeyInfo()
            + ['cat_id' => $this->getCurrentCategoryId()]
            + $params;
    }

    /**
     * Generate canonical url for page
     *
     * @param CategoryInterface $category
     * @return string
     */
    public function getCanonicalUrl(CategoryInterface $category)
    {
        $urlKey = $this->configProvider->getUrlKey();
        return $this->_urlBuilder->getUrl($urlKey . '/' . $category->getCanonicalUrl());
    }

    /**
     * return FAQ Category Description
     *
     * @return string
     */
    public function getDescription()
    {
        $description = $this->getCurrentCategory()->getDescription();

        if ($description) {
            $description = $this->filterProvider->getPageFilter()->filter($description);
            $description = $this->wrapContent($description);
        }

        return $description;
    }

    /**
     * create for using plugin in cross link module
     *
     * @param string $html
     * @return string
     */
    public function wrapContent($html)
    {
        return $html;
    }
}
