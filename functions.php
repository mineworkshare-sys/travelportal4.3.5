<?php
/**
 * Tour Portal Theme Functions
 */

// Tour System Registration
function register_tour_system() {
    // Register Site Owner Role for single operator sites
    add_role('site_owner', __('Site Owner', 'tour-portal'), array(
        'read' => true,
        'edit_posts' => true,
        'edit_pages' => true,
        'edit_others_posts' => true,
        'edit_others_pages' => true,
        'publish_posts' => true,
        'publish_pages' => true,
        'delete_posts' => true,
        'delete_pages' => true,
        'delete_others_posts' => true,
        'delete_others_pages' => true,
        'manage_categories' => true,
        'manage_tags' => true,
        'upload_files' => true,
        'edit_themes' => false,
        'install_plugins' => false,
        'update_plugin' => false,
        'update_core' => false,
        'edit_users' => false,
        'list_users' => true,
        'remove_users' => false,
        'promote_users' => false,
        'create_users' => false,
        'delete_users' => false,
        'manage_options' => true,
        'edit_tours' => true,
        'edit_others_tours' => true,
        'publish_tours' => true,
        'delete_tours' => true,
        'delete_others_tours' => true,
        'manage_tour_categories' => true,
    ));
    // Register the Tour Post Type
    register_post_type('tour', array(
        'labels' => array(
            'name' => __('Tours', 'tour-portal'),
            'singular_name' => __('Tour', 'tour-portal'),
            'menu_name' => __('Tours', 'tour-portal'),
            'add_new' => __('Add New', 'tour-portal'),
            'add_new_item' => __('Add New Tour', 'tour-portal'),
            'edit' => __('Edit', 'tour-portal'),
            'edit_item' => __('Edit Tour', 'tour-portal'),
            'new_item' => __('New Tour', 'tour-portal'),
            'view' => __('View Tour', 'tour-portal'),
            'view_item' => __('View Tour', 'tour-portal'),
            'search_items' => __('Search Tours', 'tour-portal'),
            'not_found' => __('No tours found', 'tour-portal'),
            'not_found_in_trash' => __('No tours found in Trash', 'tour-portal'),
        ),
        'public' => true,
        'has_archive' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'tour', 'with_front' => false),
        'capability_type' => 'post',
        'hierarchical' => false,
        'menu_position' => 20,
        'menu_icon' => 'dashicons-tickets-alt',
        'supports' => array('title', 'editor', 'thumbnail', 'author', 'excerpt', 'custom-fields'),
        'show_in_rest' => true,
    ));

    // Register the Tour Category Taxonomy
    register_taxonomy('tour_category', array('tour'), array(
        'labels' => array(
            'name' => __('Tour Categories', 'tour-portal'),
            'singular_name' => __('Tour Category', 'tour-portal'),
            'menu_name' => __('Categories', 'tour-portal'),
            'all_items' => __('All Categories', 'tour-portal'),
            'edit_item' => __('Edit Category', 'tour-portal'),
            'view_item' => __('View Category', 'tour-portal'),
            'update_item' => __('Update Category', 'tour-portal'),
            'add_new_item' => __('Add New Category', 'tour-portal'),
            'new_item_name' => __('New Category Name', 'tour-portal'),
            'parent_item' => __('Parent Category', 'tour-portal'),
            'parent_item_colon' => __('Parent Category:', 'tour-portal'),
            'search_items' => __('Search Categories', 'tour-portal'),
            'popular_items' => __('Popular Categories', 'tour-portal'),
            'separate_items_with_commas' => __('Separate categories with commas', 'tour-portal'),
            'add_or_remove_items' => __('Add or remove categories', 'tour-portal'),
            'choose_from_most_used' => __('Choose from the most used categories', 'tour-portal'),
            'not_found' => __('No categories found', 'tour-portal'),
        ),
        'hierarchical' => true,
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'tour-category',
                'with_front' => false,
                'pages' => false,
                'feeds' => false),
                'show_in_rest' => true,
    ));
}
add_action('init', 'register_tour_system');

