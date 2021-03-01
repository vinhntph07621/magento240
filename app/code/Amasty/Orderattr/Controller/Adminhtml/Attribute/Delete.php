<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */


namespace Amasty\Orderattr\Controller\Adminhtml\Attribute;

use Amasty\Orderattr\Api\CheckoutAttributeRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Amasty\Orderattr\Controller\Adminhtml\Attribute;

class Delete extends Attribute
{
    /**
     * @var CheckoutAttributeRepositoryInterface
     */
    private $attributeRepository;

    public function __construct(
        Context $context,
        CheckoutAttributeRepositoryInterface $attributeRepository
    ) {
        parent::__construct($context);
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('attribute_id');
        if ($id) {
            try {
                $attribute = $this->attributeRepository->getById($id);
                $this->attributeRepository->delete($attribute);
                $this->messageManager->addSuccessMessage(__('You deleted the order attribute.'));

                return $this->_redirect('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $this->_redirect(
                    '*/*/edit',
                    ['attribute_id' => $this->getRequest()->getParam('attribute_id')]
                );
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find an attribute to delete.'));

        return $this->_redirect('*/*/');
    }
}
