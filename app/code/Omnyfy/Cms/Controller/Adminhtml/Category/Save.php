<?php

/**
 * Copyright © 2016 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Omnyfy\Cms\Controller\Adminhtml\Category;

use Omnyfy\Cms\Model\Article;

/**
 * Cms category save controller
 */
class Save extends \Omnyfy\Cms\Controller\Adminhtml\Category {

    /**
     * Before model save
     * @param  \Omnyfy\Cms\Model\Category $model
     * @param  \Magento\Framework\App\Request\Http $request
     * @return void
     */
    protected function _beforeSave($model, $request) {
        /* Prepare dates */
        $dateFilter = $this->_objectManager->create('Magento\Framework\Stdlib\DateTime\Filter\Date');
        $data = $model->getData();

        $filterRules = [];
        foreach (['custom_theme_from', 'custom_theme_to'] as $dateField) {
            if (!empty($data[$dateField])) {
                $filterRules[$dateField] = $dateFilter;
            }
        }

        $inputFilter = new \Zend_Filter_Input(
                $filterRules, [], $data
        );
        $data = $inputFilter->getUnescaped();
        $model->setData($data);

        /* Prepare relative links */
        $data = $request->getPost('data');
        $links = isset($data['links']) ? $data['links'] : null;
        //\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug('$links first ' . print_r($links, true));
        if ($links && is_array($links)) {
            //foreach (['article'] as $linkType) {
            $linkType = 'article';
            if (!empty($links[$linkType]) && is_array($links[$linkType])) {
                $linksData = [];
                foreach ($links[$linkType] as $item) {
                    $linksData[$item['id']] = [
                        'position' => $item['position']
                    ];
                }
                $links[$linkType] = $linksData;
            }
            //}
            $model->setData('links', $links);
        }

        /* Prepare images */
        $data = $model->getData();
        foreach (['category_icon', 'category_banner'] as $key) {
            if (isset($data[$key]) && is_array($data[$key])) {
                if (!empty($data[$key]['delete'])) {
                    $model->setData($key, null);
                } else {
                    if (isset($data[$key][0]['name']) && isset($data[$key][0]['tmp_name'])) {
                        $image = $data[$key][0]['name'];
                        $model->setData($key, Article::BASE_MEDIA_PATH . DIRECTORY_SEPARATOR . $image);
                        $imageUploader = $this->_objectManager->get(
                                'Omnyfy\Cms\ImageUpload'
                        );
                        $imageUploader->moveFileFromTmp($image);
                    } else {
                        if (isset($data[$key][0]['name'])) {
                            $model->setData($key, $data[$key][0]['name']);
                        }
                    }
                }
            } else {
                $model->setData($key, null);
            }
        }
        
    }

    /**
     * After model save
     * @param  \Omnyfy\Cms\Model\Category $model
     * @param  \Magento\Framework\App\Request\Http $request
     * @return void
     */
    protected function _afterSave($model, $request) {
        $model->addData(
                [
                    'parent_id' => $model->getParentId(),
                    'level' => $model->getLevel(),
                ]
        );
    }

}
