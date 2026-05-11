<?php
/**
 * Multi-language Support (Polylang & WPML)
 */

// Check if Polylang is active
function tour_portal_is_polylang_active() {
    return function_exists('pll_register_string') && class_exists('Polylang');
}

// Check if WPML is active
function tour_portal_is_wpml_active() {
    return defined('ICL_SITEPRESS_VERSION') && class_exists('SitePress');
}

// Get current language
function tour_portal_get_current_language() {
    if (tour_portal_is_polylang_active()) {
        return pll_current_language('slug');
    } elseif (tour_portal_is_wpml_active()) {
        return apply_filters('wpml_current_language', null);
    }
    return 'en'; // Default fallback
}

// Get available languages
function tour_portal_get_available_languages() {
    if (tour_portal_is_polylang_active()) {
        $languages = pll_languages_list();
        $result = array();
        foreach ($languages as $lang) {
            $result[$lang] = pll_get_language($lang);
        }
        return $result;
    } elseif (tour_portal_is_wpml_active()) {
        return apply_filters('wpml_active_languages', null);
    }
    return array('en' => array('name' => 'English', 'slug' => 'en'));
}

// Register strings for translation (Polylang)
function tour_portal_register_polylang_strings() {
    if (!tour_portal_is_polylang_active()) return;
    
    // Tour type strings
    pll_register_string('Public Tours', 'Public Tours', 'Tour Portal');
    pll_register_string('Private Tours', 'Private Tours', 'Tour Portal');
    pll_register_string('Per Person', 'Per Person', 'Tour Portal');
    pll_register_string('Per Group', 'Per Group', 'Tour Portal');
    
    // Form labels
    pll_register_string('Partner Registration', 'Partner Registration', 'Tour Portal');
    pll_register_string('Partner Name', 'Partner Name', 'Tour Portal');
    pll_register_string('Contact Person', 'Contact Person', 'Tour Portal');
    pll_register_string('Email', 'Email', 'Tour Portal');
    pll_register_string('Password', 'Password', 'Tour Portal');
    pll_register_string('Phone', 'Phone', 'Tour Portal');
    pll_register_string('Address', 'Address', 'Tour Portal');
    pll_register_string('Register', 'Register', 'Tour Portal');
    
    // Tour labels
    pll_register_string('Duration', 'Duration', 'Tour Portal');
    pll_register_string('hours', 'hours', 'Tour Portal');
    pll_register_string('Languages', 'Languages', 'Tour Portal');
    pll_register_string('Price', 'Price', 'Tour Portal');
    pll_register_string('Book Now', 'Book Now', 'Tour Portal');
    pll_register_string('Learn More', 'Learn More', 'Tour Portal');
    
    // Filter labels
    pll_register_string('All Types', 'All Types', 'Tour Portal');
    pll_register_string('All Languages', 'All Languages', 'Tour Portal');
    pll_register_string('All Categories', 'All Categories', 'Tour Portal');
    pll_register_string('Apply Filters', 'Apply Filters', 'Tour Portal');
}
add_action('init', 'tour_portal_register_polylang_strings');

// Language switcher moved to language-switcher.php to avoid conflicts

// Translate tour meta fields
function tour_portal_translate_meta($value, $meta_key, $post_id = null) {
    if (tour_portal_is_polylang_active()) {
        return pll__($value);
    } elseif (tour_portal_is_wpml_active()) {
        return apply_filters('wpml_translate_single_string', $value, 'tour-portal', $meta_key);
    }
    return $value;
}

// Add language support to tour queries
function tour_portal_language_query($query) {
    if (!is_admin() && $query->is_main_query()) {
        if (tour_portal_is_polylang_active()) {
            // Polylang handles this automatically
        } elseif (tour_portal_is_wpml_active()) {
            // WPML handles this automatically
        }
    }
    return $query;
}
add_filter('pre_get_posts', 'tour_portal_language_query');

// Add language class to body
function tour_portal_language_body_class($classes) {
    $current_lang = tour_portal_get_current_language();
    $classes[] = 'lang-' . $current_lang;
    
    if (tour_portal_is_polylang_active()) {
        $classes[] = 'polylang-active';
    } elseif (tour_portal_is_wpml_active()) {
        $classes[] = 'wpml-active';
    }
    
    return $classes;
}
add_filter('body_class', 'tour_portal_language_body_class');

