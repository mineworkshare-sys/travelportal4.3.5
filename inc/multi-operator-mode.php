<?php
/**
 * Multi-Operator Mode Functions
 */

// Check if we're in multi-operator mode
function tour_portal_is_multi_operator() {
    return get_option('tour_portal_mode', 'multi-operator') === 'multi-operator';
}

// Remove Tours from all menus in multi-operator mode
function tour_portal_remove_tours_from_all_menus($nav_menu, $args) {
    if (tour_portal_is_multi_operator()) {
        // Remove any menu items containing "tour" or "Tour"
        $nav_menu = preg_replace('/<li[^>]*>.*?tour.*?<\/li>/i', '', $nav_menu);
        $nav_menu = preg_replace('/<li[^>]*>.*?Tour.*?<\/li>/i', '', $nav_menu);
        $nav_menu = preg_replace('/<a[^>]*>.*?tour.*?<\/a>/i', '', $nav_menu);
        $nav_menu = preg_replace('/<a[^>]*>.*?Tour.*?<\/a>/i', '', $nav_menu);
    }
    return $nav_menu;
}
add_filter('wp_nav_menu', 'tour_portal_remove_tours_from_all_menus', 99, 2);

// Filter navigation menus to remove Tours links in multi-operator mode
function tour_portal_filter_nav_menu_items($items, $args) {
    if (tour_portal_is_multi_operator()) {
        $filtered_items = array();
        foreach ($items as $item) {
            // Remove Tours-related menu items
            if (strpos(strtolower($item->title), 'tour') !== false || 
                strpos(strtolower($item->url), 'tour') !== false ||
                $item->object === 'tour' ||
                $item->object === 'tour_category') {
                continue;
            }
            $filtered_items[] = $item;
        }
        return $filtered_items;
    }
    return $items;
}
add_filter('wp_get_nav_menu_items', 'tour_portal_filter_nav_menu_items', 10, 2);

// Remove Tours from footer in multi-operator mode
function tour_portal_remove_tours_from_footer($content) {
    if (tour_portal_is_multi_operator()) {
        // Remove Tours links from footer content
        $content = preg_replace('/<a[^>]*>.*?tour.*?<\/a>/i', '', $content);
        $content = preg_replace('/<li[^>]*>.*?tour.*?<\/li>/i', '', $content);
    }
    return $content;
}
add_filter('the_content', 'tour_portal_remove_tours_from_footer');

// Remove Tours from navigation menu output
function tour_portal_remove_tours_from_nav_menu($menu, $args) {
    if (tour_portal_is_multi_operator()) {
        $menu = preg_replace('/<li[^>]*>.*?tour.*?<\/li>/i', '', $menu);
        $menu = preg_replace('/<li[^>]*>.*?Tour.*?<\/li>/i', '', $menu);
    }
    return $menu;
}
add_filter('wp_nav_menu', 'tour_portal_remove_tours_from_nav_menu', 10, 2);

// Remove Tours from footer widgets
function tour_portal_remove_tours_from_footer_widgets($content) {
    if (tour_portal_is_multi_operator()) {
        $content = preg_replace('/<a[^>]*>.*?tour.*?<\/a>/i', '', $content);
        $content = preg_replace('/<a[^>]*>.*?Tour.*?<\/a>/i', '', $content);
        $content = preg_replace('/<li[^>]*>.*?tour.*?<\/li>/i', '', $content);
        $content = preg_replace('/<li[^>]*>.*?Tour.*?<\/li>/i', '', $content);
    }
    return $content;
}
add_filter('widget_text', 'tour_portal_remove_tours_from_footer_widgets');
add_filter('the_content', 'tour_portal_remove_tours_from_footer_widgets');

// Remove Tours from menu items before rendering
function tour_portal_remove_tours_from_menu_items($items, $menu) {
    if (tour_portal_is_multi_operator()) {
        foreach ($items as $key => $item) {
            if (stripos($item->title, 'tour') !== false || 
                stripos($item->url, 'tour') !== false ||
                $item->object === 'tour' ||
                $item->object === 'tour_category') {
                unset($items[$key]);
            }
        }
    }
    return $items;
}
add_filter('wp_nav_menu_objects', 'tour_portal_remove_tours_from_menu_items', 10, 2);

// Block access to tour archive in multi-operator mode
function tour_portal_block_tour_archive() {
    if (tour_portal_is_multi_operator() && is_post_type_archive('tour')) {
        wp_redirect(home_url());
        exit;
    }
}
add_action('template_redirect', 'tour_portal_block_tour_archive');

// Menu filtering already handled by existing function

// DO NOT redirect tour pages - let them work normally

