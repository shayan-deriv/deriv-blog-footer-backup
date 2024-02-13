<?php

/**
 * Theme functions and definitions
 *
 * @package HelloElementor
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

define('HELLO_ELEMENTOR_VERSION', '2.6.1');

if (!isset($content_width)) {
	$content_width = 800; // Pixels.
}

if (!function_exists('hello_elementor_setup')) {
	/**
	 * Set up theme support.
	 *
	 * @return void
	 */
	function hello_elementor_setup()
	{
		if (is_admin()) {
			hello_maybe_update_theme_version_in_db();
		}

		$hook_result = apply_filters_deprecated('elementor_hello_theme_load_textdomain', [true], '2.0', 'hello_elementor_load_textdomain');
		if (apply_filters('hello_elementor_load_textdomain', $hook_result)) {
			load_theme_textdomain('hello-elementor', get_template_directory() . '/languages');
		}

		$hook_result = apply_filters_deprecated('elementor_hello_theme_register_menus', [true], '2.0', 'hello_elementor_register_menus');
		if (apply_filters('hello_elementor_register_menus', $hook_result)) {
			register_nav_menus(['menu-1' => __('Header', 'hello-elementor')]);
			register_nav_menus(['menu-2' => __('Footer', 'hello-elementor')]);
		}

		$hook_result = apply_filters_deprecated('elementor_hello_theme_add_theme_support', [true], '2.0', 'hello_elementor_add_theme_support');
		if (apply_filters('hello_elementor_add_theme_support', $hook_result)) {
			add_theme_support('post-thumbnails');
			add_theme_support('automatic-feed-links');
			add_theme_support('title-tag');
			add_theme_support(
				'html5',
				[
					'search-form',
					'comment-form',
					'comment-list',
					'gallery',
					'caption',
					'script',
					'style',
				]
			);
			add_theme_support(
				'custom-logo',
				[
					'height'      => 100,
					'width'       => 350,
					'flex-height' => true,
					'flex-width'  => true,
				]
			);

			/*
			 * Editor Style.
			 */
			add_editor_style('classic-editor.css');

			/*
			 * Gutenberg wide images.
			 */
			add_theme_support('align-wide');

			/*
			 * WooCommerce.
			 */
			$hook_result = apply_filters_deprecated('elementor_hello_theme_add_woocommerce_support', [true], '2.0', 'hello_elementor_add_woocommerce_support');
			if (apply_filters('hello_elementor_add_woocommerce_support', $hook_result)) {
				// WooCommerce in general.
				add_theme_support('woocommerce');
				// Enabling WooCommerce product gallery features (are off by default since WC 3.0.0).
				// zoom.
				add_theme_support('wc-product-gallery-zoom');
				// lightbox.
				add_theme_support('wc-product-gallery-lightbox');
				// swipe.
				add_theme_support('wc-product-gallery-slider');
			}
		}
	}
}

add_action('after_setup_theme', 'hello_elementor_setup');

function hello_maybe_update_theme_version_in_db()
{
	$theme_version_option_name = 'hello_theme_version';
	// The theme version saved in the database.
	$hello_theme_db_version = get_option($theme_version_option_name);

	// If the 'hello_theme_version' option does not exist in the DB, or the version needs to be updated, do the update.
	if (!$hello_theme_db_version || version_compare($hello_theme_db_version, HELLO_ELEMENTOR_VERSION, '<')) {
		update_option($theme_version_option_name, HELLO_ELEMENTOR_VERSION);
	}
}

