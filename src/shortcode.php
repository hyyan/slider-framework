<?php

/*
 * This file is part of the hyyan/slider package.
 * (c) Hyyan Abo Fakher <tiribthea4hyyan@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__ . '/events.php';

/**
 * Slider shortcode
 *
 * @author Hyyan
 */
class Hyyan_Slider_Shortcode {

    /**
     * The default shortcode name
     * 
     * @var string 
     */
    const SHORTCODE_NAME = 'slider';

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
     * Constrcut
     * 
     * Add slider shorcode
     * 
     * @param string $postName the custom post name
     * @param string $taxName the custom tax name
     * @param string $textdomain the translation text domain
     */
    public function __construct($postName, $taxName, $textdomain) {
        $this->postName = $postName;
        $this->taxName = $taxName;
        $this->textdomain = $textdomain;
    }

    /**
     * Resolve the shortcode
     *
     * @see Hyyan_Slider_Events::FILTER_SHORTCODE_TYPES
     * @see Hyyan_Slider_Events::FILTER_SHORTCODE_ATTS 
     * @see Hyyan_Slider_Events::FILTER_SHORTCODE_QueryArgs
     * @see Hyyan_Slider_Events::FILTER_SHORTCODE_CONTENT
     * @see Hyyan_Slider_Shortcode::FILTER_SHORTCODE_RESPONSE
     * 
     * @param array $atts
     * @param string $content
     *
     * @return string
     */
    public function resolveSliderShortcode(array $atts, $content = null) {

        /**
         * collect sliders types
         * 
         * @see Hyyan_Slider_Events::FILTER_SHORTCODE_TYPES
         * 
         * @var array 
         */
        $types = apply_filters(Hyyan_Slider_Events::FILTER_SHORTCODE_TYPES, array());

        /**
         * filter default shortcode atts
         * 
         * @see Hyyan_Slider_Events::FILTER_SHORTCODE_TYPES
         * 
         * @var array 
         */
        $default = apply_filters(Hyyan_Slider_Events::FILTER_SHORTCODE_ATTS, shortcode_atts(array(
            'name' => '',
            'type' => '',
            'order' => 'DESC',
            'orderBy' => 'rand')
                        , $atts)
        );

        if (!isset($default['name']) || !term_exists($default['name'], $this->taxName))
            return sprintf(
                    '<pre>%s %s</pre>'
                    , $default['name']
                    , __('Slider does not exist.', $this->textdomain)
            );


        if (!isset($default['type']) || !array_key_exists($default['type'], $types))
            return sprintf('<pre>%s</pre>', __('Unsupported slider type.', $this->textdomain));

        /**
         * Query object
         * 
         * @see Hyyan_Slider_Shortcode::FILTER_SHORTCODE_QueryArgs
         * 
         * @var WP_Query 
         */
        $query = new WP_Query(apply_filters(Hyyan_Slider_Events::FILTER_SHORTCODE_QueryArgs, array(
                    'post_type' => $this->postName,
                    $this->taxName => esc_attr($default['name']),
                    'post_status' => 'publish',
                    'order' => esc_attr($default['order']),
                    'orderby' => esc_attr($default['orderBy']),
                    'posts_per_page' => -1
        )));

        $callable = $types[$default['type']];
        if (!is_callable($callable))
            return sprintf(
                    '<pre>%s %s</pre>'
                    , (string) $default['type']
                    , __(
                            'does not provide a callable function or method to '
                            . 'generate the content.'
                            , $this->textdomain
                    )
            );

        /**
         * Shorcode content
         * 
         * @see Hyyan_Slider_Events::FILTER_SHORTCODE_CONTENT
         * 
         * @var string 
         */
        $content = apply_filters(Hyyan_Slider_Events::FILTER_SHORTCODE_CONTENT, $content);
        
        /** @see Hyyan_Slider_Events::FILTER_SHORTCODE_RESPONSE */
        return apply_filters(
                Hyyan_Slider_Events::FILTER_SHORTCODE_RESPONSE
                , call_user_func_array(
                        $callable
                        , array($default, $content, $query)
                )
        );
    }

    /**
     * Register the slider shortcode 
     * 
     * @see Hyyan_Slider_Shortcode::FILTER_SHORTCODE_NAME
     */
    public function registerSliderShortcode() {
        add_shortcode(
                apply_filters(Hyyan_Slider_Events::FILTER_SHORTCODE_NAME, self::SHORTCODE_NAME)
                , array($this, 'resolveSliderShortcode')
        );
    }

}
