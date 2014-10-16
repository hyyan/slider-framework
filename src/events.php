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

}
