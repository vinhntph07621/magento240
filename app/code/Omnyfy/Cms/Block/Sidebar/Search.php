<?php
/**
 * Copyright © 2016 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Omnyfy\Cms\Block\Sidebar;

/**
 * Cms sidebar categories block
 */
class Search extends \Magento\Framework\View\Element\Template
{
	use Widget;

	/**
     * @var \Omnyfy\Cms\Model\Url
     */
    protected $_url;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Omnyfy\Cms\Model\Url $url
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Omnyfy\Cms\Model\Url $url,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_url = $url;
    }

	/**
     * @var string
     */
    protected $_widgetKey = 'search';

	/**
	 * Retrieve query
	 * @return string
	 */
	public function getQuery()
	{
		return urldecode($this->getRequest()->getParam('q', ''));
	}

	/**
	 * Retrieve serch form action url
	 * @return string
	 */
	public function getFormUrl()
	{
		return $this->_url->getUrl('', \Omnyfy\Cms\Model\Url::CONTROLLER_SEARCH);
	}

}
