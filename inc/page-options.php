<?php
/**
 * Page Options Meta Box
 */

// Add Page Options Box to Editor
function tp_add_page_options_meta() {
    add_meta_box('tp_page_options', 'Page Layout Options', 'tp_page_options_callback', 'page', 'side', 'high');
}
add_action('add_meta_boxes', 'tp_add_page_options_meta');

// Page Options Callback
function tp_page_options_callback($post) {
    // Add nonce for security
    wp_nonce_field('tp_page_options_save', 'tp_page_options_nonce');
    
    // Get current values
    $hide_title = get_post_meta($post->ID, '_tp_hide_title', true);
    $sidebar_selection = get_post_meta($post->ID, '_tp_sidebar_selection', true);
    
    // Set 'none' as default if no value is saved
    if (empty($sidebar_selection)) {
        $sidebar_selection = 'none';
    }
    ?>
    <div class="tp-page-options">
        <p>
            <label>
                <input type="checkbox" name="tp_hide_title" value="1" <?php checked($hide_title, '1'); ?> />
                <?php _e('Hide Page Title', 'tour-portal'); ?>
            </label>
        </p>
        
        <p>
            <label for="tp_sidebar_selection"><?php _e('Sidebar Layout:', 'tour-portal'); ?></label>
            <select name="tp_sidebar_selection" id="tp_sidebar_selection" style="width:100%;">
                <option value="none" <?php selected($sidebar_selection, 'none'); ?>><?php _e('No Sidebar (Full Width)', 'tour-portal'); ?></option>
                <option value="main-sidebar" <?php selected($sidebar_selection, 'main-sidebar'); ?>><?php _e('Main Sidebar', 'tour-portal'); ?></option>
                <?php 
                // Add dynamic sidebar options if they exist
                global $wp_registered_sidebars;
                if (!empty($wp_registered_sidebars)) {
                    foreach ($wp_registered_sidebars as $sidebar) {
                        if ($sidebar['id'] !== 'main-sidebar') {
                            echo '<option value="' . esc_attr($sidebar['id']) . '" ' . selected($sidebar_selection, $sidebar['id']) . '>' . esc_html($sidebar['name']) . '</option>';
                        }
                    }
                }
                ?>
            </select>
        </p>
        
        <div class="tp-options-info">
            <p class="description">
                <strong><?php _e('Note:', 'tour-portal'); ?></strong> <?php _e('These settings override the default layout options in the Customizer.', 'tour-portal'); ?>
            </p>
            <p class="description">
                <strong><?php _e('No Sidebar:', 'tour-portal'); ?></strong> <?php _e('Full-width content layout (70% becomes 100%)', 'tour-portal'); ?>
            </p>
            <p class="description">
                <strong><?php _e('Main Sidebar:', 'tour-portal'); ?></strong> <?php _e('Traditional 70/30 split layout', 'tour-portal'); ?>
            </p>
        </div>
    </div>
    
    <style>
    .tp-page-options {
        padding: 10px;
        background: #f9f9f9;
        border-radius: 6px;
    }
    
    .tp-page-options label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
    }
    
    .tp-page-options select {
        width: 100%;
        margin-bottom: 15px;
    }
    
    .tp-options-info {
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #ddd;
    }
    
    .tp-options-info .description {
        font-size: 12px;
        color: #666;
        line-height: 1.4;
        margin-bottom: 8px;
    }
    
    .tp-options-info strong {
        color: #333;
    }
    </style>
    <?php
}

