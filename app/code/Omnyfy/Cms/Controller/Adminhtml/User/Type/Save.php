<?php

namespace Omnyfy\Cms\Controller\Adminhtml\User\Type;

/**
 * Cms user type save controller
 */
class Save extends \Omnyfy\Cms\Controller\Adminhtml\User\Type {

    /**
     * Before model save
     * @param  \Omnyfy\Cms\Model\UserType $model
     * @param  \Magento\Framework\App\Request\Http $request
     * @return void
     */
    protected function _beforeSave($model, $request) {
        $data = $model->getData();

        $filterRules = [];

        $inputFilter = new \Zend_Filter_Input(
                $filterRules, [], $data
        );
        $data = $inputFilter->getUnescaped();
        $model->setData($data);
    }

}