// Add custom rewrite rules for partner tour URLs
function tour_portal_add_rewrite_rules() {
    // Add rewrite rule for partner tour URLs: /company/{company_slug}/tour/{tour_slug}/
    add_rewrite_rule(
        '^company/([^/]+)/tour/([^/]+)/?$',
        'index.php?post_type=tour&name=$matches[2]&company_slug=$matches[1]',
        'top'
    );
    
    // Add rewrite rule for partner tour archives: /company/{company_slug}/tour/
    add_rewrite_rule(
        '^company/([^/]+)/tour/?$',
        'index.php?post_type=tour&company_slug=$matches[1]',
        'top'
    );
}
add_action('init', 'tour_portal_add_rewrite_rules');

// Filter tour permalinks to include company slug for partner tours
function tour_portal_custom_permalink($permalink, $post) {
    if ($post->post_type != 'tour') {
        return $permalink;
    }
    
    // Check if this tour belongs to a partner using post author
    $partner_id = $post->post_author;
    
    // Debug: Log the values (remove in production)
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log("Tour ID: " . $post->ID);
        error_log("Partner ID (author): " . ($partner_id ? $partner_id : 'none'));
    }
    
    if (!$partner_id) {
        return $permalink;
    }
    
    $partner = get_post($partner_id);
    if (!$partner || $partner->post_type != 'partner') {
        return $permalink;
    }
    
    $partner_slug = $partner->post_name;
    $tour_slug = $post->post_name;
    
    $new_permalink = home_url("/company/{$partner_slug}/tour/{$tour_slug}/");
    
    // Debug: Log the new permalink (remove in production)
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log("Old permalink: " . $permalink);
        error_log("New permalink: " . $new_permalink);
    }
    
    return $new_permalink;
}
add_filter('post_type_link', 'tour_portal_custom_permalink', 10, 2);

// Add company slug to query vars
function tour_portal_query_vars($query_vars) {
    $query_vars[] = 'company_slug';
    return $query_vars;
}
add_filter('query_vars', 'tour_portal_query_vars');

// Flush rewrite rules on theme activation
function tour_portal_flush_rewrite_rules() {
    tour_portal_add_rewrite_rules();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'tour_portal_flush_rewrite_rules');

// Add admin menu to flush rewrite rules
function tour_portal_add_rewrite_admin_menu() {
    add_options_page(
        'Tour Portal Rewrite Rules',
        'Tour Portal',
        'manage_options',
        'tour-portal-rewrite-settings',
        'tour_portal_rewrite_settings_page'
    );
}
add_action('admin_menu', 'tour_portal_add_rewrite_admin_menu');

// Force flush rewrite rules on admin init for testing
function tour_portal_force_flush_rules() {
    if (isset($_GET['flush_tour_portal_rules']) && current_user_can('manage_options')) {
        flush_rewrite_rules();
        wp_redirect(admin_url('options-general.php?page=tour-portal-rewrite-settings'));
        exit;
    }
}
add_action('admin_init', 'tour_portal_force_flush_rules');

function tour_portal_rewrite_settings_page() {
    ?>
    <div class="wrap">
        <h1>Tour Portal Settings</h1>
        <p><strong>Partner Tour URL Fix:</strong> Tour URLs should now show as /company/{partner}/tour/{tour}/</p>
        
        <div class="notice notice-info">
            <p><strong>Important:</strong> If tour cards still show old URLs, you need to flush rewrite rules:</p>
            <ol>
                <li>Click the button below, OR</li>
                <li>Visit: <code><?php echo admin_url('options-general.php?page=tour-portal-rewrite-settings&flush_tour_portal_rules=1'); ?></code></li>
                <li>Or simply visit <strong>Settings > Permalinks</strong> and click "Save Changes"</li>
            </ol>
        </div>
        
        <form method="post" action="">
            <?php wp_nonce_field('tour_portal_flush_rules', 'tour_portal_nonce'); ?>
            <input type="submit" name="flush_rewrite_rules" class="button button-primary" value="Flush Rewrite Rules Now">
        </form>
        
        <?php
        if (isset($_POST['flush_rewrite_rules']) && 
            isset($_POST['tour_portal_nonce']) && 
            wp_verify_nonce($_POST['tour_portal_nonce'], 'tour_portal_flush_rules')) {
            
            flush_rewrite_rules();
            echo '<div class="notice notice-success"><p>✓ Rewrite rules flushed successfully! Tour URLs should now work correctly.</p></div>';
        }
        ?>
        
        <?php
        if (isset($_GET['flush_tour_portal_rules'])) {
            echo '<div class="notice notice-success"><p>✓ Rewrite rules flushed via URL! Tour URLs should now work correctly.</p></div>';
        }
        ?>
    </div>
    <?php
}

