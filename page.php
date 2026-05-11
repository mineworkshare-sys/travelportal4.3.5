<?php get_header(); ?>

<?php 
$hide_title = get_post_meta(get_the_ID(), '_tp_hide_title', true);
$sidebar_layout = get_post_meta(get_the_ID(), '_tp_sidebar_selection', true);

// Debug: Check the actual value
// Uncomment for debugging: error_log('Hide title value: ' . var_export($hide_title, true));

// Default to none if not set
if (empty($sidebar_layout)) { $sidebar_layout = 'none'; }
?>

<main class="container">
    <div class="page-layout <?php echo $sidebar_layout === 'none' ? 'no-sidebar' : 'with-sidebar'; ?>">
        <article class="page-content">
            <?php while (have_posts()): the_post(); ?>
                <?php if (!$hide_title || $hide_title !== '1' && $hide_title !== true): ?>
                    <header class="page-header">
                        <h1 class="page-title"><?php the_title(); ?></h1>
                    </header>
                <?php endif; ?>
                
                <div class="entry-content">
                    <?php the_content(); ?>
                    
                    <?php
                    // Special handling for specific pages
                    if (get_the_title() === __('Partner Registration', 'tour-portal')) {
                        echo do_shortcode('[tour_registration_form]');
                    } elseif (get_the_title() === __('All Tours', 'tour-portal')) {
                        echo do_shortcode('[tour_filter]');
                        echo do_shortcode('[tour_list limit="12"]');
                    } elseif (get_the_title() === __('Tour Categories', 'tour-portal')) {
                        echo do_shortcode('[tour_categories_grid]');
                    }
                    ?>
                </div>
                
                <?php
                wp_link_pages(array(
                    'before' => '<div class="page-links">' . __('Pages:', 'tour-portal'),
                    'after' => '</div>',
                ));
                ?>
            <?php endwhile; ?>
        </article>

        <?php if ($sidebar_layout !== 'none'): ?>
            <aside class="page-sidebar">
                <?php dynamic_sidebar($sidebar_layout); ?>
            </aside>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>