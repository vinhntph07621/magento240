<?php
/**
 * Copyright © 2016 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Omnyfy\Cms\Controller\Adminhtml\Import;

/**
 * Run import controller
 */
class Run extends \Magento\Backend\App\Action
{
	/**
     * Run import
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        set_time_limit(0);

        $data = $this->getRequest()->getArticle();
        $type = '';
        try {
            if (empty($data['type'])) {
                throw new \Exception(__('Cms import type is not specified.'), 1);
            }

            $_type = ucfirst($data['type']);
            $import = $this->_objectManager->create('\Omnyfy\Cms\Model\Import\\'.$_type);
            $type = $data['type'];
            $import->prepareData($data)->execute();

            $stats = $import->getImportStatistic();

            if ($stats->getData('imported_count')) {
                if (!$stats->getData('skipped_count')) {
                    $this->messageManager->addSuccess(__(
                        'The import process was completed successfully. %1 articles and %2 categories where imported.',
                        $stats->getData('imported_articles_count'),
                        $stats->getData('imported_categories_count')
                    ));
                } else {
                    $this->messageManager->addNotice(__(
                        'The import process completed. %1 articles and %2 categories where imported. Some articles or categories where skipped.<br/> %3 %4',
                        $stats->getData('imported_articles_count'),
                        $stats->getData('imported_categories_count'),
                        $stats->getData('skipped_articles') ? __('Skipped Articles').': '. implode(', ', $stats->getData('skipped_articles')) .'.<br/>' : '',
                        $stats->getData('skipped_articles') ? __('Skipped Categories').': '. implode(', ', $stats->getData('skipped_categories')) .'. ' : ''
                    ));
                }
            } else {
                if (!$stats->getData('skipped_count')) {
                    $this->messageManager->addNotice(__('Nothing to import.'));
                } else {
                    throw new \Exception(__('Can not make import.'), 1);
                }
            }

            $this->_getSession()->setData('import_'.$type.'_form_data', null);
            $this->_redirect('*/*/');

        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong: ').' '.$e->getMessage());
            $this->_getSession()->setData('import_'.$type.'_form_data', $data);
            $this->_redirect('*/*/'.$type);
        }
    }

    /**
     * Check is allowed access
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Omnyfy_Cms::import');
    }
}
