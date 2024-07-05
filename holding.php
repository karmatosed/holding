<?php
/**
 * Plugin Name: Holding Page
 * Description: Sets a specific page as the only viewable page on the front end, with all other pages hidden and password protected.
 * Version: 1.0
 * Author: Your Name
 */

// Admin Menu
function holding_admin_menu() {
    add_menu_page(
        'Holding Page Settings', // Page title
        'Holding Page', // Menu title
        'manage_options', // Capability required to access the menu
        'holding-page-settings', // Menu slug
        'holding_settings_page', // Callback function to display the menu page
        'dashicons-sticky', // Icon for the menu item
        30 // Position of the menu item
    );
}
add_action('admin_menu', 'holding_admin_menu');

// Enqueue Gutenberg Color Picker
function holding_enqueue_color_picker() {
    wp_enqueue_script('wp-color-picker');
    wp_enqueue_style('wp-color-picker');
}
add_action('admin_enqueue_scripts', 'holding_enqueue_color_picker');

// Settings Page
function holding_settings_page() {
    if (isset($_POST['holding_page_id'])) {
        update_option('holding_page_id', absint($_POST['holding_page_id']));
        update_option('holding_page_css', sanitize_textarea_field($_POST['holding_page_css']));
        update_option('holding_page_background_color', sanitize_text_field($_POST['holding_page_background_color'])); // Add option to save background color
        echo '<div id="message" class="updated fade"><p>' . esc_html__('Settings saved.', 'holding-page') . '</p></div>';
    }
    $holding_page_id = get_option('holding_page_id');
    $holding_page_css = get_option('holding_page_css');
    $holding_page_background_color = get_option('holding_page_background_color'); // Retrieve background color option
    ?>
    <div class="wrap" style="background-color: white; padding: 20px;">
        <h2><?php esc_html_e('Holding Page Settings', 'holding-page'); ?></h2>
        <form method="post" action="">
            <p style="display: flex; flex-direction: column;">
                <label for="holding_page_id"><?php esc_html_e('Holding Page:', 'holding-page'); ?></label>
                <?php wp_dropdown_pages(array('name' => 'holding_page_id', 'selected' => $holding_page_id)); ?>
            </p>
            <p style="display: flex; flex-direction: column;">
                <label for="holding_page_css"><?php esc_html_e('Custom CSS for Holding Page:', 'holding-page'); ?></label>
                <textarea name="holding_page_css" rows="10" cols="50" style="background-color: white;"><?php echo esc_textarea($holding_page_css); ?></textarea>
            </p>
            <p style="display: flex; flex-direction: column;"> <!-- Add input field for background color -->
                <label for="holding_page_background_color"><?php esc_html_e('Background Color:', 'holding-page'); ?></label>
                <input type="text" class="color-field" name="holding_page_background_color" value="<?php echo esc_attr($holding_page_background_color); ?>" />
            </p>
            <p>
                <input type="submit" value="<?php esc_attr_e('Save Settings', 'holding-page'); ?>" class="button-primary"/>
            </p>
        </form>
    </div>
    <script>
        jQuery(document).ready(function($) {
            $('.color-field').wpColorPicker();
        });
    </script>
    <?php
}

// Template Redirect
function holding_template_redirect() {
    // Check if the user is logged in and either an admin or just a logged-in user
    if (!is_page(get_option('holding_page_id')) && !current_user_can('manage_options') && !is_user_logged_in()) {
        wp_safe_redirect(get_permalink(get_option('holding_page_id')), 302);
        exit;
    }
}
add_action('template_redirect', 'holding_template_redirect');

// Custom Styling
function holding_custom_styles() {
    if (is_page(get_option('holding_page_id'))) {
        wp_enqueue_style('holding-page-css', plugins_url('holding-page.css', __FILE__));
    }
}
add_action('wp_enqueue_scripts', 'holding_custom_styles');