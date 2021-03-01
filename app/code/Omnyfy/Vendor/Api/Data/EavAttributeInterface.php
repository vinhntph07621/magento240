<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-05-01
 * Time: 15:13
 */
namespace Omnyfy\Vendor\Api\Data;

interface EavAttributeInterface extends \Magento\Eav\Api\Data\AttributeInterface
{
    const IS_WYSIWYG_ENABLED = 'is_wysiwyg_enabled';

    const IS_HTML_ALLOWED_ON_FRONT = 'is_html_allowed_on_front';

    const USED_FOR_SORT_BY = 'used_for_sort_by';

    const IS_FILTERABLE = 'is_filterable';

    const IS_FILTERABLE_IN_SEARCH = 'is_filterable_in_search';

    const IS_USED_IN_GRID = 'is_used_in_grid';

    const IS_VISIBLE_IN_GRID = 'is_visible_in_grid';

    const IS_FILTERABLE_IN_GRID = 'is_filterable_in_grid';

    const POSITION = 'position';

    const IS_SEARCHABLE = 'is_searchable';

    const IS_VISIBLE_IN_ADVANCED_SEARCH = 'is_visible_in_advanced_search';

    const IS_VISIBLE_ON_FRONT = 'is_visible_on_front';

    const USED_IN_LISTING = 'used_in_listing';

    const USED_IN_FORM = 'used_in_form';

    const IS_VISIBLE = 'is_visible';

    const SCOPE_STORE_TEXT = 'store';

    const SCOPE_GLOBAL_TEXT = 'global';

    const SCOPE_WEBSITE_TEXT = 'website';

    const TOOLTIP = 'tooltip';

    /**
     * Enable WYSIWYG flag
     *
     * @return bool|null
     */
    public function getIsWysiwygEnabled();

    /**
     * Set whether WYSIWYG is enabled flag
     *
     * @param bool $isWysiwygEnabled
     * @return $this
     */
    public function setIsWysiwygEnabled($isWysiwygEnabled);

    /**
     * Whether the HTML tags are allowed on the frontend
     *
     * @return bool|null
     */
    public function getIsHtmlAllowedOnFront();

    /**
     * Set whether the HTML tags are allowed on the frontend
     *
     * @param bool $isHtmlAllowedOnFront
     * @return $this
     */
    public function setIsHtmlAllowedOnFront($isHtmlAllowedOnFront);

    /**
     * Whether it is used for sorting in product listing
     *
     * @return bool|null
     */
    public function getUsedForSortBy();

    /**
     * Set whether it is used for sorting in product listing
     *
     * @param bool $usedForSortBy
     * @return $this
     */
    public function setUsedForSortBy($usedForSortBy);

    /**
     * Whether it used in layered navigation
     *
     * @return bool|null
     */
    public function getIsFilterable();

    /**
     * Set whether it used in layered navigation
     *
     * @param bool $isFilterable
     * @return $this
     */
    public function setIsFilterable($isFilterable);

    /**
     * Whether it is used in search results layered navigation
     *
     * @return bool|null
     */
    public function getIsFilterableInSearch();

    /**
     * Whether it is used in catalog product grid
     *
     * @return bool|null
     */
    public function getIsUsedInGrid();

    /**
     * Whether it is visible in catalog product grid
     *
     * @return bool|null
     */
    public function getIsVisibleInGrid();

    /**
     * Whether it is filterable in catalog product grid
     *
     * @return bool|null
     */
    public function getIsFilterableInGrid();

    /**
     * Set whether it is used in search results layered navigation
     *
     * @param bool $isFilterableInSearch
     * @return $this
     */
    public function setIsFilterableInSearch($isFilterableInSearch);

    /**
     * Get position
     *
     * @return int|null
     */
    public function getPosition();

    /**
     * Set position
     *
     * @param int $position
     * @return $this
     */
    public function setPosition($position);

    /**
     * Whether the attribute can be used in Quick Search
     *
     * @return string|null
     */
    public function getIsSearchable();

    /**
     * Whether the attribute can be used in Quick Search
     *
     * @param string $isSearchable
     * @return $this
     */
    public function setIsSearchable($isSearchable);

    /**
     * Whether the attribute can be used in Advanced Search
     *
     * @return string|null
     */
    public function getIsVisibleInAdvancedSearch();

    /**
     * Set whether the attribute can be used in Advanced Search
     *
     * @param string $isVisibleInAdvancedSearch
     * @return $this
     */
    public function setIsVisibleInAdvancedSearch($isVisibleInAdvancedSearch);

    /**
     * Whether the attribute is visible on the frontend
     *
     * @return string|null
     */
    public function getIsVisibleOnFront();

    /**
     * Set whether the attribute is visible on the frontend
     *
     * @param string $isVisibleOnFront
     * @return $this
     */
    public function setIsVisibleOnFront($isVisibleOnFront);

    /**
     * Whether the attribute can be used in listing
     *
     * @return string|null
     */
    public function getUsedInListing();

    /**
     * Set whether the attribute can be used in listing
     *
     * @param string $usedInListing
     * @return $this
     */
    public function setUsedInListing($usedInListing);

    /**
     * @return string|null
     */
    public function getUsedInForm();

    /**
     * Set whether the attribute can be used in form
     * @param $usedInForm
     * @return mixed
     */
    public function setUsedInForm($usedInForm);


    /**
     * Whether attribute is visible on frontend.
     *
     * @return bool|null
     */
    public function getIsVisible();

    /**
     * Set whether attribute is visible on frontend.
     *
     * @param bool $isVisible
     * @return $this
     */
    public function setIsVisible($isVisible);

    /**
     * Retrieve attribute scope
     *
     * @return string|null
     */
    public function getScope();

    /**
     * Set attribute scope
     *
     * @param string $scope
     * @return $this
     */
    public function setScope($scope);

    /**
     * Retrieve attribute tooltip
     *
     * @return string|null
     */
    public function getTooltip();

    /**
     * Set attribute tooltip
     *
     * @param string $tooltip
     * @return $this
     */
    public function setTooltip($tooltip);

    /**
     * @return \Omnyfy\Vendor\Api\Data\EavAttributeExtensionInterface|null
     */
    public function getExtensionAttributes();
}
 