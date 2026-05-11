<?php
/**
 * User Roles and Capabilities
 */

// Register Partner User Role with ALL capabilities
function register_partner_role() {
    // Remove existing role first to ensure clean update
    remove_role('partner');
    
    // Add role with complete capabilities
    add_role('partner', __('Partner', 'tour-portal'), array(
        'read' => true,
        'edit_posts' => true,
        'delete_posts' => true,
        'publish_posts' => true,
        'upload_files' => true,
        'edit_tours' => true,
        'edit_published_tours' => true,
        'delete_published_tours' => true,
        'read_tour' => true,
        'delete_tour' => true,
        'edit_tour' => true,
        'manage_categories' => true,
        'edit_terms' => true,
        'assign_terms' => true,
        'delete_tours' => true,
        'delete_published_tour' => true,
        'manage_tour_categories' => true,
        'edit_tour_categories' => true,
        'delete_tour_categories' => true,
        'assign_tour_categories' => true,
        // Standard WordPress capabilities for FULL control
        'edit_post' => true,
        'read_post' => true,
        'delete_post' => true,
        'edit_posts' => true,
        'delete_posts' => true,
        'publish_posts' => true,
        'read_private_posts' => true,
        'delete_private_posts' => true,
        'edit_private_posts' => true,
        'delete_published_posts' => true,
        'edit_published_posts' => true,
        'edit_others_posts' => true,
        'delete_others_posts' => true,
        'read_others_posts' => true,
    ));
}
add_action('init', 'register_partner_role');

// Add commission field to user profile
function add_partner_user_fields($user) {
    if (!current_user_can('edit_user', $user->ID)) return;
    
    $commission_rate = get_user_meta($user->ID, '_commission_rate', true);
    $partner_approved = get_user_meta($user->ID, '_partner_approved', true);
    ?>
    <h3><?php _e('Partner Information', 'tour-portal'); ?></h3>
    <table class="form-table">
        <tr>
            <th><label for="commission_rate"><?php _e('Commission Rate (%)', 'tour-portal'); ?></label></th>
            <td>
                <input type="number" id="commission_rate" name="commission_rate" step="0.1" value="<?php echo esc_attr($commission_rate); ?>" />
                <p class="description"><?php _e('Commission percentage for this partner', 'tour-portal'); ?></p>
            </td>
        </tr>
        <?php if (in_array('partner', (array) $user->roles)): ?>
        <tr>
            <th><label for="partner_approved"><?php _e('Partner Status', 'tour-portal'); ?></label></th>
            <td>
                <select id="partner_approved" name="partner_approved">
                    <option value="pending" <?php selected($partner_approved, 'pending'); ?>><?php _e('Pending Approval', 'tour-portal'); ?></option>
                    <option value="approved" <?php selected($partner_approved, 'approved'); ?>><?php _e('Approved', 'tour-portal'); ?></option>
                    <option value="rejected" <?php selected($partner_approved, 'rejected'); ?>><?php _e('Rejected', 'tour-portal'); ?></option>
                </select>
            </td>
        </tr>
        <?php endif; ?>
    </table>
    <?php
}
add_action('show_user_profile', 'add_partner_user_fields');
add_action('edit_user_profile', 'add_partner_user_fields');

// Save user fields
function save_partner_user_fields($user_id) {
    if (!current_user_can('edit_user', $user_id)) return;
    
    if (isset($_POST['commission_rate'])) {
        update_user_meta($user_id, '_commission_rate', floatval($_POST['commission_rate']));
    }
    
    if (isset($_POST['partner_approved'])) {
        update_user_meta($user_id, '_partner_approved', sanitize_text_field($_POST['partner_approved']));
    }
}
add_action('personal_options_update', 'save_partner_user_fields');
add_action('edit_user_profile_update', 'save_partner_user_fields');

// Partners should have full control over their tours - no auto-publish forcing

// Remove pending status from tour post type for partners
function remove_pending_from_tour_status($statuses) {
    $user = wp_get_current_user();
    if (in_array('partner', (array) $user->roles)) {
        unset($statuses['pending']);
    }
    return $statuses;
}
add_filter('wp_translations', 'remove_pending_from_tour_status');

// Partners should control their own tour status - no auto-publish

// Filter tour queries - admin sees all, partners see only their own
function filter_tours_for_partners($query) {
    if (is_admin() && $query->get('post_type') === 'tour') {
        $current_user = wp_get_current_user();
        
        // Admin sees all tours - no filtering
        if (current_user_can('manage_options')) {
            // Don't filter anything for admin
            return $query;
        }
        
        // Partners and Site Owners see only their own tours
        if (in_array('partner', (array) $current_user->roles) || in_array('site_owner', (array) $current_user->roles)) {
            $query->set('author', $current_user->ID);
        }
    }
    
    return $query;
}
add_filter('pre_get_posts', 'filter_tours_for_partners', 5);

// Handle partner registration
function handle_partner_registration() {
    if (isset($_POST['submit_partner_registration']) && wp_verify_nonce($_POST['partner_registration_nonce'], 'partner_registration')) {
        $partner_name = sanitize_text_field($_POST['partner_name']);
        $contact_person = sanitize_text_field($_POST['contact_person']);
        $partner_email = sanitize_email($_POST['partner_email']);
        $partner_password = $_POST['partner_password'];
        $partner_phone = sanitize_text_field($_POST['partner_phone']);
        $partner_address = sanitize_textarea_field($_POST['partner_address']);
        
        if (email_exists($partner_email) || username_exists($partner_email)) {
            wp_redirect(add_query_arg('registration', 'email_exists', wp_get_referer()));
            exit;
        }
        
        $user_id = wp_create_user($partner_email, $partner_password, $partner_email);
        
        if (!is_wp_error($user_id)) {
            $user = new WP_User($user_id);
            // Check if we're in single operator mode and assign appropriate role
            $is_single_operator = !tour_portal_is_multi_operator();
            $user->set_role($is_single_operator ? 'site_owner' : 'partner');
            
            update_user_meta($user_id, 'first_name', $contact_person);
            update_user_meta($user_id, 'partner_name', $partner_name);
            update_user_meta($user_id, 'partner_phone', $partner_phone);
            update_user_meta($user_id, 'partner_address', $partner_address);
            update_user_meta($user_id, '_partner_approved', 'pending');
            
            wp_redirect(add_query_arg('registration', 'success', wp_get_referer()));
            exit;
        }
    }
}
add_action('template_redirect', 'handle_partner_registration');
