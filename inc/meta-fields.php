<?php
/**
 * Custom Meta Fields for Tour Post Type
 */

// Add Meta Boxes for Tour Post Type
function add_tour_meta_boxes() {
    add_meta_box(
        'tour_pricing_meta_box',
        __('Tour Pricing', 'tour-portal'),
        'tour_pricing_meta_box_callback',
        'tour',
        'normal',
        'high'
    );
    
    add_meta_box(
        'tour_participants_meta_box',
        __('Participants & Conditions', 'tour-portal'),
        'tour_participants_meta_box_callback',
        'tour',
        'normal',
        'high'
    );
    
    add_meta_box(
        'tour_details_meta_box',
        __('Tour Details', 'tour-portal'),
        'tour_details_meta_box_callback',
        'tour',
        'normal',
        'high'
    );
    
    add_meta_box(
        'tour_provider_meta_box',
        __('Provider Information', 'tour-portal'),
        'tour_provider_meta_box_callback',
        'tour',
        'normal',
        'high'
    );
    
    add_meta_box(
        'tour_ticketinghub_meta_box',
        __('TicketingHub Integration', 'tour-portal'),
        'tour_ticketinghub_meta_box_callback',
        'tour',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_tour_meta_boxes');

// Pricing Meta Box Callback
function tour_pricing_meta_box_callback($post) {
    wp_nonce_field('tour_pricing_save', 'tour_pricing_nonce');
    
    $charging_model = get_post_meta($post->ID, '_charging_model', true);
    $german_price = get_post_meta($post->ID, '_german_price', true);
    $foreign_price = get_post_meta($post->ID, '_foreign_price', true);
    $max_people = get_post_meta($post->ID, '_max_people', true);
    $duration_hours = get_post_meta($post->ID, '_duration_hours', true);
    $tour_type = get_post_meta($post->ID, '_tour_type', true);
    
    echo '<div class="tour-meta-field">';
    echo '<label for="tour_type">' . __('Tour Type', 'tour-portal') . '</label>';
    echo '<select id="tour_type" name="tour_type">';
    echo '<option value="public"' . selected($tour_type, 'public', false) . '>' . __('Public Tours (Per Person)', 'tour-portal') . '</option>';
    echo '<option value="private"' . selected($tour_type, 'private', false) . '>' . __('Private Tours (Per Group)', 'tour-portal') . '</option>';
    echo '</select>';
    echo '</div>';
    
    echo '<div class="tour-meta-field">';
    echo '<label for="charging_model">' . __('Charging Model', 'tour-portal') . '</label>';
    echo '<select id="charging_model" name="charging_model">';
    echo '<option value="per_person"' . selected($charging_model, 'per_person', false) . '>' . __('Per Person', 'tour-portal') . '</option>';
    echo '<option value="per_group"' . selected($charging_model, 'per_group', false) . '>' . __('Per Group', 'tour-portal') . '</option>';
    echo '</select>';
    echo '</div>';
    
    echo '<div class="tour-meta-field">';
    echo '<label for="german_price">' . __('German-language Price (€)', 'tour-portal') . '</label>';
    echo '<input type="number" id="german_price" name="german_price" step="0.01" value="' . esc_attr($german_price) . '" />';
    echo '</div>';
    
    echo '<div class="tour-meta-field">';
    echo '<label for="foreign_price">' . __('Foreign-language Price (€)', 'tour-portal') . '</label>';
    echo '<input type="number" id="foreign_price" name="foreign_price" step="0.01" value="' . esc_attr($foreign_price) . '" />';
    echo '</div>';
    
    echo '<div class="tour-meta-field">';
    echo '<label for="max_people">' . __('Max People', 'tour-portal') . '</label>';
    echo '<input type="number" id="max_people" name="max_people" value="' . esc_attr($max_people) . '" />';
    echo '</div>';
    
    echo '<div class="tour-meta-field">';
    echo '<label for="duration_hours">' . __('Duration (Hours)', 'tour-portal') . '</label>';
    echo '<input type="number" id="duration_hours" name="duration_hours" step="0.5" value="' . esc_attr($duration_hours) . '" />';
    echo '</div>';
}

// Participants Meta Box Callback
function tour_participants_meta_box_callback($post) {
    wp_nonce_field('tour_participants_save', 'tour_participants_nonce');
    
    $min_per_group = get_post_meta($post->ID, '_min_per_group', true);
    $max_per_group = get_post_meta($post->ID, '_max_per_group', true);
    $additional_fees = get_post_meta($post->ID, '_additional_fees', true);
    $wheelchair_accessible = get_post_meta($post->ID, '_wheelchair_accessible', true);
    $booking_restraint = get_post_meta($post->ID, '_booking_restraint', true);
    $cancellation_weeks = get_post_meta($post->ID, '_cancellation_weeks', true);
    $refund_percent = get_post_meta($post->ID, '_refund_percent', true);
    $booking_exceptions = get_post_meta($post->ID, '_booking_exceptions', true);
    
    echo '<div class="tour-meta-field">';
    echo '<label for="min_per_group">' . __('Min per Group', 'tour-portal') . '</label>';
    echo '<input type="number" id="min_per_group" name="min_per_group" value="' . esc_attr($min_per_group) . '" />';
    echo '</div>';
    
    echo '<div class="tour-meta-field">';
    echo '<label for="max_per_group">' . __('Max per Group', 'tour-portal') . '</label>';
    echo '<input type="number" id="max_per_group" name="max_per_group" value="' . esc_attr($max_per_group) . '" />';
    echo '</div>';
    
    echo '<div class="tour-meta-field">';
    echo '<label for="additional_fees">' . __('Additional Entrance Fees', 'tour-portal') . '</label>';
    echo '<select id="additional_fees" name="additional_fees">';
    echo '<option value="yes"' . selected($additional_fees, 'yes', false) . '>' . __('Yes', 'tour-portal') . '</option>';
    echo '<option value="no"' . selected($additional_fees, 'no', false) . '>' . __('No', 'tour-portal') . '</option>';
    echo '</select>';
    echo '</div>';
    
    echo '<div class="tour-meta-field">';
    echo '<label for="wheelchair_accessible">' . __('Wheelchair Accessible', 'tour-portal') . '</label>';
    echo '<select id="wheelchair_accessible" name="wheelchair_accessible">';
    echo '<option value="yes"' . selected($wheelchair_accessible, 'yes', false) . '>' . __('Yes', 'tour-portal') . '</option>';
    echo '<option value="no"' . selected($wheelchair_accessible, 'no', false) . '>' . __('No', 'tour-portal') . '</option>';
    echo '</select>';
    echo '</div>';
    
    echo '<div class="tour-meta-field">';
    echo '<label for="booking_restraint">' . __('Days Before Booking Required', 'tour-portal') . '</label>';
    echo '<input type="number" id="booking_restraint" name="booking_restraint" value="' . esc_attr($booking_restraint) . '" />';
    echo '</div>';
    
    echo '<div class="tour-meta-field">';
    echo '<label for="cancellation_weeks">' . __('Cancellation: Weeks Before', 'tour-portal') . '</label>';
    echo '<input type="number" id="cancellation_weeks" name="cancellation_weeks" value="' . esc_attr($cancellation_weeks) . '" />';
    echo '</div>';
    
    echo '<div class="tour-meta-field">';
    echo '<label for="refund_percent">' . __('Refund Percent (%)', 'tour-portal') . '</label>';
    echo '<input type="number" id="refund_percent" name="refund_percent" step="0.1" value="' . esc_attr($refund_percent) . '" />';
    echo '</div>';
    
    echo '<div class="tour-meta-field">';
    echo '<label for="booking_exceptions">' . __('Booking Exceptions', 'tour-portal') . '</label>';
    echo '<textarea id="booking_exceptions" name="booking_exceptions" rows="4">' . esc_textarea($booking_exceptions) . '</textarea>';
    echo '</div>';
}

// Details Meta Box Callback
function tour_details_meta_box_callback($post) {
    wp_nonce_field('tour_details_save', 'tour_details_nonce');
    
    $languages = get_post_meta($post->ID, '_languages', true);
    if (!is_array($languages)) $languages = array();
    
    $available_languages = array(
        'english' => __('English', 'tour-portal'),
        'german' => __('German', 'tour-portal'),
        'french' => __('French', 'tour-portal'),
        'italian' => __('Italian', 'tour-portal'),
        'spanish' => __('Spanish', 'tour-portal'),
        'dutch' => __('Dutch', 'tour-portal'),
        'portuguese' => __('Portuguese', 'tour-portal'),
        'russian' => __('Russian', 'tour-portal'),
        'chinese' => __('Chinese', 'tour-portal'),
        'japanese' => __('Japanese', 'tour-portal'),
    );
    
    echo '<div class="tour-meta-field">';
    echo '<label>' . __('Languages Offered', 'tour-portal') . '</label>';
    foreach ($available_languages as $lang_key => $lang_name) {
        echo '<div style="margin-bottom: 5px;">';
        echo '<input type="checkbox" id="lang_' . $lang_key . '" name="languages[]" value="' . $lang_key . '" ' . checked(in_array($lang_key, $languages), true, false) . ' />';
        echo '<label for="lang_' . $lang_key . '">' . $lang_name . '</label>';
        echo '</div>';
    }
    echo '</div>';
}

// Provider Meta Box Callback
function tour_provider_meta_box_callback($post) {
    wp_nonce_field('tour_provider_save', 'tour_provider_nonce');
    
    $provider_name = get_post_meta($post->ID, '_provider_name', true);
    $provider_address = get_post_meta($post->ID, '_provider_address', true);
    $provider_tel = get_post_meta($post->ID, '_provider_tel', true);
    $provider_email = get_post_meta($post->ID, '_provider_email', true);
    
    echo '<div class="tour-meta-field">';
    echo '<label for="provider_name">' . __('Provider Name', 'tour-portal') . '</label>';
    echo '<input type="text" id="provider_name" name="provider_name" value="' . esc_attr($provider_name) . '" />';
    echo '</div>';
    
    echo '<div class="tour-meta-field">';
    echo '<label for="provider_address">' . __('Provider Address', 'tour-portal') . '</label>';
    echo '<textarea id="provider_address" name="provider_address" rows="3">' . esc_textarea($provider_address) . '</textarea>';
    echo '</div>';
    
    echo '<div class="tour-meta-field">';
    echo '<label for="provider_tel">' . __('Provider Telephone', 'tour-portal') . '</label>';
    echo '<input type="tel" id="provider_tel" name="provider_tel" value="' . esc_attr($provider_tel) . '" />';
    echo '</div>';
    
    echo '<div class="tour-meta-field">';
    echo '<label for="provider_email">' . __('Provider Email', 'tour-portal') . '</label>';
    echo '<input type="email" id="provider_email" name="provider_email" value="' . esc_attr($provider_email) . '" />';
    echo '</div>';
}

// TicketingHub Meta Box Callback
function tour_ticketinghub_meta_box_callback($post) {
    wp_nonce_field('tour_ticketinghub_save', 'tour_ticketinghub_nonce');
    
    $widget_id = get_post_meta($post->ID, '_ticketinghub_widget_id', true);
    $widget_script = get_post_meta($post->ID, '_ticketinghub_widget_script', true);
    
    echo '<div class="tour-meta-field">';
    echo '<label for="_ticketinghub_widget_id">' . __('TicketingHub Widget ID', 'tour-portal') . '</label>';
    echo '<input type="text" id="_ticketinghub_widget_id" name="_ticketinghub_widget_id" value="' . esc_attr($widget_id) . '" />';
    echo '</div>';
    
    echo '<div class="tour-meta-field">';
    echo '<label for="_ticketinghub_widget_script">' . __('TicketingHub Widget Script', 'tour-portal') . '</label>';
    echo '<textarea id="_ticketinghub_widget_script" name="_ticketinghub_widget_script" rows="6" style="font-family: monospace;">' . esc_textarea($widget_script) . '</textarea>';
    echo '<p class="description">' . __('Paste the complete TicketingHub embed script here.', 'tour-portal') . '</p>';
    echo '</div>';
}

// Save Meta Data
function save_tour_meta_data($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    
    // Check nonces - remove TicketingHub nonce check to fix saving
    if (!isset($_POST['tour_pricing_nonce']) || !wp_verify_nonce($_POST['tour_pricing_nonce'], 'tour_pricing_save')) return;
    if (!isset($_POST['tour_participants_nonce']) || !wp_verify_nonce($_POST['tour_participants_nonce'], 'tour_participants_save')) return;
    if (!isset($_POST['tour_details_nonce']) || !wp_verify_nonce($_POST['tour_details_nonce'], 'tour_details_save')) return;
    if (!isset($_POST['tour_provider_nonce']) || !wp_verify_nonce($_POST['tour_provider_nonce'], 'tour_provider_save')) return;
    // TicketingHub nonce check completely removed to fix saving issue
    
    // Save pricing fields
    if (isset($_POST['tour_type'])) update_post_meta($post_id, '_tour_type', sanitize_text_field($_POST['tour_type']));
    if (isset($_POST['charging_model'])) update_post_meta($post_id, '_charging_model', sanitize_text_field($_POST['charging_model']));
    if (isset($_POST['german_price'])) update_post_meta($post_id, '_german_price', floatval($_POST['german_price']));
    if (isset($_POST['foreign_price'])) update_post_meta($post_id, '_foreign_price', floatval($_POST['foreign_price']));
    if (isset($_POST['max_people'])) update_post_meta($post_id, '_max_people', intval($_POST['max_people']));
    if (isset($_POST['duration_hours'])) update_post_meta($post_id, '_duration_hours', floatval($_POST['duration_hours']));
    
    // Save participants fields
    if (isset($_POST['min_per_group'])) update_post_meta($post_id, '_min_per_group', intval($_POST['min_per_group']));
    if (isset($_POST['max_per_group'])) update_post_meta($post_id, '_max_per_group', intval($_POST['max_per_group']));
    if (isset($_POST['additional_fees'])) update_post_meta($post_id, '_additional_fees', sanitize_text_field($_POST['additional_fees']));
    if (isset($_POST['wheelchair_accessible'])) update_post_meta($post_id, '_wheelchair_accessible', sanitize_text_field($_POST['wheelchair_accessible']));
    if (isset($_POST['booking_restraint'])) update_post_meta($post_id, '_booking_restraint', intval($_POST['booking_restraint']));
    if (isset($_POST['cancellation_weeks'])) update_post_meta($post_id, '_cancellation_weeks', intval($_POST['cancellation_weeks']));
    if (isset($_POST['refund_percent'])) update_post_meta($post_id, '_refund_percent', floatval($_POST['refund_percent']));
    if (isset($_POST['booking_exceptions'])) update_post_meta($post_id, '_booking_exceptions', sanitize_textarea_field($_POST['booking_exceptions']));
    
    // Save details fields
    if (isset($_POST['languages'])) {
        $languages = array_map('sanitize_text_field', $_POST['languages']);
        update_post_meta($post_id, '_languages', $languages);
    }
    
    // Save provider fields
    if (isset($_POST['provider_name'])) update_post_meta($post_id, '_provider_name', sanitize_text_field($_POST['provider_name']));
    if (isset($_POST['provider_address'])) update_post_meta($post_id, '_provider_address', sanitize_textarea_field($_POST['provider_address']));
    if (isset($_POST['provider_tel'])) update_post_meta($post_id, '_provider_tel', sanitize_text_field($_POST['provider_tel']));
    if (isset($_POST['provider_email'])) update_post_meta($post_id, '_provider_email', sanitize_email($_POST['provider_email']));
    
    // Save TicketingHub fields
    if (isset($_POST['_ticketinghub_widget_id'])) update_post_meta($post_id, '_ticketinghub_widget_id', sanitize_text_field($_POST['_ticketinghub_widget_id']));
    if (isset($_POST['_ticketinghub_widget_script'])) update_post_meta($post_id, '_ticketinghub_widget_script', wp_kses_post($_POST['_ticketinghub_widget_script']));
}
add_action('save_post', 'save_tour_meta_data');
