<?php
/**
 * Copyright © 2016 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Omnyfy\Cms\Model\Import;

/**
 * Wordpress import model
 */
class Wordpress extends AbstractImport
{
    protected $_requiredFields = ['dbname', 'uname', 'pwd', 'dbhost', 'prefix', 'store_id'];

    public function execute()
    {
        $con = $this->_connect = mysqli_connect(
            $this->getData('dbhost'),
            $this->getData('uname'),
            $this->getData('pwd'),
            $this->getData('dbname')
        );

        if (mysqli_connect_errno()) {
            throw new \Exception("Failed connect to wordpress database", 1);
        }

        $_pref = mysqli_real_escape_string($con, $this->getData('prefix'));

        $categories = [];
        $oldCategories = [];

        /* Import categories */
        $sql = 'SELECT
                    t.term_id as old_id,
                    t.name as title,
                    t.slug as identifier,
                    tt.parent as parent_id
                FROM '.$_pref.'terms t
                LEFT JOIN '.$_pref.'term_taxonomy tt on t.term_id = tt.term_id
                WHERE tt.taxonomy = "category" AND t.slug <> "uncategorized"';

        $result = $this->_mysqliQuery($sql);
        while ($data = mysqli_fetch_assoc($result)) {
            /* Prepare category data */
            foreach (['title', 'identifier'] as $key) {
                $data[$key] = utf8_encode($data[$key]);
            }

            $data['store_ids'] = [$this->getStoreId()];
            $data['is_active'] = 1;
            $data['position'] = 0;
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

        /* Reindexing parent categories */
        foreach ($categories as $ct) {
            if ($oldParentId = $ct->getData('parent_id')) {
                if (isset($oldCategories[$oldParentId])) {
                    $ct->setPath(
                        $parentId = $oldCategories[$oldParentId]->getId()
                    );
                }
            }
        }

        for ($i = 0; $i < 4; $i++) {
            $changed = false;
            foreach ($categories as $ct) {
                if ($ct->getPath()) {
                    $parentId = explode('/', $ct->getPath())[0];
                    $pt = $categories[$parentId];
                    if ($pt->getPath()) {
                        $ct->setPath($pt->getPath() . '/'. $ct->getPath());
                        $changed = true;
                    }
                }
            }

            if (!$changed) {
                break;
            }
        }
        /* end*/

        foreach($categories as $ct) {
            /* Final saving */
            $ct->save();
        }

        /* Import articles */
        $sql = 'SELECT * FROM '.$_pref.'articles WHERE `article_type` = "article"';
        $result = $this->_mysqliQuery($sql);

        while ($data = mysqli_fetch_assoc($result)) {

            /* find article categories*/
            $articleCategories = [];

            $sql = 'SELECT tt.term_id as term_id FROM '.$_pref.'term_relationships tr
                    LEFT JOIN '.$_pref.'term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                    WHERE tr.`object_id` = "'.$data['ID'].'"';

            $result2 = $this->_mysqliQuery($sql);
            while ($data2 = mysqli_fetch_assoc($result2)) {
                $oldTermId = $data2['term_id'];
                if (isset($oldCategories[$oldTermId])) {
                    $articleCategories[] = $oldCategories[$oldTermId]->getId();
                }
            }

            $data['featured_img'] = '';

            $sql = 'SELECT wm2.meta_value as featured_img
                FROM
                    '.$_pref.'articles p1
                LEFT JOIN
                    '.$_pref.'articlemeta wm1
                    ON (
                        wm1.article_id = p1.id
                        AND wm1.meta_value IS NOT NULL
                        AND wm1.meta_key = "_thumbnail_id"
                    )
                LEFT JOIN
                    wp_articlemeta wm2
                    ON (
                        wm1.meta_value = wm2.article_id
                        AND wm2.meta_key = "_wp_attached_file"
                        AND wm2.meta_value IS NOT NULL
                    )
                WHERE
                    p1.ID="'.$data['ID'].'"
                    AND p1.article_type="article"
                ORDER BY
                    p1.article_date DESC';

            $result2 = $this->_mysqliQuery($sql);
            if ($data2 = mysqli_fetch_assoc($result2)) {
                if ($data2['featured_img']) {
                    $data['featured_img'] = \Omnyfy\Cms\Model\Article::BASE_MEDIA_PATH . '/' . $data2['featured_img'];
                }
            }

            /* Prepare article data */
            foreach (['article_title', 'article_name', 'article_content'] as $key) {
                $data[$key] = utf8_encode($data[$key]);
            }

            $creationTime = strtotime($data['article_date_gmt']);
            $data = [
                'store_ids' => [$this->getStoreId()],
                'title' => $data['article_title'],
                'meta_keywords' => '',
                'meta_description' => '',
                'identifier' => $data['article_name'],
                'content_heading' => '',
                'content' => str_replace('<!--more-->', '<!-- pagebreak -->', $data['article_content']),
                'creation_time' => $creationTime,
                'update_time' => strtotime($data['article_modified_gmt']),
                'publish_time' => $creationTime,
                'is_active' => (int)($data['article_status'] == 'publish'),
                'categories' => $articleCategories,
                'featured_img' => $data['featured_img'],
            ];
            $data['identifier'] = trim(strtolower($data['identifier']));
            if (strlen($data['identifier']) == 1) {
                $data['identifier'] .= $data['identifier'];
            }

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
