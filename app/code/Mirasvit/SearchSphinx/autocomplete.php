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
 * @package   mirasvit/module-search-sphinx
 * @version   1.1.56
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\SearchSphinx;

use Mirasvit\SearchSphinx\SphinxQL\SphinxQL;
use Mirasvit\SearchSphinx\SphinxQL\Expression as QLExpression;
use Mirasvit\SearchSphinx\SphinxQL\Stemming\En;
use Mirasvit\SearchSphinx\SphinxQL\Stemming\Nl;
use Mirasvit\SearchSphinx\SphinxQL\Stemming\Ru;

if (php_sapi_name() == "cli") {
    return;
}

$configFile = dirname(dirname(dirname(__DIR__))) . '/etc/autocomplete.json';

if (stripos(__DIR__, 'vendor') !== false) {
    $configFile = dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/app/etc/autocomplete.json';
}

if (!file_exists($configFile)) {
    return;
}

$config = \Zend_Json::decode(file_get_contents($configFile));

if ($config['engine'] !== 'sphinx') {
    return;
}

class SphinxAutocomplete
{
    /**
     * @var array
     */
    private $config;
    /**
     * @var array
     */
    private $locales = [];

    /**
     * SphinxAutocomplete constructor.
     * @param array $config
     * @param En $En
     * @param Nl $Nl
     * @param Ru $Ru
     */
    public function __construct(
        array $config,
        En $En,
        Nl $Nl,
        Ru $Ru
    ) {
        $this->config = $config;
        $this->locales = ['en' => $En, 'nl' => $Nl, 'ru' => $Ru];
    }

