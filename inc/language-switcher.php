<?php
/**
 * Simple Polylang Language Switcher for Header Only
 */

// Simple Polylang language switcher
function tour_portal_simple_language_switcher() {
    // Only show if Polylang is active AND not in login process
    if (!function_exists('pll_the_languages') || (isset($_GET['action']) && $_GET['action'] === 'login')) {
        return;
    }
    
    // Get languages
    $languages = pll_the_languages(array('raw' => 1));
    
    if (empty($languages) || count($languages) <= 1) {
        return;
    }
    
    // Find current language
    $current_lang = null;
    foreach ($languages as $lang) {
        if ($lang['current_lang']) {
            $current_lang = $lang;
            break;
        }
    }
    
    if (!$current_lang) {
        return;
    }
    
    ?>
    <div class="language-switcher">
        <div class="language-dropdown">
            <button class="language-toggle" aria-expanded="false">
                <?php if (!empty($current_lang['flag'])): ?>
                    <img src="<?php echo esc_url($current_lang['flag']); ?>" alt="<?php echo esc_attr($current_lang['name']); ?>" class="language-flag">
                <?php endif; ?>
                <span class="language-name"><?php echo esc_html(strtoupper($current_lang['slug'])); ?></span>
                <span class="language-arrow">▼</span>
            </button>
            
            <ul class="language-options">
                <?php foreach ($languages as $lang): ?>
                    <?php if (!$lang['current_lang']): ?>
                        <li>
                            <a href="<?php echo esc_url($lang['url']); ?>">
                                <?php if (!empty($lang['flag'])): ?>
                                    <img src="<?php echo esc_url($lang['flag']); ?>" alt="<?php echo esc_attr($lang['name']); ?>" class="language-flag">
                                <?php endif; ?>
                                <span><?php echo esc_html(strtoupper($lang['slug'])); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    
    <style>
    .language-switcher {
        position: relative;
        z-index: 1000;
        margin-left: 20px;
    }
    
    .language-dropdown {
        position: relative;
    }
    
    .language-toggle {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        background: transparent;
        border: 1px solid rgba(255,255,255,0.3);
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        color: white;
        transition: all 0.3s ease;
    }
    
    .language-toggle:hover {
        border-color: rgba(255,255,255,0.6);
        background: rgba(255,255,255,0.1);
    }
    
    .language-flag {
        width: 20px;
        height: 14px;
        object-fit: cover;
        border-radius: 2px;
    }
    
    .language-name {
        font-weight: 500;
        text-transform: uppercase;
    }
    
    .language-arrow {
        font-size: 10px;
        color: rgba(255,255,255,0.8);
        transition: transform 0.3s ease;
    }
    
    .language-dropdown:hover .language-arrow {
        transform: rotate(180deg);
    }
    
    .language-options {
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        border: 1px solid #ddd;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        min-width: 120px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
        z-index: 1001;
        margin-top: 5px;
        list-style: none;
        padding: 0;
    }
    
    .language-dropdown:hover .language-options {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    
    .language-options a {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 15px;
        color: #333;
        text-decoration: none;
        transition: background 0.2s ease;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .language-options a:last-child {
        border-bottom: none;
    }
    
    .language-options a:hover {
        background: #f8f8f8;
    }
    
    .language-options .language-flag {
        width: 18px;
        height: 12px;
    }
    
    .language-options span {
        font-size: 14px;
        font-weight: 400;
        text-transform: uppercase;
    }
    
    @media (max-width: 768px) {
        .language-switcher {
            margin-left: 10px;
        }
        
        .language-toggle {
            padding: 6px 10px;
            font-size: 13px;
        }
        
        .language-name {
            display: none;
        }
    }
    </style>
    <?php
}

// Display language switcher dropdown
function tour_portal_language_switcher($location = 'header') {
    $languages = tour_portal_get_available_translations();
    
    if (empty($languages) || count($languages) <= 1) {
        return; // Don't show if no languages or only one language
    }
    
    $current_language = null;
    foreach ($languages as $lang) {
        if ($lang['current']) {
            $current_language = $lang;
            break;
        }
    }
    
    if (!$current_language) {
        return;
    }
    
    ?>
    <div class="language-switcher <?php echo esc_attr($location); ?>-language-switcher">
        <div class="language-dropdown">
            <button class="language-toggle" aria-expanded="false" aria-haspopup="true">
                <?php if (!empty($current_language['flag'])): ?>
                    <img src="<?php echo esc_url($current_language['flag']); ?>" alt="<?php echo esc_attr($current_language['name']); ?>" class="language-flag">
                <?php endif; ?>
                <span class="language-name"><?php echo esc_html($current_language['name']); ?></span>
                <span class="language-arrow">▼</span>
            </button>
            
            <ul class="language-options" role="menu">
                <?php foreach ($languages as $language): ?>
                    <?php if (!$language['current']): ?>
                        <li role="menuitem">
                            <a href="<?php echo esc_url($language['url']); ?>" class="language-option">
                                <?php if (!empty($language['flag'])): ?>
                                    <img src="<?php echo esc_url($language['flag']); ?>" alt="<?php echo esc_attr($language['name']); ?>" class="language-flag">
                                <?php endif; ?>
                                <span class="language-name"><?php echo esc_html($language['name']); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    
    <style>
    .language-switcher {
        position: relative;
        z-index: 1000;
    }
    
    .language-dropdown {
        position: relative;
    }
    
    .language-toggle {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        background: transparent;
        border: 1px solid #ddd;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s ease;
    }
    
    .language-toggle:hover {
        background: #f5f5f5;
        border-color: #0073aa;
    }
    
    .language-flag {
        width: 20px;
        height: 14px;
        object-fit: cover;
        border-radius: 2px;
    }
    
    .language-name {
        font-weight: 500;
        color: #333;
    }
    
    .language-arrow {
        font-size: 10px;
        color: #666;
        transition: transform 0.3s ease;
    }
    
    .language-dropdown:hover .language-arrow,
    .language-dropdown.active .language-arrow {
        transform: rotate(180deg);
    }
    
    .language-options {
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        border: 1px solid #ddd;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        min-width: 150px;
        max-height: 300px;
        overflow-y: auto;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
        z-index: 1001;
        margin-top: 5px;
        list-style: none;
        padding: 0;
    }
    
    .language-dropdown:hover .language-options,
    .language-dropdown.active .language-options {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    
    .language-option {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 15px;
        color: #333;
        text-decoration: none;
        transition: background 0.2s ease;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .language-option:last-child {
        border-bottom: none;
    }
    
    .language-option:hover {
        background: #f8f8f8;
    }
    
    .language-option .language-flag {
        width: 18px;
        height: 12px;
    }
    
    .language-option .language-name {
        font-size: 14px;
        font-weight: 400;
    }
    
    /* Header specific styles */
    .header-language-switcher {
        margin-left: 20px;
    }
    
    .header-language-switcher .language-toggle {
        border-color: rgba(255,255,255,0.3);
        color: white;
    }
    
    .header-language-switcher .language-toggle:hover {
        border-color: rgba(255,255,255,0.6);
        background: rgba(255,255,255,0.1);
    }
    
    .header-language-switcher .language-name {
        color: white;
    }
    
    .header-language-switcher .language-arrow {
        color: rgba(255,255,255,0.8);
    }
    
    /* Footer specific styles */
    .footer-language-switcher {
        margin-bottom: 20px;
    }
    
    .footer-language-switcher .language-toggle {
        background: #f8f8f8;
        border-color: #ddd;
        width: 100%;
        justify-content: space-between;
    }
    
    /* Mobile responsive */
    @media (max-width: 768px) {
        .header-language-switcher {
            margin-left: 10px;
        }
        
        .language-toggle {
            padding: 6px 10px;
            font-size: 13px;
        }
        
        .language-flag {
            width: 16px;
            height: 11px;
        }
        
        .language-name {
            display: none;
        }
        
        .language-arrow {
            margin-left: 0;
        }
    }
    </style>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const languageToggles = document.querySelectorAll('.language-toggle');
        
        languageToggles.forEach(function(toggle) {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                const dropdown = this.closest('.language-dropdown');
                const isActive = dropdown.classList.contains('active');
                
                // Close all other dropdowns
                document.querySelectorAll('.language-dropdown.active').forEach(function(activeDropdown) {
                    if (activeDropdown !== dropdown) {
                        activeDropdown.classList.remove('active');
                        activeDropdown.querySelector('.language-toggle').setAttribute('aria-expanded', 'false');
                    }
                });
                
                // Toggle current dropdown
                if (isActive) {
                    dropdown.classList.remove('active');
                    this.setAttribute('aria-expanded', 'false');
                } else {
                    dropdown.classList.add('active');
                    this.setAttribute('aria-expanded', 'true');
                }
            });
        });
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.language-dropdown')) {
                document.querySelectorAll('.language-dropdown.active').forEach(function(dropdown) {
                    dropdown.classList.remove('active');
                    dropdown.querySelector('.language-toggle').setAttribute('aria-expanded', 'false');
                });
            }
        });
        
        // Close dropdowns on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.language-dropdown.active').forEach(function(dropdown) {
                    dropdown.classList.remove('active');
                    dropdown.querySelector('.language-toggle').setAttribute('aria-expanded', 'false');
                });
            }
        });
    });
    </script>
    <?php
}

// Language switcher is now called directly in header.php and footer.php
?>
