<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


declare(strict_types=1);

namespace Amasty\Faq\Controller\Adminhtml\Category\Widget;

use Amasty\Faq\Block\Adminhtml\Category\Widget\Chooser as FaqCategoryChooser;
use Amasty\Faq\Controller\Adminhtml\AbstractWidgetChooserController;

class Chooser extends AbstractWidgetChooserController
{
    const ADMIN_RESOURCE = 'Amasty_Faq::category';

    /**
     * @return string
     */
    public function getChooserGridClass()
    {
        return FaqCategoryChooser::class;
    }
}