// Theme Setup
function tour_portal_theme_setup() {
    // Add theme support
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    add_theme_support('custom-logo');
    
    // Register menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'tour-portal'),
        'footer' => __('Footer Menu', 'tour-portal'),
    ));
}
add_action('after_setup_theme', 'tour_portal_theme_setup', 10);

// Include language switcher after theme setup
function tour_portal_load_language_switcher() {
    require_once get_template_directory() . '/inc/language-switcher.php';
}
add_action('after_setup_theme', 'tour_portal_load_language_switcher', 15);

// Enqueue Scripts and Styles
function tour_portal_enqueue_scripts() {
    wp_enqueue_style('tour-portal-style', get_stylesheet_uri());
    wp_enqueue_style('tour-portal-main', get_template_directory_uri() . '/assets/css/main.css');
    wp_enqueue_style('tour-portal-partner-layout', get_template_directory_uri() . '/assets/css/partner-layout.css');
    wp_enqueue_script('tour-portal-script', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'tour_portal_enqueue_scripts');


// Include theme functionality files
require_once get_template_directory() . '/inc/user-roles.php';
require_once get_template_directory() . '/inc/meta-fields.php';
require_once get_template_directory() . '/inc/admin-interface.php';
require_once get_template_directory() . '/inc/shortcodes.php';
require_once get_template_directory() . '/inc/theme-customizer.php';
require_once get_template_directory() . '/inc/multilang-support.php';
require_once get_template_directory() . '/inc/page-options.php';

// Custom login CSS and form modifications
add_action('login_head', function () {
    ?>
    <style>
        /* FULL WHITE CENTERED PAGE */
        body.login {
            background: #ffffff !important;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        /* LOGIN WRAPPER */
        #login {
            width:100%;
            max-width: 420px;
            padding: 0;
        }

        /* LOGIN BOX */
        #loginform {
            background: #fff;
            padding: 40px;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            text-align: center;
        }

        /* REMOVE WORDPRESS LOGO */
        .login h1 {
            display: none !important;
        }

        /* YOUR LOGO (ONCE ONLY) */
        #loginform::before {
            content: "";
            display: block;
            width: 220px;
            height: 80px;
            margin: 0 auto 20px auto;
            background: url("https://reimaginetravel.site/wp-content/uploads/2026/05/reimagine-travel-tour-and-booking-platform-logo.fw_.png") no-repeat center;
            background-size: contain;
        }

        /* INPUT FIELDS */
        .login form input[type="text"],
        .login form input[type="password"] {
            width: 100%;
            border-radius: 8px;
            padding: 12px;
            border: 1px solid #ddd;
            margin-bottom: 15px;
        }

        /* LOGIN BUTTON */
        .wp-core-ui .button-primary {
            width: 100%;
            background: #0073aa;
            border: none;
            padding: 12px;
            margin-top: 10px;
        }

        .wp-core-ui .button-primary:hover {
            background: #005a87;
        }

        /* REMOVE WORDPRESS NAV ELEMENTS */
        #nav,
        #backtoblog,
        .language-switcher {
            display: none !important;
        }

        /* LINKS UNDER BUTTON */
        .custom-login-links {
            margin-top: 15px;
            font-size: 13px;
            text-align: center;
        }

        .custom-login-links a {
            color: #0073aa;
            text-decoration: none;
        }

        .custom-login-links a:hover {
            text-decoration: underline;
        }

    </style>
    <?php
});

add_action('login_form', function () {
    ?>
    <div class="custom-login-links">
        <a href="<?php echo esc_url(wp_lostpassword_url()); ?>">Lost your password?</a><br>
        <a href="<?php echo esc_url(home_url('/')); ?>">← Go to site</a>
    </div>
    <?php
});


// Include multi-operator-mode.php after theme setup
function tour_portal_load_multi_operator_mode() {
    require_once get_template_directory() . '/inc/multi-operator-mode.php';
}
add_action('after_setup_theme', 'tour_portal_load_multi_operator_mode', 20);

