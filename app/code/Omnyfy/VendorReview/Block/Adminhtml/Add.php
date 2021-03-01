<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Omnyfy\VendorReview\Block\Adminhtml;

/**
 * Adminhtml add Review main block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Add extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Initialize add review
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_blockGroup = 'Omnyfy_VendorReview';
        $this->_controller = 'adminhtml';
        $this->_mode = 'add';

        $this->buttonList->update('save', 'label', __('Save Review'));
        $this->buttonList->update('save', 'id', 'save_button');

        $this->buttonList->update('reset', 'id', 'reset_button');

        $this->_formScripts[] = '
            require(["prototype"], function(){
                toggleParentVis("add_review_form");
                toggleVis("save_button");
                toggleVis("reset_button");
            });
        ';

        $this->_formInitScripts[] = '
            require(["jquery","prototype"], function(jQuery){
            window.review = function() {
                return {
                    vendorInfoUrl : null,
                    formHidden : true,
                    gridRowClick : function(data, click) {
                        if(Event.findElement(click,\'TR\').title){
                            review.vendorInfoUrl = Event.findElement(click,\'TR\').title;
                            review.loadVendorData();
                            review.showForm();
                            review.formHidden = false;
                        }
                    },
                    loadVendorData : function() {
                        jQuery.ajax({
                            type: "POST",
                            url: review.vendorInfoUrl,
                            data: {
                                form_key: FORM_KEY
                            },
                            showLoader: true,
                            success: review.reqSuccess,
                            error: review.reqFailure
                        });
                    },
                    showForm : function() {
                        toggleParentVis("add_review_form");
                        toggleVis("vendorGrid");
                        toggleVis("save_button");
                        toggleVis("reset_button");
                    },
                    updateRating: function() {
                        elements = [$("select_stores"), $("rating_detail").getElementsBySelector("input[type=\'radio\']")].flatten();
                        $(\'save_button\').disabled = true;
                        var params = Form.serializeElements(elements);
                        if (!params.isAjax) {
                            params.isAjax = "true";
                        }
                        if (!params.form_key) {
                            params.form_key = FORM_KEY;
                        }
                        new Ajax.Updater("rating_detail", "' .
            $this->getUrl(
                'vendorreview/vendor/ratingItems'
            ) .
            '", {parameters:params, evalScripts: true,  onComplete:function(){ $(\'save_button\').disabled = false; } });
                    },

                    reqSuccess :function(response) {
                        if( response.error ) {
                            alert(response.message);
                        } else if( response.id ){
                            $("vendor_id").value = response.id;

                            $("vendor_name").innerHTML = \'<a href="' .
            $this->getUrl(
                'catalog/vendor/edit'
            ) .
            'id/\' + response.id + \'" target="_blank">\' + response.name + \'</a>\';
                        } else if ( response.message ) {
                            alert(response.message);
                        }
                    }
                }
            }();
            Event.observe(window, \'load\', function(){
                 if ($("select_stores")) {
                     Event.observe($("select_stores"), \'change\', review.updateRating);
                 }
            });
            });
           //]]>
        ';
    }

    /**
     * Get add new review header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('New Review');
    }
}
