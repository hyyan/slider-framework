<?php

/*
 * This file is part of the hyyan/slider package.
 * (c) Hyyan Abo Fakher <tiribthea4hyyan@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Build WP_Query object for the given slider
 * 
 * @see Hyyan_Slider_Shortcode::FILTER_SHORTCODE_QueryArgs
 * 
 * @param string $slider slider name
 * @param string $order slides order
 * @param string $orderBy order slides by ?
 * 
 * @return \WP_Query
 * @throws \RuntimeException if the slider does not exist
 */
function hyyan_slider_query($slider, $order = 'DESC', $orderBy = 'rand') {

    $slider = esc_attr((string) $slider);

    /** check for term existance */
    if (!term_exists($slider, Hyyan_Slider::CUSTOM_TAXONOMY))
        throw new \RuntimeException(sprintf(
                'Can not build query for %s slider - term does not exist'
                , $slider
        ));
    
    /**
     * Query object
     * 
     * @see Hyyan_Slider_Shortcode::FILTER_SHORTCODE_QueryArgs
     * 
     * @var WP_Query 
     */
    $query = new WP_Query(apply_filters(Hyyan_Slider_Events::FILTER_SHORTCODE_QueryArgs, array(
                'post_type' => Hyyan_Slider::CUSTOM_POST,
                'taxonomy' => Hyyan_Slider::CUSTOM_TAXONOMY,
                'term' => function_exists('pll_get_term') ? @pll_get_term($slider) : $slider,
                'post_status' => 'publish',
                'order' => esc_attr($order),
                'orderby' => esc_attr($orderBy),
                'posts_per_page' => -1
    )));

    return $query;
}

/**
 * Get list of slider names
 * 
 * @return array
 */
function hyyan_slider_list() {

    $terms = get_terms(Hyyan_Slider::CUSTOM_TAXONOMY, array(
        'orderby' => 'count',
        'hide_empty' => 0
    ));

    $result = array();

    foreach ($terms as $value) {
        $result[$value->name] = $value->name;
    }

    return $result;
}

/**
 * Get slide url
 * 
 * @param int $id the slide id
 * 
 * @return string slide url
 */
function hyyan_slide_url($id) {
    return get_post_meta($id, 'hyyan-slide-url', true);
}

/**
 * Check if the slide url must be open in new window
 * 
 * @param int $id the slide id
 * 
 * @return bool true if must be open in new window , false otherwise
 */
function hyyan_slide_url_inNewWindow($id) {
    return get_post_meta($id, 'hyyan-slide-new-window', true) == 1 ? true : false;
}
