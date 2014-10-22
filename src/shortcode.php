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
    const SHORTCODE_NAME = 'hyyan-slider';

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
            'orderBy' => 'rand',
            'class' => 'default'), $atts
        ));

        if (!isset($default['name']) || !term_exists($default['name'], $this->taxName))
            return sprintf(
                    '<pre>%s %s</pre>'
                    , $default['name']
                    , __('Slider does not exist.', $this->textdomain)
            );


        if (!isset($default['type']) || !array_key_exists($default['type'], $types))
            return sprintf(
                    '<pre>%s "%s"</pre>'
                    , __('Unsupported slider type', $this->textdomain)
                    , $default['type']
            );

        $type = $default['type'];
        
        if (
                !isset($default['class']) ||
                !((isset($types[$type]['classes']) && is_array($types[$type]['classes'])) && array_key_exists($default['class'], $types[$type]['classes']))
        )
            return sprintf(
                    '<pre>%s "%s" %s "%s"</pre>'
                    , __('Slider', $this->textdomain)
                    , $default['type']
                    , __('does not support class', $this->textdomain)
                    , $default['class']
            );

        /**
         * Query object
         * 
         * @see Hyyan_Slider_Shortcode::FILTER_SHORTCODE_QueryArgs
         * 
         * @var WP_Query 
         */
        $query = hyyan_slider_query(
                $default['name']
                , $default['order']
                , $default['orderBy']
        );

        $callable = @$types[$type]['handler'];
        if (!is_callable($callable))
            return sprintf(
                    '<pre>%s %s</pre>'
                    , $type
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
                        , array($default, $content, $query, $default['class'])
                )
        );
    }

    /**
     * Add support fot Shortcode ulitmate plugin
     * 
     * Note : 
     * =========
     * A hook named "Hyyan\Slider.shortcode-ulitmate.your-slider-type-name" is 
     * fired to extend the default controll.
     * 
     * for instance : 
     * 
     * if your slider type name is : (foo) then the hook name 
     * will be (Hyyan\Slider.shortcode-ulitmate.foo).
     * 
     * @param array $shortcodes
     * 
     * @return array
     * 
     * @link http://gndev.info/shortcodes-ultimate/ 
     */
    public function supportShortcodeUltimate(array $shortcodes) {

        /**
         * collect sliders types
         * 
         * @see Hyyan_Slider_Events::FILTER_SHORTCODE_TYPES
         * 
         * @var array 
         */
        $types = apply_filters(Hyyan_Slider_Events::FILTER_SHORTCODE_TYPES, array());

        /** add controll for all types */
        foreach ($types as $name => $info) {

            $displayName = ucwords(str_replace(
                            array('-', '_')
                            , ' '
                            , $name
            ));

            $sliders = array_merge(
                    array('' => '---')
                    , hyyan_slider_list()
            );

            // Add new shortcode
            $args = apply_filters('Hyyan\Slider.shortcode-ulitmate.' . $name, array(
                // Shortcode name
                'name' => $displayName,
                // Shortcode type. Can be 'wrap' or 'single'
                // Example: [b]this is wrapped[/b], [this_is_single]
                'type' => 'single',
                // Shortcode group.
                // Can be 'content', 'box', 'media' or 'other'.
                // Groups can be mixed, for example 'content box'
                'group' => 'media gallery',
                // List of shortcode params (attributes)
                'atts' => array(
                    'slider' => array(
                        // Attribute type.
                        // Can be 'select', 'color', 'bool' or 'text'
                        'type' => 'select',
                        // Available values
                        'values' => $sliders,
                        // Default value
                        'default' => '',
                        // Attribute name
                        'name' => __('Slider', $this->textdomain),
                        // Attribute description
                        'desc' => __('Select Slider Name', $this->textdomain)
                    ),
                    'order' => array(
                        // Attribute type.
                        // Can be 'select', 'color', 'bool' or 'text'
                        'type' => 'select',
                        // Available values
                        'values' => array(
                            'ASC' => __('Ascending ', $this->textdomain),
                            'DESC' => __('Descending ', $this->textdomain),
                        ),
                        // Default value
                        'default' => 'DESC',
                        // Attribute name
                        'name' => __('Slides Order', $this->textdomain),
                        // Attribute description
                        'desc' => __('Select Slides Order', $this->textdomain)
                    ),
                    'orderBy' => array(
                        // Attribute type.
                        // Can be 'select', 'color', 'bool' or 'text'
                        'type' => 'select',
                        // Available values
                        'values' => array(
                            'none' => __('No order', $this->textdomain),
                            'ID' => __('Order by slide id', $this->textdomain),
                            'author' => __('Order by author', $this->textdomain),
                            'title' => __('Order by title', $this->textdomain),
                            'name' => __('Order by slide name', $this->textdomain),
                            'date' => __('Order by date', $this->textdomain),
                            'modified' => __('Order by last modified date', $this->textdomain),
                            'rand' => __('Random order', $this->textdomain),
                        ),
                        // Default value
                        'default' => 'rand',
                        // Attribute name
                        'name' => __('Order Slides By', $this->textdomain),
                        // Attribute description
                        'desc' => __('Organize slides order', $this->textdomain)
                    ),
                    'class' => array(
                        // Attribute type.
                        // Can be 'select', 'color', 'bool' or 'text'
                        'type' => 'select',
                        // Available values
                        'values' => isset($info['classes']) ? $info['classes'] : array(),
                        // Default value
                        'default' => 'default',
                        // Attribute name
                        'name' => __('Slider Class', $this->textdomain),
                        // Attribute description
                        'desc' => __('Choose slider class', $this->textdomain)
                    )
                ),
                'content' => '',
                // Shortcode description for cheatsheet and generator
                'desc' => $displayName,
                // Custom icon (font-awesome)
                'icon' => 'photo',
                // Name of custom shortcode function
                'function' => isset($info['handler']) ? $info['handler'] : ''
            ));

            $shortcodes[$name] = $args;
        }

        // Return modified data
        return $shortcodes;
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

    /**
     * Add support for Shortcode ultimate plugin
     * 
     * @see Hyyan_Slider_Shortcode::supportShortcodeUltimate
     * 
     * @link http://gndev.info/shortcodes-ultimate/ 
     */
    public function registerShortcodeUltimateShortcode() {
        add_filter('su/data/shortcodes', array($this, 'supportShortcodeUltimate'));
    }

}
