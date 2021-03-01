<?php
/**
 * Copyright © 2016 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Omnyfy\Cms\Ui\DataProvider\Article\Related;

use \Magento\Ui\DataProvider\AbstractDataProvider;
use Omnyfy\Cms\Model\ResourceModel\Article\Collection;
use Omnyfy\Cms\Model\ResourceModel\Article\CollectionFactory;
use Magento\Framework\App\RequestInterface;

/**
 * Class ArticleDataProvider
 */
class ArticleDataProvider extends AbstractDataProvider
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var article
     */
    private $article;

    /**
     * Construct
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data
        );
        $this->collection = $collectionFactory->create();
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection()
    {
        /** @var Collection $collection */
        $collection = parent::getCollection();

        if (!$this->getArticle()) {
            return $collection;
        }

        $collection->addFieldToFilter(
            $collection->getIdFieldName(),
            ['nin' => [$this->getArticle()->getId()]]
        );

        return $this->addCollectionFilters($collection);
    }

    /**
     * Retrieve article
     *
     * @return ArticleInterface|null
     */
    protected function getArticle()
    {
        if (null !== $this->article) {
            return $this->article;
        }
//            \Magento\Framework\App\ObjectManager::getInstance()
//                ->get('Psr\Log\LoggerInterface')->debug('test: '.print_r( $this->request->getParams(),true));
        if (!($id = $this->request->getParam('current_article_id'))) {
            return null;
        }

        return $this->article = $this->articleRepository->getById($id);
    }
}
