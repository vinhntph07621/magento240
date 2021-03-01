<?php
/**
 * Copyright © 2016 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Omnyfy\Cms\Model;

/**
 * Article management model
 */
class ArticleManagement extends AbstractManagement
{
    /**
     * @var \Omnyfy\Cms\Model\ArticleFactory
     */
    protected $_itemFactory;

    /**
     * Initialize dependencies.
     *
     * @param \Omnyfy\Cms\Model\ArticleFactory $articleFactory
     */
    public function __construct(
        \Omnyfy\Cms\Model\ArticleFactory $articleFactory
    ) {
        $this->_itemFactory = $articleFactory;
    }

}
