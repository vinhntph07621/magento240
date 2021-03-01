<?php

namespace Omnyfy\Cms\Model\ResourceModel;

/**
 * Cms userType resource model
 */
class ToolTemplate extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Initialize resource model
     * Get tablename from config
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('omnyfy_cms_tool_template', 'id');
    }

    /**
     * Process userType data before deleting
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $condition = ['id = ?' => (int)$object->getId()];
        $this->getConnection()->delete($this->getTable('omnyfy_cms_tool_template'), $condition);

        return parent::_beforeDelete($object);
    }

    /**
     * Process userType data before saving
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        /* $object->setTitle(
            trim(strtolower($object->getTitle()))
        ); */
		
		$title = $object->getTitle();

        if (!$object->getId()) {
            $userType = $object->getCollection()
                ->addFieldToFilter('title', $title)
                ->setPageSize(1)
                ->getFirstItem();
            if ($userType->getId()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('The tool template is already exist.')
                );
            }
        }

        return parent::_beforeSave($object);
    }
}
