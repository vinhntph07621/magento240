<?php


namespace Omnyfy\VendorAuth\Observer\Backend\Model;

class SaveBefore implements \Magento\Framework\Event\ObserverInterface
{

    protected $_objectManager;
    protected $_registryManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    )
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        //If not Vendor user return

        $object = $observer->getData('object');
        $id = $object->getEntityId();
        $class = get_class($object);

        $entity = $this->_objectManager->get($class)->load($id);


        $vendorId = $entity->getData('vendor_id');

        //if (!empty($vendorId)) {

       /*     \Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class)->debug('*****Save Before******');
            \Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class)->debug('*****prefix: ' . get_class($entity));
            \Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class)->debug('*****prefix: ' . get_class($object));
            \Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class)->debug('*****Entity ID: '. $entity->getId());
            \Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class)->debug('*****Vendor ID: ' . $entity->getData('vendor_id'));*/

            //throw new \Exception("You are not autherized to edit this entity.");
        //}

    }

    protected function isAction($action){

    }
}
