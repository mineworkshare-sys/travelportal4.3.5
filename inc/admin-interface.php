<?php
/**
 * Admin Interface Functions
 */

// Add admin menu pages
function tour_portal_admin_menu() {
    add_menu_page(
        __('Tour Portal Settings', 'tour-portal'),
        __('Tour Portal', 'tour-portal'),
        'manage_options',
        'tour-portal-settings',
        'tour_portal_settings_page',
        'dashicons-admin-settings',
        30
    );
    
    add_submenu_page(
        'tour-portal-settings',
        __('System Settings', 'tour-portal'),
        __('System Settings', 'tour-portal'),
        'manage_options',
        'tour-portal-settings',
        'tour_portal_settings_page'
    );
    
    add_submenu_page(
        'tour-portal-settings',
        __('Partner Management', 'tour-portal'),
        __('Partners', 'tour-portal'),
        'manage_options',
        'tour-portal-partners',
        'tour_portal_partners_page'
    );
    
    add_submenu_page(
        'tour-portal-settings',
        __('Add New Partner', 'tour-portal'),
        __('Add Partner', 'tour-portal'),
        'manage_options',
        'tour-portal-add-partner',
        'tour_portal_add_partner_page'
    );
    
    add_submenu_page(
        'tour-portal-settings',
        __('Commission Report', 'tour-portal'),
        __('Commissions', 'tour-portal'),
        'manage_options',
        'tour-portal-commissions',
        'tour_portal_commissions_page'
    );
    
    add_submenu_page(
        'tour-portal-settings',
        __('Edit Partner', 'tour-portal'),
        __('Edit Partner', 'tour-portal'),
        'manage_options',
        'tour-portal-edit-partner',
        'tour_portal_edit_partner_page'
    );
}
add_action('admin_menu', 'tour_portal_admin_menu');

// Partner Edit Page
function tour_portal_edit_partner_page() {
    // Comprehensive role validation and access control
    $current_user = wp_get_current_user();
    $user_roles = $current_user ? $current_user->roles : array();
    $can_manage_options = current_user_can('manage_options');
    
    // Admins see all partners, Site Owners see all partners but can only edit their own info
    if ($can_manage_options) {
        // Admin access - show all partners
        $partners = get_users(array(
            'role' => 'partner',
            'meta_query' => array(
                array(
                    'key' => 'partner_approved',
                    'value' => '1',
                    'compare' => '='
                )
            )
        ));
    } elseif (in_array('site_owner', $user_roles)) {
        // Site Owner access - show all partners but restrict editing to own info only
        $partners = get_users(array(
            'role' => 'site_owner',
            'meta_query' => array(
                array(
                    'key' => 'partner_approved',
                    'value' => '1',
                    'compare' => '='
                )
            )
        ));
    } else {
        // Fallback - no access
        wp_die(__('You do not have permission to access this page.', 'tour-portal'));
    }
    
        
    if (!isset($_GET['partner_id']) || !$can_manage_options) {
        wp_redirect(admin_url('admin.php?page=tour-portal-partners'));
        exit;
    }
    
    $partner_id = intval($_GET['partner_id']);
    $partner = get_user_by('id', $partner_id);
    
    if (!$partner) {
        wp_redirect(admin_url('admin.php?page=tour-portal-partners'));
        exit;
    }
    
    ?>
    <div class="wrap">
        <h1><?php _e('Edit Partner', 'tour-portal'); ?></h1>
        
        <?php if (isset($_GET['updated'])): ?>
            <div class="notice notice-success">
                <p><?php _e('Partner updated successfully!', 'tour-portal'); ?></p>
            </div>
        <?php endif; ?>
        
        <form method="post" action="admin-post.php">
            <?php wp_nonce_field('tour_portal_edit_partner', 'edit_partner_nonce'); ?>
            <input type="hidden" name="partner_id" value="<?php echo $partner_id; ?>">
            <input type="hidden" name="action" value="tour_portal_edit_partner">
            
            <table class="form-table">
                <tr>
                    <th><label for="partner_name"><?php _e('Partner Name', 'tour-portal'); ?></label></th>
                    <td><input type="text" id="partner_name" name="partner_name" value="<?php echo esc_attr(get_user_meta($partner_id, 'partner_name', true)); ?>" class="regular-text" /></td>
                </tr>
                
                <tr>
                    <th><label for="partner_email"><?php _e('Email', 'tour-portal'); ?></label></th>
                    <td><input type="email" id="partner_email" name="partner_email" value="<?php echo esc_attr($partner->user_email); ?>" class="regular-text" /></td>
                </tr>
                
                <tr>
                    <th><label for="partner_phone"><?php _e('Phone', 'tour-portal'); ?></label></th>
                    <td><input type="text" id="partner_phone" name="partner_phone" value="<?php echo esc_attr(get_user_meta($partner_id, 'partner_phone', true)); ?>" class="regular-text" /></td>
                </tr>
                
                <tr>
                    <th><label for="partner_address"><?php _e('Address', 'tour-portal'); ?></label></th>
                    <td><textarea id="partner_address" name="partner_address" rows="3" class="large-text"><?php echo esc_textarea(get_user_meta($partner_id, 'partner_address', true)); ?></textarea></td>
                </tr>
                
                <tr>
                    <th><label for="partner_website"><?php _e('Website', 'tour-portal'); ?></label></th>
                    <td><input type="url" id="partner_website" name="partner_website" value="<?php echo esc_attr(get_user_meta($partner_id, 'partner_website', true)); ?>" class="regular-text" /></td>
                </tr>
                
                <tr>
                    <th><label for="partner_description"><?php _e('Company Description', 'tour-portal'); ?></label></th>
                    <td><textarea id="partner_description" name="partner_description" rows="6" class="large-text"><?php echo esc_textarea(get_user_meta($partner_id, 'partner_description', true)); ?></textarea></td>
                </tr>
                
                <tr>
                    <th><label for="partner_logo"><?php _e('Logo URL', 'tour-portal'); ?></label></th>
                    <td><input type="url" id="partner_logo" name="partner_logo" value="<?php echo esc_attr(get_user_meta($partner_id, 'partner_logo', true)); ?>" class="regular-text" /></td>
                </tr>
            </table>
            
            <p class="submit">
                <input type="submit" name="update_partner" class="button button-primary" value="<?php _e('Update Partner', 'tour-portal'); ?>">
            </p>
        </form>
    </div>
    <?php
}

