<?php

namespace Omnyfy\Postcode\Model\ResourceModel\Postcode;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Omnyfy\Postcode\Model\Postcode', 'Omnyfy\Postcode\Model\ResourceModel\Postcode');
    }

    /**
     * Add keyword filter
     *
     * @param string $keyword
     * @return \Omnyfy\Postcode\Model\ResourceModel\Postcode\Collection
     */
    public function addKeywordFilter($keyword)
    {
        if (!$this->getFlag('has_keyword_filter')) {
            $keywords = (array) explode(' ', $keyword);
            foreach ($keywords as $keyword) {
                $this->addFieldToFilter(['postcode', 'suburb'], [
                    ['like' => '%' . $keyword . '%'],
                    ['like' => '%' . $keyword . '%']
                ]);
            }

            $this->setFlag('has_keyword_filter', 1);
        }

        return $this;
    }

    /**
     * @param $lat
     * @param $lng
     * @param int $cnt
     * @return $this
     */
    public function filterDistance($lat, $lng, $cnt=1)
    {
        if (!$this->getFlag('has_distance_filter')) {
            $this->addExpressionFieldToSelect(
                'distance',
                '(6371*ACOS(' . cos(deg2rad($lat)) . '*COS(RADIANS(latitude))*COS(RADIANS(longitude)-' . deg2rad($lng) . ')+' . sin(deg2rad($lat)) . '*SIN(RADIANS(latitude))))',
                'distance'
            );
            $this->setPageSize($cnt);
            $this->setOrder('distance', \Magento\Framework\Data\Collection::SORT_ORDER_ASC);

            $this->setFlag('has_distance_filter', 1);
        }

        return $this;
    }
}