// Language switcher shortcode
function tour_portal_language_switcher_shortcode($atts) {
    $atts = shortcode_atts(array(
        'type' => 'dropdown', // dropdown or flags
    ), $atts);
    
    if (!tour_portal_is_polylang_active() && !tour_portal_is_wpml_active()) {
        return '';
    }
    
    ob_start();
    
    if ($atts['type'] === 'dropdown') {
        echo '<div class="language-switcher-dropdown">';
        echo '<select id="language-selector" onchange="window.location.href=this.value">';
        
        if (tour_portal_is_polylang_active()) {
            $languages = pll_the_languages(array('raw' => 1));
            foreach ($languages as $lang) {
                $selected = $lang['current_lang'] ? 'selected' : '';
                echo '<option value="' . esc_url($lang['url']) . '" ' . $selected . '>' . esc_html($lang['name']) . '</option>';
            }
        } elseif (tour_portal_is_wpml_active()) {
            $languages = apply_filters('wpml_active_languages', null);
            foreach ($languages as $lang) {
                $selected = $lang['active'] ? 'selected' : '';
                echo '<option value="' . esc_url($lang['url']) . '" ' . $selected . '>' . esc_html($lang['translated_name']) . '</option>';
            }
        }
        
        echo '</select>';
        echo '</div>';
    } else {
        echo '<div class="language-switcher-flags">';
        
        if (tour_portal_is_polylang_active()) {
            $languages = pll_the_languages(array('raw' => 1, 'show_flags' => 1));
            foreach ($languages as $lang) {
                $current = $lang['current_lang'] ? 'current' : '';
                echo '<a href="' . esc_url($lang['url']) . '" class="language-flag ' . $current . '">';
                if (!empty($lang['flag'])) {
                    echo '<img src="' . esc_url($lang['flag']) . '" alt="' . esc_attr($lang['name']) . '" />';
                } else {
                    echo '<span class="flag-text">' . strtoupper($lang['slug']) . '</span>';
                }
                echo '</a>';
            }
        } elseif (tour_portal_is_wpml_active()) {
            $languages = apply_filters('wpml_active_languages', null);
            foreach ($languages as $lang) {
                $current = $lang['active'] ? 'current' : '';
                echo '<a href="' . esc_url($lang['url']) . '" class="language-flag ' . $current . '">';
                echo '<span class="flag-text">' . strtoupper($lang['code']) . '</span>';
                echo '</a>';
            }
        }
        
        echo '</div>';
    }
    
    return ob_get_clean();
}
add_shortcode('language_switcher', 'tour_portal_language_switcher_shortcode');

// Add language styles
function tour_portal_language_styles() {
    ?>
    <style>
    .language-switcher {
        position: relative;
        display: inline-block;
    }
    
    .language-current {
        cursor: pointer;
        padding: 5px 10px;
        background: #f0f0f0;
        border-radius: 4px;
        font-weight: bold;
    }
    
    .language-switcher .sub-menu {
        position: absolute;
        top: 100%;
        left: 0;
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        z-index: 1000;
        min-width: 50px;
    }
    
    .language-switcher .sub-menu li {
        margin: 0;
        list-style: none;
    }
    
    .language-switcher .sub-menu a {
        display: block;
        padding: 8px 12px;
        color: #333;
        text-decoration: none;
    }
    
    .language-switcher .sub-menu a:hover {
        background: #f5f5f5;
    }
    
    .language-switcher-dropdown select {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        background: white;
        cursor: pointer;
    }
    
    .language-switcher-flags {
        display: flex;
        gap: 10px;
    }
    
    .language-flag {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #f0f0f0;
        text-decoration: none;
        font-weight: bold;
        font-size: 12px;
        transition: background-color 0.3s ease;
    }
    
    .language-flag:hover {
        background: #e0e0e0;
    }
    
    .language-flag.current {
        background: <?php echo esc_attr(get_theme_mod('tour_portal_primary_color', '#0073aa')); ?>;
        color: white;
    }
    
    .language-flag img {
        width: 20px;
        height: 20px;
        border-radius: 50%;
    }
    
    .flag-text {
        font-size: 10px;
        line-height: 1;
    }
    </style>
    <?php
}
add_action('wp_head', 'tour_portal_language_styles');