// Save Page Options
function tp_save_page_options($post_id) {
    // Check if our nonce is set and verify it
    if (!isset($_POST['tp_page_options_nonce']) || !wp_verify_nonce($_POST['tp_page_options_nonce'], 'tp_page_options_save')) {
        return $post_id;
    }
    
    // Check if this is an autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }
    
    // Check user permissions
    if (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }
    
    // Save Title Toggle - use proper boolean handling
    $hide_title = isset($_POST['tp_hide_title']) ? '1' : '0';
    update_post_meta($post_id, '_tp_hide_title', $hide_title);

    // Save Sidebar Dropdown Selection
    if (isset($_POST['tp_sidebar_selection'])) {
        update_post_meta($post_id, '_tp_sidebar_selection', sanitize_text_field($_POST['tp_sidebar_selection']));
    }
    
    return $post_id;
}
add_action('save_post', 'tp_save_page_options', 10);

// Add custom CSS to admin head for layout preview
function tp_admin_page_styles() {
    global $post;
    
    if ($post && $post->post_type === 'page') {
        $sidebar_layout = get_post_meta($post->ID, '_tp_sidebar_selection', true);
        $hide_title = get_post_meta($post->ID, '_tp_hide_title', true);
        
        echo '<style type="text/css">';
        if ($hide_title === '1') {
            echo '.editor-post-title__input { display: none; }';
        }
        
        if ($sidebar_layout === 'none') {
            echo '.wp-block { max-width: 100%; }';
        }
        echo '</style>';
    }
}
add_action('admin_head', 'tp_admin_page_styles');

// Add layout indicator to page list
function tp_add_layout_column($columns) {
    $columns['layout'] = __('Layout', 'tour-portal');
    return $columns;
}
add_filter('manage_pages_columns', 'tp_add_layout_column');

function tp_show_layout_column($column_name, $post_id) {
    if ($column_name === 'layout') {
        $sidebar_layout = get_post_meta($post_id, '_tp_sidebar_selection', true);
        $hide_title = get_post_meta($post_id, '_tp_hide_title', true);
        
        $layout_info = array();
        
        if ($hide_title === '1') {
            $layout_info[] = '<span style="color: #e74c3c;" title="' . __('Title Hidden', 'tour-portal') . '">🚫</span>';
        } else {
            $layout_info[] = '<span style="color: #46b450;" title="' . __('Title Visible', 'tour-portal') . '">👁</span>';
        }
        
        if ($sidebar_layout === 'none') {
            $layout_info[] = '<span style="color: #0073aa;" title="' . __('Full Width', 'tour-portal') . '">📄</span>';
        } elseif ($sidebar_layout === 'main-sidebar') {
            $layout_info[] = '<span style="color: #0073aa;" title="' . __('70/30 Layout', 'tour-portal') . '">📊</span>';
        } else {
            $layout_info[] = '<span style="color: #666;" title="' . __('Custom Layout', 'tour-portal') . '">⚙️</span>';
        }
        
        echo implode(' ', $layout_info);
    }
}
add_action('manage_pages_custom_column', 'tp_show_layout_column');

// Quick Edit support for layout options
function tp_quick_edit_custom_fields($column_name, $post_type) {
    if ($column_name !== 'layout' || $post_type !== 'page') {
        return;
    }
    
    global $post;
    $sidebar_layout = get_post_meta($post->ID, '_tp_sidebar_selection', true);
    $hide_title = get_post_meta($post->ID, '_tp_hide_title', true);
    
    echo '<div class="inline-edit-col">';
    echo '<select name="tp_sidebar_selection_edit">';
    echo '<option value="none"' . selected($sidebar_layout, 'none', false) . '>' . __('Full Width', 'tour-portal') . '</option>';
    echo '<option value="main-sidebar"' . selected($sidebar_layout, 'main-sidebar', false) . '>' . __('70/30 Layout', 'tour-portal') . '</option>';
    echo '</select>';
    
    echo '<label style="margin-left: 10px;">';
    echo '<input type="checkbox" name="tp_hide_title_edit" value="1"' . checked($hide_title, '1', false) . '> ' . __('Hide Title', 'tour-portal');
    echo '</label>';
    echo '</div>';
}
add_action('quick_edit_custom_box', 'tp_quick_edit_custom_fields', 10, 2);

// Quick edit save removed to avoid conflicts with main save function