// Handle partner update
function handle_partner_update() {
    if (!isset($_POST['update_partner']) || !isset($_POST['edit_partner_nonce']) || !wp_verify_nonce($_POST['edit_partner_nonce'], 'tour_portal_edit_partner')) {
        return;
    }
    
    $partner_id = intval($_POST['partner_id']);
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Update partner fields
    if (isset($_POST['partner_name'])) {
        update_user_meta($partner_id, 'partner_name', sanitize_text_field($_POST['partner_name']));
    }
    if (isset($_POST['partner_email'])) {
        wp_update_user(array('ID' => $partner_id), array('user_email' => sanitize_email($_POST['partner_email'])));
    }
    if (isset($_POST['partner_phone'])) {
        update_user_meta($partner_id, 'partner_phone', sanitize_text_field($_POST['partner_phone']));
    }
    if (isset($_POST['partner_address'])) {
        update_user_meta($partner_id, 'partner_address', sanitize_textarea_field($_POST['partner_address']));
    }
    if (isset($_POST['partner_website'])) {
        update_user_meta($partner_id, 'partner_website', esc_url_raw($_POST['partner_website']));
    }
    if (isset($_POST['partner_description'])) {
        update_user_meta($partner_id, 'partner_description', sanitize_textarea_field($_POST['partner_description']));
    }
    if (isset($_POST['partner_logo'])) {
        update_user_meta($partner_id, 'partner_logo', esc_url_raw($_POST['partner_logo']));
    }
    
    // Redirect with success message
    wp_redirect(add_query_arg('updated', '1', admin_url('admin.php?page=tour-portal-edit-partner&partner_id=' . $partner_id)));
    exit;
}
add_action('admin_post_tour_portal_edit_partner', 'handle_partner_update');

