<?php
namespace Omnyfy\VendorSignUp\Block\Adminhtml\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
/**
 * Class ApproveButton
 */
class ApproveButton extends GenericButton implements ButtonProviderInterface
{

    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$signUpData = $objectManager->create('Omnyfy\VendorSignUp\Model\SignUp')->load($this->getDeleteId());
        if ($this->getDeleteId() &&  $signUpData->getStatus() != 1) {
            $data = [
                'label' => __('Approve'),
                'class' => 'approve',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to delete the signup request from '.$signUpData->getBusinessName().'?'
                ) . '\', \'' . $this->getApproveUrl() . '\')',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getApproveUrl()
    {
        return $this->getUrl('*/*/approve', ['id' => $this->getDeleteId()]);
    }
}