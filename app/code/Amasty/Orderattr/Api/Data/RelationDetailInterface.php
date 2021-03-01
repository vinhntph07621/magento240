<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */


namespace Amasty\Orderattr\Api\Data;

interface RelationDetailInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const RELATION_DETAIL_ID = 'relation_detail_id';

    const ATTRIBUTE_ID = 'attribute_id';

    const OPTION_ID = 'option_id';

    const DEPENDENT_ATTRIBUTE_ID = 'dependent_attribute_id';

    const RELATION_ID = 'relation_id';
    /**#@-*/

    /**
     * Returns Detail Relation ID
     *
     * @return int
     */
    public function getRelationDetailId();

    /**
     * @param int $relationDetailId
     *
     * @return $this
     */
    public function setDetailIdId($relationDetailId);

    /**
     * Returns Relation ID
     *
     * @return int
     */
    public function getRelationId();

    /**
     * @param int $relationId
     *
     * @return $this
     */
    public function setRelationId($relationId);

    /**
     * Returns EAV Attribute ID
     *
     * @return int
     */
    public function getAttributeId();

    /**
     * @param int $attributeId
     *
     * @return $this
     */
    public function setAttributeId($attributeId);

    /**
     * Returns Attribute Option ID
     *
     * @return int
     */
    public function getOptionId();

    /**
     * @param int $optionId
     *
     * @return $this
     */
    public function setOptionId($optionId);

    /**
     * Returns Dependent EAV Attribute ID
     *
     * @return int
     */
    public function getDependentAttributeId();

    /**
     * @param int $attributeId
     *
     * @return $this
     */
    public function setDependentAttributeId($attributeId);
}
