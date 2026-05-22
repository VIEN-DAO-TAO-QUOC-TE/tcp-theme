<?php
// 4) Enqueue Swiper CDN (hunt-swiper hero) and business category card assets
if (!function_exists('tcp_enqueue_home_courses_slider')) {
	add_action('wp_enqueue_scripts', function() {
		// Swiper CDN — used by hunt-swiper on homepage hero section
		wp_enqueue_style('swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css');
		wp_enqueue_script('swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', [], null, true);
		// Business category card style
		wp_enqueue_style('tcp-business-category-card', get_stylesheet_directory_uri() . '/assets/css/business-category-card.css');
		// Home courses: Flickity grid override (mobile-only slider, grid on tablet+desktop)
		wp_enqueue_style('tcp-home-courses-slider', get_stylesheet_directory_uri() . '/assets/css/home-courses-slider.css');
	});
}

defined('ABSPATH') || exit;

// 1) Load helpers (function tcp(), view helpers, class merge...)
require_once __DIR__ . '/inc/Support/helpers.php';

// 2) Register autoloader
require_once __DIR__ . '/inc/Core/Autoloader.php';
\TCP\Theme\Core\Autoloader::register();

// 3) Boot theme (Singleton)
\TCP\Theme\Theme::instance()->boot();

// Make frontend search universal: strip post_type=product from query vars when
// a search keyword is present, so search.php handles both Woo and core results.
// This does not modify the header search form HTML (Flatsome styling intact).
add_filter('request', function ($query_vars) {
    if (!is_admin()
        && !empty($query_vars['s'])
        && isset($query_vars['post_type'])
        && $query_vars['post_type'] === 'product'
    ) {
        unset($query_vars['post_type']);
    }
    return $query_vars;
});

// Force theme search.php to render whenever is_search() is true (defensive
// fallback for any other entry point that may bypass the request filter).
add_filter('template_include', function ($template) {
    if (is_search()) {
        $custom = locate_template('search.php');
        if (!empty($custom)) {
            return $custom;
        }
    }
    return $template;
}, 99);

// Floating chat icons (Zalo & Viber) injected site-wide in footer
add_action('wp_footer', function() {
		$viber = esc_url(get_stylesheet_directory_uri() . '/assets/images/icons/Viber.png');
		$zalo  = esc_url(get_stylesheet_directory_uri() . '/assets/images/icons/Zalo.png');
		?>
		<div class="tcp-floating-chats" aria-hidden="false">
			<!-- <a class="tcp-chat tcp-chat-viber" href="#" title="Viber" rel="noopener" aria-label="Viber contact">
				<img src="<?php echo $viber; ?>" alt="Viber">
			</a> -->
			<a class="tcp-chat tcp-chat-zalo" href="#" title="Zalo" rel="noopener" aria-label="Zalo contact">
				<img src="<?php echo $zalo; ?>" alt="Zalo">
			</a>
		</div>
		<style>
			.tcp-floating-chats{position:fixed;right:18px;bottom: 5%;transform:translateY(-50%);display:flex;flex-direction:column;gap:12px;z-index:99999}
			.tcp-floating-chats .tcp-chat{width:56px;height:auto;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;overflow:hidden}
			.tcp-floating-chats .tcp-chat img{width:50px;height:50px;display:block}
			@media (max-width:768px){.tcp-floating-chats{right:12px}}
		</style>
		<?php
});