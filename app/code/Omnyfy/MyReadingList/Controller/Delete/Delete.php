<?php
/**
 * Created by PhpStorm.
 * User: Sanjaya-offline
 * Date: 26/02/2018
 * Time: 11:40 AM
 */

namespace Omnyfy\MyReadingList\Controller\Delete;

use Omnyfy\MyReadingList\Model\ResourceModel\ReadingListArticles\CollectionFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;

class Delete extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;
    protected $_resultPageFactory;
    protected $_resultJsonFactory;

    public function __construct(
        Context $context,
        CollectionFactory $readingList,
        PageFactory $resultPageFactory
    ){
        $this->resultPageFactory = $resultPageFactory;
        $this->_resultPageFactory = $readingList;
        //$this->_resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $rlstid = $this->getRequest()->getParam('readinglistarticleid',false);
        if ($rlstid) {
            try {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
                $connection = $resource->getConnection();
                $tableName = $resource->getTableName('omnyfy_myreadinglists_articles');
                $sql = "DELETE FROM " . $tableName . " WHERE readinglist_article_id = ".$rlstid;
                $connection->query($sql);
                return $resultJson->setData(['success' => "Article Unbookmarked Successfully"]);
            } catch (\Exception $e) {
                return $resultJson->setData(['error' => $e->getMessage()]);
            }
        } else {
            return $resultJson->setData(['error' => "No Article Found."]);
        }
    }
}