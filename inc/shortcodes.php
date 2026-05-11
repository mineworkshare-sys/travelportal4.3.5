<?php
/**
 * Tour Portal Shortcodes
 */

// Registration Form Shortcode
function tour_registration_form_shortcode() {
    ob_start();
    ?>
    <div class="tour-registration-form">
        <h2><?php _e('Partner Registration', 'tour-portal'); ?></h2>
        <?php
        if (isset($_GET['registration'])) {
            if ($_GET['registration'] === 'success') {
                echo '<div class="alert alert-success">' . __('Registration successful! Your account is pending approval.', 'tour-portal') . '</div>';
            } elseif ($_GET['registration'] === 'email_exists') {
                echo '<div class="alert alert-error">' . __('Email already exists. Please use a different email.', 'tour-portal') . '</div>';
            }
        }
        ?>
        <form id="partner-registration-form" method="post">
            <?php wp_nonce_field('partner_registration', 'partner_registration_nonce'); ?>
            <div class="form-group">
                <label for="partner_name"><?php _e('Partner Name', 'tour-portal'); ?></label>
                <input type="text" id="partner_name" name="partner_name" required />
            </div>
            <div class="form-group">
                <label for="contact_person"><?php _e('Contact Person', 'tour-portal'); ?></label>
                <input type="text" id="contact_person" name="contact_person" required />
            </div>
            <div class="form-group">
                <label for="partner_email"><?php _e('Email', 'tour-portal'); ?></label>
                <input type="email" id="partner_email" name="partner_email" required />
            </div>
            <div class="form-group">
                <label for="partner_password"><?php _e('Password', 'tour-portal'); ?></label>
                <input type="password" id="partner_password" name="partner_password" required />
            </div>
            <div class="form-group">
                <label for="partner_phone"><?php _e('Phone', 'tour-portal'); ?></label>
                <input type="tel" id="partner_phone" name="partner_phone" />
            </div>
            <div class="form-group">
                <label for="partner_address"><?php _e('Address', 'tour-portal'); ?></label>
                <textarea id="partner_address" name="partner_address" rows="3"></textarea>
            </div>
            <div class="form-group">
                <input type="submit" name="submit_partner_registration" value="<?php _e('Register', 'tour-portal'); ?>" class="button button-primary" />
            </div>
        </form>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('tour_registration_form', 'tour_registration_form_shortcode');

// Tour List Shortcode
function tour_list_shortcode($atts) {
    $atts = shortcode_atts(array(
        'category' => '',
        'limit' => 10,
        'partner' => '',
        'tour_type' => '',
    ), $atts);
    
    $args = array(
        'post_type' => 'tour',
        'posts_per_page' => intval($atts['limit']),
        'post_status' => 'publish',
    );
    
    if (!empty($atts['category'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'tour_category',
                'field' => 'slug',
                'terms' => $atts['category'],
            ),
        );
    }
    
    if (!empty($atts['partner'])) {
        $args['author'] = intval($atts['partner']);
    }
    
    if (!empty($atts['tour_type'])) {
        $args['meta_query'] = array(
            array(
                'key' => '_tour_type',
                'value' => $atts['tour_type'],
                'compare' => '=',
            ),
        );
    }
    
    $query = new WP_Query($args);
    
    ob_start();
    if ($query->have_posts()) {
        echo '<div class="tour-list">';
        while ($query->have_posts()) {
            $query->the_post();
            get_template_part('template-parts/tour-card');
        }
        echo '</div>';
    } else {
        echo '<p>' . __('No tours found.', 'tour-portal') . '</p>';
    }
    wp_reset_postdata();
    
    return ob_get_clean();
}
add_shortcode('tour_list', 'tour_list_shortcode');

// Tour Categories Grid Shortcode
function tour_categories_grid_shortcode($atts) {
    $atts = shortcode_atts(array(
        'partner' => '',
    ), $atts);
    
    $args = array(
        'taxonomy' => 'tour_category',
        'hide_empty' => true,
    );
    
    if (!empty($atts['partner'])) {
        // This would need custom implementation to filter categories by partner
    }
    
    $categories = get_terms($args);
    
    ob_start();
    if (!empty($categories) && !is_wp_error($categories)) {
        echo '<div class="tour-categories-grid">';
        foreach ($categories as $category) {
            $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
            $image = wp_get_attachment_image_url($thumbnail_id, 'medium');
            
            echo '<div class="category-card">';
            if ($image) {
                echo '<div class="category-image"><img src="' . esc_url($image) . '" alt="' . esc_attr($category->name) . '" /></div>';
            }
            echo '<h3><a href="' . get_term_link($category) . '">' . esc_html($category->name) . '</a></h3>';
            echo '<p>' . esc_html($category->description) . '</p>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<p>' . __('No categories found.', 'tour-portal') . '</p>';
    }
    
    return ob_get_clean();
}
add_shortcode('tour_categories_grid', 'tour_categories_grid_shortcode');

// Tour Filter Shortcode
function tour_filter_shortcode($atts) {
    $atts = shortcode_atts(array(
        'show_type' => 'yes',
        'show_language' => 'yes',
        'show_category' => 'yes',
    ), $atts);
    
    ob_start();
    ?>
    <div class="tour-filter">
        <?php if ($atts['show_type'] === 'yes'): ?>
        <div class="filter-group">
            <label><?php _e('Tour Type', 'tour-portal'); ?></label>
            <select id="filter-tour-type">
                <option value=""><?php _e('All Types', 'tour-portal'); ?></option>
                <option value="public"><?php _e('Public Tours', 'tour-portal'); ?></option>
                <option value="private"><?php _e('Private Tours', 'tour-portal'); ?></option>
            </select>
        </div>
        <?php endif; ?>
        
        <?php if ($atts['show_language'] === 'yes'): ?>
        <div class="filter-group">
            <label><?php _e('Language', 'tour-portal'); ?></label>
            <select id="filter-language">
                <option value=""><?php _e('All Languages', 'tour-portal'); ?></option>
                <option value="english"><?php _e('English', 'tour-portal'); ?></option>
                <option value="german"><?php _e('German', 'tour-portal'); ?></option>
                <option value="french"><?php _e('French', 'tour-portal'); ?></option>
                <option value="italian"><?php _e('Italian', 'tour-portal'); ?></option>
                <option value="spanish"><?php _e('Spanish', 'tour-portal'); ?></option>
            </select>
        </div>
        <?php endif; ?>
        
        <?php if ($atts['show_category'] === 'yes'): ?>
        <div class="filter-group">
            <label><?php _e('Category', 'tour-portal'); ?></label>
            <?php
            wp_dropdown_categories(array(
                'taxonomy' => 'tour_category',
                'show_option_none' => __('All Categories', 'tour-portal'),
                'name' => 'filter_category',
                'id' => 'filter-category',
            ));
            ?>
        </div>
        <?php endif; ?>
        
        <button type="button" id="apply-filters" class="button"><?php _e('Apply Filters', 'tour-portal'); ?></button>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('tour_filter', 'tour_filter_shortcode');