// Remove Tours links from footer in multi-operator mode
function tour_portal_filter_footer_links() {
    if (tour_portal_is_multi_operator()) {
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Remove Tours links from footer
            var footerLinks = document.querySelectorAll('.footer-menu a, .footer a');
            footerLinks.forEach(function(link) {
                if (link.textContent.toLowerCase().includes('tour') || 
                    link.href.toLowerCase().includes('tour')) {
                    link.style.display = 'none';
                }
            });
        });
        </script>
        <?php
    }
}
add_action('wp_footer', 'tour_portal_filter_footer_links');

// Partners page will be manually created by user

// Partner list shortcode
function tour_portal_partner_list_shortcode($atts) {
    $atts = shortcode_atts(array(
        'limit' => -1,
        'category' => '',
    ), $atts, 'partner_list');
    
    // Check if we're in single operator mode
    $is_single_operator = !tour_portal_is_multi_operator();
    
    $args = array(
        'role' => $is_single_operator ? 'site_owner' : 'partner',
        'meta_query' => array(
            array(
                'key' => $is_single_operator ? 'partner_approved' : 'partner_approved',
                'value' => '1',
                'compare' => '='
            )
        )
    );
    
    if ($atts['category']) {
        $args['meta_query'][] = array(
            'key' => 'partner_category',
            'value' => $atts['category'],
            'compare' => '='
        );
    }
    
    if ($atts['limit'] > 0) {
        $args['number'] = $atts['limit'];
    }
    
    $partners = get_users($args);
    
    ob_start();
    ?>
    <div class="partner-list">
        <div class="partner-grid">
            <?php foreach ($partners as $partner): ?>
                <?php
                $partner_name = get_user_meta($partner->ID, 'partner_name', true);
                $partner_logo = get_user_meta($partner->ID, 'partner_logo', true);
                $partner_description = get_user_meta($partner->ID, 'partner_description', true);
                $partner_website = get_user_meta($partner->ID, 'partner_website', true);
                $partner_address = get_user_meta($partner->ID, 'partner_address', true);
                $company_slug = sanitize_title($partner_name);
                ?>
                
                <div class="partner-card">
                    <div class="partner-logo">
                        <?php if ($partner_logo): ?>
                            <img src="<?php echo esc_url($partner_logo); ?>" alt="<?php echo esc_attr($partner_name); ?>">
                        <?php else: ?>
                            <div class="partner-placeholder-logo">
                                <?php echo esc_html(substr($partner_name, 0, 2)); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="partner-info">
                        <h3 class="partner-name">
                            <a href="<?php echo home_url('/company/' . $company_slug . '/'); ?>">
                                <?php echo esc_html($partner_name); ?>
                            </a>
                        </h3>
                        
                        <p class="partner-role">
                            <?php 
                            if (in_array('site_owner', $partner->roles)) {
                                echo 'Site Owner';
                            } else {
                                echo 'Partner';
                            }
                            ?>
                        </p>
                        
                        <?php if ($partner_description): ?>
                            <p class="partner-description"><?php echo esc_html($partner_description); ?></p>
                        <?php endif; ?>
                        
                        <?php if ($partner_address): ?>
                            <p class="partner-address">
                                <i class="dashicons dashicons-location"></i>
                                <?php echo esc_html($partner_address); ?>
                            </p>
                        <?php endif; ?>
                        
                        <?php if ($partner_website): ?>
                            <p class="partner-website">
                                <a href="<?php echo esc_url($partner_website); ?>" target="_blank" rel="noopener">
                                    <?php echo esc_url($partner_website); ?>
                                </a>
                            </p>
                        <?php endif; ?>
                        
                        <div class="partner-actions">
                            <a href="<?php echo home_url('/company/' . $company_slug . '/'); ?>" class="button">
                                <?php _e('View Tours', 'tour-portal'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <style>
    .partner-list {
        padding: 20px 0;
    }
    
    .partner-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin-top: 30px;
    }
    
    .partner-card {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .partner-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .partner-logo img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 50%;
        margin-bottom: 15px;
    }
    
    .partner-placeholder-logo {
        width: 80px;
        height: 80px;
        background: var(--tp-primary-color);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin: 0 auto 15px;
        font-size: 24px;
        font-weight: bold;
    }
    
    .partner-name {
        margin-bottom: 10px;
    }
    
    .partner-name a {
        color: var(--tp-primary-color);
        text-decoration: none;
        font-weight: bold;
    }
    
    .partner-name a:hover {
        text-decoration: underline;
    }
    
    .partner-description {
        color: #666;
        margin-bottom: 15px;
        min-height: 40px;
    }
    
    .partner-address {
        color: #666;
        font-size: 14px;
        margin-bottom: 10px;
    }
    
    .partner-website a {
        color: var(--tp-primary-color);
        font-size: 14px;
    }
    
    .partner-actions {
        margin-top: 20px;
    }
    
    @media (max-width: 768px) {
        .partner-grid {
            grid-template-columns: 1fr;
        }
    }
    </style>
    <?php
    
    return ob_get_clean();
}
add_shortcode('partner_list', 'tour_portal_partner_list_shortcode');
