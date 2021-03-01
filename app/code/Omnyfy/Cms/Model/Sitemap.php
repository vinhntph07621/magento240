<?php
/**
 * Copyright © 2016 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Omnyfy\Cms\Model;

/**
 * Overide sitemap
 */
class Sitemap extends \Magento\Sitemap\Model\Sitemap
{
    /**
     * Initialize sitemap items
     *
     * @return void
     */
    protected function _initSitemapItems()
    {
        parent::_initSitemapItems();

        $this->_sitemapItems[] = new \Magento\Framework\DataObject(
            [
                'changefreq' => 'weekly',
                'priority' => '0.25',
                'collection' =>  \Magento\Framework\App\ObjectManager::getInstance()->create(
                        'Omnyfy\Cms\Model\Category'
                    )->getCollection($this->getStoreId())
                    ->addStoreFilter($this->getStoreId())
                    ->addActiveFilter(),
            ]
        );

        $this->_sitemapItems[] = new \Magento\Framework\DataObject(
            [
                'changefreq' => 'weekly',
                'priority' => '0.25',
                'collection' =>  \Magento\Framework\App\ObjectManager::getInstance()->create(
                        'Omnyfy\Cms\Model\Article'
                    )->getCollection($this->getStoreId())
                    ->addStoreFilter($this->getStoreId())
                    ->addActiveFilter(),
            ]
        );
    }

}
