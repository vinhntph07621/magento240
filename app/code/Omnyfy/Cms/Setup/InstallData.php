<?php

namespace Omnyfy\Cms\Setup;

use Omnyfy\Cms\Model\Article;
use Omnyfy\Cms\Model\ArticleFactory;
use Magento\Framework\Module\Setup\Migration;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * Article factory
     *
     * @var \Omnyfy\Cms\Model\ArticleFactory
     */
    private $_articleFactory;

    /**
     * Init
     *
     * @param \Omnyfy\Cms\Model\ArticleFactory $articleFactory
     */
    public function __construct(\Omnyfy\Cms\Model\ArticleFactory $articleFactory)
    {
        $this->_articleFactory = $articleFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
//        $data = [
//            'title' => 'Hello world!',
//            'meta_keywords' => 'magento 2 cms',
//            'meta_description' => 'Magento 2 cms default article.',
//            'identifier' => 'hello-world',
//            'content_heading' => 'Hello world!',
//            'content' => '<p>Welcome to <a title="Omnyfy - solutions for Magento 2" href="http://omnyfy.com/" target="_blank">Omnyfy</a> cms extension for Magento&reg; 2. This is your first article. Edit or delete it, then start cmsging!</p>
//<p><!-- pagebreak --></p>
//<p>Please also read&nbsp;<a title="Magento 2 Cms online documentation" href="http://omnyfy.com/docs/magento-2-cms/" target="_blank">Online documentation</a>&nbsp;and&nbsp;<a href="http://omnyfy.com/cms/add-read-more-tag-to-cms-article-content/" target="_blank">How to add "read more" tag to article content</a></p>
//<p>Follow Omnyfy on:</p>
//<p><a title="Cms Extension for Magento 2 code" href="https://github.com/omnyfy/module-cms" target="_blank">GitHub</a>&nbsp;|&nbsp;<a href="https://twitter.com/magento2fan" target="_blank">Twitter</a>&nbsp;|&nbsp;<a href="https://www.facebook.com/omnyfy/" target="_blank">Facebook</a>&nbsp;|&nbsp;<a href="https://plus.google.com/+Omnyfy_Magento_2/articles/" target="_blank">Google +</a></p>',
//            'store_ids' => [0]
//        ];
//
//        $this->_articleFactory->create()->setData($data)->save();
    }

}
