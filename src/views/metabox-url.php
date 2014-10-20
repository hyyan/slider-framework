<?php
$url_input = self::METABOX_ID . '-input-url';
$checkbox_input = self::METABOX_ID . '-input-checkbox';
?>

<?php wp_nonce_field(self::METABOX_ID, self::METABOX_ID); ?>
<p>
    <label for="<?php echo $url_input ?>">
        <?php _e("Slide URL  :", $this->textdomain); ?>
    </label>
    <br/>
    <input 
        class="widefat" 
        type="url" 
        name="<?php echo $url_input ?>" 
        id="<?php echo $url_input ?>" 
        value="<?php echo esc_url(get_post_meta($post->ID, 'hyyan-slide-url', true)); ?>"
        />
    <small>
        <em><?php _e('(optional - leave blank for no link)', $this->textdomain); ?>
        </em>
    </small>
    <br/>
</p>

<p>
    <label for="<?php echo $checkbox_input ?>">
        <input 
            class="widefat" 
            type="checkbox" 
            name="<?php echo $checkbox_input ?>" 
            id="<?php echo $checkbox_input ?>" 
            <?php
            if (get_post_meta($post->ID, 'hyyan-slide-new-window', true) == '1') {
                echo ' checked="checked"';
            }
            ?>
            />

        <?php _e("Open slide link in new widnow.", $this->textdomain); ?>
    </label>
    <br/>
</p>