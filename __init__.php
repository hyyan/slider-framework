<?php

/*
 * This file is part of the hyyan/slider package.
 * (c) Hyyan Abo Fakher <tiribthea4hyyan@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*
 * Plugin Name: Hyyan Slider Framework
 * Plugin URI: https://github.com/hyyan/slider-framework/
 * Description: Wordpress plugin to create wordpress sliders using custom post types and taxonomies
 * Author: Hyyan Abo Fakher
 * Version: 0.3
 * Author URI: https://github.com/hyyan
 * GitHub Plugin URI: hyyan/slider-framework
 * Domain Path: /languages
 * Text Domain: hyyan-slider-framework
 * License: MIT License
 */

if (!defined('ABSPATH'))
    exit('restricted access');


require_once __DIR__ . '/src/slider.php';

new Hyyan_Slider();
