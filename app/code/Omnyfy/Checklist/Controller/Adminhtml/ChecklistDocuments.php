<?php
/**
 * Created by PhpStorm.
 * User: Sanjaya-offline
 * Date: 4/3/2018
 * Time: 5:31 PM
 */

namespace Omnyfy\Checklist\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Omnyfy\Checklist\Model\ResourceModel\ChecklistDocumentsFactory;

abstract class ChecklistDocuments extends \Magento\Backend\App\Action
{

    protected $_coreRegistry;
    const ADMIN_RESOURCE = 'Omnyfy_Checklist::top_level';

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init page
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     */
    public function initPage($resultPage)
    {
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE)
            ->addBreadcrumb(__('Omnyfy'), __('Omnyfy'))
            ->addBreadcrumb(__('Checklist Document'), __('Checklist Document'));
        return $resultPage;
    }
}