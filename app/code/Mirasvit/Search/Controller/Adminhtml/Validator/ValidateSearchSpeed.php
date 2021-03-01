<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-search
 * @version   1.0.151
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Search\Controller\Adminhtml\Validator;

use Mirasvit\Search\Controller\Adminhtml\Validator;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;


use Magento\Search\Model\SearchEngine;
use Magento\CatalogSearch\Model\Advanced\Request\BuilderFactory as RequestBuilderFactory;
use Magento\Framework\App\ScopeResolverInterface;

class ValidateSearchSpeed extends Validator
{
    /**
     * @var SearchEngine
     */
    protected $searchEngine;
    /**
     * @var RequestBuilderFactory
     */
    protected $requestBuilderFactory;
    /**
     * @var ScopeResolverInterface
     */
    protected $scopeResolver;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * ValidateSearchSpeed constructor.
     * @param JsonFactory $resultJsonFactory
     * @param SearchEngine $searchEngine
     * @param RequestBuilderFactory $requestBuilderFactory
     * @param ScopeResolverInterface $scopeResolver
     * @param Context $context
     */
    public function __construct(
        JsonFactory $resultJsonFactory,
        SearchEngine $searchEngine,
        RequestBuilderFactory $requestBuilderFactory,
        ScopeResolverInterface $scopeResolver,
        Context $context
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->searchEngine = $searchEngine;
        $this->requestBuilderFactory = $requestBuilderFactory;
        $this->scopeResolver = $scopeResolver;
        
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $response = $this->resultJsonFactory->create();
        $query = '';
        if ($this->getRequest()->getParam('q') && !empty($this->getRequest()->getParam('q'))) {
            $query = $this->getRequest()->getParam('q');
        } else {
            $status = self::STATUS_ERROR;
            $result = '<p>Please specify search term</p>';
            return $response->setData(['result' => $result,'status' => $status]);
        }
        
        $start = microtime(true);
 
        $requestBuilder = $this->requestBuilderFactory->create();
        
        $requestBuilder->bind('search_term', $query);

        $requestBuilder->bindDimension('scope', $this->scopeResolver->getScope());

        $requestBuilder->setRequestName('catalogsearch_fulltext');

        $queryRequest = $requestBuilder->create();

        $collection = $this->searchEngine->search($queryRequest);
        $ids = [];
        foreach ($collection->getIterator() as $item) {
            $ids[] = $item->getId();
        }

        $result = 'Total: '.count($ids).' results in '.round(microtime(true) - $start, 4);

        return $response->setData(['result' => $result . ' sec']);
    }
}
