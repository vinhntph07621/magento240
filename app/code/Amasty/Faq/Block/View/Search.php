<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Block\View;

use Amasty\Faq\Model\ConfigProvider;
use Magento\Framework\View\Element\Template;

class Search extends Template
{
    /**
     * @var ConfigProvider
     */
    protected $configProvider;

    public function __construct(
        Template\Context $context,
        ConfigProvider $configProvider,
        array $data = []
    ) {
        $this->configProvider = $configProvider;

        parent::__construct($context, $data);
    }

    /**
     * @return string|null
     */
    public function getQuery()
    {
        return $this->getRequest()->getParam('query');
    }

    /**
     * @return string|null
     */
    public function getTagQuery()
    {
        return $this->getRequest()->getParam('tag');
    }

    /**
     * @return bool
     */
    public function isShowQuestionForm()
    {
        return $this->configProvider->isShowAskQuestionOnAnswerPage();
    }

    /**
     * Add metadata to page header
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        if ($query = $this->getQuery()) {
            $title = __('Search results for "%1":', $this->escapeHtml($query));
            $this->pageConfig->getTitle()->set($title);

            /** @var \Magento\Theme\Block\Html\Title $headingBlock */
            if ($headingBlock = $this->getLayout()->getBlock('page.main.title')) {
                $headingBlock->setPageTitle($title);
            }
        }

        $this->pageConfig->setRobots('NOINDEX,NOFOLLOW');

        return parent::_prepareLayout();
    }
}
