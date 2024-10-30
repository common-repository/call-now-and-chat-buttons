<?php
if (!defined('ABSPATH')) exit;
/*
 * Plugin Name: Call Now and Chat Buttons
 * Description: Add instant "Call Now" and "WhatsApp" buttons to your website, allowing visitors to seamlessly contact you with a single click.
 * Version: 1.2.0
 * Author: Dalinovate
 * Author URI: https://dalinovate.com/en/wordpress-plugin-development-agency/
 * License: GPL2
 */

function CNACB_enqueue_frontend_styles()
{
    wp_enqueue_style('CNACB_styles', plugins_url('css/styles.css', __FILE__));
    wp_enqueue_style('bootstrap-icons', plugins_url('css/bootstrap-icons.min.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'CNACB_enqueue_frontend_styles');

function CNACB_display_widget_in_footer()
{
    CNACB_widget_html();
}
add_action('wp_footer', 'CNACB_display_widget_in_footer');

function CNACB_create_settings_page()
{
    add_options_page('Call now and Chat Settings', 'CNACB Settings', 'manage_options', 'CNACB_settings', 'CNACB_settings_page_content');
}
add_action('admin_menu', 'CNACB_create_settings_page');

function CNACB_settings_page_content()
{
    ?>
    <div class="wrap">
        <h2>Call Now and Chat Buttons</h2>
        <form method="post" action="options.php">
            <?php
            settings_fields('CNACB_settings_group');
            do_settings_sections('CNACB_settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

function CNACB_display_phone_field()
{
    $phone = esc_attr(get_option('CNACB_phone_number', ''));
    echo "<input type='text' name='CNACB_phone_number' value='" . esc_attr($phone) . "' />";
}

function CNACB_display_call_color_field()
{
    $color = esc_attr(get_option('CNACB_call_color', '#007bff'));
    echo "<input type='color' name='CNACB_call_color' value='" . esc_attr($color) . "' />";
}

function CNACB_display_chat_color_field()
{
    $color = esc_attr(get_option('CNACB_chat_color', '#25d366'));
    echo "<input type='color' name='CNACB_chat_color' value='" . esc_attr($color) . "' />";
}

function CNACB_display_whatsapp_button_field()
{
    $whatsapp_enabled = esc_attr(get_option('CNACB_whatsapp_enabled', '1'));
    ?>
    <input type="radio" name="CNACB_whatsapp_enabled" value="1" <?php checked($whatsapp_enabled, '1'); ?> /> Enable
    <input type="radio" name="CNACB_whatsapp_enabled" value="0" <?php checked($whatsapp_enabled, '0'); ?> /> Disable
    <?php
}

function CNACB_register_settings()
{
    add_settings_section('CNACB_main_section', 'Main Settings', null, 'CNACB_settings');

    add_settings_field('CNACB_phone_number', 'Phone Number', 'CNACB_display_phone_field', 'CNACB_settings', 'CNACB_main_section');
    register_setting('CNACB_settings_group', 'CNACB_phone_number');

    add_settings_field('CNACB_call_color', 'Call Button Color', 'CNACB_display_call_color_field', 'CNACB_settings', 'CNACB_main_section');
    register_setting('CNACB_settings_group', 'CNACB_call_color');

    add_settings_field('CNACB_call_text', 'Call Button Text', 'CNACB_display_call_text_field', 'CNACB_settings', 'CNACB_main_section');
    register_setting('CNACB_settings_group', 'CNACB_call_text');

    add_settings_field('CNACB_chat_color', 'Chat Button Color', 'CNACB_display_chat_color_field', 'CNACB_settings', 'CNACB_main_section');
    register_setting('CNACB_settings_group', 'CNACB_chat_color');

    add_settings_field('CNACB_chat_number', 'chat Number', 'CNACB_display_chat_field', 'CNACB_settings', 'CNACB_main_section');
    register_setting('CNACB_settings_group', 'CNACB_chat_number');

    add_settings_field('CNACB_whatsapp_enabled', 'Enable WhatsApp Button', 'CNACB_display_whatsapp_button_field', 'CNACB_settings', 'CNACB_main_section');
    register_setting('CNACB_settings_group', 'CNACB_whatsapp_enabled');

    add_settings_field('CNACB_widget_size', 'Button Size', 'CNACB_display_size_field', 'CNACB_settings', 'CNACB_main_section');
    register_setting('CNACB_settings_group', 'CNACB_widget_size');
    
    add_settings_field('CNACB_whatsapp_qr_code_enabled', 'Enable WhatsApp QR Code on Desktop', 'CNACB_display_whatsapp_qr_code_field', 'CNACB_settings', 'CNACB_main_section');
    register_setting('CNACB_settings_group', 'CNACB_whatsapp_qr_code_enabled');

}
add_action('admin_init', 'CNACB_register_settings');

function CNACB_display_chat_field()
{
    $chat = esc_attr(get_option('CNACB_chat_number', ''));
    echo "<input type='text' name='CNACB_chat_number' value='" . esc_attr($chat) . "' />";
}

function CNACB_display_call_text_field()
{
    $call_text = esc_attr(get_option('CNACB_call_text', 'Call Us Now'));
    echo "<input type='text' name='CNACB_call_text' value='" . esc_attr($call_text) . "' />";
}

function CNACB_display_size_field()
{
    $size = esc_attr(get_option('CNACB_widget_size', 'medium'));
    $sizes = ['small', 'medium', 'large'];
    echo "<select name='CNACB_widget_size'>";
    foreach ($sizes as $s) {
        $selected = $size == $s ? 'selected' : '';
        echo "<option value='" . esc_attr($s) . "' " . esc_attr($selected) . ">" . esc_html($s) . "</option>";
    }
    echo "</select>";
}

function CNACB_display_whatsapp_qr_code_field() {
    $whatsapp_qr_enabled = esc_attr(get_option('CNACB_whatsapp_qr_code_enabled', '0')); // Default disabled
    ?>
    <label>
        <input type="radio" name="CNACB_whatsapp_qr_code_enabled" value="1" <?php checked($whatsapp_qr_enabled, '1'); ?> />
        <?php _e('Enable', 'your-text-domain'); ?>
    </label>
    <label>
        <input type="radio" name="CNACB_whatsapp_qr_code_enabled" value="0" <?php checked($whatsapp_qr_enabled, '0'); ?> />
        <?php _e('Disable', 'your-text-domain'); ?>
    </label>
    <?php
}

function CNACB_widget_html()
{
    $phone = esc_attr(get_option('CNACB_phone_number', '999999'));
    $chat_number = esc_attr(get_option('CNACB_chat_number', '999999'));
    $call_color = esc_attr(get_option('CNACB_call_color', '#007bff'));
    $call_text = esc_html(get_option('CNACB_call_text', 'Call Us Now'));
    $chat_color = esc_attr(get_option('CNACB_chat_color', '#25d366'));
    $size = esc_attr(get_option('CNACB_widget_size', 'medium'));
    $position = esc_attr(get_option('CNACB_widget_position', 'bottom-right'));
    $whatsapp_enabled = esc_attr(get_option('CNACB_whatsapp_enabled', '1'));
    $whatsapp_qr_enabled = esc_attr(get_option('CNACB_whatsapp_qr_code_enabled', '0'));

    $size_styles = [
        'small' => 'padding: 5px 10px; font-size: 12px;',
        'medium' => 'padding: 10px 15px; font-size: 16px;',
        'large' => 'padding: 15px 20px; font-size: 20px;',
    ];

    $size_style = isset($size_styles[$size]) ? $size_styles[$size] : '';

    ?>
    <div class="<?php echo esc_attr($whatsapp_enabled) == '0' ? 'call-widget-full' : ''; echo esc_attr($whatsapp_enabled) == '1' ? 'call-widget' : ''?>" style="background-color: <?php echo esc_attr($call_color); ?>; <?php echo esc_attr($size_style); ?>">
        <a href="tel:<?php echo esc_attr($phone); ?>" class="call-link"><i class="bi-telephone"></i> <?php echo esc_attr($call_text); ?></a>
    </div>
    <?php if ($whatsapp_enabled == '1') { ?>
        <div class="whatsapp-widget whatsapp-widget-left" style="background-color: <?php echo esc_attr($chat_color); ?>; <?php echo esc_attr($size_style); ?>">
            <a href="<?php echo esc_url("https://api.whatsapp.com/send?phone=" . $chat_number); ?>" class="whatsapp-link"><i class="bi bi-whatsapp"></i> WhatsApp</a>
        </div>
    <?php } ?>
    <?php
    
    if ($whatsapp_qr_enabled == '1') {
        ?>
        <script>
            if (window.innerWidth > 768) {
                var qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=90x90&data=https://api.whatsapp.com/send?phone=<?php echo esc_js($chat_number); ?>";
                
                var qrCodeContainer = document.createElement("div");
                qrCodeContainer.className = "whatsapp-qr-container";
    
                var qrBackground = document.createElement("div");
                qrBackground.className = "whatsapp-qr-background";
    
                var qrImage = document.createElement("img");
                qrImage.src = qrCodeUrl;
                qrImage.alt = "Scan this QR code to contact us on WhatsApp";
    
                qrBackground.appendChild(qrImage);
                qrCodeContainer.appendChild(qrBackground);
                document.body.appendChild(qrCodeContainer);
            }
        </script>
        <?php
    }
}

function CNACB_add_settings_link($links)
{
    $settings_link = '<a href="admin.php?page=CNACB_settings">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'CNACB_add_settings_link');