// Partner Permissions and Admin Fixes
function tour_portal_partner_permissions() {
    // Allow partners to publish tours without review
    $partner_role = get_role('partner');
    if ($partner_role) {
        $partner_role->add_cap('publish_tours');
        $partner_role->add_cap('edit_published_tours');
        $partner_role->add_cap('delete_published_tours');
        $partner_role->add_cap('edit_tours');
        $partner_role->add_cap('delete_tours');
        $partner_role->add_cap('manage_categories');
    }
    
    // Allow editors to help partners
    $editor_role = get_role('editor');
    if ($editor_role) {
        $editor_role->add_cap('edit_tours');
        $editor_role->add_cap('read_private_tours');
    }
}
add_action('init', 'tour_portal_partner_permissions');

// Fix tour categories for partners
function tour_portal_fix_partner_categories() {
    // Ensure tour categories are available for partners
    register_taxonomy_for_object_type('tour_category', 'tour');
}
add_action('init', 'tour_portal_fix_partner_categories');

// Show tours in admin for partners
function tour_portal_show_tours_in_admin($query) {
    global $pagenow;
    
    if ($pagenow === 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'tour') {
        // Allow partners to see all tours
        if (is_array($query)) {
            $query['post_status'] = array('publish', 'pending', 'draft', 'trash');
        }
    }
    
    return $query;
}
add_filter('parse_query', 'tour_portal_show_tours_in_admin');

// Allow editing without review
function tour_portal_skip_review($post_id, $post) {
    if ($post->post_type === 'tour' && current_user_can('publish_tours')) {
        // Auto-publish for partners
        remove_action('save_post', 'wp_save_post_revision');
    }
}
add_action('save_post', 'tour_portal_skip_review', 10, 2);

// Admin should see all tours - no filtering needed
// WordPress default behavior shows admin all posts

// Tour post type is already registered in register_tour_system() function with standard capabilities

// Add partner filter to tour admin
function tour_portal_add_partner_filter() {
    global $typenow;
    
    if ($typenow === 'tour') {
        add_action('restrict_manage_posts', 'tour_portal_partner_filter_dropdown');
    }
}
add_action('restrict_manage_posts', 'tour_portal_add_partner_filter');

// Create author filter dropdown
function tour_portal_partner_filter_dropdown() {
    if (current_user_can('manage_options')) {
        // Admin gets author filter dropdown
        $users = get_users(array('role__in' => array('partner', 'administrator', 'editor')));
        
        if (!empty($users)) {
            echo '<select name="author">';
            echo '<option value="0">' . __('All Authors', 'tour-portal') . '</option>';
            
            foreach ($users as $user) {
                $selected = isset($_GET['author']) && $_GET['author'] == $user->ID ? 'selected' : '';
                $role_label = in_array('partner', $user->roles) ? ' (Partner)' : '';
                echo '<option value="' . $user->ID . '" ' . $selected . '>' . esc_html($user->display_name) . $role_label . '</option>';
            }
            
            echo '</select>';
        }
    } else {
        // Partners get no filter (they only see their own)
        return;
    }
}

// WordPress handles author filtering natively with the 'author' parameter

// Template redirect for company pages ONLY
function tour_portal_template_redirect() {
    $request_uri = $_SERVER['REQUEST_URI'];
    
    // Check if this is /company/ page (exact match)
    if (rtrim($request_uri, '/') === '/company') {
        // Load blank company template
        include get_template_directory() . '/company.php';
        exit;
    }
    
    // Check if this is a partner company page - BUT NOT tour URLs OR homepage paths
    if (strpos($request_uri, '/company/') !== false && $request_uri !== '/company') {
        // Make sure this isn't a tour URL OR homepage
        if (strpos($request_uri, '/tour/') === false) {
            // Exclude common homepage paths that should NOT use company-landing.php
            $excluded_paths = array('/home', '/home-deutsch', '/start', '/start-deutsch', '/willkommen', '/accueil');
            $is_homepage_path = false;
            
            foreach ($excluded_paths as $path) {
                if (strpos($request_uri, $path) !== false) {
                    $is_homepage_path = true;
                    break;
                }
            }
            
            if (!$is_homepage_path) {
                $parts = explode('/company/', $request_uri);
                if (isset($parts[1]) && !empty(trim($parts[1]))) {
                    // Load company-landing template ONLY for actual company pages
                    include get_template_directory() . '/company-landing.php';
                    exit;
                }
            }
        }
    }
}
add_action('template_redirect', 'tour_portal_template_redirect');

// Flush rewrite rules on theme activation
function tour_portal_rewrite_flush() {
    register_tour_system();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'tour_portal_rewrite_flush');
