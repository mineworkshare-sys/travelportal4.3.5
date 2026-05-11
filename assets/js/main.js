/**
 * Tour Portal Main JavaScript
 */

jQuery(document).ready(function($) {
    // Tour filtering functionality
    function filterTours() {
        var tourType = $('#filter-tour-type').val();
        var language = $('#filter-language').val();
        var category = $('#filter-category').val();
        
        $('.tour-card').each(function() {
            var card = $(this);
            var showCard = true;
            
            // Filter by tour type
            if (tourType && card.data('tour-type') !== tourType) {
                showCard = false;
            }
            
            // Filter by language
            if (language && !card.data('languages').includes(language)) {
                showCard = false;
            }
            
            // Filter by category
            if (category && !card.hasClass('category-' + category)) {
                showCard = false;
            }
            
            if (showCard) {
                card.fadeIn(300);
            } else {
                card.fadeOut(300);
            }
        });
    }
    
    // Apply filters button click
    $('#apply-filters').on('click', function() {
        filterTours();
    });
    
    // Auto-filter on change
    $('#filter-tour-type, #filter-language, #filter-category').on('change', function() {
        filterTours();
    });
    
    // Tour card hover effects
    $('.tour-card').hover(
        function() {
            $(this).find('.tour-image img').addClass('hover-scale');
        },
        function() {
            $(this).find('.tour-image img').removeClass('hover-scale');
        }
    );
    
    // Category card hover effects
    $('.category-card').hover(
        function() {
            $(this).find('.category-image img').addClass('hover-scale');
        },
        function() {
            $(this).find('.category-image img').removeClass('hover-scale');
        }
    );
    
    // Smooth scroll for anchor links
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        var target = $(this.getAttribute('href'));
        if (target.length) {
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 100
            }, 1000);
        }
    });
    
    // Mobile menu toggle
    $('.mobile-menu-toggle').on('click', function() {
        $('.main-navigation').toggleClass('mobile-open');
        $(this).toggleClass('active');
    });
    
    // Language switcher dropdown
    $('.language-current').on('click', function(e) {
        e.preventDefault();
        $(this).siblings('.sub-menu').toggleClass('show');
    });
    
    // Close language switcher when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.language-switcher').length) {
            $('.language-switcher .sub-menu').removeClass('show');
        }
    });
    
    // Form validation for registration
    $('#partner-registration-form').on('submit', function(e) {
        var form = $(this);
        var isValid = true;
        
        // Remove previous error classes
        form.find('.form-group').removeClass('has-error');
        form.find('.error-message').remove();
        
        // Validate required fields
        form.find('input[required], textarea[required]').each(function() {
            if (!$(this).val().trim()) {
                $(this).closest('.form-group').addClass('has-error');
                $(this).after('<span class="error-message">' + tourPortalL10n.requiredField + '</span>');
                isValid = false;
            }
        });
        
        // Validate email
        var email = $('#partner_email');
        if (email.val() && !isValidEmail(email.val())) {
            email.closest('.form-group').addClass('has-error');
            email.after('<span class="error-message">' + tourPortalL10n.invalidEmail + '</span>');
            isValid = false;
        }
        
        // Validate password strength
        var password = $('#partner_password');
        if (password.val() && password.val().length < 8) {
            password.closest('.form-group').addClass('has-error');
            password.after('<span class="error-message">' + tourPortalL10n.passwordTooShort + '</span>');
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            // Scroll to first error
            $('html, body').animate({
                scrollTop: $('.has-error').first().offset().top - 100
            }, 500);
        }
    });
    
    // Email validation helper
    function isValidEmail(email) {
        var pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return pattern.test(email);
    }
    
    // Lazy loading for images
    if ('IntersectionObserver' in window) {
        var imageObserver = new IntersectionObserver(function(entries, observer) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    var img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        document.querySelectorAll('img[data-src]').forEach(function(img) {
            imageObserver.observe(img);
        });
    }
    
    // Tour search functionality
    $('#tour-search').on('input', function() {
        var searchTerm = $(this).val().toLowerCase();
        
        $('.tour-card').each(function() {
            var card = $(this);
            var title = card.find('.tour-title').text().toLowerCase();
            var excerpt = card.find('.tour-excerpt').text().toLowerCase();
            
            if (title.includes(searchTerm) || excerpt.includes(searchTerm)) {
                card.show();
            } else {
                card.hide();
            }
        });
    });
    
    // Price range slider
    if ($('#price-range').length) {
        $('#price-range').slider({
            range: true,
            min: 0,
            max: 500,
            values: [0, 500],
            slide: function(event, ui) {
                $('#price-min').val(ui.values[0]);
                $('#price-max').val(ui.values[1]);
                filterTours();
            }
        });
    }
    
    // Duration filter
    $('#duration-filter').on('change', function() {
        var maxDuration = $(this).val();
        
        $('.tour-card').each(function() {
            var card = $(this);
            var duration = parseFloat(card.data('duration'));
            
            if (maxDuration === 'all' || duration <= parseFloat(maxDuration)) {
                card.show();
            } else {
                card.hide();
            }
        });
    });
    
    // Booking widget initialization
    function initializeBookingWidget() {
        var widgetContainer = $('.booking-widget');
        if (widgetContainer.length) {
            // Initialize TicketingHub widget here
            // This would be replaced with actual TicketingHub initialization code
            console.log('Booking widget initialized');
        }
    }
    
    initializeBookingWidget();
    
    // Print tour details
    $('.print-tour').on('click', function(e) {
        e.preventDefault();
        window.print();
    });
    
    // Share tour functionality
    $('.share-tour').on('click', function(e) {
        e.preventDefault();
        if (navigator.share) {
            navigator.share({
                title: $('.tour-title').text(),
                text: $('.tour-excerpt').text(),
                url: window.location.href
            });
        } else {
            // Fallback: copy to clipboard
            var dummy = document.createElement('input');
            document.body.appendChild(dummy);
            dummy.value = window.location.href;
            dummy.select();
            document.execCommand('copy');
            document.body.removeChild(dummy);
            
            // Show notification
            showNotification(tourPortalL10n.linkCopied);
        }
    });
    
    // Notification system
    function showNotification(message, type = 'success') {
        var notification = $('<div class="notification notification-' + type + '">' + message + '</div>');
        $('body').append(notification);
        
        setTimeout(function() {
            notification.addClass('show');
        }, 100);
        
        setTimeout(function() {
            notification.removeClass('show');
            setTimeout(function() {
                notification.remove();
            }, 300);
        }, 3000);
    }
    
    // Add to favorites
    $('.add-to-favorites').on('click', function(e) {
        e.preventDefault();
        var button = $(this);
        var tourId = button.data('tour-id');
        
        // Toggle favorite state
        button.toggleClass('favorited');
        
        if (button.hasClass('favorited')) {
            showNotification(tourPortalL10n.addedToFavorites);
            // Save to localStorage or send to server
            saveFavorite(tourId);
        } else {
            showNotification(tourPortalL10n.removedFromFavorites);
            // Remove from localStorage or server
            removeFavorite(tourId);
        }
    });
    
    // Save favorite to localStorage
    function saveFavorite(tourId) {
        var favorites = JSON.parse(localStorage.getItem('tourFavorites') || '[]');
        if (!favorites.includes(tourId)) {
            favorites.push(tourId);
            localStorage.setItem('tourFavorites', JSON.stringify(favorites));
        }
    }
    
    // Remove favorite from localStorage
    function removeFavorite(tourId) {
        var favorites = JSON.parse(localStorage.getItem('tourFavorites') || '[]');
        var index = favorites.indexOf(tourId);
        if (index > -1) {
            favorites.splice(index, 1);
            localStorage.setItem('tourFavorites', JSON.stringify(favorites));
        }
    }
    
    // Check if tour is favorited on page load
    function checkFavorites() {
        var favorites = JSON.parse(localStorage.getItem('tourFavorites') || '[]');
        $('.add-to-favorites').each(function() {
            var button = $(this);
            var tourId = button.data('tour-id');
            if (favorites.includes(tourId)) {
                button.addClass('favorited');
            }
        });
    }
    
    checkFavorites();
    
    // Image gallery for tour details
    $('.tour-gallery').each(function() {
        var gallery = $(this);
        var mainImage = gallery.find('.main-image img');
        var thumbnails = gallery.find('.thumbnail');
        
        thumbnails.on('click', function() {
            var thumbnail = $(this);
            var imageSrc = thumbnail.data('image');
            
            mainImage.attr('src', imageSrc);
            thumbnails.removeClass('active');
            thumbnail.addClass('active');
        });
    });
    
    // Map initialization (if needed)
    function initializeMap() {
        var mapContainer = $('#tour-map');
        if (mapContainer.length && typeof L !== 'undefined') {
            // Initialize Leaflet map
            var map = L.map('tour-map').setView([51.505, -0.09], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
            
            // Add tour location marker
            var lat = mapContainer.data('lat');
            var lng = mapContainer.data('lng');
            if (lat && lng) {
                L.marker([lat, lng]).addTo(map);
                map.setView([lat, lng], 15);
            }
        }
    }
    
    // Initialize map when page loads
    initializeMap();
    
    // Accessibility improvements
    $('.tour-card, .category-card').attr('tabindex', '0');
    
    $('.tour-card, .category-card').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            // Don't trigger click - let default behavior handle link navigation
            return true;
        }
    });
    
    // Loading states
    $('.btn-primary, .btn-secondary').on('click', function() {
        var button = $(this);
        if (!button.hasClass('loading')) {
            button.addClass('loading');
            button.prop('disabled', true);
            
            // Reset loading state after 3 seconds (adjust as needed)
            setTimeout(function() {
                button.removeClass('loading');
                button.prop('disabled', false);
            }, 3000);
        }
    });
    
    // Dynamic content loading
    $('.load-more-tours').on('click', function(e) {
        e.preventDefault();
        var button = $(this);
        var page = button.data('page') || 1;
        var category = button.data('category');
        
        button.addClass('loading');
        button.prop('disabled', true);
        
        // AJAX call to load more tours
        $.ajax({
            url: tourPortalL10n.ajaxurl,
            type: 'POST',
            data: {
                action: 'load_more_tours',
                page: page,
                category: category,
                nonce: tourPortalL10n.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('.tour-list').append(response.data.html);
                    button.data('page', page + 1);
                    
                    if (response.data.hasMore) {
                        button.removeClass('loading');
                        button.prop('disabled', false);
                    } else {
                        button.remove();
                    }
                }
            },
            error: function() {
                button.removeClass('loading');
                button.prop('disabled', false);
                showNotification(tourPortalL10n.loadError, 'error');
            }
        });
    });
});
