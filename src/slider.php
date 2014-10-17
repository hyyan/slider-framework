<?php

/*
 * This file is part of the hyyan/slider package.
 * (c) Hyyan Abo Fakher <tiribthea4hyyan@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__ . '/events.php';
require_once __DIR__ . '/table.php';
require_once __DIR__ . '/shortcode.php';
require_once __DIR__ . '/metabox-url.php';

/**
 * HyyanSlider
 * 
 * The class will create the basic api to create slider of any type
 *
 * @author Hyyan
 */
class Hyyan_Slider {

    /**
     * The custom post name
     * 
     * @var string 
     */
    const CUSTOM_POST = 'hyyan-slide';

    /**
     * The custom taxonomy name
     * 
     * @var string 
     */
    const CUSTOM_TAXONOMY = 'hyyan-slider';

    /**
     * The translation textdomain
     * 
     * @var string 
     */
    const TEXTDOMAIN = 'hyyan-slider';

    /**
     * Constrcutor
     * 
     * Constrcut the plugin api
     */
    public function __construct() {
        add_action('init', array($this, 'init'));
    }

    /**
     * Init
     * 
     * fire a bunch or actions in the init hook
     */
    public function init() {
        $this->addCustomPost();
        $this->addCustomTaxonomy();

        add_image_size('slide_preview', 55, 55, true);
        $table = new Hyyan_Slider_Table(
                self::CUSTOM_POST
                , self::CUSTOM_TAXONOMY
                , self::TEXTDOMAIN
                , 'slide_preview'
        );
        $table->registerSliderFilter();
        $table->registerSlidePreviewColumn();


        $shortcode = new Hyyan_Slider_Shortcode(
                self::CUSTOM_POST
                , self::CUSTOM_TAXONOMY
                , self::TEXTDOMAIN
        );
        $shortcode->registerSliderShortcode();


        $metabox = new Hyyan_Slider_Metabox_URL(
                self::CUSTOM_POST
                , self::CUSTOM_TAXONOMY
                , self::TEXTDOMAIN
        );
        $metabox->registerMetabox();
    }

    /**
     * Add custom post
     * 
     * Register the custom post 
     * 
     * @see Hyyan_Slider_Events::FILTER_SLIDE_LABLES
     * @see Hyyan_Slider_Events::FILTER_SLIDE_ARGS
     * @see HyyanSlider::addCutomPostMessages
     */
    public function addCustomPost() {
        /**
         * Default custom post lables
         * 
         * @see Hyyan_Slider_Events::FILTER_SLIDE_LABLES
         * 
         * @var array 
         */
        $labels = apply_filters(Hyyan_Slider_Events::FILTER_SLIDE_LABLES, array(
            'name' => __('Slides', self::TEXTDOMAIN),
            'singular_name' => __('Slide', self::TEXTDOMAIN),
            'all_items' => __('Slides', self::TEXTDOMAIN),
            'new_item' => __('New Slide', self::TEXTDOMAIN),
            'add_new' => __('Add New', self::TEXTDOMAIN),
            'add_new_item' => __('Add New Slide', self::TEXTDOMAIN),
            'edit_item' => __('Edit Slide', self::TEXTDOMAIN),
            'view_item' => __('View Slide', self::TEXTDOMAIN),
            'search_items' => __('Search Slides', self::TEXTDOMAIN),
            'not_found' => __('No Slides found', self::TEXTDOMAIN),
            'not_found_in_trash' => __('No Slides found in trash', self::TEXTDOMAIN),
            'parent_item_colon' => __('Parent Slide', self::TEXTDOMAIN),
            'menu_name' => __('Slides', self::TEXTDOMAIN),
        ));

        /**
         * Default custom post arguments
         * 
         * @see Hyyan_Slider_Events::FILTER_SLIDE_ARGS
         * 
         * @var array 
         */
        $args = apply_filters(Hyyan_Slider_Events::FILTER_SLIDE_ARGS, array(
            'labels' => $labels,
            'public' => false,
            'exclude_from_search' => true,
            'show_ui' => true,
            'show_in_admin_bar' => true,
            'rewrite' => false,
            'query_var' => false,
            'menu_position' => 7,
            'menu_icon' => 'dashicons-images-alt2',
            'supports' => array('title', 'thumbnail', 'excerpt', 'editor')
        ));

        /*
         * Register the post type
         */
        register_post_type(self::CUSTOM_POST, $args);

        /**
         * Register the custom post messages
         * 
         * @see HyyanSlider::addCutomPostMessages
         */
        add_filter('post_updated_messages', array($this, 'addCutomPostMessages'));
    }

