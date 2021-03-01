<?php
/**
 * Created by PhpStorm.
 * User: Sanjaya-offline
 * Date: 4/09/2019
 * Time: 6:07 PM
 */

namespace Omnyfy\VendorDashBoard\Controller\Adminhtml\Dashboard;


use Magento\Backend\App\Action;

class Generate extends \Magento\Backend\App\Action
{
    /** @var \Omnyfy\VendorDashBoard\Helper\Data  */
    protected $_data;

    public function __construct(
        Action\Context $context,
        \Omnyfy\VendorDashBoard\Helper\Data $data
    )
    {
        $this->_data = $data;
        parent::__construct($context);
    }

    /**
     * Generate Vendor Dashboard
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $this->_data->generateDashBoards();
            $this->messageManager->addSuccessMessage(__('Successfully generated vendor dashboards.'));
        } catch (\Exception $exception){
            $this->messageManager->addErrorMessage(__($exception->getMessage()));
        }

        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('omnyfy_vendordashboard/dashboard/index');
        return $resultRedirect;
    }
}