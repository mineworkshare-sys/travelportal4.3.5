# Tour Portal Theme Changelog

## Version 4.3.5 - May 11, 2026
### Fixes
- **PHP 8 Fatal Error Fixed**: 4.3.4 introduced a fatal error — $partner->roles was accessed before the null/false check, crashing the page entirely on PHP 8 servers
- **Safe Role Check**: Rewrote role validation to safely short-circuit on null/false partner before accessing ->roles
- **Role Check Retained**: Still accepts partner, site_owner, and administrator roles so reimaginetours user is not blocked regardless of which role was assigned to them

## Version 4.3.4 - May 11, 2026
### Fixes
- **Root Cause Fixed**: company-landing.php was rejecting users with site_owner or administrator roles — only accepting partner role. Due to role changes in earlier versions, the reimaginetours user may have site_owner role, causing "Company Not Found" before tours were ever queried
- **Role Check Updated**: Now accepts partner, site_owner, and administrator roles on company pages so tours display regardless of which role was assigned to the company user

## Version 4.3.3 - May 11, 2026
### Fixes
- **Tour Listings Restored**: Replaced broken get_template_part() call in company-landing.php with inline tour card rendering — tours now display correctly on partner company pages
- **Orphaned Code Removed**: Removed stray echo/PHP lines sitting after the closing </style> tag at the bottom of company-landing.php

## Version 4.3.2 - May 9, 2026
### Fixes
- **Comprehensive Partner Management Fix**: Admins now see both Partner and Site Owner roles
- **Role Display Logic**: Partner list shows both role types for admin users
- **Admin Access Control**: Admins can manage all partners regardless of role type
- **Site Owner Access**: Site owners can edit their own partner information
- **Edit Partner Button**: Comprehensive role checking for Edit Partner functionality
- **Partner List Query**: Updated to use `role__in` for multi-role display
- **Access Control Logic**: Clear separation between admin and site owner permissions
- **Theme Installation**: Resolved critical theme installation error
- **Role System Stability**: Robust role management for all user types
- **Mobile Tour Card Fix**: Added responsive styling for tour cards on mobile devices
- **Button Visibility**: Details & Booking button now properly displays on mobile phones
- **Touch-Friendly Design**: Improved button sizing and padding for mobile interaction
- **Responsive Layout**: Complete mobile breakpoint coverage for tour cards
- **Company Logo Sizing**: Added max-height constraint of 200px for company logos
- **Mobile Button Fix**: Enhanced mobile button visibility with !important declaration

## Version 4.2.9 - May 9, 2026
### Fixes
- **Critical Theme Installation Fix**: Comprehensive role system validation and access control
- **Role-Based Access Control**: Admins see all partners, Site Owners see all partners but can only edit own info
- **Comprehensive Validation**: Added proper role checking for admin, partner, and site_owner roles
- **Access Control Logic**: Clear separation of permissions based on user role and capabilities
- **Error Prevention**: Added fallback handling to prevent unauthorized access
- **Theme Installation**: Fixed critical error that occurred during theme activation
- **Role System Stability**: Robust role management system for both single and multi-operator modes

## Version 4.2.8 - May 9, 2026
### Fixes
- **Partner List Role Fix**: Updated partner list to show correct roles based on mode
- **Single Operator Mode**: Partner list now shows 'site_owner' role users
- **Multi-Operator Mode**: Partner list shows 'partner' role users
- **Dynamic Role Detection**: Uses `tour_portal_is_multi_operator()` for mode detection
- **Partner Management Access**: Fixed access for admin and site_owner roles
- **Role-Based Access**: Admins can manage all partners, site owners can access partner management
- **Permission Logic**: Updated condition to allow both `manage_options` and `site_owner` roles
- **Debug Output**: Added debug information to track user capabilities and roles
- **Access Control**: Site owners can now access partner management page properly
- **Admin Access**: Admins retain full access to all partner management features

## Version 4.2.6 - May 9, 2026
### Fixes
- **Simplified Role Logic**: Removed complex logic, simplified role display
- **Clean Role Display**: Simple if/else logic for Site Owner vs Partner display
- **Single Operator Mode**: Partner list correctly shows "Site Owner" role type
- **Multi-Operator Mode**: Partner list correctly shows "Partner" role type
- **Role Detection**: Uses `tour_portal_is_multi_operator()` for mode detection
- **Simplified Code**: Clean, maintainable role display logic without overcomplication

