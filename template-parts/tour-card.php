<?php
/**
 * Tour Card Template Part - Partner Page Style
 */

$tour_id = get_the_ID();
$partner_id = get_the_author_meta('ID');

// Get tour meta data
$tour_type = get_post_meta($tour_id, '_tour_type', true);
$charging_model = get_post_meta($tour_id, '_charging_model', true);
$german_price = get_post_meta($tour_id, '_german_price', true);
$foreign_price = get_post_meta($tour_id, '_foreign_price', true);
$duration_hours = get_post_meta($tour_id, '_duration_hours', true);
$languages = get_post_meta($tour_id, '_languages', true);
$max_people = get_post_meta($tour_id, '_max_people', true);
?>

<div class="tour-card" tabindex="0">
    <div class="tour-image">
        <?php if (has_post_thumbnail()): ?>
            <?php the_post_thumbnail('medium_large', array('class' => '')); ?>
        <?php endif; ?>
    </div>
    <div class="tour-content">
        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
        <p>
            <?php 
            $excerpt = get_the_excerpt();
            if ($excerpt) {
                echo esc_html(substr($excerpt, 0, 200));
                if (strlen($excerpt) > 200) {
                    echo '...';
                }
            } else {
                $content = get_the_content();
                echo esc_html(substr($content, 0, 200));
                if (strlen($content) > 200) {
                    echo '...';
                }
            }
            ?>
        </p>
        <div class="tour-details">
            <div class="detail-block">
                <h4>Duration</h4>
                <?php
                if ($duration_hours) {
                    echo esc_html($duration_hours) . ' hours';
                } else {
                    echo '2 hours';
                }
                ?>
                
                <h4>Languages</h4>
                <ul>
                    <?php
                    if (!empty($languages) && is_array($languages)) {
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
                    if ($german_price || $foreign_price) {
                        if ($charging_model === 'per_group') {
                            echo 'Price per group: from ';
                        } else {
                            echo 'Price per person: from ';
                        }
                        
                        if ($foreign_price) {
                            echo '€' . number_format($foreign_price, 2);
                        } elseif ($german_price) {
                            echo '€' . number_format($german_price, 2);
                        }
                    } else {
                        echo 'Contact for pricing';
                    }
                    ?>
                </p>
                <div class="tour-button">
                    <a href="<?php the_permalink(); ?>" class="btn-booking">Details & booking</a>
                </div>
            </div>
        </div>
    </div>
</div>
