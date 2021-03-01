<?php

namespace Omnyfy\Cms\Controller\Adminhtml\Article;

use Omnyfy\Cms\Model\Article;
/**
 * Cms article save controller
 */
class Save extends \Omnyfy\Cms\Controller\Adminhtml\Article
{
    /**
     * Before model save
     * @param  \Omnyfy\Cms\Model\Article $model
     * @param  \Magento\Framework\App\Request\Http $request
     * @return void
     */
    protected function _beforeSave($model, $request)
    {
        /* Prepare dates */
        $dateFilter = $this->_objectManager->create('Magento\Framework\Stdlib\DateTime\Filter\Date');
        $data = $model->getData();

        $filterRules = [];
        foreach (['publish_time', 'custom_theme_from', 'custom_theme_to'] as $dateField) {
            if (!empty($data[$dateField])) {
                $filterRules[$dateField] = $dateFilter;
            }
        }

        $inputFilter = new \Zend_Filter_Input(
            $filterRules,
            [],
            $data
        );
        $data = $inputFilter->getUnescaped();
        $model->setData($data);

        /* Prepare author */
        if (!$model->getAuthorId()) {
            $authSession = $this->_objectManager->get('Magento\Backend\Model\Auth\Session');
            $model->setAuthorId($authSession->getUser()->getId());
        }

        /* Prepare relative links */
        $data = $request->getPost('data');
        $links = isset($data['links']) ? $data['links'] : null;
       
        if ($links && is_array($links)) {
            foreach (['article', 'product', 'service', 'tool'] as $linkType) {
                if (!empty($links[$linkType]) && is_array($links[$linkType])) {
                    $linksData = [];
                    foreach ($links[$linkType] as $item) {
                        $linksData[$item['id']] = [
                            'position' => $item['position']
                        ];
                    }
                    $links[$linkType] = $linksData;
                }
            }
//            echo "<pre>";
//            print_r($links);
//            print_r($model->getData());
//            die('<br>test');
            $model->setData('links', $links);
        }

        /* Prepare images */
        $data = $model->getData();
        foreach (['featured_img', 'og_img'] as $key) {
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

}
