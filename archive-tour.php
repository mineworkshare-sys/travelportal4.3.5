<?php get_header(); ?>

<style>
/* Tour Card Styles - Same as partner page */
.tour-card {
    display: flex;
    gap: 20px;
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
    width: 100%;
}

.tour-image {
    flex: 0 0 30%;
    max-width: 30%;
}

.tour-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.tour-content {
    flex: 0 0 70%;
    max-width: 70%;
    padding: 20px;
}

.tour-content h3 {
    margin: 0 0 10px 0;
    font-size: 16px;
}

.tour-content h3 a {
    color: #333;
    text-decoration: none;
}

.tour-content p {
    margin: 0 0 5px 0;
    color: #666;
    font-size: 13px;
    line-height: 1.2;
}

.tour-details {
    display: flex;
    gap: 30px;
    margin-top: 5px;
    margin-bottom: 10px;
}

.detail-block h4 {
    margin: 0 0 5px 0;
    font-size: 14px;
    font-weight: 600;
    color: #333;
}

.detail-block ul {
    margin: 0;
    padding: 0;
    list-style: none;
    display: flex;
    gap: 5px;
}

.detail-block li {
    background: #f8f9fa;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 12px;
}

.tour-button {
    margin-top: 10px;
}

.btn-booking {
    background: #007cba;
    color: white;
    padding: 10px 20px;
    border-radius: 4px;
    text-decoration: none;
    display: inline-block;
}
</style>

<div class="container">
    <div class="partner-layout">
        <!-- Left Column with Search -->
        <div class="partner-sidebar">
            <!-- Search Box -->
            <div class="partner-search">
                <h3>Search Tours</h3>
                <form class="tour-search-form" method="get" action="<?php echo home_url('/'); ?>">
                    <input type="text" name="s" placeholder="Search tours..." class="search-input">
                    <button type="submit" class="search-button">Search</button>
                </form>
            </div>
            
            <!-- Tour Filter -->
            <div class="tour-filter">
                <div class="filter-group">
                    <label>Tour Type</label>
                    <select id="filter-tour-type">
                        <option value="">All Types</option>
                        <option value="public">Public Tours</option>
                        <option value="private">Private Tours</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label>Language</label>
                    <select id="filter-language">
                        <option value="">All Languages</option>
                        <option value="english">English</option>
                        <option value="german">German</option>
                        <option value="french">French</option>
                        <option value="italian">Italian</option>
                        <option value="spanish">Spanish</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label>Category</label>
                    <?php wp_dropdown_categories(array(
                        'taxonomy' => 'tour_category',
                        'name' => 'filter_category',
                        'id' => 'filter-category',
                        'show_option_none' => 'All Categories',
                        'option_none_value' => '-1',
                        'class' => 'postform'
                    )); ?>
                </div>
                
                <button type="button" id="apply-filters" class="button">Apply Filters</button>
            </div>
        </div>
        
        <!-- Right Column with Tours -->
        <div class="partner-content">
            <!-- Tour Categories Grid -->
            <section class="tour-section">
                <h2>Tour Categories</h2>
                <div class="tour-categories-grid">
                    <?php
                    $categories = get_terms(array(
                        'taxonomy' => 'tour_category',
                        'hide_empty' => true
                    ));
                    
                    if ($categories && !is_wp_error($categories)) {
                        foreach ($categories as $category) {
                            ?>
                            <div class="category-card" tabindex="0">
                                <div class="category-content">
                                    <h3 class="category-title">
                                        <a href="<?php echo esc_url(get_term_link($category)); ?>">
                                            <?php echo esc_html($category->name); ?>
                                        </a>
                                    </h3>
                                    <p class="category-description"><?php echo esc_html($category->description); ?></p>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
            </section>
            
            <!-- Partner's Tours -->
            <section class="partner-tours">
                <h2>All Tours</h2>
                <div class="tour-list">
                    <?php if (have_posts()): ?>
                        <?php while (have_posts()): the_post(); ?>
                            <?php get_template_part('template-parts/tour-card'); ?>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="no-tours"><?php _e('No tours available at the moment.', 'tour-portal'); ?></p>
                    <?php endif; ?>
                    <?php wp_reset_postdata(); ?>
                </div>
                
                <!-- Pagination -->
                <div class="tour-pagination">
                    <?php
                    $args = array(
                        'mid_size' => 2,
                        'prev_text' => __('&laquo; Previous', 'tour-portal'),
                        'next_text' => __('Next &raquo;', 'tour-portal'),
                        'screen_reader_text' => __('Posts navigation', 'tour-portal'),
                        'type' => 'array',
                        'current' => max(1, get_query_var('paged')),
                    );
                    
                    $links = paginate_links($args);
                    
                    if (!empty($links)) {
                        echo '<nav class="navigation pagination" role="navigation">';
                        echo '<div class="nav-links">';
                        foreach ($links as $link) {
                            echo $link;
                        }
                        echo '</div>';
                        echo '</nav>';
                    }
                    ?>
                </div>
            </section>
        </div>
    </div>
    
    <!-- Company Info at Bottom -->
    <div class="partner-info-bottom">
        <div class="partner-info-content">
            <?php 
            // Get the single site owner user with role 'site_owner' in single operator mode
            $partner_args = array(
                'role' => 'site_owner',
                'number' => 1,
                'fields' => 'ID'
            );
            $partner_users = get_users($partner_args);
            $partner_id = !empty($partner_users) ? $partner_users[0] : null;
            $partner = get_user_by('id', $partner_id);
            
            $partner_logo = get_user_meta($partner_id, 'partner_logo', true);
            $partner_name = get_user_meta($partner_id, 'partner_name', true);
            $partner_description = get_user_meta($partner_id, 'partner_description', true);
            $partner_phone = get_user_meta($partner_id, 'partner_phone', true);
            $partner_website = get_user_meta($partner_id, 'partner_website', true);
            $partner_address = get_user_meta($partner_id, 'partner_address', true);
            
            if ($partner_logo) {
                echo '<img src="' . esc_url($partner_logo) . '" alt="' . esc_attr($partner_name ? $partner_name : $partner->display_name) . '" class="partner-featured-image">';
            } else {
                echo '<img src="' . get_template_directory_uri() . '/assets/images/default-partner-logo.png" alt="' . esc_attr($partner_name ? $partner_name : $partner->display_name) . '" class="partner-featured-image">';
            }
            
            echo '<h2 class="partner-name">' . esc_html($partner_name ? $partner_name : $partner->display_name) . '</h2>';
            echo '<p class="partner-description">' . esc_html($partner_description) . '</p>';
            
            echo '<div class="partner-contact-info">';
            
            if ($partner_address) {
                echo '<div class="partner-address">';
                echo '<i class="dashicons dashicons-location"></i>';
                echo esc_html($partner_address);
                echo '</div>';
            }
            
            if ($partner_phone) {
                echo '<div class="partner-phone">';
                echo '<i class="dashicons dashicons-phone"></i>';
                echo esc_html($partner_phone);
                echo '</div>';
            }
            
            if ($partner_website) {
                echo '<div class="partner-website">';
                echo '<a href="' . esc_url($partner_website) . '" target="_blank" rel="noopener">';
                echo esc_url($partner_website);
                echo '</a>';
                echo '</div>';
            }
            
            echo '</div>';
            ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
