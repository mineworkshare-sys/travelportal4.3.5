<?php 
/*
Template Name: Company Landing Page
*/

get_header();

// Check if page title should be hidden
$hide_title = get_post_meta(get_the_ID(), '_tp_hide_title', true);

// Get partner info from URL
$partner = null;
$company_slug = '';

// Try to get partner from URL path
$request_uri = $_SERVER['REQUEST_URI'];
if (strpos($request_uri, '/company/') !== false) {
    $parts = explode('/company/', $request_uri);
    if (isset($parts[1])) {
        $company_slug = rtrim($parts[1], '/');
        
        // Debug: Log the slug we're looking for
        error_log("Looking for partner with slug: " . $company_slug);
        
        // Try to find partner by user_login (username) first - this is most reliable
        $partner = get_user_by('login', $company_slug);
        
        if (!$partner) {
            // Try by slug
            $partner = get_user_by('slug', $company_slug);
        }
        
        if (!$partner) {
            // Search by partner_name meta field
            $users = get_users(array(
                'meta_key' => 'partner_name',
                'meta_value' => $company_slug,
                'meta_compare' => '=',
                'role' => 'partner'
            ));
            if (!empty($users)) {
                $partner = $users[0];
            }
        }
        
        // Debug: Log if partner found
        if ($partner) {
            error_log("Found partner: " . $partner->user_login . " (ID: " . $partner->ID . ")");
        } else {
            error_log("Partner not found for slug: " . $company_slug);
        }
    }
}

$valid_roles = array('partner', 'site_owner', 'administrator');
$has_valid_role = $partner && !empty(array_intersect($valid_roles, (array) $partner->roles));

if (!$has_valid_role) {
    echo '<div class="container"><h1>' . __('Company Not Found', 'tour-portal') . '</h1></div>';
    get_footer();
    return;
}

$partner_id = $partner->ID;
$partner_name = get_user_meta($partner_id, 'partner_name', true) ?: $partner->display_name;
$partner_info = get_user_meta($partner_id, 'partner_address', true);
$partner_phone = get_user_meta($partner_id, 'partner_phone', true);
$partner_email = $partner->user_email;
$partner_logo = get_user_meta($partner_id, 'partner_logo', true);
$partner_website = get_user_meta($partner_id, 'partner_website', true);
$partner_description = get_user_meta($partner_id, 'partner_description', true);
?>

