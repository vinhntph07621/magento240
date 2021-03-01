<?php
/**
 * Created by PhpStorm.
 * User: Sanjaya-offline
 * Date: 4/6/2018
 * Time: 2:37 PM
 */

namespace Omnyfy\Checklist\Model\Source;


class ChecklistArticles implements \Magento\Framework\Option\ArrayInterface
{
    protected $_optionFactory;
    protected $_articleCollectionFactory;

    public function __construct(
        \Omnyfy\Cms\Model\ResourceModel\Article\CollectionFactory $articleCollectionFactory
    )
    {
        $this->_articleCollectionFactory  = $articleCollectionFactory;
    }

    public function toOptionArray()
    {
        $articles = $this->_articleCollectionFactory->create();
		$options = array();
        foreach ($articles as $article) {
            $options[] = [
                'label' => $article['title'],
                'value' => $article['article_id']
            ];
        }
        return $options;
    }
}