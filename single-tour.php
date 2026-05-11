<?php 
get_header();

// Get tour meta data
$tour_id = get_the_ID();
$partner_id = get_post_field('post_author', $tour_id);
$partner_name = get_user_meta($partner_id, 'partner_name', true) ?: get_the_author_meta('display_name');

// Tour details
$tour_type = get_post_meta($tour_id, '_tour_type', true);
$charging_model = get_post_meta($tour_id, '_charging_model', true);
$german_price = get_post_meta($tour_id, '_german_price', true);
$foreign_price = get_post_meta($tour_id, '_foreign_price', true);
$max_people = get_post_meta($tour_id, '_max_people', true);
$duration_hours = get_post_meta($tour_id, '_duration_hours', true);
$languages = get_post_meta($tour_id, '_languages', true);

// Participants & conditions
$min_per_group = get_post_meta($tour_id, '_min_per_group', true);
$max_per_group = get_post_meta($tour_id, '_max_per_group', true);
$additional_fees = get_post_meta($tour_id, '_additional_fees', true);
$wheelchair_accessible = get_post_meta($tour_id, '_wheelchair_accessible', true);
$booking_restraint = get_post_meta($tour_id, '_booking_restraint', true);
$cancellation_weeks = get_post_meta($tour_id, '_cancellation_weeks', true);
$refund_percent = get_post_meta($tour_id, '_refund_percent', true);
$booking_exceptions = get_post_meta($tour_id, '_booking_exceptions', true);

// Provider info
$provider_name = get_post_meta($tour_id, '_provider_name', true);
$provider_address = get_post_meta($tour_id, '_provider_address', true);
$provider_tel = get_post_meta($tour_id, '_provider_tel', true);
$provider_email = get_post_meta($tour_id, '_provider_email', true);

// TicketingHub - get widget ID from script field, fallback to widget ID field
$ticketinghub_script = get_post_meta($tour_id, '_ticketinghub_widget_script', true);
$ticketinghub_widget_id = get_post_meta($tour_id, '_ticketinghub_widget_id', true);

// Extract widget ID from script if script exists, otherwise use widget ID field
if (!empty($ticketinghub_script)) {
    // Look for widgetId in the script (old format)
    if (preg_match('/widgetId[\'"]\s*:\s*[\'"]([^\'"]+)[\'"]/', $ticketinghub_script, $matches)) {
        $ticketinghub_widget_id = $matches[1];
    }
    // Look for data-widget in the script (new format)
    elseif (preg_match('/data-widget[\'"]\s*=\s*[\'"]([^\'"]+)[\'"]/', $ticketinghub_script, $matches)) {
        $ticketinghub_widget_id = $matches[1];
    }
}

