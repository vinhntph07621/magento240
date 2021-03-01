<?php
/**
 * Copyright © 2016 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Omnyfy\Cms\Block\Article;

use Magento\Store\Model\ScopeInterface;

/**
 * Abstract article мшуц block
 */
abstract class AbstractArticle extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * @var \Omnyfy\Cms\Model\Article
     */
    protected $_article;

    /**
     * Page factory
     *
     * @var \Omnyfy\Cms\Model\ArticleFactory
     */
    protected $_articleFactory;

    /**
     * @var Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var string
     */
    protected $_defaultArticleInfoBlock = 'Omnyfy\Cms\Block\Article\Info';

    /**
     * @var \Omnyfy\Cms\Model\Url
     */
    protected $_url;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Cms\Model\Page $article
     * @param \Magento\Framework\Registry $coreRegistry,
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magento\Cms\Model\PageFactory $articleFactory
     * @param \Omnyfy\Cms\Model\Url $url
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Omnyfy\Cms\Model\Article $article,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Omnyfy\Cms\Model\ArticleFactory $articleFactory,
        \Omnyfy\Cms\Model\Url $url,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_article = $article;
        $this->_coreRegistry = $coreRegistry;
        $this->_filterProvider = $filterProvider;
        $this->_articleFactory = $articleFactory;
        $this->_url = $url;
    }

    /**
     * Retrieve article instance
     *
     * @return \Omnyfy\Cms\Model\Article
     */
    public function getArticle()
    {
        if (!$this->hasData('article')) {
            $this->setData('article',
                $this->_coreRegistry->registry('current_cms_article')
            );
        }
        return $this->getData('article');
    }

    /**
     * Retrieve article short content
     *
     * @return string
     */
    public function getShorContent()
    {
        $content = $this->getContent();
        $pageBraker = '<!-- pagebreak -->';

        if ($p = mb_strpos($content, $pageBraker)) {
            $content = mb_substr($content, 0, $p);
        }

        $dom = new \DOMDocument();
        $dom->loadHTML($content);
        $content = $dom->saveHTML();

        return $content;
    }

    /**
     * Retrieve article content
     *
     * @return string
     */
    public function getContent()
    {
        $article = $this->getArticle();
        $key = 'filtered_content';
        if (!$article->hasData($key)) {
            $cotent = $this->_filterProvider->getPageFilter()->filter(
                $article->getContent()
            );
            $article->setData($key, $cotent);
        }
        return $article->getData($key);
    }

    /**
     * Retrieve article info html
     *
     * @return string
     */
    public function getInfoHtml()
    {
        return $this->getInfoBlock()->toHtml();
    }

    /**
     * Retrieve article info block
     *
     * @return \Omnyfy\Cms\Block\Article\Info
     */
    public function getInfoBlock()
    {
        $k = 'info_block';
        if (!$this->hasData($k)) {
            $blockName = $this->getArticleInfoBlockName();
            if ($blockName) {
                $block = $this->getLayout()->getBlock($blockName);
            }

            if (empty($block)) {
                $block = $this->getLayout()->createBlock($this->_defaultArticleInfoBlock, uniqid(microtime()));
            }

            $this->setData($k, $block);
        }

        return $this->getData($k)->setArticle($this->getArticle());
    }

}
