<?php
/**
 * User: Sanjaya-offline
 * Date: 20/02/2018
 * Time: 2:28 PM
 */

namespace Vendor\Module\Controller\Index;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Omnyfy\MyReadingList\Model\ResourceModel\ReadingList\CollectionFactory;

class View extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;

    public function __construct(
        Context $context,
        CollectionFactory $exampleFactory,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->exampleFactory = $exampleFactory;
        parent::__construct($context);
    }
    /**
     * Index action
     *
     * @return $this
     */
    public function execute()
    {
        /*Put below your code*/
        $id = $this->getRequest()->getParam('id', false);
        $responseData = $this->exampleFactory->create();

        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($responseData);
        return $resultJson;
    }
}