<div class="container">
    <div class="partner-layout">
        <!-- Left Column with Search -->
        <div class="partner-sidebar">
            <!-- Search Box -->
            <div class="partner-search">
                <h3><?php _e('Search Tours', 'tour-portal'); ?></h3>
                <form class="tour-search-form" method="get" action="<?php echo home_url('/'); ?>">
                    <input type="hidden" name="partner" value="<?php echo esc_attr($partner_id); ?>">
                    <input type="text" name="s" placeholder="<?php _e('Search tours...', 'tour-portal'); ?>" class="search-input">
                    <button type="submit" class="search-button"><?php _e('Search', 'tour-portal'); ?></button>
                </form>
            </div>
            
            <!-- Tour Filter -->
            <?php echo do_shortcode('[tour_filter partner="' . $partner_id . '"]'); ?>
        </div>
        
        <!-- Right Column with Tours -->
        <div class="partner-content">
            <!-- Tour Categories Grid -->
            <section class="tour-section">
                <h2><?php _e('Tour Categories', 'tour-portal'); ?></h2>
                <?php 
                // Get categories for this partner's tours
                $partner_tours = get_posts(array(
                    'post_type' => 'tour',
                    'author' => $partner_id,
                    'posts_per_page' => -1,
                    'fields' => 'ids'
                ));
                
                if (!empty($partner_tours)) {
                    $categories = wp_get_object_terms($partner_tours, 'tour_category');
                    if (!empty($categories)) {
                        echo '<div class="tour-categories-grid">';
                        foreach ($categories as $category) {
                            echo '<div class="category-card">';
                            echo '<div class="category-content">';
                            echo '<h3 class="category-title">';
                            echo '<a href="' . esc_url(add_query_arg('partner', $partner_id, get_term_link($category))) . '">';
                            echo esc_html($category->name);
                            echo '</a>';
                            echo '</h3>';
                            echo '<p class="category-description">' . esc_html($category->description) . '</p>';
                            echo '</div>';
                            echo '</div>';
                        }
                        echo '</div>';
                    }
                }
                ?>
            </section>
            
            <!-- Partner's Tours -->
            <section class="partner-tours">
                <h2><?php _e('All Tours', 'tour-portal'); ?></h2>
                <?php 
                // Get tours with pagination
                $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                $args = array(
                    'post_type' => 'tour',
                    'author' => $partner_id,
                    'posts_per_page' => 10,
                    'paged' => $paged,
                    'post_status' => 'publish'
                );
                $tours_query = new WP_Query($args);
                
                if ($tours_query->have_posts()) : ?>
                    <div class="tour-list">
                        <?php while ($tours_query->have_posts()) : $tours_query->the_post(); ?>
                            <div class="tour-card">
                                <div class="tour-image">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <img src="<?php echo get_the_post_thumbnail_url(null, 'large'); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                                    <?php else : ?>
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/default-tour.jpg" alt="<?php echo esc_attr(get_the_title()); ?>">
                                    <?php endif; ?>
                                </div>
                                <div class="tour-content">
                                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                    <p>
                                        <?php 
                                        $excerpt = get_the_excerpt();
                                        if ($excerpt) {
                                            echo esc_html(substr($excerpt, 0, 200));
                                            if (strlen($excerpt) > 200) echo '...';
                                        } else {
                                            $content = get_the_content();
                                            echo esc_html(substr($content, 0, 200));
                                            if (strlen($content) > 200) echo '...';
                                        }
                                        ?>
                                    </p>
                                    <div class="tour-details">
                                        <div class="detail-block">
                                            <h4>Duration</h4>
                                            <?php
                                            $duration_hours = get_post_meta(get_the_ID(), '_duration_hours', true);
                                            echo $duration_hours ? esc_html($duration_hours) . ' hours' : '2 hours';
                                            ?>
                                            <h4>Languages</h4>
                                            <ul>
                                                <?php
                                                $languages = get_post_meta(get_the_ID(), '_languages', true);
                                                if ($languages && is_array($languages)) {
                                                    foreach ($languages as $lang) {
                                                        echo '<li>' . esc_html(ucfirst($lang)) . '</li>';
                                                    }
                                                } else {
                                                    echo '<li>English</li>';
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                        <div class="detail-block">
                                            <h4>Prices</h4>
                                            <p>
                                                <?php
                                                $german_price  = get_post_meta(get_the_ID(), '_german_price', true);
                                                $foreign_price = get_post_meta(get_the_ID(), '_foreign_price', true);
                                                $charging_model = get_post_meta(get_the_ID(), '_charging_model', true);
                                                if ($german_price || $foreign_price) {
                                                    echo $charging_model === 'per_group' ? 'Price per group: from ' : 'Price per person: from ';
                                                    echo '€' . number_format($foreign_price ?: $german_price, 2);
                                                } else {
                                                    echo 'Contact for pricing';
                                                }
                                                ?>
                                            </p>
                                            <div class="tour-button">
                                                <a href="<?php the_permalink(); ?>" class="btn-booking">Details &amp; booking</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="tour-pagination">
                        <?php
                        $total_pages = $tours_query->max_num_pages;
                        if ($total_pages > 1) :
                            $current_page = max(1, $paged);
                            
                            echo '<div class="pagination-links">';
                            
                            // Previous page
                            if ($current_page > 1) {
                                echo '<a href="' . esc_url(get_pagenum_link($current_page - 1)) . '" class="prev-page">&laquo; Previous</a>';
                            }
                            
                            // Page numbers
                            for ($i = 1; $i <= $total_pages; $i++) {
                                if ($i == $current_page) {
                                    echo '<span class="current-page">' . $i . '</span>';
                                } else {
                                    echo '<a href="' . esc_url(get_pagenum_link($i)) . '" class="page-number">' . $i . '</a>';
                                }
                            }
                            
                            // Next page
                            if ($current_page < $total_pages) {
                                echo '<a href="' . esc_url(get_pagenum_link($current_page + 1)) . '" class="next-page">Next &raquo;</a>';
                            }
                            
                            echo '</div>';
                        endif;
                        ?>
                    </div>
                    
                <?php else : ?>
                    <p class="no-tours"><?php _e('No tours available at the moment.', 'tour-portal'); ?></p>
                <?php endif; ?>
                
                <?php wp_reset_postdata(); ?>
            </section>
        </div>
    </div>
    
    <!-- Company Info at Bottom -->
    <div class="partner-info-bottom">
        <div class="partner-info-content">
            <?php if ($partner_logo): ?>
                <img src="<?php echo esc_url($partner_logo); ?>" alt="<?php echo esc_attr($partner_name); ?>" class="partner-featured-image">
            <?php endif; ?>
            
            <h2 class="partner-name"><?php echo esc_html($partner_name); ?></h2>
            <p class="partner-description"><?php echo esc_html($partner_description); ?></p>
            
            <div class="partner-contact-info">
                <?php if ($partner_info): ?>
                    <div class="partner-address">
                        <i class="dashicons dashicons-location"></i>
                        <?php echo esc_html($partner_info); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($partner_phone): ?>
                    <div class="partner-phone">
                        <i class="dashicons dashicons-phone"></i>
                        <?php echo esc_html($partner_phone); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($partner_website): ?>
                    <div class="partner-website">
                        <a href="<?php echo esc_url($partner_website); ?>" target="_blank" rel="noopener">
                            <?php echo esc_url($partner_website); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.partner-layout {
    display: grid;
    grid-template-columns: 30% 70%;
    gap: 30px;
    margin-top: 30px;
    clear: both;
}

.partner-layout::after {
    content: "";
    display: table;
    clear: both;
}

.partner-sidebar {
    background: #f8f9fa;
    padding: 25px;
    border-radius: 12px;
    height: fit-content;
}

.partner-search {
    margin-bottom: 20px;
}

.partner-search h3 {
    margin-bottom: 15px;
    color: var(--tp-primary-color);
}

.tour-search-form {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.search-input {
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
}

.search-button {
    background: var(--tp-primary-color);
    color: white;
    border: none;
    padding: 12px;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.search-button:hover {
    background: var(--tp-accent-color);
}

.partner-content {
    background: white;
    position: relative;
    overflow: hidden;
}

.tour-section {
    margin-bottom: 40px;
}

.tour-section h2 {
    margin-bottom: 20px;
    color: #333;
}

.tour-categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.category-card {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #e0e0e0;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.category-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.category-title {
    margin-bottom: 10px;
}

.category-title a {
    color: var(--tp-primary-color);
    text-decoration: none;
    font-weight: bold;
}

.category-title a:hover {
    text-decoration: underline;
}

.category-description {
    color: #666;
    font-size: 14px;
    line-height: 1.5;
}

/* Simple Tour Cards */
.tour-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

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

.tp-media-image {
    position: relative;
    overflow: hidden;
    border-radius: 8px;
    width: 100%;
    aspect-ratio: 4/3;
}

.tp-media-image img {
    width: 100%;
    height: 100%;
    min-height: 250px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.tp-media-image:hover img {
    transform: scale(1.05);
}

.tp-media-image figure {
    margin: 0;
    padding: 0;
}

.tp-media-text {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.tp-box-headline {
    margin: 0;
    font-size: 22px;
    font-weight: 600;
    line-height: 1.3;
    color: #333;
}

.tp-box-headline a {
    color: inherit;
    text-decoration: none;
    transition: color 0.3s ease;
}

.tp-box-headline a:hover {
    color: var(--tp-primary-color);
}

.tp-media-description {
    color: #666;
    font-size: 15px;
    line-height: 1.6;
    margin: 0;
}

.tp-media-detail {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-top: 10px;
}

.tp-media-detail-block {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.tp-box-subheadline {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
    color: #333;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.tp-media-detail-block p {
    margin: 0;
    color: #666;
    font-size: 14px;
    line-height: 1.5;
}

.tp-media-detail-block ul {
    margin: 0;
    padding: 0;
    list-style: none;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.tp-media-detail-block li {
    background: #f8f9fa;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 13px;
    color: #333;
    border: 1px solid #e0e0e0;
}

.pure-button.tp-btn-details.tp-btn-booking {
    background: var(--tp-primary-color);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    display: inline-block;
    text-align: center;
    transition: background-color 0.3s ease, transform 0.3s ease;
    cursor: pointer;
    margin-top: 20px;
    align-self: flex-start;
}

.pure-button.tp-btn-details.tp-btn-booking:hover {
    background: var(--tp-accent-color);
    transform: translateY(-1px);
    color: white;
    text-decoration: none;
}

.tp-objectfit {
    object-fit: cover;
    font-family: 'object-fit: cover;';
}

.tp-headline-secondary {
    font-size: 22px;
    font-weight: 600;
    color: #333;
}

.tp-headline-quaternary {
    font-size: 14px;
    font-weight: 600;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Pagination */
.tour-pagination {
    margin-top: 30px;
    text-align: center;
}

.pagination-links {
    display: flex;
    justify-content: center;
    gap: 10px;
    align-items: center;
}

.pagination-links a,
.pagination-links span {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    text-decoration: none;
    color: #666;
}

.pagination-links a:hover {
    background: var(--tp-primary-color);
    color: white;
    border-color: var(--tp-primary-color);
}

.pagination-links .current-page {
    background: var(--tp-primary-color);
    color: white;
    border-color: var(--tp-primary-color);
}

.pagination-links .prev-page,
.pagination-links .next-page {
    background: #f8f9fa;
}

.pagination-links .prev-page:hover,
.pagination-links .next-page:hover {
    background: var(--tp-primary-color);
    color: white;
}

/* Company Info at Bottom */
.partner-info-bottom {
    background: #f8f9fa;
    padding: 40px;
    border-radius: 12px;
    margin-top: 40px;
    text-align: center;
}

.partner-info-content {
    max-width: 800px;
    margin: 0 auto;
}

.partner-info-bottom .partner-featured-image {
    width: 150px;
    height: auto;
    margin-bottom: 20px;
    border-radius: 8px;
}

.partner-info-bottom .partner-name {
    font-size: 28px;
    font-weight: bold;
    margin-bottom: 15px;
    color: var(--tp-primary-color);
}

.partner-info-bottom .partner-description {
    color: #666;
    line-height: 1.6;
    margin-bottom: 25px;
    font-size: 16px;
}

.partner-contact-info {
    display: flex;
    justify-content: center;
    gap: 30px;
    flex-wrap: wrap;
}

.partner-contact-info .partner-address,
.partner-contact-info .partner-phone {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #666;
}

.partner-contact-info .partner-address .dashicons,
.partner-contact-info .partner-phone .dashicons {
    color: var(--tp-primary-color);
}

.partner-contact-info .partner-website a {
    color: var(--tp-primary-color);
    text-decoration: none;
}

.partner-contact-info .partner-website a:hover {
    text-decoration: underline;
}

.no-tours {
    text-align: center;
    color: #666;
    font-size: 18px;
    margin-top: 30px;
}

/* Mobile Responsive */
@media (max-width: 1200px) {
    .tp-results-item {
        grid-template-columns: 35% 65%;
        gap: 20px;
    }
    
    .tp-media-image img {
        height: 220px;
    }
}

@media (max-width: 1024px) {
    .tp-results-item {
        grid-template-columns: 40% 60%;
        gap: 15px;
    }
    
    .tp-media-image img {
        height: 200px;
    }
    
    .tp-box-headline {
        font-size: 20px;
    }
    
    .tp-media-detail {
        grid-template-columns: 1fr;
        gap: 20px;
    }
}

@media (max-width: 768px) {
    .partner-layout {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .tp-results-item {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .tp-media-image img {
        height: 200px;
    }
    
    .tp-box-headline {
        font-size: 18px;
    }
    
    .tp-media-description {
        font-size: 14px;
    }
    
    .tp-media-detail {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .tp-box-subheadline {
        font-size: 13px;
    }
    
    .partner-info-bottom {
        padding: 30px 20px;
    }
    
    .partner-contact-info {
        flex-direction: column;
        gap: 15px;
        align-items: center;
    }
    
    .pagination-links {
        flex-wrap: wrap;
        gap: 5px;
    }
    
    .pagination-links a,
    .pagination-links span {
        padding: 6px 10px;
        font-size: 14px;
    }
}

@media (max-width: 480px) {
    .partner-sidebar {
        padding: 20px;
    }
    
    .tp-box-frame {
        padding: 15px;
    }
    
    .tp-media-image img {
        height: 180px;
    }
    
    .tp-box-headline {
        font-size: 16px;
    }
    
    .tp-media-description {
        font-size: 13px;
    }
    
    .tp-media-detail-block {
        gap: 6px;
    }
    
    .tp-box-subheadline {
        font-size: 12px;
    }
    
    .tp-media-detail-block p {
        font-size: 13px;
    }
    
    .tp-media-detail-block li {
        font-size: 12px;
        padding: 3px 6px;
    }
    
    .pure-button.tp-btn-details.tp-btn-booking {
        padding: 10px 20px;
        font-size: 13px;
    }
    
    .partner-info-bottom .partner-name {
        font-size: 24px;
    }
    
    .partner-info-bottom .partner-description {
        font-size: 14px;
    }
}
</style>

<?php get_footer(); ?>