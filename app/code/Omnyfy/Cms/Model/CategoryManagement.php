<?php
/**
 * Copyright © 2016 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Omnyfy\Cms\Model;

/**
 * Category management model
 */
class CategoryManagement extends AbstractManagement
{
    /**
     * @var \Omnyfy\Cms\Model\CategoryFactory
     */
    protected $_itemFactory;

    /**
     * Initialize dependencies.
     *
     * @param \Omnyfy\Cms\Model\CategoryFactory $categoryFactory
     */
    public function __construct(
        \Omnyfy\Cms\Model\CategoryFactory $categoryFactory
    ) {
        $this->_itemFactory = $categoryFactory;
    }

}
