<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


declare(strict_types=1);

namespace Amasty\Faq\Block\Adminhtml\Category\Widget;

use Amasty\Faq\Api\CategoryRepositoryInterface;
use Amasty\Faq\Api\Data\CategoryInterface;
use Amasty\Faq\Model\OptionSource\Category\Status;
use Amasty\Faq\Model\ResourceModel\Category\CollectionFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data as BackendHelper;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class Chooser extends Extended
{
    /**
     * @var CollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var CategoryRepositoryInterface
     */
    private $faqCategoryRepository;

    public function __construct(
        CollectionFactory $categoryCollectionFactory,
        CategoryRepositoryInterface $faqCategoryRepository,
        BackendHelper $backendHelper,
        Context $context,
        array $data = []
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->faqCategoryRepository = $faqCategoryRepository;

        parent::__construct(
            $context,
            $backendHelper,
            $data
        );
    }

    /**
     * @param AbstractElement $element
     *
     * @return AbstractElement
     * @throws LocalizedException
     */
    public function prepareElementHtml(AbstractElement $element)
    {
        $uniqId = $this->mathRandom->getUniqueHash($element->getId());
        $sourceUrl = $this->getUrl(
            'amastyfaq/category_widget/chooser',
            ['uniq_id' => $uniqId]
        );

        $chooser = $this->getLayout()->createBlock(
            \Magento\Widget\Block\Adminhtml\Widget\Chooser::class
        )->setElement(
            $element
        )->setConfig(
            $this->getConfig()
        )->setFieldsetId(
            $this->getFieldsetId()
        )->setSourceUrl(
            $sourceUrl
        )->setUniqId(
            $uniqId
        );

        if ($faqCategoryId = $element->getValue()) {
            try {
                $category = $this->faqCategoryRepository->getById((int)$faqCategoryId);
                $chooser->setLabel($category->getTitle());
            } catch (NoSuchEntityException $exception) {
                $chooser->setLabel(__('Not Selected'));
            }
        }

        $element->setData('after_element_html', $chooser->toHtml());

        return $element;
    }

    /**
     * Grid Row JS Callback
     *
     * @return string
     */
    public function getRowClickCallback()
    {
        $chooserJsObject = $this->getId();
        $categoryIdLabel = CategoryInterface::CATEGORY_ID;

        return '
            function (grid, event) {
                var trElement = Event.findElement(event, "tr");
                var pageTitle = trElement.down(".col-' . CategoryInterface::TITLE . '").innerHTML;
                var categoryId = trElement.down(".col-' . $categoryIdLabel . '").innerHTML.replace(/^\s+|\s+$/g,"");
                ' .
            $chooserJsObject .
            '.setElementValue(categoryId);
                ' .
            $chooserJsObject .
            '.setElementLabel(pageTitle);
                ' .
            $chooserJsObject .
            '.close();
            }
        ';
    }

    /**
     * @return Extended
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            CategoryInterface::CATEGORY_ID,
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => CategoryInterface::CATEGORY_ID,
                'filter_index' => 'main_table.' . CategoryInterface::CATEGORY_ID,
                'header_css_class' => 'col-' . CategoryInterface::CATEGORY_ID,
                'column_css_class' => 'col-' . CategoryInterface::CATEGORY_ID
            ]
        )->addColumn(
            CategoryInterface::TITLE,
            [
                'header' => __('Title'),
                'sortable' => true,
                'index' => CategoryInterface::TITLE,
                'filter_index' => 'main_table.' . CategoryInterface::TITLE,
                'header_css_class' => 'col-' . CategoryInterface::TITLE,
                'column_css_class' => 'col-' . CategoryInterface::TITLE
            ]
        )->addColumn(
            CategoryInterface::URL_KEY,
            [
                'header' => __('Url Key'),
                'sortable' => true,
                'index' => CategoryInterface::URL_KEY,
                'filter_index' => 'main_table.' . CategoryInterface::URL_KEY,
                'header_css_class' => 'col-' . CategoryInterface::URL_KEY,
                'column_css_class' => 'col-' . CategoryInterface::URL_KEY
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return Extended
     */
    protected function _prepareCollection()
    {
        $collection = $this->categoryCollectionFactory->create();
        $collection->addFieldToFilter(CategoryInterface::STATUS, Status::STATUS_ENABLED);
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }
}
