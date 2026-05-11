<footer class="site-footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h3><?php echo get_bloginfo('name'); ?></h3>
                <p><?php echo get_bloginfo('description'); ?></p>
                <?php 
                // Get admin user biographical info (user ID 1)
                $admin_user = get_user_by('id', 1);
                if ($admin_user && !empty($admin_user->description)) {
                    echo '<p>' . esc_html($admin_user->description) . '</p>';
                }
                ?>
                <?php if (has_nav_menu('footer')): ?>
                    <?php wp_nav_menu(array(
                        'theme_location' => 'footer',
                        'menu_class' => 'footer-menu',
                        'container' => false,
                        'depth' => 1
                    )); ?>
                <?php endif; ?>
            </div>
            
            <div class="footer-section">
                <h3><?php _e('Quick Links', 'tour-portal'); ?></h3>
                <ul>
                    <?php if (!function_exists('tour_portal_is_multi_operator') || !tour_portal_is_multi_operator()): ?>
                        <li><a href="<?php echo esc_url(get_post_type_archive_link('tour')); ?>"><?php _e('All Tours', 'tour-portal'); ?></a></li>
                    <?php endif; ?>
                    <?php 
                    // Use WP_Query instead of deprecated get_page_by_title
                    $about_query = new WP_Query(array(
                        'post_type' => 'page',
                        'title' => __('About', 'tour-portal'),
                        'posts_per_page' => 1
                    ));
                    if ($about_query->have_posts()): ?>
                        <?php $about_query->the_post(); ?>
                        <li><a href="<?php echo esc_url(get_permalink()); ?>"><?php _e('About Us', 'tour-portal'); ?></a></li>
                        <?php wp_reset_postdata(); ?>
                    <?php else: ?>
                        <li><a href="/about/"><?php _e('About Us', 'tour-portal'); ?></a></li>
                    <?php endif; ?>
                    <?php 
                    $contact_query = new WP_Query(array(
                        'post_type' => 'page',
                        'title' => __('Contact', 'tour-portal'),
                        'posts_per_page' => 1
                    ));
                    if ($contact_query->have_posts()): ?>
                        <?php $contact_query->the_post(); ?>
                        <li><a href="<?php echo esc_url(get_permalink()); ?>"><?php _e('Contact', 'tour-portal'); ?></a></li>
                        <?php wp_reset_postdata(); ?>
                    <?php else: ?>
                        <li><a href="/contact/"><?php _e('Contact', 'tour-portal'); ?></a></li>
                    <?php endif; ?>
                    <?php 
                    $partner_query = new WP_Query(array(
                        'post_type' => 'page',
                        'title' => __('Partner Registration', 'tour-portal'),
                        'posts_per_page' => 1
                    ));
                    if ($partner_query->have_posts()): ?>
                        <?php $partner_query->the_post(); ?>
                        <li><a href="<?php echo esc_url(get_permalink()); ?>"><?php _e('Become a Partner', 'tour-portal'); ?></a></li>
                        <?php wp_reset_postdata(); ?>
                    <?php else: ?>
                        <li><a href="/partner-registration/"><?php _e('Become a Partner', 'tour-portal'); ?></a></li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <div class="footer-section">
                                
                <div class="social-links">
                    <h4><?php _e('Follow Us', 'tour-portal'); ?></h4>
                    <?php if (get_theme_mod('social_facebook')): ?>
                        <a href="<?php echo esc_url(get_theme_mod('social_facebook')); ?>" target="_blank" rel="noopener">Facebook</a>
                    <?php endif; ?>
                    <?php if (get_theme_mod('social_twitter')): ?>
                        <a href="<?php echo esc_url(get_theme_mod('social_twitter')); ?>" target="_blank" rel="noopener">Twitter</a>
                    <?php endif; ?>
                    <?php if (get_theme_mod('social_instagram')): ?>
                        <a href="<?php echo esc_url(get_theme_mod('social_instagram')); ?>" target="_blank" rel="noopener">Instagram</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p><?php echo get_theme_mod('footer_text', '© ' . date('Y') . ' ' . get_bloginfo('name') . '. All rights reserved.'); ?></p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>