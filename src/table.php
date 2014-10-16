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
     * Constrcut
     * 
     * Extend the the default preview of the slides table
     * 
     * @param string $postName the custom post name
     * @param string $taxName the custom tax name
     */
    public function __construct($postName, $taxName) {
        $this->postName = $postName;
        $this->taxName = $taxName;
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
                'show_option_all' => __("Show All {$info_taxonomy->label}"),
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
     * Register the slider filter select box
     */
    public function registerSliderFilter() {
        add_action('restrict_manage_posts', array($this, 'addSliderFilterSelectBox'));
        add_filter('parse_query', array($this, 'handleSliderFilterQuery'));
    }

}