    /**
     * Add custom post messages
     * 
     * Register the custom post messages
     * 
     * @see Hyyan_Slider_Events::FILTER_SLIDE_MESSAGES
     * 
     * @global WP_Post $post
     * @param array $messages
     * 
     * @return array new messages
     */
    public function addCutomPostMessages($messages) {

        global $post;


        /**
         * Add custom post messages
         * 
         * @see Hyyan_Slider_Events::FILTER_SLIDE_MESSAGES
         */
        $messages['hyyan-slide'] = apply_filters(Hyyan_Slider_Events::FILTER_SLIDE_MESSAGES, array(
            0 => '', // Unused. Messages start at index 1.
            1 => __('Slide updated.', self::TEXTDOMAIN),
            2 => __('Custom field updated.', self::TEXTDOMAIN),
            3 => __('Custom field deleted.', self::TEXTDOMAIN),
            4 => __('Slide updated.', self::TEXTDOMAIN),
            /* translators: %s: date and time of the revision */
            5 => isset($_GET['revision']) ? sprintf(__('Slide restored to revision from %s', self::TEXTDOMAIN), wp_post_revision_title((int) $_GET['revision'], false)) : false,
            6 => __('Slide published.', self::TEXTDOMAIN),
            7 => __('Slide saved.', self::TEXTDOMAIN),
            8 => __('Slide submitted.', self::TEXTDOMAIN),
            9 => sprintf(__('Slide scheduled for: <strong>%1$s</strong>', self::TEXTDOMAIN),
                    // translators: Publish box date format, see http://php.net/date
                    date_i18n(__('M j, Y @ G:i'), strtotime($post->post_date))),
            10 => __('Slide draft updated.', self::TEXTDOMAIN),
        ));

        return $messages;
    }

    /**
     * Add custom taxonomy
     * 
     * Register custom taxonomy to be used with this custom post
     * 
     * @see Hyyan_Slider_Events::FILTER_SLIDER_LABLES
     * @see Hyyan_Slider_Events::FILTER_SLIDER_ARGS
     */
    public function addCustomTaxonomy() {
        /**
         * Default custom taxonomy lables
         * 
         * @see Hyyan_Slider_Events::FILTER_SLIDER_LABLES
         * 
         * @var array 
         */
        $labels = apply_filters(Hyyan_Slider_Events::FILTER_SLIDER_LABLES, array(
            'name' => __('Sliders', self::TEXTDOMAIN),
            'singular_name' => _x('Slider', 'taxonomy general name', self::TEXTDOMAIN),
            'search_items' => __('Search Sliders', self::TEXTDOMAIN),
            'popular_items' => __('Popular Sliders', self::TEXTDOMAIN),
            'all_items' => __('All Sliders', self::TEXTDOMAIN),
            'parent_item' => __('Parent Slider', self::TEXTDOMAIN),
            'parent_item_colon' => __('Parent Slider:', self::TEXTDOMAIN),
            'edit_item' => __('Edit Slider', self::TEXTDOMAIN),
            'update_item' => __('Update Slider', self::TEXTDOMAIN),
            'add_new_item' => __('New Slider', self::TEXTDOMAIN),
            'new_item_name' => __('New Slider', self::TEXTDOMAIN),
            'separate_items_with_commas' => __('Sliders separated by comma', self::TEXTDOMAIN),
            'add_or_remove_items' => __('Add or remove Sliders', self::TEXTDOMAIN),
            'choose_from_most_used' => __('Choose from the most used Sliders', self::TEXTDOMAIN),
            'menu_name' => __('Sliders', self::TEXTDOMAIN),
        ));

        /**
         * Default custom taxonomy arguments
         * 
         * @see Hyyan_Slider_Events::FILTER_SLIDER_ARGS
         * 
         * @var array 
         */
        $args = apply_filters(Hyyan_Slider_Events::FILTER_SLIDER_ARGS, array(
            'labels' => $labels,
            'hierarchical' => false,
            'public' => false,
            'show_in_nav_menus' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => false,
            'rewrite' => false,
            'capabilities' => array(
                'manage_terms' => 'edit_posts',
                'edit_terms' => 'edit_posts',
                'delete_terms' => 'edit_posts',
                'assign_terms' => 'edit_posts'
            ),
        ));

        /**
         * Register the custom taxonomy
         */
        register_taxonomy(self::CUSTOM_TAXONOMY, array(self::CUSTOM_POST), $args);
    }

}
