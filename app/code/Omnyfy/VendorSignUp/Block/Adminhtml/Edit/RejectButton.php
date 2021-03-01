<?php
namespace Omnyfy\VendorSignUp\Block\Adminhtml\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
/**
 * Class RejectButton
 */
class RejectButton extends GenericButton implements ButtonProviderInterface
{

    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$signUpData = $objectManager->create('Omnyfy\VendorSignUp\Model\SignUp')->load($this->getDeleteId());
        if ($this->getDeleteId() &&  $signUpData->getStatus() == 0) {
            $data = [
                'label' => __('Reject'),
                'class' => 'reject',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Do you want to reject the signup request from '.$signUpData->getBusinessName().'?'
                ) . '\', \'' . $this->getRejectUrl() . '\')',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getRejectUrl()
    {
        return $this->getUrl('*/*/reject', ['id' => $this->getDeleteId()]);
    }
}