// Settings page
function tour_portal_settings_page() {
    if (isset($_POST['save_settings'])) {
        check_admin_referer('tour_portal_settings');
        
        // Check lockdown password if trying to change settings
        $lockdown_enabled = get_option('tour_portal_lockdown', 'no') === 'yes';
        $lockdown_password = get_option('tour_portal_lockdown_password', '');
        
        if ($lockdown_enabled && !empty($lockdown_password)) {
            if (!isset($_POST['lockdown_password_confirm']) || $_POST['lockdown_password_confirm'] !== $lockdown_password) {
                echo '<div class="notice notice-error"><p>' . __('Incorrect lockdown password. Settings not saved.', 'tour-portal') . '</p></div>';
                return;
            }
        }
        
        update_option('tour_portal_mode', sanitize_text_field($_POST['portal_mode']));
        update_option('tour_portal_lockdown', isset($_POST['lockdown']) ? 'yes' : 'no');
        update_option('tour_portal_show_signup', isset($_POST['show_signup']) ? 'yes' : 'no');
        update_option('tour_portal_default_commission', floatval($_POST['default_commission']));
        
        // Save lockdown password if provided
        if (isset($_POST['lockdown_password']) && !empty($_POST['lockdown_password'])) {
            update_option('tour_portal_lockdown_password', sanitize_text_field($_POST['lockdown_password']));
        }
        
        echo '<div class="notice notice-success"><p>' . __('Settings saved successfully!', 'tour-portal') . '</p></div>';
    }
    
    $portal_mode = get_option('tour_portal_mode', 'multi-operator');
    $lockdown = get_option('tour_portal_lockdown', 'no');
    $show_signup = get_option('tour_portal_show_signup', 'yes');
    $default_commission = get_option('tour_portal_default_commission', 10.0);
    ?>
    <div class="wrap">
        <h1><?php _e('Tour Portal Settings', 'tour-portal'); ?></h1>
        
        <form method="post" action="">
            <?php wp_nonce_field('tour_portal_settings'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row"><?php _e('Portal Operation Mode', 'tour-portal'); ?></th>
                    <td>
                        <select name="portal_mode" id="portal_mode">
                            <option value="multi-operator" <?php selected($portal_mode, 'multi-operator'); ?>><?php _e('Multi-Operator Mode (Hosted Portal)', 'tour-portal'); ?></option>
                            <option value="single-operator" <?php selected($portal_mode, 'single-operator'); ?>><?php _e('Single-Operator Mode (Sold Software)', 'tour-portal'); ?></option>
                        </select>
                        <p class="description">
                            <?php _e('Multi-Operator: Multiple partners can register and manage tours.', 'tour-portal'); ?><br>
                            <?php _e('Single-Operator: Locked to one operator only.', 'tour-portal'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr id="lockdown_row">
                    <th scope="row"><?php _e('Lockdown Mode', 'tour-portal'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="lockdown" <?php checked($lockdown, 'yes'); ?> />
                            <?php _e('Enable lockdown (prevents changing system mode)', 'tour-portal'); ?>
                        </label>
                        <p class="description">
                            <?php _e('When enabled, requires password to change system settings.', 'tour-portal'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr id="lockdown_password_row">
                    <th scope="row"><?php _e('Lockdown Password', 'tour-portal'); ?></th>
                    <td>
                        <input type="password" name="lockdown_password" value="<?php echo esc_attr(get_option('tour_portal_lockdown_password', '')); ?>" class="regular-text" />
                        <p class="description">
                            <?php _e('Password required to change settings when lockdown is enabled.', 'tour-portal'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr id="signup_row">
                    <th scope="row"><?php _e('Partner Signup', 'tour-portal'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="show_signup" <?php checked($show_signup, 'yes'); ?> />
                            <?php _e('Show partner registration form', 'tour-portal'); ?>
                        </label>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Default Commission Rate (%)', 'tour-portal'); ?></th>
                    <td>
                        <input type="number" name="default_commission" step="0.1" value="<?php echo esc_attr($default_commission); ?>" />
                        <p class="description"><?php _e('Default commission rate for new partners', 'tour-portal'); ?></p>
                    </td>
                </tr>
            </table>
            
            <?php 
    // Show password confirmation if lockdown is enabled
    $lockdown_enabled = get_option('tour_portal_lockdown', 'no') === 'yes';
    $lockdown_password = get_option('tour_portal_lockdown_password', '');
    
    if ($lockdown_enabled && !empty($lockdown_password)) {
        ?>
        <p class="description" style="color: #d63638;">
            <strong><?php _e('Lockdown is enabled. You must enter the password to save settings.', 'tour-portal'); ?></strong>
        </p>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Confirm Password', 'tour-portal'); ?></th>
                <td>
                    <input type="password" name="lockdown_password_confirm" required class="regular-text" />
                    <p class="description"><?php _e('Enter the lockdown password to save settings.', 'tour-portal'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }
    
    submit_button(__('Save Settings', 'tour-portal'), 'primary', 'save_settings'); 
    ?>
        </form>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        function toggleSettings() {
            var mode = $('#portal_mode').val();
            var lockdown = $('input[name="lockdown"]').prop('checked');
            
            if (mode === 'single-operator' || lockdown) {
                $('#signup_row').hide();
                if (lockdown) {
                    $('#portal_mode').prop('disabled', true);
                }
            } else {
                $('#signup_row').show();
                $('#portal_mode').prop('disabled', false);
            }
        }
        
        $('#portal_mode, input[name="lockdown"]').on('change', toggleSettings);
        toggleSettings();
    });
    </script>
    <?php
}

// Partners management page
function tour_portal_partners_page() {
    // Check if user is admin
    $current_user = wp_get_current_user();
    $can_manage_options = current_user_can('manage_options');
    
    // Show both role types for admins, mode-specific for others
    if ($can_manage_options) {
        // Admin sees both partners and site owners
        $partners = get_users(array(
            'role__in' => array('partner', 'site_owner'),
            'meta_query' => array(
                array(
                    'key' => 'partner_approved',
                    'value' => '1',
                    'compare' => '='
                )
            )
        ));
    } else {
        // Others see mode-specific roles
        $is_single_operator = !tour_portal_is_multi_operator();
        $partners = get_users(array(
            'role' => $is_single_operator ? 'site_owner' : 'partner',
            'meta_query' => array(
                array(
                    'key' => 'partner_approved',
                    'value' => '1',
                    'compare' => '='
                )
            )
        ));
    }
    ?>
    <div class="wrap">
        <h1><?php _e('Partner Management', 'tour-portal'); ?></h1>
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('Partner Name', 'tour-portal'); ?></th>
                    <th><?php _e('Contact Person', 'tour-portal'); ?></th>
                    <th><?php _e('Email', 'tour-portal'); ?></th>
                    <th><?php _e('Role Type', 'tour-portal'); ?></th>
                    <th><?php _e('Commission %', 'tour-portal'); ?></th>
                    <th><?php _e('Status', 'tour-portal'); ?></th>
                    <th><?php _e('Tours', 'tour-portal'); ?></th>
                    <th><?php _e('Actions', 'tour-portal'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($partners as $partner): ?>
                <tr>
                    <td><?php echo esc_html(get_user_meta($partner->ID, 'partner_name', true)); ?></td>
                    <td><?php echo esc_html($partner->first_name); ?></td>
                    <td><?php echo esc_html($partner->user_email); ?></td>
                    <td>
                        <p class="partner-role">
                            <?php 
                            if (in_array('site_owner', $partner->roles)) {
                                echo 'Site Owner';
                            } else {
                                echo 'Partner';
                            }
                            ?>
                        </p>
                    </td>
                    <td><?php echo esc_html(get_user_meta($partner->ID, '_commission_rate', true)); ?>%</td>
                    <td>
                        <?php
                        $status = get_user_meta($partner->ID, '_partner_approved', true);
                        $status_label = $status === 'approved' ? __('Approved', 'tour-portal') : 
                                       ($status === 'rejected' ? __('Rejected', 'tour-portal') : __('Pending', 'tour-portal'));
                        $status_class = $status === 'approved' ? 'approved' : 
                                        ($status === 'rejected' ? 'rejected' : 'pending');
                        echo '<span class="status-' . $status_class . '">' . $status_label . '</span>';
                        ?>
                    </td>
                    <td>
                        <?php
                        $tours = count_user_posts($partner->ID, 'tour');
                        echo $tours;
                        ?>
                    </td>
                    <td>
                        <?php if ($can_manage_options): ?>
                            <a href="<?php echo admin_url('admin.php?page=tour-portal-edit-partner&partner_id=' . $partner->ID); ?>" class="button"><?php _e('Edit', 'tour-portal'); ?></a>
                        <?php elseif (in_array('site_owner', $partner->roles)): ?>
                            <a href="<?php echo admin_url('admin.php?page=tour-portal-edit-partner&partner_id=' . $partner->ID); ?>" class="button"><?php _e('Edit Partner', 'tour-portal'); ?></a>
                        <?php else: ?>
                            <span class="text-muted"><?php _e('No access', 'tour-portal'); ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <style>
    .status-approved { color: #46b450; font-weight: bold; }
    .status-rejected { color: #dc3232; font-weight: bold; }
    .status-pending { color: #ffb900; font-weight: bold; }
    </style>
    <?php
}

// Add New Partner page
function tour_portal_add_partner_page() {
    if (isset($_POST['add_partner'])) {
        check_admin_referer('tour_portal_add_partner');
        
        // Create new user account
        $username = sanitize_user($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = wp_generate_password(12, false);
        
        $user_id = wp_create_user($username, $password, $email);
        
        if (!is_wp_error($user_id)) {
            // Assign partner role
            $user = new WP_User($user_id);
            $user->set_role('partner');
            
            // Save partner details
            update_user_meta($user_id, 'partner_name', sanitize_text_field($_POST['partner_name']));
            update_user_meta($user_id, 'partner_address', sanitize_textarea_field($_POST['partner_address']));
            update_user_meta($user_id, 'partner_phone', sanitize_text_field($_POST['partner_phone']));
            update_user_meta($user_id, 'partner_website', esc_url_raw($_POST['partner_website']));
            update_user_meta($user_id, 'partner_logo', esc_url_raw($_POST['partner_logo']));
            update_user_meta($user_id, '_commission_rate', floatval($_POST['commission_rate']));
            update_user_meta($user_id, 'partner_description', sanitize_textarea_field($_POST['partner_description']));
            update_user_meta($user_id, 'partner_approved', '1'); // Auto-approve manually added partners
            
            // Send welcome email
            wp_mail($email, 'Welcome to Tour Portal', 
                "Your partner account has been created. Username: $username, Password: $password");
            
            echo '<div class="notice notice-success"><p>' . __('Partner created successfully!', 'tour-portal') . '</p></div>';
        } else {
            echo '<div class="notice notice-error"><p>' . $user_id->get_error_message() . '</p></div>';
        }
    }
    ?>
    <div class="wrap">
        <h1><?php _e('Add New Partner', 'tour-portal'); ?></h1>
        
        <form method="post" action="">
            <?php wp_nonce_field('tour_portal_add_partner'); ?>
            
            <table class="form-table">
                <tr>
                    <th><label for="partner_name"><?php _e('Partner Name', 'tour-portal'); ?></label></th>
                    <td><input type="text" name="partner_name" id="partner_name" class="regular-text" required></td>
                </tr>
                
                <tr>
                    <th><label for="username"><?php _e('Username', 'tour-portal'); ?></label></th>
                    <td><input type="text" name="username" id="username" class="regular-text" required></td>
                </tr>
                
                <tr>
                    <th><label for="email"><?php _e('Email', 'tour-portal'); ?></label></th>
                    <td><input type="email" name="email" id="email" class="regular-text" required></td>
                </tr>
                
                <tr>
                    <th><label for="partner_phone"><?php _e('Phone', 'tour-portal'); ?></label></th>
                    <td><input type="tel" name="partner_phone" id="partner_phone" class="regular-text"></td>
                </tr>
                
                <tr>
                    <th><label for="partner_address"><?php _e('Address', 'tour-portal'); ?></label></th>
                    <td><textarea name="partner_address" id="partner_address" rows="3" class="large-text"></textarea></td>
                </tr>
                
                <tr>
                    <th><label for="partner_website"><?php _e('Website', 'tour-portal'); ?></label></th>
                    <td><input type="url" name="partner_website" id="partner_website" class="regular-text"></td>
                </tr>
                
                <tr>
                    <th><label for="partner_logo"><?php _e('Logo URL', 'tour-portal'); ?></label></th>
                    <td><input type="url" name="partner_logo" id="partner_logo" class="regular-text"></td>
                </tr>
                
                <tr>
                    <th><label for="commission_rate"><?php _e('Commission Rate (%)', 'tour-portal'); ?></label></th>
                    <td><input type="number" name="commission_rate" id="commission_rate" min="0" max="100" step="0.1" value="10" class="small-text"></td>
                </tr>
                
                <tr>
                    <th><label for="partner_description"><?php _e('Description', 'tour-portal'); ?></label></th>
                    <td><textarea name="partner_description" id="partner_description" rows="5" class="large-text"></textarea></td>
                </tr>
            </table>
            
            <p class="submit">
                <input type="submit" name="add_partner" class="button button-primary" value="<?php _e('Add Partner', 'tour-portal'); ?>">
            </p>
        </form>
    </div>
    <?php
}

// Commission report page
function tour_portal_commissions_page() {
    // This would integrate with TicketingHub API for actual commission tracking
    // For now, showing a basic report structure
    $partners = get_users(array('role' => 'partner'));
    ?>
    <div class="wrap">
        <h1><?php _e('Commission Report', 'tour-portal'); ?></h1>
        
        <div class="notice notice-info">
            <p><?php _e('This report will integrate with TicketingHub API to show actual commission data from bookings.', 'tour-portal'); ?></p>
        </div>
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('Partner', 'tour-portal'); ?></th>
                    <th><?php _e('Commission Rate', 'tour-portal'); ?></th>
                    <th><?php _e('Total Bookings', 'tour-portal'); ?></th>
                    <th><?php _e('Commission Earned', 'tour-portal'); ?></th>
                    <th><?php _e('Period', 'tour-portal'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($partners as $partner): ?>
                <tr>
                    <td><?php echo esc_html(get_user_meta($partner->ID, 'partner_name', true)); ?></td>
                    <td><?php echo esc_html(get_user_meta($partner->ID, '_commission_rate', true)); ?>%</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

// Add custom columns to tour admin list
function tour_portal_custom_columns($columns) {
    $columns['tour_type'] = __('Type', 'tour-portal');
    $columns['price'] = __('Price', 'tour-portal');
    $columns['duration'] = __('Duration', 'tour-portal');
    $columns['partner'] = __('Partner', 'tour-portal');
    return $columns;
}
add_filter('manage_tour_posts_columns', 'tour_portal_custom_columns');

// Display custom column data
function tour_portal_custom_column_data($column, $post_id) {
    switch ($column) {
        case 'tour_type':
            $tour_type = get_post_meta($post_id, '_tour_type', true);
            echo $tour_type === 'private' ? __('Private', 'tour-portal') : __('Public', 'tour-portal');
            break;
            
        case 'price':
            $german_price = get_post_meta($post_id, '_german_price', true);
            $charging_model = get_post_meta($post_id, '_charging_model', true);
            if ($german_price) {
                echo '€' . number_format($german_price, 2);
                echo $charging_model === 'per_group' ? ' ' . __('per group', 'tour-portal') : ' ' . __('per person', 'tour-portal');
            }
            break;
            
        case 'duration':
            $duration = get_post_meta($post_id, '_duration_hours', true);
            if ($duration) {
                echo $duration . ' ' . __('hours', 'tour-portal');
            }
            break;
            
        case 'partner':
            $author = get_post_field('post_author', $post_id);
            $user = get_user_by('id', $author);
            if ($user) {
                echo esc_html($user->display_name);
            }
            break;
    }
}
add_action('manage_tour_posts_custom_column', 'tour_portal_custom_column_data', 10, 2);

// Add admin styles
function tour_portal_admin_styles() {
    wp_enqueue_style('tour-portal-admin', get_template_directory_uri() . '/assets/css/admin.css');
}
add_action('admin_enqueue_scripts', 'tour_portal_admin_styles');
