<?php

namespace Omnyfy\Vendor\Block\Backend\Grid;

/**
 * Class Indexer
 * @package Omnyfy\Vendor\Block\Backend\Grid
 */
class Indexer extends \Magento\Framework\View\Element\Text
{
    /**
     * Indexer constructor.
     * @param \Magento\Framework\View\Element\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $script = "
        <script>
            require(['jquery', 'domReady!'], function($) {
                'use strict';
                $('.omnyfy-reindex-info').closest('.message-success.success').addClass('omnyfy-hidden');
                $('.omnyfy-reindex-show').click(function () {
                    if ($('.omnyfy-reindex-info').length > 0) {
                        $('.omnyfy-reindex-info').each(function () {
                            if ($(this).closest('.message-success.success').hasClass('omnyfy-hidden')) {
                                $(this).closest('.message-success.success').removeClass('omnyfy-hidden');
                            } else {
                                $(this).closest('.message-success.success').addClass('omnyfy-hidden');
                            }
                        });
                    }
                });
            });
        </script>
        <style>
            .omnyfy-hidden{
                display: none;
            }
        </style>";
        return $script;
    }

}
