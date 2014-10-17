<?php

/*
 * This file is part of the hyyan/slider package.
 * (c) Hyyan Abo Fakher <tiribthea4hyyan@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Hyyan Slider Events
 * 
 * An interface which contains all filters and actions used by this plugin
 * 
 * @author Hyyan
 */
interface Hyyan_Slider_Events {

    /**
     * Filter Slide Lables
     * 
     * the filter is fired to change the default lables of the custom post before
     * the cusotm post is registered.
     */
    const FILTER_SLIDE_LABLES = 'Hyyan\Slider.slide.labels';

    /**
     * Filter Slide Args
     * 
     * the filter is fired to change the default arguments of the custom post before
     * the cusotm post is registered.
     */
    const FILTER_SLIDE_ARGS = 'Hyyan\Slider.slide.ARGS';

    /**
     * Filter Slide Messages
     * 
     * the filter is fired to change the default messages of the custom post before
     * the cusotm post messages are registered.
     */
    const FILTER_SLIDE_MESSAGES = 'Hyyan\Slider.slide.messages';

    /**
     * Filter Slider Lables
     * 
     * the filter is fired to change the default lables of the custom taxonomy before
     * the cusotm taxonomy is registered.
     */
    const FILTER_SLIDER_LABLES = 'Hyyan\Slider.slider.lables';

    /**
     * Filter Slider Args
     * 
     * the filter is fired to change the default arguments of the custom taxonomy before
     * the cusotm taxonomy is registered.
     */
    const FILTER_SLIDER_ARGS = 'Hyyan\Slider.slider.args';

    /**
     * Filter slide preview size
     * 
     * the filter is fired to change the default size name used to display the slide 
     * preview
     */
    const FILTER_SLIDE_PREVIEW_SIZE_NAME = 'Hyyan\Slider.slide.preview-size-name';

    /**
     * Filter shortcode types
     * 
     * the filter is fired to collect slider types before the shortcode is 
     * resolved
     */
    const FILTER_SHORTCODE_TYPES = 'Hyyan\Slider.shortcode-types';

    /**
     * Filter shortcode atts
     * 
     * the filter is fired to edit the default shortcode atts before the 
     * shortcode is resolved
     */
    const FILTER_SHORTCODE_ATTS = 'Hyyan\Slider.shortcode-atts';

    /**
     * Filter shortcode queryArgs
     * 
     * the filter is fired before the query args is passed to the WP_Query object
     */
    const FILTER_SHORTCODE_QueryArgs = 'Hyyan\Slider.shortcode-queryargs';

    /**
     * Filter shortcode contetn
     * 
     * the filter is fired before the shortcode content is passed to the shortcode
     * resolver
     */
    const FILTER_SHORTCODE_CONTENT = 'Hyyan\Slider.shortcode-content';

    /**
     * Filter shortcode response
     * 
     * The filter is fired before the shortcode response is returned
     */
    const FILTER_SHORTCODE_RESPONSE = 'Hyyan\Slider.shortcode-response';

    /**
     * Filter shortcode name
     * 
     * The filter is fired before the shortcode name is registered
     */
    const FILTER_SHORTCODE_NAME = 'Hyyan\Slider.shortcode-name';

}
