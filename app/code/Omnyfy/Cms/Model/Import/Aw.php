<?php
/**
 * Copyright © 2016 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Omnyfy\Cms\Model\Import;

use Magento\Framework\Config\ConfigOptionsListConstants;

/**
 * Aw import model
 */
class Aw extends AbstractImport
{
    public function execute()
    {
        $config = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magento\Framework\App\DeploymentConfig');
        $pref = ConfigOptionsListConstants::CONFIG_PATH_DB_CONNECTION_DEFAULT . '/';
        $this->setData('dbhost',
            $config->get($pref . ConfigOptionsListConstants::KEY_HOST)
        )->setData('uname',
            $config->get($pref . ConfigOptionsListConstants::KEY_USER)
        )->setData('pwd',
            $config->get($pref . ConfigOptionsListConstants::KEY_PASSWORD)
        )->setData('dbname',
            $config->get($pref . ConfigOptionsListConstants::KEY_NAME)
        );

        $con = $this->_connect = mysqli_connect(
            $this->getData('dbhost'),
            $this->getData('uname'),
            $this->getData('pwd'),
            $this->getData('dbname')
        );

        if (mysqli_connect_errno()) {
            throw new \Exception("Failed connect to magento database", 1);
        }

        $_pref = mysqli_real_escape_string($con,
            $config->get($pref . ConfigOptionsListConstants::KEY_PREFIX)
        );

        $sql = 'SELECT * FROM '.$_pref.'aw_cms_cat LIMIT 1';
        try {
            $this->_mysqliQuery($sql);
        } catch (\Exception $e) {
            throw new \Exception(__('AheadWorks Cms Extension not detected.'), 1);
        }

        $storeIds = array_keys($this->_storeManager->getStores(true));

        $categories = [];
        $oldCategories = [];

        /* Import categories */
        $sql = 'SELECT
                    t.cat_id as old_id,
                    t.title as title,
                    t.identifier as identifier,
                    t.sort_order as position,
                    t.meta_keywords as meta_keywords,
                    t.meta_description as meta_description
                FROM '.$_pref.'aw_cms_cat t';

        $result = $this->_mysqliQuery($sql);
        while ($data = mysqli_fetch_assoc($result)) {
            /* Prepare category data */

            /* Find store ids */
            $data['store_ids'] = [];
            $s_sql = 'SELECT store_id FROM '.$_pref.'aw_cms_cat_store WHERE cat_id = "'.$data['old_id'].'"';
            $s_result = $this->_mysqliQuery($s_sql);
            while ($s_data = mysqli_fetch_assoc($s_result)) {
                $data['store_ids'][] = $s_data['store_id'];
            }

            foreach ($data['store_ids'] as $key => $id) {
                if (!in_array($id, $storeIds)) {
                    unset($data['store_ids'][$key]);
                }
            }

            if (empty($data['store_ids']) || in_array(0, $data['store_ids'])) {
                $data['store_ids'] = 0;
            }

            $data['is_active'] = 1;
            $data['path'] = 0;
            $data['identifier'] = trim(strtolower($data['identifier']));
            if (strlen($data['identifier']) == 1) {
                $data['identifier'] .= $data['identifier'];
            }

            $category = $this->_categoryFactory->create();
            try {
                /* Initial saving */
                $category->setData($data)->save();
                $this->_importedCategoriesCount++;
                $categories[$category->getId()] = $category;
                $oldCategories[$category->getOldId()] = $category;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                unset($category);
                $this->_skippedCategories[] = $data['title'];
            }
        }

        /* Import articles */
        $sql = 'SELECT * FROM '.$_pref.'aw_cms';
        $result = $this->_mysqliQuery($sql);

        while ($data = mysqli_fetch_assoc($result)) {

            /* Find article categories*/
            $c_sql = 'SELECT cat_id as category_id FROM '.$_pref.'aw_cms_article_cat WHERE article_id = "'.$data['article_id'].'"';
            $c_result = $this->_mysqliQuery($c_sql);
            while ($c_data = mysqli_fetch_assoc($c_result)) {
                $oldId = $c_data['category_id'];
                if (isset($oldCategories[$oldId])) {
                    $id = $oldCategories[$oldId]->getId();
                    $articleCategories[$id] = $id;
                }
            }

            /* Find store ids */
            $data['store_ids'] = [];
            $s_sql = 'SELECT store_id FROM '.$_pref.'aw_cms_store WHERE article_id = "'.$data['article_id'].'"';
            $s_result = $this->_mysqliQuery($s_sql);
            while ($s_data = mysqli_fetch_assoc($s_result)) {
                $data['store_ids'][] = $s_data['store_id'];
            }

            foreach ($data['store_ids'] as $key => $id) {
                if (!in_array($id, $storeIds)) {
                    unset($data['store_ids'][$key]);
                }
            }

            if (empty($data['store_ids']) || in_array(0, $data['store_ids'])) {
                $data['store_ids'] = 0;
            }

            /* Prepare article data */
            $data = [
                'store_ids' => $data['store_ids'],
                'title' => $data['title'],
                'meta_keywords' => $data['meta_keywords'],
                'meta_description' => $data['meta_description'],
                'identifier' => $data['identifier'],
                'content_heading' => '',
                'content' => str_replace('<!--more-->', '<!-- pagebreak -->', $data['article_content']),
                'creation_time' => strtotime($data['created_time']),
                'update_time' => strtotime($data['update_time']),
                'publish_time' => strtotime($data['created_time']),
                'is_active' => (int)($data['status'] == 1),
                'categories' => $articleCategories,
            ];
            $data['identifier'] = trim(strtolower($data['identifier']));

            $article = $this->_articleFactory->create();
            try {
                /* Article saving */
                $article->setData($data)->save();
                $this->_importedArticlesCount++;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->_skippedArticles[] = $data['title'];
            }

            unset($article);
        }
        /* end */

        mysqli_close($con);
    }

}

