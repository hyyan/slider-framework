<?php

/*
 * This file is part of the hyyan/slider package.
 * (c) Hyyan Abo Fakher <tiribthea4hyyan@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Slides Table
 *
 * @author Hyyan
 */
class Hyyan_Slider_Table {

    /**
     * The name of preview column
     * 
     * @var string 
     */
    const PREVIEW_COLUMN_NAME = 'slide-preview';

    /**
     * The custom post name
     * 
     * @var string
     */
    protected $postName;

    /**
     * The custom taxonomy name
     * 
     * @var string 
     */
    protected $taxName;

    /**
     * The translation textdomain
     * 
     * @var string
     */
    protected $textdomain;

    /**
     * The size name of the preview image
     * 
     * @var string
     */
    protected $sizeName;

    /**
     * Constrcut
     * 
     * Extend the the default preview of the slides table
     * 
     * @param string $postName the custom post name
     * @param string $taxName the custom tax name
     * @param string $textdomain the translation text domain
     * @param string $sizeName the size name of the preview image
     */
    public function __construct($postName, $taxName, $textdomain, $sizeName = '') {
        $this->postName = $postName;
        $this->taxName = $taxName;
        $this->textdomain = $textdomain;
        $this->sizeName = $sizeName;
    }

    /**
     * Add preview column 
     * 
     * Add slider preview column to the table view
     * 
     * @param array $columns
     * @return array columns array
     */
    public function addSlidePreviewColumn($columns) {
        return array_merge($columns, array(
            self::PREVIEW_COLUMN_NAME => __('Slide', $this->textdomain)
        ));
    }

    /**
     * Print the slide preview(feature image) for every slide
     * 
     * @param string $name
     * @param int $id
     */
    public function printSlidePreview($name, $id) {
        if ($name != self::PREVIEW_COLUMN_NAME)
            return;
        
        $id = get_post_thumbnail_id($id);
        if ($id) {
            $img = wp_get_attachment_image_src(
                    $id
                    , apply_filters(Hyyan_Slider_Events::FILTER_SLIDE_PREVIEW_SIZE_NAME, $this->sizeName)
            );
            if (($preview = $img[0])) {
                echo '<img src="' . $preview . '" />';
            }
        }
    }

    /**
     * Add slider filter
     * 
     * Add a select box to the slides table to filter by slider names
     * 
     * @global string $typenow current post type
     */
    public function addSliderFilterSelectBox() {
        global $typenow;
        if ($typenow == $this->postName) {
            $selected = isset($_GET[$this->taxName]) ? esc_attr($_GET[$this->taxName]) : '';
            $info_taxonomy = get_taxonomy($this->taxName);
            wp_dropdown_categories(array(
                'show_option_all' => __("Show All {$info_taxonomy->label}",$this->textdomain),
                'taxonomy' => $this->taxName,
                'name' => $this->taxName,
                'orderby' => 'name',
                'selected' => $selected,
                'show_count' => true,
                'hide_empty' => true,
            ));
        };
    }

    /**
     * Handle filter request
     * 
     * Handle sliders filter request 
     * 
     * @global string $pagenow 
     * @param string $query
     */
    public function handleSliderFilterQuery($query) {
        global $pagenow;
        $queryVars = &$query->query_vars;
        if (
                ($pagenow == 'edit.php' && isset($queryVars['post_type'])) &&
                ($queryVars['post_type'] == $this->postName && (isset($queryVars[$this->taxName]) && (int) $queryVars[$this->taxName] > 0))
        ) {
            $term = get_term_by('id', $queryVars[$this->taxName], $this->taxName);
            $queryVars[$this->taxName] = $term->slug;
        }
    }

    /**
     * Register slide preview column
     */
    public function registerSlidePreviewColumn() {
        add_filter("manage_edit-{$this->postName}_columns", array($this, 'addSlidePreviewColumn'));
        add_action("manage_{$this->postName}_posts_custom_column", array($this, 'printSlidePreview'), 10, 2);
    }

    /**
     * Register the slider filter select box
     */
    public function registerSliderFilter() {
        add_action('restrict_manage_posts', array($this, 'addSliderFilterSelectBox'));
        add_filter('parse_query', array($this, 'handleSliderFilterQuery'));
    }

}