if (!function_exists('hello_elementor_scripts_styles')) {
	/**
	 * Theme Scripts & Styles.
	 *
	 * @return void
	 */
	function hello_elementor_scripts_styles()
	{
		$enqueue_basic_style = apply_filters_deprecated('elementor_hello_theme_enqueue_style', [true], '2.0', 'hello_elementor_enqueue_style');
		$min_suffix          = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		if (apply_filters('hello_elementor_enqueue_style', $enqueue_basic_style)) {
			wp_enqueue_style(
				'hello-elementor',
				get_template_directory_uri() . '/style' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		if (apply_filters('hello_elementor_enqueue_theme_style', true)) {
			wp_enqueue_style(
				'hello-elementor-theme-style',
				get_template_directory_uri() . '/theme' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}
	}
}
add_action('wp_enqueue_scripts', 'hello_elementor_scripts_styles');

if (!function_exists('hello_elementor_register_elementor_locations')) {
	/**
	 * Register Elementor Locations.
	 *
	 * @param ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $elementor_theme_manager theme manager.
	 *
	 * @return void
	 */
	function hello_elementor_register_elementor_locations($elementor_theme_manager)
	{
		$hook_result = apply_filters_deprecated('elementor_hello_theme_register_elementor_locations', [true], '2.0', 'hello_elementor_register_elementor_locations');
		if (apply_filters('hello_elementor_register_elementor_locations', $hook_result)) {
			$elementor_theme_manager->register_all_core_location();
		}
	}
}
add_action('elementor/theme/register_locations', 'hello_elementor_register_elementor_locations');

if (!function_exists('hello_elementor_content_width')) {
	/**
	 * Set default content width.
	 *
	 * @return void
	 */
	function hello_elementor_content_width()
	{
		$GLOBALS['content_width'] = apply_filters('hello_elementor_content_width', 800);
	}
}
add_action('after_setup_theme', 'hello_elementor_content_width', 0);

if (is_admin()) {
	require get_template_directory() . '/includes/admin-functions.php';
}

/**
 * If Elementor is installed and active, we can load the Elementor-specific Settings & Features
 */

// Allow active/inactive via the Experiments
require get_template_directory() . '/includes/elementor-functions.php';

/**
 * Include customizer registration functions
 */
function hello_register_customizer_functions()
{
	if (is_customize_preview()) {
		require get_template_directory() . '/includes/customizer-functions.php';
	}
}
add_action('init', 'hello_register_customizer_functions');

if (!function_exists('hello_elementor_check_hide_title')) {
	/**
	 * Check hide title.
	 *
	 * @param bool $val default value.
	 *
	 * @return bool
	 */
	function hello_elementor_check_hide_title($val)
	{
		if (defined('ELEMENTOR_VERSION')) {
			$current_doc = Elementor\Plugin::instance()->documents->get(get_the_ID());
			if ($current_doc && 'yes' === $current_doc->get_settings('hide_title')) {
				$val = false;
			}
		}
		return $val;
	}
}
add_filter('hello_elementor_page_title', 'hello_elementor_check_hide_title');

/**
 * Wrapper function to deal with backwards compatibility.
 */
if (!function_exists('hello_elementor_body_open')) {
	function hello_elementor_body_open()
	{
		if (function_exists('wp_body_open')) {
			wp_body_open();
		} else {
			do_action('wp_body_open');
		}
	}
}

if (!function_exists('get_user_ip')) {
	function get_user_ip()
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {

			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
	}
}

if (!function_exists('is_user_from_eu')) {
	function is_user_from_eu()
	{
		$is_user_from_eu = false;
		$eu_countries = [
			'it',
			'de',
			'fr',
			'lu',
			'gr',
			'mf',
			'es',
			'sk',
			'lt',
			'nl',
			'at',
			'bg',
			'si',
			'cy',
			'be',
			'ro',
			'hr',
			'pt',
			'pl',
			'lv',
			'ee',
			'cz',
			'fi',
			'hu',
			'dk',
			'se',
			'ie',
			'im',
			'mt',
		];

		$user_ip = get_user_ip();
		// $user_info = json_decode(file_get_contents("https://ipinfo.io/$user_ip/geo"), true);
		$user_info = json_decode(file_get_contents("http://ip-api.com/php/$user_ip"), true);

		$user_country  = strtolower($user_info['countryCode']);
		if (in_array($user_country, $eu_countries))
			$is_user_from_eu = true;

		return [$is_user_from_eu, $user_country, $user_ip];
	}
}

add_action('elementor/query/filter_eu_posts', function ($query) {

	$is_user_from_eu = is_user_from_eu();

	if ($is_user_from_eu) {
		$meta_query = array(
			'post_status' => 'publish',
			'meta_query' => array(
				'relation' => 'OR',
				array(
					'key' => 'is_eu',
					'compare' => '=',
					'value' => 1
				),
				array(
					'key' => 'is_eu',
					'compare' => 'NOT EXISTS'
				)
			),
		);
	} else {
		$meta_query = array(
			'post_status' => 'publish',
			'meta_query' => array(
				array(
					'key' => 'is_eu',
					'compare' => 'NOT EXISTS'
				)
			),
		);
	}

	$query->set('meta_query', $meta_query);
});

// add_action('wp_footer', 'my_custom_footer_scripts');

// function my_custom_footer_scripts() {
// 		echo "<div style='background:red;padding:25px;position:fixed;bottom:0;right:0;left:0;z-index:100000'>this is my custom footer</div>";
// }

// customize footer
function my_custom_styles()
{
	// Register my custom stylesheet
	wp_register_style('custom-styles', get_template_directory_uri() . '/deriv-style.css?v=66');
	// Load my custom stylesheet
	wp_enqueue_style('custom-styles');
}
add_action('wp_enqueue_scripts', 'my_custom_styles');

add_action('wp_footer', 'my_custom_footer_scripts');


function my_custom_footer_scripts()
{
	$is_eu = false;
	// $u_c = is_user_from_eu()[1];
	// $u_ip = is_user_from_eu()[2];
	$base_url = $is_eu ? 'https://eu.deriv.com' : 'https://deriv.com';
	$eu_text = $is_eu ? 'eu' : '';
	$social_media_links = [
		'facebook' => $is_eu ? 'https://www.facebook.com/derivEU' : 'https://www.facebook.com/derivdotcom',
		'instagram' => $is_eu ? 'https://www.instagram.com/deriv_eu/' : 'https://www.instagram.com/deriv_official/',
		'twitter' => $is_eu ? 'https://www.twitter.com/deriv_eu/' : 'https://twitter.com/derivdotcom/',
		'youtube' => 'https://www.youtube.com/@deriv',
		'linkedin' => 'https://www.linkedin.com/company/derivdotcom/',
		// 'telegram' => 'https://t.me/derivdotcomofficial',
	];
	echo "
	<footer style='margin-bottom:80px'>
	<div class='footer--brand'>
		<div class='footer--logo'>
			<img src='https://blog.deriv.com/wp-content/uploads/2023/04/deriv-new-logo.svg' alt='logo'>
		</div>
		<div class='social-media'>
			<a href='" . $social_media_links["facebook"] . "'><img id='fb-icon' src='https://blog.deriv.com/wp-content/uploads/2023/04/facebook.svg' alt='facebook'></a>
			<a href='" . $social_media_links["instagram"] . "'><img src='https://blog.deriv.com/wp-content/uploads/2023/04/instagram.svg' alt='instagram'></a>
			<a href='" . $social_media_links["twitter"] . "'><img src='https://blog.deriv.com/wp-content/uploads/2023/04/twitter.svg' alt='twitter'></a>
			<a href='" . $social_media_links["youtube"] . "'><img src='https://blog.deriv.com/wp-content/uploads/2023/04/youtube.svg' alt='youtube'></a>
			<a href='" . $social_media_links["linkedin"] . "'><img src='https://blog.deriv.com/wp-content/uploads/2023/04/linkedin.svg' alt='linkedin'></a>";
	// if (!$is_eu) echo "<a class='row_content' href='" . $social_media_links["telegram"] . "'><img src='https://blog.deriv.com/wp-content/uploads/2023/04/telegram.svg' alt='telegram'></a>";
	echo "</div>
	</div>
	<div class='footer--menu'>
		<ul class='footer--menu--col'>
			<h3>ABOUT US</h3>
			<a href='$base_url/who-we-are/'>
				<li>Who we are</li>
			</a>
			<a href='$base_url/why-choose-us/'>
				<li>Why choose us</li>
			</a>
			<a href='$base_url/our-principles/'>
				<li>Principles</li>
			</a>
			<a href='$base_url/partners/'>
				<li>Partnership programmes</li>
			</a>
			<a href='$base_url/contact_us/'>
				<li>Contact us</li>
			</a>
			<a href='$base_url/careers/'>
				<li>Careers</li>
			</a>
			<a href='https://derivlife.com/'>
				<li>Deriv life</li>
			</a>
			<!--<a href='$base_url/'>
				<li id='investers-in-people'>
					<img src='https://blog.deriv.com/wp-content/uploads/2023/04/investers-in-people.svg' />
				</li>
			</a>
			--!>
		</ul>
		<ul class='footer--menu--col'>
			<h3>TRADE TYPES</h3>
			<a href='$base_url/trade-types/cfds/'>
				<li>CFDs</li>
			</a>";
	if (!$is_eu)
		echo "
				<a class='row_content' href='$base_url/trade-types/options/'>
					<li>Options</li>
				</a>
			";
	echo
	"<a href='$base_url/trade-types/multiplier/'>
				<li>Multipliers</li>
			</a>
		</ul>
		<ul class='footer--menu--col'>
			<h3>MARKETS</h3>
			<a href='$base_url/markets/forex/'>
				<li>Forex</li>
			</a>
			<a href='$base_url/markets/synthetic/'>
				<li>Derived</li>
			</a>
			<a href='$base_url/markets/stock/'>
				<li>Stocks & indices</li>
			</a>
			<a class='row_content' href='$base_url/markets/exchange-traded-funds/'>
				<li>ETF</li>
			</a>
			<a href='$base_url/markets/cryptocurrencies/'>
				<li>Cryptocurrencies</li>
			</a>
			<a href='$base_url/markets/commodities/'>
				<li>Commodities</li>
			</a>
		</ul>
		<ul class='footer--menu--col'>
			<h3>PLATFORMS</h3>
			<a href='$base_url/dmt5/'>
				<li>Deriv MT5</li>
			</a>";

	if (!$is_eu)
		echo "<a class='row_content' href='$base_url/derivx/'>
				<li>Deriv X</li>
			</a>
			<a class='row_content' href='$base_url/deriv-go/'>
				<li>Deriv GO</li>
			</a>
			";

	echo "<a href='$base_url/dtrader/'>
				<li>Deriv Trader</li>
			</a>";
	if (!$is_eu)
		echo "<a class='row_content' href='https://smarttrader.deriv.com/'>
				<li>SmartTrader</li>
			</a>
			<a class='diel_content' href='$base_url/dbot/'>
				<li>Deriv Bot</li>
			</a>
			<a class='diel_content' href='https://bot.deriv.com/'>
				<li>Binary Bot</li>
			</a>
			<a class='diel_content' href='$base_url/deriv-ctrader/'>
				<li>Deriv cTrader</li>
			</a>";
	echo "</ul>
		<ul class='footer--menu--col'>
			<h3>LEGAL</h3>
			<a href='$base_url/regulatory/'>
				<li>Regulatory information</li>
			</a>
			<a href='$base_url/terms-and-conditions/#clients'>
				<li>Terms & conditions</li>
			</a>
			<a href='$base_url/responsible/'>
				<li>Secure & responsible trading</li>
			</a>
		</ul>
		<ul class='footer--menu--col'>
			<h3>PARTNER</h3>
			<a href='$base_url/partners/affiliate-ib/'>
				<li>Affiliates and IBs</li>
			</a>";
	if (!$is_eu)
		echo "<a class='row_content' href='$base_url/partners/payment-agent/'>
				<li>Payment agents</li>
			</a>";

	echo "<a href='https://api.deriv.com/'>
				<li>API</li>
			</a>
			<a href='https://hackerone.com/deriv?type=team'>
				<li>Bug bounty</li>
			</a>
		</ul>
		<ul class='footer--menu--col'>
			<h3>SUPPORT</h3>
			<a href='$base_url/help-centre/'>
				<li>Help centre</li>
			</a>
			<a href='https://community.deriv.com/'>
				<li>Community</li>
			</a>
			<a href='$base_url/payment-methods/'>
				<li>Payment methods</li>
			</a>
			<a href='https://deriv.statuspage.io/'>
				<li>Status page</li>
			</a>
			<a href='https://blog.deriv.com/'>
				<li class='active'>Deriv Blog</li>
			</a>
		</ul>


	</div>
	<div class='licenses'>
		<p class='license row_content'>
			Deriv Investments <a href='$base_url/regulatory/Deriv_Investments_(Europe)_Limited.pdf'>(Europe)</a> Limited is licensed and regulated by the Malta Financial Services Authority, Triq L-Imdina, Zone 1, Central Business District, Birkirkara CBD 1010, Malta, under the Investment Services Act (licence). The registered office of Deriv Investments (Europe) Limited is at W Business Centre, Level 3, Triq Dun Karm, Birkirkara BKR9033, Malta.
		</p>

		<p class='license row_content'>
			Deriv (FX) Ltd is licensed by the Labuan Financial Services Authority <a href='$base_url/regulatory/Deriv_(FX)_Ltd.pdf'>(licence)</a>. Deriv (BVI) Ltd is licensed by the British Virgin Islands Financial Services Commission <a href='$base_url/regulatory/Deriv_(BVI)_Ltd.pdf'>(licence)</a>. Deriv (V) Ltd is licensed and regulated by the Vanuatu Financial Services Commission <a href='$base_url/regulatory/Deriv_(V)_Ltd.pdf'>(licence)</a>. Deriv (SVG) LLC has a registered office at First Floor, SVG Teachers Credit Union Uptown Building, Corner of James and Middle Street, Kingstown P.O., St Vincent and the Grenadines.
		</p>

		<p class='license row_content'>
			Deriv.com Limited is the holding company for the above subsidiaries with the registration number 71479 and the registered address of 2nd Floor, 1 Cornet Street, St Peter Port, Guernsey, GY1 1BZ.
		</p>

		<p class='license row_content'>
			For complete regulatory information, click <a href='https://deriv.com/regulatory/'>here</a>.
		</p>

		<p class='license row_content'>
			This website's services are not available in certain countries, including the USA, Canada, and Hong Kong, or to persons below 18.
		</p>

		<p class='license row_content'>
			The information contained in the Blog is for educational purposes only.
		</p>

		<p class='license diel_content'>
			Please read our <a href='https://deriv.com/terms-and-conditions/#clients' target='_blank'>Terms
			and conditions</a>, <a href='https://deriv.com/tnc/risk-disclosure.pdf' target='_blank'>Risk
			disclosure</a>, and <a href='https://deriv.com/responsible/' target='_blank'>Secure and responsible trading
			</a> to fully understand the risks involved before using our services. The information on this website does not constitute investment advice.
		</p>

	</div>";
	echo "</footer>
	";
	echo "<div class='eu-disclaimer eu_content'>
		<p>
			CFDs and other products offered on this website are complex instruments with high risk of losing money rapidly owing to leverage. <b>73% of retail investor accounts lose money when trading CFDs with Deriv.</b> You should consider whether you understand how these products work and whether you can afford to risk losing your money.
		</p>
	</div>
	<div class='eu-disclaimer diel_content'>
		<p>
			CFDs and other products offered on this website are complex instruments with high risk of losing money rapidly owing to leverage. <b>70.1% of retail investor accounts lose money when trading CFDs with Deriv.</b> You should consider whether you understand how these products work and whether you can afford to risk losing your money.
		</p>
	</div>";
}

// allow to upload SVG files in media library

add_filter('wp_check_filetype_and_ext', function ($data, $file, $filename, $mimes) {
	global $wp_version;
	if ($wp_version !== '4.7.1') return $data;

	$filetype = wp_check_filetype($filename, $mimes);

	return [
		'ext'				=>	$filetype['ext'],
		'type'				=>	$filetype['type'],
		'proper_filename'	=>	$filetype['proper_filename']
	];
}, 10, 4);

function cc_mime_types($mimes)
{
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
}

add_filter('upload_mimes', 'cc_mime_types');

function fix_svg()
{
	echo '<style type"text/css"> .attachment-266x266, .thumbnail img{width:100% !important; height: auto !important;} </style>';
}

add_action('admin_head', 'fix_svg');
