<?php
/**
 * Project: apcd.
 * User: jing
 * Date: 30/8/18
 * Time: 12:51 PM
 */
namespace Omnyfy\Vendor\Controller\Adminhtml\Promo\Widget;

class Chooser extends \Omnyfy\Vendor\Controller\Adminhtml\Promo\Widget
{
    public function execute()
    {
        $request = $this->getRequest();

        switch ($request->getParam('attribute')) {
            case 'vendor_id':

                $block = $this->_view->getLayout()->createBlock(
                    'Omnyfy\Vendor\Block\Adminhtml\Promo\Widget\Chooser\Vendor',
                    'promo_widget_chooser_vendor',
                    ['data' => ['js_form_object' => $request->getParam('form')]]
                );
                break;

            case 'location_id':

                $block = $this->_view->getLayout()->createBlock(
                    'Omnyfy\Vendor\Block\Adminhtml\Promo\Widget\Chooser\Location',
                    'promo_widget_chooser_location',
                    ['data' => ['js_form_object' => $request->getParam('form')]]
                );
                break;

            default:
                $block = false;
                break;
        }

        if ($block) {
            $this->getResponse()->setBody($block->toHtml());
        }
    }
}