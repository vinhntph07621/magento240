<?php
/**
 * Project: Blog Homepage
 * Author: seth
 * Date: 7/5/20
 * Time: 11:15 am
 **/

namespace Omnyfy\Cms\Block\Homepage;


use Magento\Framework\View\Element\Template;

class Blog extends \Magento\Framework\View\Element\Template
{
    protected $coreRegistry;
    protected $cmsArticle;
    protected $cmsCategory;

    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        Template\Context $context,
        \Omnyfy\Cms\Model\ArticleFactory $cmsArticle,
        \Omnyfy\Cms\Model\CategoryFactory $cmsCategory,
        array $data = []
    )
    {
        $this->_coreRegistry = $coreRegistry;
        $this->cmsArticle = $cmsArticle;
        $this->cmsCategory = $cmsCategory;
        parent::__construct($context, $data);
    }

    public function getLatestArticles()
    {
        // search cms_Article category with above
        $cmsArticles = $this->cmsArticle->create();
        $articlesData = $cmsArticles
            ->getCollection()
            ->addFieldToSelect([
                'title',
                'identifier',
                'content',
                'publish_time',
                'featured_img',
                'author_id'
            ])
            ->addFieldToFilter('is_active', 1)
            ->setOrder('publish_time', 'DESC')
            ->setPageSize(3);

        $cmsLatestArticles = [];

        if (!empty($articlesData)) {
            foreach ($articlesData as $articleData) {
                array_push($cmsLatestArticles, $articleData);
            }
        }

        if (count($cmsLatestArticles) > 0) {
            return $cmsLatestArticles;
        }
        else {
            return false;
        }
    }
}
