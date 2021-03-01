<?php
/**
 * Created by PhpStorm.
 * User: Sanjaya-offline
 * Date: 3/8/2018
 * Time: 12:07 PM
 */

namespace Omnyfy\MyReadingList\Controller\Add;

use Magento\Framework\Controller\ResultFactory;

class Add extends \Magento\Framework\App\Action\Action
{
    protected $_resultPageFactory;
    protected $_resultJsonFactory;
    protected $_readingListModel;
    protected $_readingListArticleModel;
    protected $_readingListCollectionFactory;
    protected $_readingListArticleCollectionFactory;
    protected $_readingListArticlesRepository;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Omnyfy\MyReadingList\Model\ReadingList $readingListModel,
        \Omnyfy\MyReadingList\Model\ReadingListArticlesFactory $readingListArticlesFactory,
        \Omnyfy\MyReadingList\Model\ResourceModel\ReadingList\CollectionFactory $readingListCollectionFactory,
        \Omnyfy\MyReadingList\Model\ResourceModel\ReadingListArticles\CollectionFactory $readingListArticlesCollectionFactory,
        \Omnyfy\MyReadingList\Model\ReadingListArticlesRepository $readingListArticlesRepository,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ){
        $this->_resultPageFactory = $resultPageFactory;
        $this->_readingListCollectionFactory = $readingListCollectionFactory;
        $this->_readingListArticleCollectionFactory = $readingListArticlesCollectionFactory;
        $this->_readingListModel = $readingListModel;
        $this->_readingListArticleModel = $readingListArticlesFactory;
        $this->_readingListArticlesRepository = $readingListArticlesRepository;

        parent::__construct($context);
    }

    public function execute()
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $articleId  = $this->getRequest()->getParam('articleid',false);
        $blockInstance = $this->_objectManager->get('Omnyfy\MyReadingList\Block\ReadingList');
        $userId     = $blockInstance->getUserId();
        if ($userId) {
            if ($articleId) {
                try {
					$message = "";
                    $type = "";
                    $readingListId = "";

					//Check Reading List Excists for the user and create one if not.
                    $readingListCollection = $this->_readingListCollectionFactory->create();
                    $readingListCollection->addFieldToFilter('user_id',['eq' => $userId]);

                    if ($readingListCollection->count() == 0) {
                        $readingListData = [
                            "user_id" => $userId,
                        ];

                        $this->_readingListModel->setData($readingListData);
                        $this->_readingListModel->save();
                        $readingListId = $this->_readingListModel->getId();
                    } else {
                        $readingListCollection->getFirstItem();
                        $readingListId = $readingListCollection->getFirstItem()->getReadingListId();
                    }

                    if ($readingListId){
                        $readingListArticleCollection = $this->_readingListArticleCollectionFactory->create();
                        $readingListArticleCollection->addFieldToFilter('readinglist_id', ['eq' => $readingListId]);
                        $readingListArticleCollection->addFieldToFilter('article_id',['eq' => $articleId]);

                        if ($readingListArticleCollection->count() == 0){
                            $articleData = [
                                'readinglist_id' => $readingListId,
                                'article_id' => $articleId
                            ];

                            $article = $this->_readingListArticleModel->create();
                            $article->setData($articleData);
                            $article->save();


                            $message = "Success! We’ve added the article to your reading list.";
                            $type    = "added";
                        } else {
                            foreach($readingListArticleCollection as $article){
                                $readingListArticle = $this->_readingListArticlesRepository->deleteById($article->getId());
                                if($readingListArticle) {
                                    $message = "All done. The article has been removed from your reading list.";
                                    $type = "removed";
                                }else {
                                    $message = "Bookmark couldn't be removed";
                                    $type = "error";
                                }
                            }
                        }
                    } else {
                        $message = "Could not find the reading list";
                    }

                    return $resultJson->setData(['message' => __($message),"type" => $type]);
                } catch (\Exception $e) {
                    return $resultJson->setData(['message' => "Error: ".$e->getMessage()]);

                }
            } else {
                return $resultJson->setData(['message' => __("No Article Found.")]);
            }
        } else {
            return $resultJson->setData(['message' => __("Please login to bookmark this article")]);
        }
    }
}