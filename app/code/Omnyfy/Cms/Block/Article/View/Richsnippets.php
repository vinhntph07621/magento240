<?php
/**
 * Copyright © 2016 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */
namespace Omnyfy\Cms\Block\Article\View;

use Magento\Store\Model\ScopeInterface;

/**
 * Cms article view rich snippets
 */
class Richsnippets extends Opengraph
{
    /**
     * @param  array
     */
    protected $_options;

    /**
     * Retrieve snipet params
     *
     * @return array
     */
    public function getOptions()
    {
        if ($this->_options === null) {
            $article = $this->getArticle();

            $logoBlock = $this->getLayout()->getBlock('logo');

            $this->_options = [
                '@context' => 'http://schema.org',
                '@type' => 'CmsArticleing',
                '@id' => $article->getArticleUrl(),
                'author' => $this->getAuthor(),
                'headline' => $this->getTitle(),
                'description' => $this->getDescription(),
                'datePublished' => $article->getPublishDate('c'),
                'dateModified' => $article->getUpdateDate('c'),
                'image' => [
                    '@type' => 'ImageObject',
                    'url' => $this->getImage() ?:
                        ($logoBlock ? $logoBlock->getLogoSrc() : ''),
                    'width' => 720,
                    'height' => 720,
                ],
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => $this->getPublisher(),
                    'logo' => [
                        '@type' => 'ImageObject',
                        'url' => $logoBlock ? $logoBlock->getLogoSrc() : '',
                    ],
                ],
                'mainEntityOfPage' => $this->_url->getBaseUrl(),
            ];
        }

        return $this->_options;
    }

    /**
     * Retrieve author name
     *
     * @return array
     */
    public function getAuthor()
    {
        if ($author = $this->getArticle()->getAuthor()) {
            if ($author->getTitle()) {
                return $author->getTitle();
            }
        }

        // if no author name return name of publisher
        return $this->getPublisher();
    }

    /**
     * Retrieve publisher name
     *
     * @return array
     */
    public function getPublisher()
    {
        $publisher =  $this->_scopeConfig->getValue(
            'general/store_information/name',
            ScopeInterface::SCOPE_STORE
        );

        if (!$publisher) {
            $publisher = 'Magento2 Store';
        }

        return $publisher;
    }

    /**
     * Render html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        return '<script type="application/ld+json">'
            . json_encode($this->getOptions())
            . '</script>';
    }
}