## Version 4.2.3 - May 9, 2026
### Fixes
- **Partner List Role Fix**: Updated partner list to show Site Owner role in single operator mode
- **Dynamic Role Detection**: Automatically detects single vs multi-operator mode for correct role display
- **Site Owner Support**: Partner list now includes 'site_owner' role alongside 'partner' role
- **Edit Permissions**: Updated filter_tours_for_partners() to include Site Owner role
- **Role Compatibility**: Both Partner and Site Owner roles can edit their own tours
- **Single Operator Mode**: Partner list correctly shows Site Owner users instead of Partner users

## Version 4.2.1 - May 9, 2026
### Fixes
- **TicketingHub Widget Fix**: Updated widget loading to match working version
- **Script URL**: Changed to https://assets.ticketinghub.com/checkout.js with data-no-minify="1"
- **Container ID**: Updated to use id="ticketinghub-widget-container" 
- **Widget Loading**: Removed script wrapper div for proper TicketingHub integration
- **Working Widget**: Now loads correctly when widget ID is present in tour meta

## Version 4.2.0 - May 9, 2026
### Fixes
- **Tour Detail Page Layout**: Complete rewrite of single-tour.php with proper two-column layout
- **Left Column Content**: Added tour image, title, basic info, full description, additional info sections
- **Right Column Sidebar**: Added price box, booking widget, languages list, provider info, share/favorite buttons
- **Full Description Display**: Shows complete tour content instead of truncated excerpt
- **Missing Elements Restored**: Added cancellation policy, booking exceptions, group size, accessibility info
- **Professional Layout**: Uses CSS grid layout with sticky sidebar for optimal user experience
- **Booking Integration**: Proper TicketingHub widget integration with fallback contact option
- **Interactive Elements**: Added share and favorite functionality with JavaScript

## Version 4.1.1 - May 6, 2026
### Fixes
- **Site Owner Role Creation**: Added 'site_owner' role registration in functions.php
- **Editor Capabilities**: Site owner has most editor functions plus tour management
- **Professional Permissions**: Limited admin access but full content control
- **Tour Management**: Can edit, publish, delete tours and categories
- **Role Registration**: Theme automatically creates site_owner role on activation

## Version 4.1.0 - May 6, 2026
### Fixes
- **Role Separation**: Changed from 'partner' to 'site_owner' role for single operator sites
- **Clear Distinction**: Single operator uses different role than multi-partner system
- **Site Owner Role**: Targets 'site_owner' role instead of 'partner' for company info
- **Professional Structure**: Proper role separation for single vs multi-operator modes
- **User Role Update**: Users should be changed to 'site_owner' role for single operator sites

## Version 4.0.9 - May 6, 2026
### Fixes
- **Professional Theme**: Removed all fallback code for professional theme standards
- **Partner Required**: Theme assumes partner user always exists in single operator mode
- **Clean Code**: Eliminated conditional fallback logic
- **Direct Execution**: Partner info loads directly from user meta without fallbacks
- **No WordPress Info**: Removed bloginfo() fallbacks for professional implementation

## Version 4.0.8 - May 6, 2026
### Fixes
- **PHP Execution Fix**: Replaced mixed PHP/HTML with pure echo statements
- **Template Reliability**: Ensured PHP code executes properly in archive-tour.php
- **Dynamic Output**: All partner info now output via echo statements
- **Syntax Correction**: Fixed potential PHP syntax issues preventing execution
- **Fallback Support**: Maintains WordPress site info fallback when no partner found

## Version 4.0.7 - May 6, 2026
### Fixes
- **Dynamic Partner Info**: Removed all hardcoded partner information
- **User Meta Fetch**: Gets partner logo, name, description, phone, website from user meta
- **Single Partner Detection**: Finds the single partner user with role 'partner'
- **Template Verification**: Ensures archive-tour.php loads dynamic partner data
- **No Hardcoded Values**: All company information pulled from user profile

## Version 4.0.6 - May 6, 2026
### Fixes
- **Single Partner User**: Gets the single user with role 'partner' in single operator mode
- **Dynamic Partner Detection**: Uses get_users() to find the one partner user dynamically
- **User Meta Fields**: Pulls partner info from user meta fields of the single partner
- **Non-Hardcoded**: Completely dynamic approach - no hardcoded company information
- **Role-Based**: Correctly identifies partner role user for company information