    /**
     * @return array
     */
    public function process()
    {
        $result = [
            'indices' => [],
        ];
        $totalItems = 0;

        foreach ($this->config['indexes'][$this->getStoreId()] as $i => $config) {
            $identifier = $config['identifier'];
            $sphinxQL = new SphinxQL($this->getConnection());
            $metaQL = new SphinxQL($this->getConnection());

            try {
                $response = $sphinxQL
                ->select(['autocomplete','LENGTH(autocomplete) AS autocomplete_strlen',new QLExpression('weight()')])
                ->from($config['index'])
                ->match('*', $this->getQuery())
                ->where('autocomplete_strlen', '>', 0)
                ->limit(0, $config['limit'])
                ->option('max_matches', 1000000)
                ->option('field_weights', $this->getWeights($i))
                ->option('ranker', new QLExpression("expr('sum(1/min_hit_pos*user_weight
                    + word_count*user_weight + exact_hit*user_weight*1000 + lcs*user_weight) * 1000 + bm25')"))
                ->enqueue($metaQL->query('SHOW META'))
                ->enqueue()
                ->executeBatch();
            } catch (\Exception $e) {
                $result['noResults'] = true;
                break;
            }
            $total = $response[1][0]['Value'];
            $items = $this->mapHits($response[0], $config);
            if ($total && $items) {
                $result['indices'][] = [
                    'identifier'   => $identifier == 'catalogsearch_fulltext' ? 'magento_catalog_product' : $identifier,
                    'isShowTotals' => true,
                    'order'        => $config['order'],
                    'title'        => $config['title'],
                    'totalItems'   => $total,
                    'items'        => $items,
                ];
                $totalItems += $total;
            }
        }

        $result['query'] = $this->getQueryText();
        $result['totalItems'] = $totalItems;
        $result['noResults'] = $totalItems == 0;
        $result['textEmpty'] = sprintf($this->config['textEmpty'][$this->getStoreId()], $this->getQueryText());
        $result['textAll'] = sprintf($this->config['textAll'][$this->getStoreId()], $result['totalItems']);
        $result['urlAll'] = $this->config['urlAll'][$this->getStoreId()] . $this->getQueryText();

        return $result;
    }

    /**
     * @return SphinxQL\Connection
     */
    private function getConnection()
    {
        $connection = new \Mirasvit\SearchSphinx\SphinxQL\Connection();
        $connection->setParams([
                'host' => $this->config['host'],
                'port' => $this->config['port'],
            ]);

        return $connection;
    }

    /**
     * @param string $identifier
     * @return array
     */
    private function getWeights($identifier)
    {
        $weights = [];
        foreach ($this->config['indexes'][$this->getStoreId()][$identifier]['fields'] as $f => $w) {
            $weights['`'. $f .'`'] = pow(2, $w);
        }

        return $weights;
    }

    /**
     * @return mixed|string
     */
    private function getQueryText()
    {
        return filter_input(INPUT_GET, 'q') != null ? filter_input(INPUT_GET, 'q') : '';
    }

    /**
     * @return mixed
     */
    private function getStoreId()
    {
        return filter_input(INPUT_GET, 'store_id') != null ? filter_input(INPUT_GET, 'store_id') : array_keys($this->config['indexes'])[0] ;
    }

    /**
     * @return mixed
     */
    private function getLocale()
    {
        return $this->config['advancedConfig']['locale'][$this->getStoreId()];
    }

    /**
     * @return QLExpression
     */
    private function getQuery()
    {
        $terms = array_filter(explode(" ", $this->getQueryText()));

        $conditions = [];
        foreach ($terms as $term) {
            $term = $this->escape(mb_strtolower($term));
            $conditions[] = $this->prepareQuery($term);
        }

        return new QLExpression(implode(" ", $conditions));
    }

    /**
     * @param string $term
     * @return string
     */
    private function prepareQuery($term)
    {
        $searchTerm = [];

        if (in_array($term, $this->config['advancedConfig']['not_words'])) {
            return '!';
        }

        if (isset($this->config['advancedConfig']['stopwords'][$this->getStoreId()])) {
            if (in_array($term, explode(',', $this->config['advancedConfig']['stopwords'][$this->getStoreId()]))) {
                return ' ';
            }
        }

        if (isset($this->config['advancedConfig']['replace_words'][$term])) {
            $term = $this->config['advancedConfig']['replace_words'][$term];
        }

        $searchTerm[] = $this->getWildcard($term);

        $searchTerm[] = $this->lemmatize($term);
        $searchTerm[] = $this->getLongTail($term);
        $searchTerm[] = $this->getSynonyms($term);

        $searchTerm = array_filter($searchTerm);

        $searchTerm = array_unique($searchTerm);

        return '('. implode(' | ', $searchTerm) .')';
    }

    /**
     * @param string $term
     * @return string
     */
    private function getWildcard($term)
    {
        if (in_array($term, $this->config['advancedConfig']['wildcard_exceptions'])) {
            return $term;
        }

        $result = [];
        $result[] = $term;

        switch ($this->config['advancedConfig']['wildcard']) {
            case 'infix':
                if (strlen($term) > 1) {
                    $result[] = '*'. $term .'*';
                } else {
                    $result[] = $term .'*';
                }
                break;
            case 'suffix':
                $result[] = $term .'*';
                break;
            case 'prefix':
                $result[] = '*'. $term;
                break;
            default:
                break;
        }

        return implode(' | ', $result);
    }

    /**
     * @param string $term
     * @return string
     */
    private function lemmatize($term)
    {
        if (array_key_exists($this->getLocale(), $this->locales)) {
            return $this->getWildcard($this->locales[$this->getLocale()]->singularize($term));
        } else {
            return '';
        }
    }

    /**
     * @param string $term
     * @return string
     */
    private function getLongTail($term)
    {
        $result = [];
        if (!empty($this->config['advancedConfig']['long_tail'])) {
            foreach ($this->config['advancedConfig']['long_tail'] as $expression) {
                $matches = null;
                preg_match_all($expression['match_expr'], $term, $matches);
                foreach ($matches[0] as $match) {
                    $match = preg_replace($expression['replace_expr'], $expression['replace_char'], $match);
                    if ($match) {
                        $result[] = $this->getWildcard($match);
                    }
                }
            }
        }

        return implode(' | ', $result);
    }

    /**
     * @param string $term
     * @return string
     */
    private function getSynonyms($term)
    {
        $result = [];
        if (isset($this->config['advancedConfig']['synonyms'][$this->getStoreId()])) {
            if (in_array($term, array_keys($this->config['advancedConfig']['synonyms'][$this->getStoreId()]))) {
                foreach (explode(',', $this->config['advancedConfig']['synonyms'][$this->getStoreId()][$term]) as $synonym) {
                    $result[] = $synonym;
                }
            }
        }

        return implode(' | ', $result);
    }

    /**
     * @param string $term
     * @param string $locale
     */
    private function singularize($term, $locale)
    {
    }

    /**
     * @param string $value
     * @return string|string[]|null
     */
    private function escape($value)
    {
        $pattern = '/(\+|-|\/|&&|\|\||!|\(|\)|\{|}|\[|]|\^|"|~|\*|\?|:|\\\)/';
        $replace = '\\\$1';

        return preg_replace($pattern, $replace, $value);
    }

    /**
     * @param mixed $response
     * @param mixed $config
     * @return array
     */
    private function mapHits($response, $config)
    {
        $items = [];
        foreach ($response as $hit) {
            if (count($items) > $config['limit']) {
                break;
            }

            $item = [
                'name'        => null,
                'url'         => null,
                'sku'         => null,
                'image'       => null,
                'description' => null,
                'price'       => null,
                'rating'      => null,
            ];

            try {
                $item = array_merge($item, \Zend_Json::decode($hit['autocomplete']));
                $items[] = $item;
            } catch (\Exception $e) {
            }
        }

        return $items;
    }
}

$result = (new SphinxAutocomplete($config, new En, new Nl, new Ru))->process();

/** mp comment start **/
exit(\Zend_Json::encode($result));
/** mp comment end **/

/** mp uncomment start
return \Zend_Json::encode($result);
mp uncomment end **/
