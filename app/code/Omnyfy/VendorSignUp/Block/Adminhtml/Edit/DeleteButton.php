<?php
namespace Omnyfy\VendorSignUp\Block\Adminhtml\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
/**
 * Class DeleteButton
 */
class DeleteButton extends GenericButton implements ButtonProviderInterface
{

    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$signUpData = $objectManager->create('Omnyfy\VendorSignUp\Model\SignUp')->load($this->getDeleteId());
        if ($this->getDeleteId()) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to delete the signup request from '.$signUpData->getBusinessName().'?'
                ) . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', ['id' => $this->getDeleteId()]);
    }
}