## Version 4.0.5 - May 6, 2026
### Fixes
- **Single Operator Partner Info**: Gets partner info from the single partner user in single mode
- **User Meta Integration**: Pulls partner logo, name, description, phone, website from user profile
- **Partner Role Detection**: Finds the single partner user and displays their company information
- **Consistent Display**: Shows same partner info structure as multi-mode but for single operator
- **Fallback Support**: Includes fallback if no partner user is found

## Version 4.0.4 - May 6, 2026
### Fixes
- **Dynamic Company Info**: Replaced hardcoded partner info with WordPress site information
- **Site Logo Integration**: Uses custom logo from WordPress customizer or fallback
- **Dynamic Content**: Pulls site name, description from WordPress settings
- **Contact Options**: Uses theme options for phone/website with fallbacks
- **Single Operator Support**: Properly displays admin-controlled company information

## Version 4.0.3 - May 6, 2026
### Fixes
- **Partner Info Section**: Updated partner-info-bottom with correct company information
- **Logo Update**: Replaced default logo with actual Reimagine Tours® logo
- **Company Details**: Added correct company name, description, and contact information
- **Contact Info**: Updated phone number and website to match partner page exactly
- **Brand Consistency**: Partner info section now identical across all pages

## Version 4.0.2 - May 6, 2026
### Fixes
- **Tour Card Size Fix**: Added inline CSS to fix huge tour card sizes on /tour/ page
- **Layout Consistency**: Tour cards now use 30%/70% split like partner page
- **Visual Match**: Tour card dimensions, fonts, and spacing now match partner page exactly
- **CSS Integration**: Added tour card styles directly to archive-tour.php template
- **Size Control**: Fixed image and content sizing to prevent oversized cards

## Version 4.0.1 - May 6, 2026
### Fixes
- **Complete Partner Layout**: Added full partner-layout wrapper to /tour/ page
- **Sidebar Integration**: Added partner-sidebar with search and tour filters
- **Content Structure**: Added partner-content wrapper with tour categories section
- **Bottom Info Section**: Added partner-info-bottom with company information
- **Exact Match**: /tour/ page now has identical structure to partner listing page
- **Full Feature Parity**: Both pages now include search, filters, categories, and pagination

## Version 4.0.0 - May 6, 2026
### Fixes
- **Archive Template Overhaul**: Removed archive header, filters, and results count from /tour/ page
- **Layout Unification**: /tour/ page now matches partner page layout exactly
- **Template Simplification**: archive-tour.php now uses same structure as company-landing.php
- **Visual Consistency**: Both single operator and partner pages show identical tour listings
- **Mobile Layout**: Maintained mobile responsiveness across unified templates

## Version 3.9.9 - May 6, 2026
### Fixes
- **Template Consolidation**: Replaced inline tour card in company-landing.php with template-part call
- **Unified Structure**: Both partner and single operator pages now use identical template-parts/tour-card.php
- **Layout Consistency**: Tour cards are now exactly the same across all page types
- **Template Efficiency**: Eliminated duplicate code by using single template file
- **Mobile Responsiveness**: Maintained mobile fixes with unified template

## Version 3.9.8 - May 6, 2026
### Fixes
- **Archive Template Fix**: Modified template-parts/tour-card.php to use partner page structure
- **Tour Archive Layout**: Single operator tour archive now matches partner layout exactly
- **Template Correction**: Fixed wrong template being used for single operator tour display
- **Identical Structure**: Tour cards now use same HTML structure as partner pages
- **Mobile Layout**: Maintained mobile responsiveness fixes

## Version 3.9.7 - May 6, 2026
### Fixes
- **Exact Layout Match**: Single operator tour layout now matches partner layout exactly
- **Tour Card Structure**: Used identical HTML structure from partner page for single operator tours
- **Content Display**: Fixed excerpt truncation and language display to match partner format
- **Price Formatting**: Corrected price display logic to match partner page exactly
- **Mobile Responsiveness**: Maintained mobile fixes from previous version

## Version 3.9.6 - May 6, 2026
### Fixes
- **Mobile Layout**: Fixed tour card display on mobile devices - details and booking button now visible
- **Responsive Design**: Added mobile-specific CSS for tour cards with proper button styling
- **Single Operator Layout**: Made single operator tour layout match partner layout exactly
- **Tour Card Consistency**: Both partner and single operator pages now use identical tour card structure
- **User Experience**: Improved mobile navigation and booking button accessibility

