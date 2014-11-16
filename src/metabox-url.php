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
 * metabox-url
 *
 * @author Hyyan
 */
class Hyyan_Slider_Metabox_URL {

    /**
     * The metabox id
     * 
     * @var string 
     */
    const METABOX_ID = 'hyyan-slider-metabox-url';

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
     * Register the metabox
     */
    public function registerMetabox() {
        add_action('add_meta_boxes', array($this, 'addMetabox'));
        add_action('save_post', array($this, 'saveMetabox'), 10, 2);
    }

    /**
     * Add the url metabox
     * 
     * @see Hyyan_Slider_Events::FIILTER_METABOX_URL_TITLE
     */
    public function addMetabox() {
        add_meta_box(
                self::METABOX_ID
                , apply_filters(
                        Hyyan_Slider_Events::FIILTER_METABOX_URL_TITLE
                        , esc_html__('Slide URL', $this->textdomain)
                )
                , array($this, 'metaboxMarkup')
                , $this->postName
                , apply_filters(
                        Hyyan_Slider_Events::FIILTER_METABOX_URL_CONTEXT
                        , 'side'
                )
                , apply_filters(
                        Hyyan_Slider_Events::FIILTER_METABOX_URL_PRIORITY
                        , 'high'
                )
        );
    }

    /**
     * Print the metabox markup
     * 
     * @see Hyyan_Slider_Events::FILTER_METABOX_URL_MARKUP
     * 
     * @param WP_Post $post The object for the current post/page.
     * @param array $metabox an array with metabox id, title, callback, and args elements
     */
    public function metaboxMarkup($post, $metabox) {
        ob_start();
        include __DIR__ . '/views/metabox-url.php';
        print apply_filters(
                        Hyyan_Slider_Events::FILTER_METABOX_URL_MARKUP
                        , ob_get_clean()
        );
    }

    /**
     * Save metabox
     * 
     * @param integer $id the post id
     * 
     * @return int post id if validation failed
     */
    public function saveMetabox($id) {

        $nonce = self::METABOX_ID;
        $url_input = self::METABOX_ID . '-input-url';
        $checkbox_input = self::METABOX_ID . '-input-checkbox';

        /*
         * We need to verify this came from the our screen and with proper authorization,
         * because save_post can be triggered at other times.
         */

        // Check if our nonce is set.
        if (!isset($_POST[$nonce]))
            return $id;

        $nonce = $_POST[$nonce];

        // Verify that the nonce is valid.
        if (!wp_verify_nonce($nonce, self::METABOX_ID))
            return $id;

        // If this is an autosave, our form has not been submitted,
        // so we don't want to do anything.
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return $id;

        // Check the user's permissions.
        if ($this->postName == $_POST['post_type']) {

            if (!current_user_can('edit_post', $id))
                return $id;
        } else {

            if (!current_user_can('edit_post', $id))
                return $id;
        }

        /* OK, its safe for us to save the data now. */

        // Sanitize the user inputs.
        $url = esc_url($_POST[$url_input]);
        $target = isset($_POST[$checkbox_input]);

        // Update the meta field.
        update_post_meta($id, 'hyyan-slide-url', $url);
        update_post_meta($id, 'hyyan-slide-new-window', $target ? 1 : 0);
    }

}
