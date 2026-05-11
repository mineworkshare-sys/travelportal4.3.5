<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header class="site-header">
    <div class="container">
        <div class="header-content">
            <div class="site-branding">
                <?php 
                $logo_or_text = get_theme_mod('tour_portal_logo_or_text', 'logo');
                if ($logo_or_text === 'logo' && has_custom_logo()) {
                    the_custom_logo();
                } else {
                    echo '<h1 class="site-title"><a href="' . esc_url(home_url('/')) . '">' . get_bloginfo('name') . '</a></h1>';
                }
                ?>
            </div>
            
            <nav class="main-navigation">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_class' => 'primary-menu',
                    'container' => false,
                    'fallback_cb' => 'tour_portal_fallback_menu'
                ));
                ?>
                
                <?php
                // Add simple Polylang language switcher to header
                tour_portal_simple_language_switcher();
                ?>
            </nav>
        </div>
    </div>
</header>

<?php if (is_front_page()): ?>
<section class="hero-section">
    <div class="container">
        <h1 class="hero-title"><?php echo get_theme_mod('hero_title', __('Discover Amazing Tours', 'tour-portal')); ?></h1>
        <p class="hero-subtitle"><?php echo get_theme_mod('hero_subtitle', __('Book unforgettable experiences with local guides', 'tour-portal')); ?></p>
        <a href="<?php echo esc_url(get_theme_mod('hero_button_link', '#tours')); ?>" class="cta-button"><?php echo get_theme_mod('hero_button_text', __('Explore Tours', 'tour-portal')); ?></a>
    </div>
</section>
<?php endif; ?>

<?php
function tour_portal_fallback_menu() {
    echo '<ul>';
    echo '<li><a href="' . esc_url(home_url('/')) . '">' . __('Home', 'tour-portal') . '</a></li>';
    
    // Only show Tours link if not in multi-operator mode
    if (post_type_exists('tour') && (!function_exists('tour_portal_is_multi_operator') || !tour_portal_is_multi_operator())) {
        echo '<li><a href="' . esc_url(get_post_type_archive_link('tour')) . '">' . __('Tours', 'tour-portal') . '</a></li>';
    }
    
    // Use WP_Query for Contact page instead of deprecated get_page_by_title
    $contact_query = new WP_Query(array(
        'post_type' => 'page',
        'title' => __('Contact', 'tour-portal'),
        'posts_per_page' => 1
    ));
    if ($contact_query->have_posts()) {
        $contact_query->the_post();
        echo '<li><a href="' . esc_url(get_permalink()) . '">' . __('Contact', 'tour-portal') . '</a></li>';
        wp_reset_postdata();
    } else {
        echo '<li><a href="/contact/">' . __('Contact', 'tour-portal') . '</a></li>';
    }
    
    echo '</ul>';
}
?>