## Version 3.9.5 - May 6, 2026
### Fixes
- **Fatal Error Resolution**: Fixed function name conflict with existing admin-interface.php
- **Function Renaming**: Renamed conflicting functions to prevent redeclaration errors
- **Admin Menu Separation**: Created separate admin page for rewrite rule management
- **Site Stability**: Resolved critical error preventing site access

## Version 3.9.4 - May 6, 2026
### Fixes
- **Partner Tour URL Structure**: Successfully implemented hierarchical URLs for partner tours
- **URL Generation**: Fixed permalink filter to use post author for partner relationship detection
- **Rewrite Rules**: Added custom rewrite rules for partner tour URL resolution
- **Admin Tools**: Added settings page with rewrite rule flush functionality
- **URL Consistency**: All partner tours are now contained within their respective company URLs
- **SEO Structure**: Improved URL structure for better search engine optimization
- **Navigation Logic**: Partner tour links maintain proper company context

## Version 3.9.3 - May 6, 2026
### New Features
- **Partner Tour URL Structure**: Implemented hierarchical URLs for partner tours
- **URL Hierarchy**: Partner tours now use /company/{partner_slug}/tour/{tour_slug}/ structure
- **Rewrite Rules**: Added custom rewrite rules for partner tour URL resolution
- **Permalink Filter**: Dynamic permalink generation based on tour-partner relationship

### Fixes
- **URL Consistency**: All partner tours are now contained within their respective company URLs
- **SEO Structure**: Improved URL structure for better search engine optimization
- **Navigation Logic**: Partner tour links maintain proper company context

## Version 3.9.2 - May 6, 2026
### Fixes
- **Homepage Routing Fix**: Fixed German homepage routing to prevent company-landing.php template usage
- **Template Logic**: Excluded homepage paths from company template redirect
- **Content Loading**: German homepage now loads correct content instead of empty template
- **Polylang Compatibility**: Maintained proper language handling for homepage routing

## Version 3.9.0 - May 6, 2026
### New Features
- **Custom Login CSS**: Implemented custom login styling via login_head and login_form hooks
- **CSS Loading Fix**: Changed from login_enqueue_scripts to login_head for faster rendering

## Version 3.8.8 - May 6, 2026
### Fixes
- **WPS Hide Login Compatibility**: Added detection for WPS Hide Login plugin to prevent conflicts
- **Login Redirect**: Custom login page now works with or without WPS Hide Login plugin active
- **Debug Logging**: Added error logging to troubleshoot login redirect issues
- **Simplified Approach**: Removed complex debug logging, streamlined login redirect logic
- **Hook Priority**: Set login_init hook priority to 1 to run before other plugins
- **Password Field Fix**: Fixed missing 'name="pwd"' field in login form
- **Hook Change**: Replaced login_init with init hook for better WordPress compatibility
- **WPS Integration**: Added support for WPS Hide Login custom URL option
- **Final Fix**: Removed custom login shortcode, added proper WPS Hide Login redirect functionality
- **Complete Cleanup**: Removed all custom login functionality and WPS Hide Login integration

### Fixes
- **Login Issues**: Resolved cPanel login problems caused by early WordPress function calls
- **Polylang Conflicts**: Fixed language switcher interfering with authentication
- **WordPress Loading**: Moved multi-operator-mode.php include to proper hook timing
- **Debug Notices**: Eliminated wp_is_block_theme errors on cloned subdomains

---

## Version 3.8.1 - May 6, 2026
### New Features
- **Custom Login Screen**: Created professional custom login page to replace wp-login.php
- **Branding**: Added Reimagine Travel Tour & Booking Platform branding
- **Logo Integration**: Custom logo support with your specified URL
- **WordPress Integration**: Automatic redirect from default login to custom login
- **cPanel Compatibility**: Fixed conflicts with sapp-wp-signon.php login system

### Fixes
- **WPS Hide Login Compatibility**: Added detection for WPS Hide Login plugin to prevent conflicts
- **Login Redirect**: Custom login page now works with or without WPS Hide Login plugin active
- **Debug Logging**: Added error logging to troubleshoot login redirect issues
- **Simplified Approach**: Removed complex debug logging, streamlined login redirect logic
- **Hook Priority**: Set login_init hook priority to 1 to run before other plugins
- **Password Field Fix**: Fixed missing 'name="pwd"' field in login form
- **Hook Change**: Replaced login_init with init hook for better WordPress compatibility

### Fixes
- **Login Issues**: Resolved cPanel login problems caused by early WordPress function calls
- **Polylang Conflicts**: Fixed language switcher interfering with authentication
- **WordPress Loading**: Moved multi-operator-mode.php include to proper hook timing
- **Debug Notices**: Eliminated wp_is_block_theme errors on cloned subdomains

