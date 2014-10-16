<?php

/*
 * This file is part of the hyyan/slider package.
 * (c) Hyyan Abo Fakher <tiribthea4hyyan@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__ . '/HyyanSliderEvents.php';

/**
 * HyyanSlider
 * 
 * The class will create the basic api to create slider of any type
 *
 * @author Hyyan
 */
class HyyanSlider {

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
    }

    /**
     * Add custom post
     * 
     * Register the custom post 
     * 
     * @see HyyanSliderEvents::FILTER_SLIDE_LABLES
     * @see HyyanSliderEvents::FILTER_SLIDE_ARGS
     * @see HyyanSlider::addCutomPostMessages
     */
    public function addCustomPost() {
        /**
         * Default custom post lables
         * 
         * @see HyyanSliderEvents::FILTER_SLIDE_LABLES
         * 
         * @var array 
         */
        $labels = apply_filters(HyyanSliderEvents::FILTER_SLIDE_LABLES, array(
            'name' => __('Slides', 'hyyan-slider'),
            'singular_name' => __('Slide', 'hyyan-slider'),
            'all_items' => __('Slides', 'hyyan-slider'),
            'new_item' => __('New Slide', 'hyyan-slider'),
            'add_new' => __('Add New', 'hyyan-slider'),
            'add_new_item' => __('Add New Slide', 'hyyan-slider'),
            'edit_item' => __('Edit Slide', 'hyyan-slider'),
            'view_item' => __('View Slide', 'hyyan-slider'),
            'search_items' => __('Search Slides', 'hyyan-slider'),
            'not_found' => __('No Slides found', 'hyyan-slider'),
            'not_found_in_trash' => __('No Slides found in trash', 'hyyan-slider'),
            'parent_item_colon' => __('Parent Slide', 'hyyan-slider'),
            'menu_name' => __('Slides', 'hyyan-slider'),
        ));

        /**
         * Default custom post arguments
         * 
         * @see HyyanSliderEvents::FILTER_SLIDE_ARGS
         * 
         * @var array 
         */
        $args = apply_filters(HyyanSliderEvents::FILTER_SLIDE_ARGS, array(
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
        register_post_type('hyyan-slide', $args);

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
     * @see HyyanSliderEvents::FILTER_SLIDE_MESSAGES
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
         * @see HyyanSliderEvents::FILTER_SLIDE_MESSAGES
         */
        $messages['hyyan-slide'] = apply_filters(HyyanSliderEvents::FILTER_SLIDE_MESSAGES, array(
            0 => '', // Unused. Messages start at index 1.
            1 => __('Slide updated.', 'hyyan-slider'),
            2 => __('Custom field updated.', 'hyyan-slider'),
            3 => __('Custom field deleted.', 'hyyan-slider'),
            4 => __('Slide updated.', 'hyyan-slider'),
            /* translators: %s: date and time of the revision */
            5 => isset($_GET['revision']) ? sprintf(__('Slide restored to revision from %s', 'hyyan-slider'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
            6 => __('Slide published.', 'hyyan-slider'),
            7 => __('Slide saved.', 'hyyan-slider'),
            8 => __('Slide submitted.', 'hyyan-slider'),
            9 => sprintf(__('Slide scheduled for: <strong>%1$s</strong>', 'hyyan-slider'),
                    // translators: Publish box date format, see http://php.net/date
                    date_i18n(__('M j, Y @ G:i'), strtotime($post->post_date))),
            10 => __('Slide draft updated.', 'hyyan-slider'),
        ));

        return $messages;
    }

}