// Language names
$language_names = array(
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
?>

<div class="container">
    <div class="tour-details">
        <!-- Left Column - Main Content -->
        <div class="tour-main-content">
            <!-- Tour Image -->
            <?php if (has_post_thumbnail()): ?>
                <div class="tour-featured-image">
                    <?php the_post_thumbnail('large', array('class' => 'responsive-image')); ?>
                </div>
            <?php endif; ?>
            
            <!-- Tour Title -->
            <h1 class="tour-title"><?php the_title(); ?></h1>
            
            <!-- Basic Tour Info -->
            <div class="tour-basic-info">
                <div class="info-item">
                    <i class="dashicons dashicons-clock"></i>
                    <span class="label">Duration:</span>
                    <span class="value">
                        <?php 
                        if ($duration_hours) {
                            echo esc_html($duration_hours) . ' hours';
                        } else {
                            echo '2 hours';
                        }
                        ?>
                    </span>
                </div>
                
                <div class="info-item">
                    <i class="dashicons dashicons-translation"></i>
                    <span class="label">Languages:</span>
                    <span class="value">
                        <?php
                        if (!empty($languages) && is_array($languages)) {
                            $lang_names = array();
                            foreach ($languages as $lang) {
                                $lang_names[] = isset($language_names[$lang]) ? $language_names[$lang] : ucfirst($lang);
                            }
                            echo implode(', ', $lang_names);
                        } else {
                            echo 'English';
                        }
                        ?>
                    </span>
                </div>
            </div>
            
            <!-- Full Description -->
            <div class="tour-description">
                <h2>Tour Description</h2>
                <div class="description-content">
                    <?php the_content(); ?>
                </div>
            </div>
            
            <!-- Additional Information -->
            <div class="tour-additional-info">
                <h2>Additional Information</h2>
                
                <?php if ($min_per_group || $max_per_group): ?>
                    <div class="info-section">
                        <h3>Group Size</h3>
                        <p>
                            <?php
                            if ($min_per_group && $max_per_group) {
                                echo esc_html($min_per_group) . ' - ' . esc_html($max_per_group) . ' people';
                            } elseif ($min_per_group) {
                                echo 'Minimum ' . esc_html($min_per_group) . ' people';
                            } elseif ($max_per_group) {
                                echo 'Maximum ' . esc_html($max_per_group) . ' people';
                            }
                            ?>
                        </p>
                    </div>
                <?php endif; ?>
                
                <?php if ($wheelchair_accessible): ?>
                    <div class="info-section">
                        <h3>Accessibility</h3>
                        <p>This tour is wheelchair accessible.</p>
                    </div>
                <?php endif; ?>
                
                <?php if ($additional_fees): ?>
                    <div class="info-section">
                        <h3>Additional Fees</h3>
                        <p><?php echo esc_html($additional_fees); ?></p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Cancellation Policy -->
            <?php if ($cancellation_weeks || $refund_percent): ?>
                <div class="tour-cancellation">
                    <h2>Cancellation Policy</h2>
                    <p>
                        <?php
                        if ($cancellation_weeks && $refund_percent) {
                            echo 'Cancel up to ' . esc_html($cancellation_weeks) . ' weeks before for a ' . esc_html($refund_percent) . '% refund.';
                        } elseif ($cancellation_weeks) {
                            echo 'Cancel up to ' . esc_html($cancellation_weeks) . ' weeks before for a full refund.';
                        } elseif ($refund_percent) {
                            echo 'Get a ' . esc_html($refund_percent) . '% refund with advance cancellation.';
                        }
                        ?>
                    </p>
                </div>
            <?php endif; ?>
            
            <!-- Booking Exceptions -->
            <?php if ($booking_exceptions): ?>
                <div class="tour-exceptions">
                    <h2>Booking Exceptions</h2>
                    <p><?php echo esc_html($booking_exceptions); ?></p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Right Column - Sidebar -->
        <div class="tour-sidebar">
            <!-- Price Box -->
            <div class="price-box">
                <div class="price">
                    <?php
                    if ($foreign_price) {
                        echo '€' . number_format($foreign_price, 2);
                    } elseif ($german_price) {
                        echo '€' . number_format($german_price, 2);
                    } else {
                        echo 'Contact for pricing';
                    }
                    ?>
                </div>
                <div class="price-type">
                    <?php
                    if ($charging_model === 'per_group') {
                        echo 'per group';
                    } else {
                        echo 'per person';
                    }
                    ?>
                </div>
                <?php if ($max_people): ?>
                    <div class="max-people">Max <?php echo esc_html($max_people); ?> people</div>
                <?php endif; ?>
            </div>
            
            <!-- Booking Widget -->
            <?php if (!empty($ticketinghub_widget_id)): ?>
                <div class="booking-widget">
                    <div id="ticketinghub-widget-container"></div>
                    <script src="https://assets.ticketinghub.com/checkout.js" data-widget="<?php echo esc_js($ticketinghub_widget_id); ?>" data-no-minify="1"></script>
                </div>
            <?php endif; ?>
            
            <!-- Available Languages -->
            <div class="languages-list">
                <h4>Available Languages</h4>
                <ul>
                    <?php
                    if (!empty($languages) && is_array($languages)) {
                        foreach ($languages as $lang) {
                            echo '<li>' . (isset($language_names[$lang]) ? $language_names[$lang] : ucfirst($lang)) . '</li>';
                        }
                    } else {
                        echo '<li>English</li>';
                    }
                    ?>
                </ul>
            </div>
            
            <!-- Provider Information -->
            <div class="provider-info">
                <h4>Provider Information</h4>
                <p><strong>Provided by:</strong> <?php echo esc_html($partner_name); ?></p>
                <?php if ($provider_email): ?>
                    <p><strong>Email:</strong> <a href="mailto:<?php echo esc_attr($provider_email); ?>"><?php echo esc_html($provider_email); ?></a></p>
                <?php endif; ?>
                <?php if ($provider_tel): ?>
                    <p><strong>Phone:</strong> <a href="tel:<?php echo esc_attr($provider_tel); ?>"><?php echo esc_html($provider_tel); ?></a></p>
                <?php endif; ?>
                <?php if ($provider_address): ?>
                    <p><strong>Address:</strong><br><?php echo esc_html($provider_address); ?></p>
                <?php endif; ?>
            </div>
            
            <!-- Share and Favorite Buttons -->
            <div class="tour-actions">
                <button class="share-button" onclick="navigator.share ? navigator.share({title: '<?php echo esc_js(get_the_title()); ?>', url: '<?php echo esc_url(get_permalink()); ?>'}) : window.open('<?php echo esc_url(get_permalink()); ?>', '_blank')">
                    <i class="dashicons dashicons-share"></i>
                    Share Tour
                </button>
                <button class="favorite-button" onclick="toggleFavorite()">
                    <i class="dashicons dashicons-heart"></i>
                    Add to Favorites
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function toggleFavorite() {
    // Simple favorite toggle implementation
    const button = document.querySelector('.favorite-button');
    const icon = button.querySelector('i');
    
    if (icon.classList.contains('dashicons-heart')) {
        icon.classList.remove('dashicons-heart');
        icon.classList.add('dashicons-heart-filled');
        button.innerHTML = '<i class="dashicons dashicons-heart-filled"></i> Remove from Favorites';
    } else {
        icon.classList.remove('dashicons-heart-filled');
        icon.classList.add('dashicons-heart');
        button.innerHTML = '<i class="dashicons dashicons-heart"></i> Add to Favorites';
    }
}
</script>

<?php get_footer(); ?>