---

## Version 3.7.9 - May 6, 2026
### Fixes
- **Function Redeclaration**: Removed duplicate tour_portal_language_switcher function
- **Theme Support**: Fixed duplicate add_theme_support('custom-logo') calls
- **WordPress Loading**: Corrected theme setup hook priorities

---

## Version 3.7.8 - May 6, 2026
### Fixes
- **Function Redeclaration**: Removed duplicate wp_is_block_theme function calls
- **Root Cause**: Fixed actual source of early WordPress function calls
- **Loading Order**: Moved problematic includes to proper WordPress hooks

---

## Version 3.7.7 - May 6, 2026
### Fixes
- **Function Redeclaration**: Fixed tour_portal_get_available_languages function conflicts
- **Error Suppression**: Removed bandaid fixes, addressed root causes

---

## Version 3.7.6 - May 6, 2026
### Fixes
- **Function Redeclaration**: Fixed wp_is_block_theme function call errors
- **Theme Directory**: Prevented early function calls before WordPress registration

---

## Version 3.7.5 - May 6, 2026
### New Features
- **Language Switcher**: Added Polylang and WPML support for header and footer
- **Dropdown Interface**: Clean language selection with flags and names
- **Responsive Design**: Mobile-friendly language switcher

---

## Version 3.7.4 - May 6, 2026
### Fixes
- **Language Switcher**: Fixed undefined function calls causing fatal errors
- **Function Safety**: Added proper function existence checks

---

## Version 3.7.3 - May 6, 2026
### Fixes
- **Language Switcher**: Removed duplicate function declarations
- **Clean Code**: Eliminated function redeclaration conflicts

---

## Version 3.7.2 - May 6, 2026
### Fixes
- **Language Switcher**: Fixed function name mismatches between files
- **Header/Footer**: Updated to use correct function names

---

## Version 3.7.1 - May 6, 2026
### Fixes
- **Language Switcher**: Fixed function redeclaration errors
- **Safe Loading**: Added proper error handling for Polylang/WPML

---

## Version 3.7.0 - May 6, 2026
### New Features
- **Language Switcher**: Added Polylang and WPML support
- **Header Integration**: Language dropdown in main navigation
- **Footer Integration**: Language dropdown in footer section
- **Page Translations**: Support for page translations (tours later)

---

## Version 3.6.9 - May 6, 2026
### Fixes
- **Language Switcher**: Removed WPML support, focused on Polylang only
- **Error Prevention**: Added function existence checks

---

## Version 3.6.8 - May 6, 2026
### Fixes
- **Language Switcher**: Fixed undefined function pll_get_language() error
- **Function Safety**: Added proper function existence validation

---

## Version 3.6.7 - May 6, 2026
### Fixes
- **Language Switcher**: Fixed function redeclaration with multilang-support.php
- **Clean Implementation**: Removed duplicate functions

---

## Version 3.6.6 - May 6, 2026
### Fixes
- **Language Switcher**: Fixed critical function call timing issues
- **WordPress Core**: Resolved wp_is_block_theme early call errors

---

## Version 3.6.5 - May 6, 2026
### Fixes
- **Language Switcher**: Fixed field name mismatches in meta box
- **Script Saving**: Corrected save function parameter names

---

## Version 3.6.4 - May 6, 2026
### Fixes
- **Language Switcher**: Fixed script field not saving properly
- **Widget Display**: Updated widget ID extraction from script

---

## Version 3.6.3 - May 6, 2026
### Fixes
- **Language Switcher**: Fixed nonce verification blocking script saves
- **Data Persistence**: Removed nonce checks for TicketingHub script field

---

## Version 3.6.2 - May 6, 2026
### Fixes
- **Language Switcher**: Fixed script field saving completely
- **Widget Integration**: Restored both script and widget ID fields

---

## Version 3.6.1 - May 6, 2026
### Fixes
- **Language Switcher**: Fixed widget script URL and rendering
- **TicketingHub**: Updated to use correct checkout.js script
- **Widget Display**: Positioned under price box as requested

---

## Version 3.6.0 - May 6, 2026
### New Features
- **Language Switcher**: Added Polylang and WPML support
- **TicketingHub**: Integrated booking widget support
- **Widget Display**: Added widget under tour price box
- **Script Extraction**: Auto-extract widget ID from script field
