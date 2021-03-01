<?php
/**
 * User: Sanjaya-offline
 * Date: 20/02/2018
 * Time: 2:28 PM
 */

namespace Omnyfy\MyReadingList\Controller\View;

use Magento\Framework\Controller\ResultFactory;
use Omnyfy\Cms\Model\ResourceModel\Article\Collection;
use Omnyfy\MyReadingList\Model\ResourceModel\ReadingListArticles;

class View extends \Magento\Framework\App\Action\Action
{
    protected $_readingListCollectionFactory;
    protected $_readingListArticleCollectionFactory;
    protected $_articleCollectionFactory;
    protected $resultPageFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Omnyfy\MyReadingList\Model\ResourceModel\ReadingList\CollectionFactory $readingListCollectionFactory,
        \Omnyfy\MyReadingList\Model\ResourceModel\ReadingListArticles\CollectionFactory $readingListArticleCollectionFactory,
        \Omnyfy\Cms\Model\ResourceModel\Article\CollectionFactory $articleCollectionFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_readingListCollectionFactory = $readingListCollectionFactory;
        $this->_readingListArticleCollectionFactory =  $readingListArticleCollectionFactory;
        $this->_articleCollectionFactory = $articleCollectionFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $responseData = [];
        try {
            $customer_id = $this->getRequest()->getParam('customer_id', false);

            if ($customer_id) {

                $readingLists = $this->_readingListCollectionFactory->create();
                $readingLists->addFieldToFilter('user_id', ['eq' => $customer_id]);

                if ($readingLists->count() == 1) {
                    $readingList = $readingLists->getFirstItem();
                    $readingListId = $readingList->getReadinglistId();

                    $readingListArticles = $this->_readingListArticleCollectionFactory->create();
                    $readingListArticles->addFieldToFilter('readinglist_id',['eq' => $readingListId]);

                    $articleIds = [];
                    $articleListIds = [];

                    foreach($readingListArticles as $articles){
                        $articleIds[] = $articles->getArticleId();
                        $articleListIds[$articles->getArticleId()] = $articles->getId();
                    }

                    //$responseData[$articles->getId()]["id"] = $articles->getId();

                    $cmsArticles = $this->_articleCollectionFactory->create();
                    $cmsArticles->addFieldToFilter("article_id",['in' => $articleIds]);
                    $cmsArticles->addFieldToFilter("is_active",['eq' => 1]);
                    $cmsArticles->load();

                    $responseData['totalRecords'] = $cmsArticles->count();
                    $index = 0;
                    foreach($cmsArticles as $cmsArticle) {
                        $responseData['items'][$index]["readinglist_id"] = $readingListId;
                        $responseData['items'][$index]["topic"] = $cmsArticle->getData('title');
                        $responseData['items'][$index]["title"] = $cmsArticle->getData('title');
                        $responseData['items'][$index]["user_id"] = $customer_id;
                        $responseData['items'][$index]["updated_time"] = $cmsArticle->getData('update_time');
                        $responseData['items'][$index]["list_id"] = $articleListIds[$cmsArticle->getData('article_id')];
                        $responseData['items'][$index]["article_id"] = $cmsArticle->getData('article_id');
                        $responseData['items'][$index]["url-key"] = $cmsArticle->getData('identifier');
                        $responseData['items'][$index]["is_active"] = $cmsArticle->getData('is_active');
                        $responseData['items'][$index]["is_active"] = $cmsArticle->getData('is_active');
                        $index++;
                    }

                }

                $resultJson->setData($responseData);
            }else {
                return $resultJson->setData(['message' => "Please login to view your reading list."]);
            }
            return $resultJson;
        }catch(\Exception $exception){
            return $resultJson->setData(['message' => "Error: ".$exception->getMessage()]);
        }
    }
}