<?php
/**
 * Copyright © 2016 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Omnyfy\Cms\Controller;

use \Omnyfy\Cms\Model\Url;
/**
 * Cms Controller Router
 */
class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * Event manager
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Page factory
     *
     * @var \Omnyfy\Cms\Model\ArticleFactory
     */
    protected $_articleFactory;

    /**
     * Category factory
     *
     * @var \Omnyfy\Cms\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * Author factory
     *
     * @var \Omnyfy\Cms\Model\AuthorFactory
     */
    protected $_authorFactory;

    /**
     * Tag factory
     *
     * @var \Omnyfy\Cms\Model\TagFactory
     */
    protected $_tagFactory;

    /**
     * Config primary
     *
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * Url
     *
     * @var \Omnyfy\Cms\Model\Url
     */
    protected $_url;

    /**
     * Response
     *
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     * @var int
     */
    protected $_articleId;

    /**
     * @var int
     */
    protected $_categoryId;
	
    /**
     * @var int
     */
    protected $_industryId;

    /**
     * @var int
     */
    protected $_authorId;

    /**
     * @var int
     */
    protected $_tagId;

    /**
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\UrlInterface $url
     * @param \Omnyfy\Cms\Model\ArticleFactory $articleFactory
     * @param \Omnyfy\Cms\Model\CategoryFactory $categoryFactory
     * @param \Omnyfy\Cms\Model\AuthorFactory $authorFactory
     * @param \Omnyfy\Cms\Model\TagFactory $tagFactory
     * @param \Omnyfy\Cms\Model\Url $url
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\ResponseInterface $response
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        Url $url,
        \Omnyfy\Cms\Model\ArticleFactory $articleFactory,
        \Omnyfy\Cms\Model\CategoryFactory $categoryFactory,
        \Omnyfy\Cms\Model\IndustryFactory $industryFactory,
        \Omnyfy\Cms\Model\AuthorFactory $authorFactory,
        \Omnyfy\Cms\Model\TagFactory $tagFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResponseInterface $response
    ) {
        $this->actionFactory = $actionFactory;
        $this->_eventManager = $eventManager;
        $this->_url = $url;
        $this->_articleFactory = $articleFactory;
        $this->_industryFactory = $industryFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_authorFactory = $authorFactory;
        $this->_tagFactory = $tagFactory;
        $this->_storeManager = $storeManager;
        $this->_response = $response;
    }

    /**
     * Validate and Match Cms Pages and modify request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $_identifier = trim($request->getPathInfo(), '/');

        $pathInfo = explode('/', $_identifier);
        $cmsRoute = $this->_url->getRoute();

        if ($pathInfo[0] != $cmsRoute) {
            return;
        }
        unset($pathInfo[0]);

        switch ($this->_url->getPermalinkType()) {
            case Url::PERMALINK_TYPE_DEFAULT:
                foreach ($pathInfo as $i => $route) {
                    $pathInfo[$i] = $this->_url->getControllerName($route);
                }
                break;
            case Url::PERMALINK_TYPE_SHORT:
                if (isset($pathInfo[1])) {
                    if ($pathInfo[1] == $this->_url->getRoute(Url::CONTROLLER_SEARCH)) {
                        $pathInfo[1] = Url::CONTROLLER_SEARCH;
                    } elseif ($pathInfo[1] == $this->_url->getRoute(Url::CONTROLLER_AUTHOR)) {
                        $pathInfo[1] = Url::CONTROLLER_AUTHOR;
                    } elseif ($pathInfo[1] == $this->_url->getRoute(Url::CONTROLLER_TAG)) {
                        $pathInfo[1] = Url::CONTROLLER_TAG;
                    } elseif (count($pathInfo) == 1) {
                        if ($this->_isArchiveIdentifier($pathInfo[1])) {
                            $pathInfo[2] = $pathInfo[1];
                            $pathInfo[1] = Url::CONTROLLER_ARCHIVE;
                        } elseif ($articleId = $this->_getArticleId($pathInfo[1])) {
                            $pathInfo[2] = $pathInfo[1];
                            $pathInfo[1] = Url::CONTROLLER_POST;
                        } elseif ($categoryId = $this->_getCategoryId($pathInfo[1])) {
                            $pathInfo[2] = $pathInfo[1];
                            $pathInfo[1] = Url::CONTROLLER_CATEGORY;
                        } elseif ($industryId = $this->_getIndustryId($pathInfo[1])) {
                            $pathInfo[2] = $pathInfo[1];
                            $pathInfo[1] = Url::CONTROLLER_INDUSTRY;
                        }
                    }
                }
                break;
        }

        $identifier = implode('/', $pathInfo);

        $condition = new \Magento\Framework\DataObject(['identifier' => $identifier, 'continue' => true]);
        $this->_eventManager->dispatch(
            'omnyfy_cms_controller_router_match_before',
            ['router' => $this, 'condition' => $condition]
        );

        if ($condition->getRedirectUrl()) {
            $this->_response->setRedirect($condition->getRedirectUrl());
            $request->setDispatched(true);
            return $this->actionFactory->create(
                'Magento\Framework\App\Action\Redirect',
                ['request' => $request]
            );
        }

        if (!$condition->getContinue()) {
            return null;
        }

        $identifier = $condition->getIdentifier();

        $success = false;
        $info = explode('/', $identifier);

        if (!$identifier) {
            $request->setModuleName('cms')->setControllerName('index')->setActionName('index');
            $success = true;
        } elseif (count($info) > 1) {

            $store = $this->_storeManager->getStore()->getId();

            switch ($info[0]) {
                case Url::CONTROLLER_POST :
                    if (!$articleId = $this->_getArticleId($info[1])) {
                        return null;
                    }

                    $request->setModuleName('cms')
                        ->setControllerName(Url::CONTROLLER_POST)
                        ->setActionName('view')
                        ->setParam('id', $articleId);

                    $success = true;
                    break;

                case Url::CONTROLLER_CATEGORY :
                    if (!$categoryId = $this->_getCategoryId($info[1])) {
                        return null;
                    }

                    $request->setModuleName('cms')
                        ->setControllerName(Url::CONTROLLER_CATEGORY)
                        ->setActionName('view')
                        ->setParam('id', $categoryId);

                    $success = true;
                    break;

                case Url::CONTROLLER_INDUSTRY :
                    if (!$industryId = $this->_getIndustryId($info[1])) {
                        return null;
                    }

                    $request->setModuleName('cms')
                        ->setControllerName(Url::CONTROLLER_INDUSTRY)
                        ->setActionName('view')
                        ->setParam('id', $industryId);

                    $success = true;
                    break;

                case Url::CONTROLLER_ARCHIVE :
                    $request->setModuleName('cms')
                        ->setControllerName(Url::CONTROLLER_ARCHIVE)
                        ->setActionName('view')
                        ->setParam('date', $info[1]);

                    $success = true;
                    break;

                case Url::CONTROLLER_AUTHOR :
                    if (!$authorId = $this->_getAuthorId($info[1])) {
                        return null;
                    }

                    $request->setModuleName('cms')
                        ->setControllerName(Url::CONTROLLER_AUTHOR)
                        ->setActionName('view')
                        ->setParam('id', $authorId);

                    $success = true;
                    break;

                case Url::CONTROLLER_TAG :
                    if (!$tagId = $this->_getTagId($info[1])) {
                        return null;
                    }

                    $request->setModuleName('cms')
                        ->setControllerName(Url::CONTROLLER_TAG)
                        ->setActionName('view')
                        ->setParam('id', $tagId);

                    $success = true;
                    break;

                case Url::CONTROLLER_SEARCH :
                    $request->setModuleName('cms')
                        ->setControllerName(Url::CONTROLLER_SEARCH)
                        ->setActionName('index')
                        ->setParam('q', $info[1]);

                    $success = true;
                    break;

                case Url::CONTROLLER_RSS :
                    $request->setModuleName('cms')
                        ->setControllerName(Url::CONTROLLER_RSS)
                        ->setActionName(
                            isset($info[1]) ? $info[1] : 'index'
                        );

                    $success = true;
                    break;
            }

        }

        if (!$success) {
            return null;
        }

        $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $_identifier);

        return $this->actionFactory->create(
            'Magento\Framework\App\Action\Forward',
            ['request' => $request]
        );
    }

    /**
     * Retrieve article id by identifier
     * @param  string $identifier
     * @return int
     */
    protected function _getArticleId($identifier)
    {
        if (is_null($this->_articleId)) {
            $article = $this->_articleFactory->create();
            $this->_articleId = $article->checkIdentifier(
                $identifier,
                $this->_storeManager->getStore()->getId()
            );
        }

        return $this->_articleId;
    }

    /**
     * Retrieve category id by identifier
     * @param  string $identifier
     * @return int
     */
    protected function _getCategoryId($identifier)
    {
        if (is_null($this->_categoryId)) {
            $category = $this->_categoryFactory->create();
            $this->_categoryId = $category->checkIdentifier(
                $identifier,
                $this->_storeManager->getStore()->getId()
            );
        }

        return $this->_categoryId;
    }
	
    /**
     * Retrieve category id by identifier
     * @param  string $identifier
     * @return int
     */
    protected function _getIndustryId($identifier)
    {
        if (is_null($this->_industryId)) {
            $category = $this->_industryFactory->create();
            $this->_industryId = $category->checkIdentifier(
                $identifier,
                $this->_storeManager->getStore()->getId()
            );
        }

        return $this->_industryId;
    }

    /**
     * Retrieve category id by identifier
     * @param  string $identifier
     * @return int
     */
    protected function _getAuthorId($identifier)
    {
        if (is_null($this->_authorId)) {
            $author = $this->_authorFactory->create();
            $this->_authorId = $author->checkIdentifier(
                $identifier
            );
        }

        return $this->_authorId;
    }

    /**
     * Retrieve tag id by identifier
     * @param  string $identifier
     * @return int
     */
    protected function _getTagId($identifier)
    {
        if (is_null($this->_tagId)) {
            $tag = $this->_tagFactory->create();
            $this->_tagId = $tag->checkIdentifier(
                $identifier
            );
        }

        return $this->_tagId;
    }

    /**
     * Detect arcive identifier
     * @param  string  $identifier
     * @return boolean
     */
    protected function _isArchiveIdentifier($identifier)
    {
        $info = explode('-', $identifier);
        return count($info) == 2
            && strlen($info[0]) == 4
            && strlen($info[1]) == 2
            && is_numeric($info[0])
            && is_numeric($info[1]);
    }

}
