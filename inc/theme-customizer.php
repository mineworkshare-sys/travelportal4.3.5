<?php
/**
 * Theme Customizer - Unified Design & Page Options
 */

// Add theme customizer settings
function tour_portal_customize_register($wp_customize) {
    
    // 1. Unified Design Section
    $wp_customize->add_section('tp_design_settings', array(
        'title' => __('Design & Typography', 'tour-portal'),
        'priority' => 30,
    ));
    
    // Font Choices
    $font_choices = array(
        'Arial' => 'System Sans',
        'Roboto' => 'Roboto',
        'Open Sans' => 'Open Sans',
        'Montserrat' => 'Montserrat',
        'Lato' => 'Lato',
        'Oswald' => 'Oswald',
        'Playfair Display' => 'Playfair Display',
        'Poppins' => 'Poppins',
        'Raleway' => 'Raleway',
        'Merriweather' => 'Merriweather'
    );
    
    // Elements for typography
    $elements = array(
        'body' => __('Body Text', 'tour-portal'),
        'h1' => __('Heading 1', 'tour-portal'),
        'h2' => __('Heading 2', 'tour-portal'),
        'h3' => __('Heading 3', 'tour-portal'),
        'h4' => __('Heading 4', 'tour-portal'),
        'h5' => __('Heading 5', 'tour-portal'),
        'h6' => __('Heading 6', 'tour-portal')
    );
    
    // Typography Controls
    foreach ($elements as $id => $label) {
        // Font Family Dropdown
        $wp_customize->add_setting("tp_{$id}_font", array(
            'default' => ($id === 'body' ? 'Open Sans' : 'Montserrat'),
            'sanitize_callback' => 'sanitize_text_field',
        ));
        
        $wp_customize->add_control("tp_{$id}_font_ctrl", array(
            'label' => $label . ' ' . __('Font', 'tour-portal'),
            'section' => 'tp_design_settings',
            'settings' => "tp_{$id}_font",
            'type' => 'select',
            'choices' => $font_choices,
        ));
        
        // Font Size Input
        $wp_customize->add_setting("tp_{$id}_size", array(
            'default' => ($id === 'body' ? '16' : '32'),
            'sanitize_callback' => 'absint',
        ));
        
        $wp_customize->add_control("tp_{$id}_size_ctrl", array(
            'label' => $label . ' ' . __('Size (px)', 'tour-portal'),
            'section' => 'tp_design_settings',
            'settings' => "tp_{$id}_size",
            'type' => 'number',
            'input_attrs' => array(
                'min' => ($id === 'body' ? 12 : 20),
                'max' => ($id === 'body' ? 24 : 60),
                'step' => 1,
            ),
        ));
    }
    
    // Color Theme Picker
    $wp_customize->add_setting('tp_primary_color', array(
        'default' => '#0073aa',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'tp_primary_color_ctrl', array(
        'label' => __('Primary Theme Color', 'tour-portal'),
        'section' => 'tp_design_settings',
        'settings' => 'tp_primary_color',
    )));
    
    // Secondary Color
    $wp_customize->add_setting('tp_secondary_color', array(
        'default' => '#666666',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'tp_secondary_color_ctrl', array(
        'label' => __('Secondary Theme Color', 'tour-portal'),
        'section' => 'tp_design_settings',
        'settings' => 'tp_secondary_color',
    )));
    
    // Accent Color
    $wp_customize->add_setting('tp_accent_color', array(
        'default' => '#ff6b35',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'tp_accent_color_ctrl', array(
        'label' => __('Accent Color', 'tour-portal'),
        'section' => 'tp_design_settings',
        'settings' => 'tp_accent_color',
    )));
    
    // 2. Site Identity Section - Enhanced
    $title_tagline_section = $wp_customize->get_section('title_tagline');
    if ($title_tagline_section) {
        $title_tagline_section->title = __('Site Identity', 'tour-portal');
    }
    
    // Logo/Text Toggle
    $wp_customize->add_setting('tour_portal_logo_or_text', array(
        'default' => 'logo',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('tour_portal_logo_or_text', array(
        'label' => __('Show Logo or Site Title', 'tour-portal'),
        'section' => 'title_tagline',
        'type' => 'radio',
        'choices' => array(
            'logo' => __('Logo', 'tour-portal'),
            'text' => __('Site Title', 'tour-portal'),
        ),
    ));
    
    // Logo Size Setting & Control
    $wp_customize->add_setting('logo_size', array(
        'default' => '150',
        'sanitize_callback' => 'absint',
        'transport' => 'refresh',
    ));
    
    $wp_customize->add_control('logo_size_control', array(
        'label' => __('Logo Width (px)', 'tour-portal'),
        'section' => 'title_tagline',
        'settings' => 'logo_size',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 50,
            'max' => 300,
            'step' => 10,
        ),
    ));
    
    // 3. Footer Section & Text Control
    $wp_customize->add_section('tp_footer_section', array(
        'title' => __('Footer Settings', 'tour-portal'),
        'priority' => 35,
    ));
    
    $wp_customize->add_setting('footer_text', array(
        'default' => 'Reimagine Travel',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));
    
    $wp_customize->add_control('footer_text_control', array(
        'label' => __('Footer Text', 'tour-portal'),
        'section' => 'tp_footer_section',
        'settings' => 'footer_text',
        'type' => 'text',
    ));
    
    // 4. Hero Section Settings
    $wp_customize->add_section('tp_hero_section', array(
        'title' => __('Hero Section', 'tour-portal'),
        'priority' => 25,
    ));
    
    $wp_customize->add_setting('hero_title', array(
        'default' => __('Discover Amazing Tours', 'tour-portal'),
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('hero_title_control', array(
        'label' => __('Hero Title', 'tour-portal'),
        'section' => 'tp_hero_section',
        'settings' => 'hero_title',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('hero_subtitle', array(
        'default' => __('Book unforgettable experiences with local guides', 'tour-portal'),
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('hero_subtitle_control', array(
        'label' => __('Hero Subtitle', 'tour-portal'),
        'section' => 'tp_hero_section',
        'settings' => 'hero_subtitle',
        'type' => 'textarea',
    ));
    
    $wp_customize->add_setting('hero_button_text', array(
        'default' => __('Explore Tours', 'tour-portal'),
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('hero_button_text_control', array(
        'label' => __('Hero Button Text', 'tour-portal'),
        'section' => 'tp_hero_section',
        'settings' => 'hero_button_text',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('hero_button_link', array(
        'default' => '#tours',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('hero_button_link_control', array(
        'label' => __('Hero Button Link', 'tour-portal'),
        'section' => 'tp_hero_section',
        'settings' => 'hero_button_link',
        'type' => 'text',
        'description' => __('Enter the URL or anchor link (e.g., #tours, /tours/, https://example.com)', 'tour-portal'),
    ));
    
    // 5. Layout Section
    $wp_customize->add_section('tp_layout_section', array(
        'title' => __('Layout Options', 'tour-portal'),
        'priority' => 40,
    ));
    
    // Sidebar Toggle
    $wp_customize->add_setting('tour_portal_show_sidebar', array(
        'default' => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('tour_portal_show_sidebar', array(
        'label' => __('Show Sidebar (Default)', 'tour-portal'),
        'section' => 'tp_layout_section',
        'type' => 'checkbox',
        'description' => __('Enable sidebar by default on pages', 'tour-portal'),
    ));
    
    // Page Title Toggle
    $wp_customize->add_setting('tour_portal_show_page_title', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('tour_portal_show_page_title', array(
        'label' => __('Show Page Titles', 'tour-portal'),
        'section' => 'tp_layout_section',
        'type' => 'checkbox',
        'description' => __('Show page titles by default (can be overridden per page)', 'tour-portal'),
    ));
    
    // 6. Social Media Section
    $wp_customize->add_section('tp_social_section', array(
        'title' => __('Social Media', 'tour-portal'),
        'priority' => 45,
    ));
    
    $social_links = array(
        'social_facebook' => __('Facebook URL', 'tour-portal'),
        'social_twitter' => __('Twitter URL', 'tour-portal'),
        'social_instagram' => __('Instagram URL', 'tour-portal'),
    );
    
    foreach ($social_links as $setting => $label) {
        $wp_customize->add_setting($setting, array(
            'sanitize_callback' => 'esc_url_raw',
        ));
        
        $wp_customize->add_control($setting . '_control', array(
            'label' => $label,
            'section' => 'tp_social_section',
            'settings' => $setting,
            'type' => 'url',
        ));
    }
}
// Add theme customizer
add_action('customize_register', 'tour_portal_customize_register');

// Load and Apply to Frontend
add_action('wp_head', 'tour_portal_customizer_css');

// Output customizer styles
function tour_portal_customizer_css() {
    $heading_font = get_theme_mod('tour_portal_heading_font', 'Montserrat');
    $body_font = get_theme_mod('tour_portal_body_font', 'Open Sans');
    $base_font_size = get_theme_mod('tour_portal_base_font_size', '16');
    $heading_font_size = get_theme_mod('tour_portal_heading_font_size', '32');
    $primary_color = get_theme_mod('tour_portal_primary_color', '#0073aa');
    $secondary_color = get_theme_mod('tour_portal_secondary_color', '#666666');
    $accent_color = get_theme_mod('tour_portal_accent_color', '#ff6b35');
    $logo_size = get_theme_mod('logo_size', '150');
    
    // Get all font choices for Google Fonts import
    $font_choices = array(
        'Arial' => 'System Sans',
        'Roboto' => 'Roboto',
        'Open Sans' => 'Open Sans',
        'Montserrat' => 'Montserrat',
        'Lato' => 'Lato',
        'Oswald' => 'Oswald',
        'Playfair Display' => 'Playfair Display',
        'Poppins' => 'Poppins',
        'Raleway' => 'Raleway',
        'Merriweather' => 'Merriweather',
    );
    
    // Collect unique fonts used
    $used_fonts = array();
    if ($heading_font !== 'Arial' && isset($font_choices[$heading_font])) {
        $used_fonts[$heading_font] = $font_choices[$heading_font];
    }
    if ($body_font !== 'Arial' && isset($font_choices[$body_font])) {
        $used_fonts[$body_font] = $font_choices[$body_font];
    }
    
    // Import Google Fonts
    if (!empty($used_fonts)) {
        $font_imports = array();
        foreach ($used_fonts as $font_name => $display_name) {
            $font_imports[] = $font_name;
        }
        $import = str_replace(' ', '+', implode('|', array_unique($font_imports)));
        echo "<link href='https://fonts.googleapis.com/css?family={$import}:400,700&display=swap' rel='stylesheet'>\n";
    }
    
    // Output CSS custom properties
    echo "<style>\n";
    echo "    :root {\n";
    echo "        --tp-primary-color: {$primary_color};\n";
    echo "        --tp-secondary-color: {$secondary_color};\n";
    echo "        --tp-accent-color: {$accent_color};\n";
    echo "        --tp-heading-font: '{$heading_font}', sans-serif;\n";
    echo "        --tp-body-font: '{$body_font}', sans-serif;\n";
    echo "        --tp-base-font-size: {$base_font_size}px;\n";
    echo "        --tp-heading-font-size: {$heading_font_size}px;\n";
    echo "        --tp-logo-size: {$logo_size}px;\n";
    echo "    }\n\n";
    
    echo "    body {\n";
    echo "        font-family: var(--tp-body-font);\n";
    echo "        font-size: var(--tp-base-font-size);\n";
    echo "        line-height: 1.6;\n";
    echo "        color: #333;\n";
    echo "    }\n\n";
    
    echo "    h1, h2, h3, h4, h5, h6 {\n";
    echo "        font-family: var(--tp-heading-font);\n";
    echo "        font-size: var(--tp-heading-font-size);\n";
    echo "        font-weight: 700;\n";
    echo "        line-height: 1.2;\n";
    echo "        margin-bottom: 1rem;\n";
    echo "        color: var(--tp-primary-color);\n";
    echo "    }\n\n";
    
    echo "    a {\n";
    echo "        color: var(--tp-primary-color);\n";
    echo "        text-decoration: none;\n";
    echo "        transition: color 0.3s ease;\n";
    echo "    }\n\n";
    
    echo "    a:hover {\n";
    echo "        color: var(--tp-accent-color);\n";
    echo "        text-decoration: underline;\n";
    echo "    }\n\n";
    
    echo "    .button, button, input[type='submit'] {\n";
    echo "        background-color: var(--tp-primary-color);\n";
    echo "        color: #fff;\n";
    echo "        border: none;\n";
    echo "        padding: 10px 20px;\n";
    echo "        text-decoration: none;\n";
    echo "        display: inline-block;\n";
    echo "        transition: all 0.3s ease;\n";
    echo "    }\n\n";
    
    echo "    .button:hover, button:hover, input[type='submit']:hover {\n";
    echo "        background-color: var(--tp-accent-color);\n";
    echo "        color: #fff;\n";
    echo "        text-decoration: none;\n";
    echo "    }\n\n";
    
    echo "    .custom-logo-link img {\n";
    echo "        width: var(--tp-logo-size) !important;\n";
    echo "        height: auto;\n";
    echo "    }\n\n";
    
    echo "</style>\n";
}

// Custom logo support is already added in functions.php

// Add missing sanitization function if not available
if (!function_exists('sanitize_hex_color')) {
    function sanitize_hex_color($color) {
        if (preg_match('|^#([A-Fa-f0-9]{3}){0,1}[A-Fa-f0-9]{0,1})$|', $color)) {
            return $color;
        }
        return '';
    }
}

// Add missing text field sanitization if not available
if (!function_exists('sanitize_text_field')) {
    function sanitize_text_field($text) {
        return sanitize_text_field($text);
    }
}

// Add missing boolean validation if not available
if (!function_exists('wp_validate_boolean')) {
    function wp_validate_boolean($value) {
        return ($value === 'true' || $value === '1' || $value === true);
    }
}

