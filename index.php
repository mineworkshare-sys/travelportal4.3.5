<?php get_header(); ?>

<?php 
// Check if page title should be hidden
$hide_title = get_post_meta(get_the_ID(), '_tp_hide_title', true);
?>

<div class="container">
    <?php if (is_front_page()): ?>
        <?php if (!$hide_title || $hide_title !== '1' && $hide_title !== true): ?>
            <header class="page-header">
                <h1 class="page-title"><?php the_title(); ?></h1>
            </header>
        <?php endif; ?>
        <?php
        // Check if we're in multi-operator mode
        if (function_exists('tour_portal_is_multi_operator') && tour_portal_is_multi_operator()) {
            // Multi-operator mode - show partners instead of tours
            $partners = get_users(array(
                'role' => 'partner',
                'meta_query' => array(
                    array(
                        'key' => 'partner_approved',
                        'value' => '1',
                        'compare' => '='
                    )
                ),
                'orderby' => 'display_name',
                'order' => 'ASC'
            ));
            ?>
            
            <!-- Partners Section -->
            <section class="partners-section">
                <h2><?php _e('Our Tour Partners', 'tour-portal'); ?></h2>
                <?php if (!empty($partners)): ?>
                    <div class="partners-grid">
                        <?php foreach ($partners as $partner): ?>
                            <?php
                            $partner_id = $partner->ID;
                            $partner_name = get_user_meta($partner_id, 'partner_name', true) ?: $partner->display_name;
                            $partner_logo = get_user_meta($partner_id, 'partner_logo', true);
                            $partner_description = get_user_meta($partner_id, 'partner_description', true);
                            $partner_address = get_user_meta($partner_id, 'partner_address', true);
                            $partner_website = get_user_meta($partner_id, 'partner_website', true);
                            $company_slug = sanitize_title($partner_name);
                            $tour_count = count_user_posts($partner_id, 'tour');
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
                                    
                                    <div class="partner-stats">
                                        <span class="tour-count"><?php echo $tour_count; ?> <?php _e('Tours', 'tour-portal'); ?></span>
                                    </div>
                                    
                                    <div class="partner-actions">
                                        <a href="<?php echo home_url('/company/' . $company_slug . '/'); ?>" class="button">
                                            <?php _e('View Tours', 'tour-portal'); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="no-partners"><?php _e('No tour partners available at the moment.', 'tour-portal'); ?></p>
                <?php endif; ?>
            </section>
            
            <style>
            .partners-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
                gap: 30px;
                margin-top: 30px;
            }
            
            .partner-card {
                background: #fff;
                border: 1px solid #e0e0e0;
                border-radius: 12px;
                padding: 25px;
                text-align: center;
                transition: transform 0.3s ease, box-shadow 0.3s ease;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            }
            
            .partner-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            }
            
            .partner-logo img {
                width: 120px;
                height: 120px;
                object-fit: cover;
                border-radius: 50%;
                margin-bottom: 20px;
                border: 4px solid var(--tp-primary-color);
            }
            
            .partner-placeholder-logo {
                width: 120px;
                height: 120px;
                background: var(--tp-primary-color);
                color: white;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                margin: 0 auto 20px;
                font-size: 32px;
                font-weight: bold;
                border: 4px solid var(--tp-primary-color);
            }
            
            .partner-name {
                margin-bottom: 15px;
            }
            
            .partner-name a {
                color: var(--tp-primary-color);
                text-decoration: none;
                font-size: 20px;
                font-weight: bold;
            }
            
            .partner-name a:hover {
                text-decoration: underline;
            }
            
            .partner-description {
                color: #666;
                margin-bottom: 15px;
                min-height: 50px;
                line-height: 1.5;
            }
            
            .partner-address {
                color: #666;
                font-size: 14px;
                margin-bottom: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }
            
            .partner-address .dashicons {
                color: var(--tp-primary-color);
            }
            
            .partner-website a {
                color: var(--tp-primary-color);
                font-size: 14px;
                text-decoration: none;
            }
            
            .partner-website a:hover {
                text-decoration: underline;
            }
            
            .partner-stats {
                margin: 15px 0;
                padding: 10px;
                background: #f8f9fa;
                border-radius: 6px;
            }
            
            .tour-count {
                color: var(--tp-primary-color);
                font-weight: bold;
            }
            
            .partner-actions {
                margin-top: 20px;
            }
            
            .partner-actions .button {
                background: var(--tp-primary-color);
                color: white;
                text-decoration: none;
                padding: 12px 24px;
                border-radius: 6px;
                display: inline-block;
                transition: background-color 0.3s ease;
            }
            
            .partner-actions .button:hover {
                background: var(--tp-accent-color);
                color: white;
                text-decoration: none;
            }
            
            .no-partners {
                text-align: center;
                color: #666;
                font-size: 18px;
                margin-top: 50px;
            }
            
            @media (max-width: 768px) {
                .partners-grid {
                    grid-template-columns: 1fr;
                }
                
                .partner-card {
                    padding: 20px;
                }
                
                .partner-logo img,
                .partner-placeholder-logo {
                    width: 100px;
                    height: 100px;
                    font-size: 24px;
                }
            }
            </style>
            
            <?php
        } else {
            // Single-operator mode - show tour categories
            ?>
            
            <!-- Front Page - Tour Categories Grid -->
            <section id="tours" class="tour-section">
                <h2><?php _e('Explore Tour Categories', 'tour-portal'); ?></h2>
                <?php echo do_shortcode('[tour_categories_grid]'); ?>
            </section>
            
            <section class="featured-tours">
                <h2><?php _e('Featured Tours', 'tour-portal'); ?></h2>
                <?php echo do_shortcode('[tour_list limit="6"]'); ?>
            </section>
            <?php
        }
        ?>
        
    <?php else: ?>
        <!-- Company/Partner Landing Page -->
        <header class="partner-header">
            <?php 
            // Get partner info from URL or current author
            $partner_id = get_queried_object_id();
            if (is_author()) {
                $partner_id = get_the_author_meta('ID');
            }
            
            $partner_name = get_user_meta($partner_id, 'partner_name', true);
            $partner_info = get_user_meta($partner_id, 'partner_address', true);
            $partner_phone = get_user_meta($partner_id, 'partner_phone', true);
            ?>
            
            <div class="partner-info">
                <h1><?php echo esc_html($partner_name ? $partner_name : get_the_author_meta('display_name')); ?></h1>
                <?php if ($partner_info): ?>
                    <p class="partner-description"><?php echo esc_html($partner_info); ?></p>
                <?php endif; ?>
                <?php if ($partner_phone): ?>
                    <p class="partner-contact"><?php _e('Contact:', 'tour-portal'); ?> <?php echo esc_html($partner_phone); ?></p>
                <?php endif; ?>
            </div>
        </header>

        <!-- Tour Filter -->
        <?php echo do_shortcode('[tour_filter]'); ?>
        
        <!-- Tour Categories Grid -->
        <section class="tour-section">
            <h2><?php _e('Available Tour Categories', 'tour-portal'); ?></h2>
            <?php 
            // Filter categories by this partner's tours
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
                        $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
                        $image = wp_get_attachment_image_url($thumbnail_id, 'medium');
                        ?>
                        <div class="category-card">
                            <?php if ($image): ?>
                                <div class="category-image">
                                    <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($category->name); ?>" />
                                </div>
                            <?php endif; ?>
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
                    echo '</div>';
                }
            }
            ?>
        </section>
        
        <?php endif; ?>
</div>

<?php get_footer(); ?>