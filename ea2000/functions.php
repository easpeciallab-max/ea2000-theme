<?php
/**
 * EA2000 · Theme functions
 *
 * @package ea2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'EA2000_VERSION', '1.0.0' );

/* --------------------------------------------------------------
 * Theme setup
 * -------------------------------------------------------------- */
function ea2000_setup() {
	load_theme_textdomain( 'ea2000', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'elementor' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 120,
			'width'       => 120,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	register_nav_menus(
		array(
			'primary' => 'เมนูหลัก (Header)',
			'footer'  => 'เมนูท้ายเว็บ (Footer)',
		)
	);
}
add_action( 'after_setup_theme', 'ea2000_setup' );

/* --------------------------------------------------------------
 * Styles & scripts
 * -------------------------------------------------------------- */
function ea2000_assets() {
	$style_path    = get_stylesheet_directory() . '/style.css';
	$script_path   = get_template_directory() . '/assets/js/main.js';
	$style_version = file_exists( $style_path ) ? filemtime( $style_path ) : EA2000_VERSION;
	$script_version = file_exists( $script_path ) ? filemtime( $script_path ) : EA2000_VERSION;

	wp_enqueue_style(
		'ea2000-fonts',
		'https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@500;600;700&family=Bai+Jamjuree:wght@400;500;600;700&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'ea2000-style', get_stylesheet_uri(), array( 'ea2000-fonts' ), $style_version );
	wp_enqueue_script( 'ea2000-main', get_template_directory_uri() . '/assets/js/main.js', array(), $script_version, true );

	/* หน้าแรก v3 (Control Room) เท่านั้น · home.js ใช้ window.ea2000 จาก main.js จึงต้องพึ่ง ea2000-main */
	if ( is_front_page() ) {
		$home_path = get_template_directory() . '/assets/js/home.js';
		wp_enqueue_script( 'ea2000-home', get_template_directory_uri() . '/assets/js/home.js', array( 'ea2000-main' ), file_exists( $home_path ) ? filemtime( $home_path ) : EA2000_VERSION, true );
	}

	wp_localize_script(
		'ea2000-main',
		'ea2000LoadMore',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'ea2000_load_more' ),
		)
	);
	if ( ea2000_mod( 'ga_measurement_id' ) || ea2000_mod( 'fb_pixel_id' ) ) {
		wp_localize_script(
			'ea2000-main',
			'ea2000Tracking',
			array(
				'ga'    => ea2000_mod( 'ga_measurement_id' ),
				'pixel' => ea2000_mod( 'fb_pixel_id' ),
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'ea2000_assets' );

/**
 * Preload รูปกล่องสินค้าใน hero (LCP element ของหน้าแรก)
 * พิมพ์เฉพาะหน้าแรกที่เปิด hero และมี hero_image · ต้องตรงกับ src ของ <img> ใน front-page.php
 */
function ea2000_preload_hero() {
	if ( ! is_front_page() || ! ea2000_mod( 'show_hero' ) ) {
		return;
	}

	/* หน้าแรกที่ใช้ Elementor เต็มหน้าจะไม่พิมพ์ hero ของธีม */
	if ( ea2000_has_elementor_content() && ea2000_uses_elementor_page_template() ) {
		return;
	}

	$src = trim( (string) ea2000_mod( 'hero_image' ) );
	if ( '' === $src ) {
		return;
	}

	printf( '<link rel="preload" as="image" href="%s" fetchpriority="high">' . "\n", esc_url( $src ) );
}
add_action( 'wp_head', 'ea2000_preload_hero', 1 );

/**
 * รายการ HUD ใต้ hero (hero_hud_items) · บรรทัดละ "ป้าย|ค่า"
 * ข้ามบรรทัดที่ไม่มี | และบรรทัดที่อ่านเป็นผลเทรดได้ (ค่าขึ้นต้นด้วยตัวเลขแล้วตามด้วย % หรือป้าย/ค่ามีคำว่า กำไร)
 * เพื่อให้ HUD แสดงได้เฉพาะข้อเท็จจริงของระบบ ไม่ใช่ตัวเลขผลการเทรด
 *
 * @return array รายการ array( 'label' => ..., 'value' => ... )
 */
function ea2000_hero_hud_items() {
	$items = array();

	foreach ( ea2000_lines( ea2000_mod( 'hero_hud_items' ) ) as $ea2000_hud_line ) {
		if ( false === strpos( $ea2000_hud_line, '|' ) ) {
			continue;
		}

		list( $ea2000_hud_label, $ea2000_hud_value ) = array_map( 'trim', explode( '|', $ea2000_hud_line, 2 ) );
		if ( '' === $ea2000_hud_label || '' === $ea2000_hud_value ) {
			continue;
		}

		if ( preg_match( '/^[+-]?\d[\d.,]*\s*%/u', $ea2000_hud_value ) || false !== strpos( $ea2000_hud_line, 'กำไร' ) ) {
			continue;
		}

		$items[] = array(
			'label' => $ea2000_hud_label,
			'value' => $ea2000_hud_value,
		);
	}

	return $items;
}

/* --------------------------------------------------------------
 * Builder compatibility
 * -------------------------------------------------------------- */
function ea2000_is_elementor_page( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_queried_object_id();
	}

	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	return $post_id && 'builder' === get_post_meta( $post_id, '_elementor_edit_mode', true );
}

function ea2000_elementor_data_has_widgets( $elements ) {
	if ( ! is_array( $elements ) ) {
		return false;
	}

	foreach ( $elements as $element ) {
		if ( ! empty( $element['widgetType'] ) ) {
			return true;
		}

		if ( ! empty( $element['elements'] ) && ea2000_elementor_data_has_widgets( $element['elements'] ) ) {
			return true;
		}
	}

	return false;
}

function ea2000_has_elementor_content( $post_id = null ) {
	if ( ! ea2000_is_elementor_page( $post_id ) ) {
		return false;
	}

	if ( ! $post_id ) {
		$post_id = get_queried_object_id();
	}

	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$elementor_data = get_post_meta( $post_id, '_elementor_data', true );
	if ( empty( $elementor_data ) ) {
		return false;
	}

	$elements = json_decode( $elementor_data, true );

	return ea2000_elementor_data_has_widgets( $elements );
}

function ea2000_uses_elementor_page_template( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_queried_object_id();
	}

	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$template = $post_id ? get_page_template_slug( $post_id ) : '';

	return in_array(
		$template,
		array(
			'elementor_canvas',
			'elementor_header_footer',
			'template-elementor-canvas.php',
			'template-elementor-full-width.php',
		),
		true
	);
}

function ea2000_body_classes( $classes ) {
	if ( is_page() && ea2000_is_elementor_page( get_queried_object_id() ) ) {
		$classes[] = 'ea2000-has-elementor';
	}

	return $classes;
}
add_filter( 'body_class', 'ea2000_body_classes' );

/* --------------------------------------------------------------
 * Default content (ทุกค่าแก้ได้ในหน้า "ปรับแต่ง / Customize")
 * -------------------------------------------------------------- */
function ea2000_defaults() {
	static $d = null;
	if ( null !== $d ) {
		return $d;
	}

	$install_assets = get_template_directory_uri() . '/assets/img/install/';

	$img_assets = get_template_directory_uri() . '/assets/img/';

	$d = array(
		/* แบรนด์ */
		'brand_name'           => 'EA2000',
		'brand_tagline'        => 'EA for MT5',
		'brand_wordmark'       => $img_assets . 'wordmark.webp', // โลโก้แนวนอน (เว้นว่าง = แสดงโลโก้กลม + ชื่อแบรนด์)
		'product_name'         => 'EA2000', // ชื่อสินค้าที่ส่งให้ Google ในข้อมูลโครงสร้าง (SoftwareApplication) · เว้นว่าง = ใช้ชื่อแบรนด์
		'product_os'           => 'Windows', // ระบบปฏิบัติการที่รันโปรแกรมได้จริง (operatingSystem) · MT5 เป็นแพลตฟอร์ม ไม่ใช่ OS จึงอยู่ในช่องถัดไป
		'product_requirements' => 'MetaTrader 5', // แพลตฟอร์มที่ต้องมี (softwareRequirements)
		'product_version'      => '', // เวอร์ชันของ EA (softwareVersion) · เว้นว่าง = ไม่ประกาศเวอร์ชัน

		/* ทั่วไป (เว้นว่าง = ซ่อนปุ่ม/ลิงก์นั้น จนกว่าเจ้าของจะกรอก) */
		'line_url'        => '',
		'facebook_url'    => '',
		'instagram_url'   => '',
		'tiktok_url'      => '',
		'youtube_url'     => '',
		'contact_email'   => '',
		'show_float_line' => false,
		'float_line_text' => 'สอบถามทาง LINE',
		'contact_fallback_text' => 'ติดต่อทีมงาน', // ข้อความปุ่มติดต่อเมื่อยังไม่กรอก LINE OA (ชี้ไปหน้า /go/) · ใช้ทั้งหน้าแรกและแถบท้ายเว็บ
		'show_language_switcher' => true,
		'language_fallback_items' => "th|🇹🇭|TH|ไทย\nen|🇬🇧|EN|English\nzh|🇨🇳|ZH|中文\nfr|🇫🇷|FR|Français\nde|🇩🇪|DE|Deutsch\nru|🇷🇺|RU|Русский\nja|🇯🇵|JA|日本語\nko|🇰🇷|KO|한국어",

		/* Mobile bottom bar */
		'show_mobile_nav'         => true,
		'mobile_nav_home_label'   => 'หน้าแรก',
		'mobile_nav_home_url'     => '/',
		'mobile_nav_test_label'   => 'ผลทดสอบ',
		'mobile_nav_test_url'     => '/forward-test/',
		'mobile_nav_price_label'   => 'แพ็กเกจ',
		'mobile_nav_price_url'     => '/pricing/',
		'mobile_nav_install_label' => 'วิธีติดตั้ง',
		'mobile_nav_install_url'   => '/how-to-install/',
		'mobile_nav_line_label'    => 'ทัก LINE',

		/* SEO / แชร์ลิงก์ (Open Graph) */
		'og_default_image'       => $img_assets . 'og-share.jpg',
		'og_default_description' => 'EA2000 · ระบบช่วยเทรดอัตโนมัติ (Expert Advisor) บน MetaTrader 5 เน้นวินัยและการบริหารความเสี่ยง',
		'search_console_verify'  => '',
		'bing_verify'            => '',

		/* หน้า Link Hub (template-links.php) · ปลายทางยิงแอด สไตล์ Linktree */
		'links_title'      => 'EA2000',
		'links_tagline'    => 'ระบบช่วยเทรดอัตโนมัติบน MetaTrader 5 เน้นบริหารความเสี่ยง มีทีมไทยดูแล',
		'links_badges'     => '',
		'links_logo'       => '',
		'links_signup_label' => 'เปิดบัญชี MT5',
		'links_signup_url'   => '',
		'links_account_guide_label' => 'วิธีเปิดบัญชี MT5',
		'links_account_guide_url'   => '',
		'links_mt5_download_label'  => 'คู่มือติดตั้ง MT5',
		'links_mt5_download_url'    => '',
		'links_line_label' => 'ทัก LINE ปรึกษาทีมงาน',
		'links_openchat_label' => 'เข้ากลุ่ม EA2000 OpenChat',
		'links_openchat_url'   => '',
		'links_fast_enabled' => true,
		'links_fast_img'     => $img_assets . 'card-download.webp',
		'links_fast_url'     => '',
		'links_fast_alt'     => 'ดาวน์โหลด EA2000 สำหรับ MetaTrader 5',
		'links_feature_img'     => $img_assets . 'card-download.webp',
		'links_feature_url'     => '',
		'links_feature_caption' => '',
		'links_feature2_img'     => '',
		'links_feature2_url'     => '',
		'links_feature2_caption' => '',
		'links_feature2_placeholder' => '1200 × 630',
		'links_btn1_label' => 'ดูผล Forward Test',
		'links_btn1_url'   => '/forward-test/',
		'links_btn2_label' => 'แพ็กเกจ & ราคา',
		'links_btn2_url'   => '/pricing/',
		'links_btn3_label' => '',
		'links_btn3_url'   => '',
		'links_btn4_label' => '',
		'links_btn4_url'   => '',
		'links_btn5_label' => '',
		'links_btn5_url'   => '',
		'links_btn6_label' => '',
		'links_btn6_url'   => '',
		'links_guide1_label' => 'คู่มือติดตั้ง EA2000',
		'links_guide1_url'   => '/how-to-install/',
		'links_guide2_label' => 'ใช้งาน VPS บน Windows',
		'links_guide2_url'   => '',
		'links_guide3_label' => 'ใช้งาน VPS ผ่าน Android',
		'links_guide3_url'   => '',
		'links_guide4_label' => 'ใช้งาน VPS ผ่าน iPhone / iOS',
		'links_guide4_url'   => '',
		'links_guide5_label' => 'ใช้งาน VPS บน macOS',
		'links_guide5_url'   => '',
		'links_note'       => 'การเทรด Forex, CFD และสินทรัพย์ทางการเงินอื่น ๆ มีความเสี่ยงสูง ผู้ใช้งานอาจขาดทุนได้ · EA2000 เป็นเครื่องมือช่วยเทรดตามเงื่อนไขที่กำหนด ไม่ใช่การรับประกันผลกำไร',

		/* คุกกี้ / Consent + Tracking (โหลด tracking เฉพาะหลังกดยอมรับ) */
		'show_cookie_consent' => false,
		'cookie_consent_text' => 'เว็บไซต์นี้ใช้คุกกี้เพื่อปรับปรุงประสบการณ์การใช้งานและวิเคราะห์การเข้าชม คุณเลือกยอมรับหรือปฏิเสธคุกกี้ที่ไม่จำเป็นได้',
		'ga_measurement_id'   => '',
		'fb_pixel_id'         => '',

		/* Hero · บล็อก 0 "Boot" (H1 = hero_title + hero_subtitle) · หน้าแรก v3 Control Room */
		'show_hero'      => true,
		'show_rail'      => true, // รางเลขบทด้านซ้าย (เดสก์ท็อป) และเส้น progress ใต้ header (จอเล็ก)
		'fig_label'      => 'ภาพ', // คำนำหน้าคำบรรยายภาพ เช่น ภาพ 01
		'hero_badge'     => 'Expert Advisor สำหรับ MetaTrader 5',
		'hero_title'     => 'EA2000',
		'hero_subtitle'  => 'EA MT5 ระบบเทรดอัตโนมัติ เพื่อการเทรดที่มีวินัย',
		'hero_desc'      => 'Expert Advisor สำหรับ MetaTrader 5 ใช้ได้กับหลายคู่เงิน ทำงานตามกฎที่ตั้งไว้ล่วงหน้า ไม่ตัดสินใจตามอารมณ์ ทุนและความเสี่ยงคุณกำหนดเอง',
		'hero_btn1_text' => 'สอบถามทาง LINE',
		'hero_btn2_text' => 'EA2000 ทำงานอย่างไร',
		'hero_note'      => 'การเทรดมีความเสี่ยง โปรดศึกษาข้อมูลก่อนตัดสินใจใช้งาน',
		'hero_image'     => $img_assets . 'hero-box.webp', // ภาพกล่องสินค้าพื้นโปร่ง 1000×1000
		'hero_img_alt'   => 'กล่องผลิตภัณฑ์ EA2000 Expert Advisor สำหรับ MT5',
		'hero_img_note'  => 'รูปที่ต้องใส่: ภาพกล่องสินค้าพื้นโปร่ง 1000x1000 px',
		/* แถบ HUD ใต้ปุ่ม · บรรทัดละ "ป้าย|ค่า" · แสดงเฉพาะข้อเท็จจริงของระบบ (ea2000_hero_hud_items() ตัดค่าที่เป็น % หรือคำว่า กำไร ทิ้ง) */
		'hero_hud_items' => "แพลตฟอร์ม|MetaTrader 5\nสินทรัพย์|หลายคู่เงิน\nรูปแบบ|เทรดอัตโนมัติตามกฎที่ตั้งไว้\nการส่งมอบ|ไฟล์ EA และคู่มือ",
		'hero_hud_note'  => 'ป้ายข้อมูลระบบ ไม่ใช่ผลการเทรด', // ข้อความสำหรับ screen reader อธิบายแถบ HUD

		/* ปัญหานักเทรด · บล็อก 2 Diagnostic ledger (แถวขีดฆ่า + แถวสรุปว่าระบบเข้ามาแทนอะไร) */
		'show_pain'     => true,
		'pain_kicker'   => 'ปัญหาของการเทรดมือ',
		'pain_title'    => 'ใช้ EA เทรด forex ดีไหม · ปัญหาที่เทรดเดอร์มือเองเจอ',
		'pain_subtitle' => 'ก่อนตอบว่า EA เทรด forex เหมาะกับคุณไหม ลองดูว่าคุณเจอปัญหาเหล่านี้บ่อยแค่ไหน นี่คือสิ่งที่ระบบเทรดอัตโนมัติออกแบบมาเพื่อแก้',
		'pain1_title'   => 'เข้าออเดอร์ตามอารมณ์',
		'pain1_desc'    => 'กลัวตกรถเลยรีบเข้า พอราคาย่อก็รีบออก สุดท้ายผลลัพธ์ไม่ตรงกับแผนที่วางไว้ตั้งแต่แรก',
		'pain2_title'   => 'ไม่มีเวลาเฝ้าจอ',
		'pain2_desc'    => 'ตลาด forex เปิดเกือบตลอด 24 ชั่วโมง แต่คุณมีงานประจำ จังหวะที่ดีมักมาตอนที่ไม่ได้ดูกราฟ',
		'pain3_title'   => 'คุม Lot ไม่เป็นระบบ',
		'pain3_desc'    => 'บางไม้ใหญ่เกินทุน พอร์ตแกว่งแรงจน Drawdown สูงกว่าที่รับไหว ทั้งที่รู้อยู่แล้วว่าไม่ควร',
		'pain4_title'   => 'ขาดวินัยตามแผน',
		'pain4_desc'    => 'วางแผนไว้ดี แต่พอกราฟวิ่งจริงกลับเปลี่ยนใจกลางทาง ระบบที่ทำตามกฎเดิมทุกครั้งช่วยตรงนี้ได้',
		'pain_resolved_label' => 'สิ่งที่ระบบอัตโนมัติเข้ามาแทน',
		'pain_resolved_text'  => 'ระบบทำตามกฎเดิมทุกครั้ง ส่วนทุน ความเสี่ยง และการตัดสินใจเริ่มหรือหยุดยังเป็นของคุณ',

		/* EA2000 คืออะไร (ชุดเดิม · คงไว้เพื่อความเข้ากันได้ · หน้าแรกใหม่ใช้บล็อก what_* ด้านล่างแทน) */
		'show_about'  => true,
		'about_title' => 'EA2000 คืออะไร?',
		'about_text'  => "EA2000 คือระบบช่วยเทรดอัตโนมัติ (Expert Advisor) บนแพลตฟอร์ม MetaTrader 5 ที่ทำงานตามเงื่อนไขที่กำหนดไว้ล่วงหน้า ช่วยให้การเข้าและออกออเดอร์เป็นระบบมากขึ้น ลดการตัดสินใจจากอารมณ์ และติดตามการทำงานของระบบได้ง่ายผ่าน Dashboard\n\nEA ไม่ใช่เครื่องมือการันตีกำไร แต่เป็นผู้ช่วยให้คุณเทรดตามแผนได้สม่ำเสมอขึ้น ภายใต้การบริหารความเสี่ยงที่คุณเป็นผู้กำหนดเอง",

		/* EA2000 คืออะไร · บล็อก 2 (what_*) · รูปเว้นว่าง = แสดงโครงรอใส่รูปพร้อม what_img_note */
		'show_what'     => true,
		'what_kicker'   => 'ระบบคืออะไร',
		'what_title'    => 'EA2000 คืออะไร · EA เทรดอัตโนมัติบน MT5 ทำงานอย่างไร',
		'what_text'     => "EA2000 คือ Expert Advisor หรือที่หลายคนเรียกว่าบอทเทรดและโรบอทเทรด สำหรับแพลตฟอร์ม MetaTrader 5 โปรแกรมติดตั้งบนกราฟ MT5 แล้วเฝ้าดูราคาแทนคุณตลอดเวลาที่ตลาดเปิด เมื่อราคาเข้าเงื่อนไขที่ตั้งไว้ล่วงหน้า ระบบจะเปิดออเดอร์ จัดการขนาดออเดอร์และเงื่อนไขปิดตามค่าที่ตั้งไว้ แล้วปิดเมื่อครบเงื่อนไข โดยไม่ต้องเฝ้าจอเอง\n\nEA2000 ใช้ได้กับหลายคู่เงินบนบัญชี MT5 ของโบรกเกอร์ที่คุณเลือก คุณกำหนดทุนและระดับความเสี่ยงเอง หยุดระบบได้ทุกเมื่อ สิ่งที่ระบบทำให้คือความสม่ำเสมอ: ทำตามกฎเดิมทุกครั้ง ไม่รีบเข้า ไม่ลังเลตอนควรออก EA ไม่ใช่เครื่องมือการันตีกำไร ผลลัพธ์ขึ้นกับตลาดและการตั้งค่าของคุณ",
		/* datasheet · บรรทัดละ "ป้าย|ข้อความ" (บรรทัดที่ไม่มี | จะใช้เลขลำดับเป็นป้าย) */
		'what_points'   => "แพลตฟอร์ม|ทำงานบน MetaTrader 5 โดยตรง ติดตั้งครั้งเดียวแล้วรันต่อเนื่องบนคอมพิวเตอร์หรือ VPS\nวินัย|ทำตามกฎเดิมทุกครั้ง ไม่ให้ความกลัวหรือความโลภมาแทรกการตัดสินใจ\nการควบคุม|ผู้ใช้กำหนดทุน ขนาดออเดอร์ และระดับความเสี่ยงเอง พร้อม Dashboard บนกราฟให้ตรวจสถานะได้ตลอด",
		'what_principle_label' => 'หลักการของเรา',
		'what_principle'       => 'เราไม่แสดงตัวเลขที่ยังตรวจสอบไม่ได้ และไม่รับประกันผลกำไร',
		'what_img'      => '',
		'what_img_alt'  => 'หน้าจอ MetaTrader 5 ขณะรัน EA2000',
		'what_img_note' => 'รูปที่ต้องใส่: ภาพหน้าจอ MT5 ขณะ EA2000 ทำงาน เห็นกราฟและแผง Dashboard ไม่ต้องเห็นตัวเลขบัญชี · แนะนำ 1280x800 px',

		/* ทำงานอย่างไร · บล็อก 4 (how_*) */
		'show_how'        => true,
		'how_kicker'      => 'ลำดับการทำงาน',
		'how_title'       => 'EA2000 ทำงานอย่างไรบน MetaTrader 5',
		'how_intro'       => 'บอทเทรด MT5 อย่าง EA2000 ทำงานเป็นวงจร 4 ขั้นซ้ำกันทุกวัน ตั้งแต่ตอนคุณตั้งค่าครั้งแรกไปจนถึงการติดตามผล',
		'how_step1_title' => 'ตั้งทุนและระดับความเสี่ยง',
		'how_step1_desc'  => 'กรอกทุนที่จะใช้กับระบบ เลือกระดับความเสี่ยงและขนาด Lot ให้สอดคล้องกับบัญชี MT5 ของคุณ ทีมงานมีไฟล์ Preset ให้ตามแพ็กเกจ เพื่อให้เริ่มจากค่าที่เหมาะกับทุน',
		'how_step2_title' => 'ระบบตรวจเงื่อนไขตลาดตามกฎ',
		'how_step2_desc'  => 'EA2000 อ่านราคาจากกราฟตลอดเวลาที่ตลาดเปิด แล้วเทียบกับเงื่อนไขที่ตั้งไว้ล่วงหน้า ถ้ายังไม่เข้าเงื่อนไขก็รอ ไม่เดาและไม่ไล่ราคา ทำงานแบบเดียวกันทุกวัน',
		'how_step3_title' => 'เปิดและปิดออเดอร์อัตโนมัติ',
		'how_step3_desc'  => 'เมื่อเข้าเงื่อนไข ระบบส่งคำสั่งซื้อหรือขายไปยังโบรกเกอร์ พร้อมจัดการขนาดออเดอร์และเงื่อนไขปิดตามค่าที่คุณตั้งไว้ แล้วปิดออเดอร์เมื่อครบกฎ โดยคุณไม่ต้องกดเอง',
		'how_step4_title' => 'ติดตามผลผ่าน Dashboard และ LINE',
		'how_step4_desc'  => 'แผง Dashboard บนกราฟแสดงทุน กำไรขาดทุน Drawdown และสถานะออเดอร์แบบเรียลไทม์ ดูจาก MT5 บนมือถือได้ และสอบถามทีมงานทาง LINE ได้เมื่อมีคำถาม',
		'how_req_title'   => 'ต้องมีอะไรบ้างก่อนเริ่ม',
		'how_req_items'   => "บัญชี MT5 กับโบรกเกอร์ที่รองรับ\nทุนที่พร้อมรับความเสี่ยง\nคอมพิวเตอร์ที่เปิดตลอดหรือ VPS\nเวลาศึกษาคู่มือประมาณ 30 นาที",
		'how_img'         => '',
		'how_img_alt'     => 'แผง Dashboard ของ EA2000 บนกราฟ MT5',
		'how_img_note'    => 'รูปที่ต้องใส่: ภาพกราฟ MT5 ที่แนบ EA2000 แล้ว เห็นแผง Dashboard ปิดตัวเลขบัญชีได้ · แนะนำ 1280x800 px',
		/* เทอร์มินัลจำลองลำดับการทำงาน (บล็อก 3) · ไม่มีเวลา ราคา หรือผลเทรดในบรรทัดใด */
		'show_how_log'    => true,
		'how_log_title'   => 'ภาพจำลองลำดับการทำงาน',
		'how_log_prompt'  => 'ea2000@mt5:~$',
		'how_log_start'   => 'เริ่มลำดับการทำงาน',
		'how_log_lines'   => '', // เว้นว่าง = ใช้ชื่อ 4 ขั้นด้านบนอัตโนมัติ · บรรทัดละ 1 ข้อความ
		'how_log_ready'   => 'พร้อมทำงาน · รอเงื่อนไขตามกฎที่ตั้งไว้',

		/* จุดเด่น · บล็อก 4 โมดูลของระบบ (ตารางเซลล์ hairline) */
		'show_features'     => true,
		'features_kicker'   => 'โมดูลของระบบ',
		'feat_module_label' => 'โมดูล', // คำนำหน้าเลขในแต่ละเซลล์ เช่น โมดูล 01
		'features_title'    => 'จุดเด่นของ EA2000',
		'features_subtitle' => 'ออกแบบมาเพื่อให้การเทรดของคุณเป็นระบบ ตรวจสอบได้ และอยู่ในกรอบความเสี่ยงที่วางไว้',
		'feat1_title'       => 'ระบบช่วยเทรดอัตโนมัติ',
		'feat1_desc'        => 'เข้าและออกออเดอร์ตามเงื่อนไขที่กำหนดไว้ล่วงหน้า ไม่ใช่ตามอารมณ์',
		'feat2_title'       => 'รองรับ MetaTrader 5',
		'feat2_desc'        => 'พัฒนาด้วย MQL5 สำหรับผู้ใช้งานแพลตฟอร์ม MT5 โดยเฉพาะ',
		'feat3_title'       => 'Dashboard ดูง่าย',
		'feat3_desc'        => 'ติดตามสถานะระบบ กำไร/ขาดทุน และเงื่อนไขการทำงานได้ชัดเจนในหน้าจอเดียว',
		'feat4_title'       => 'แนวคิดบริหารความเสี่ยง',
		'feat4_desc'        => 'วางแผนขนาดออเดอร์และระดับ Drawdown ที่ยอมรับได้ ให้เหมาะกับทุนของคุณ',
		'feat5_title'       => 'เหมาะกับคนไม่มีเวลาเฝ้าจอ',
		'feat5_desc'        => 'ให้ระบบช่วยทำงานตามแผนที่วางไว้ แม้ในเวลาที่คุณไม่อยู่หน้าจอ',
		'feat6_title'       => 'มีทีมช่วยแนะนำการติดตั้ง',
		'feat6_desc'        => 'ดูแลตั้งแต่ติดตั้งจนตั้งค่าเสร็จ มือใหม่ก็เริ่มต้นได้อย่างมั่นใจ',

		/* ภาพระบบ (เว้นว่าง = แสดงโครงรอใส่ภาพหน้าจอจริง · ปิดไว้บนหน้าแรกแบบ 10 บล็อก) */
		'show_gallery'     => false,
		'gallery_title'    => 'หน้าตาระบบจริง',
		'gallery_subtitle' => 'Dashboard ออกแบบให้ดูง่าย เห็นสถานะระบบ เงื่อนไขหลัก และผลการทำงานในบัญชีได้ชัดเจน',
		'gallery_note'     => '* ภาพใช้เพื่อประกอบการนำเสนอ ไม่ใช่การรับประกันผลลัพธ์',
		'gallery_img1'     => '',
		'gallery_cap1'     => 'Dashboard แสดงสถานะการทำงานของ EA2000',
		'gallery_img2'     => '',
		'gallery_cap2'     => 'ตัวอย่างการตั้งค่าความเสี่ยงและ Lot Size',
		'gallery_img3'     => '',
		'gallery_cap3'     => 'หน้าต่างตั้งค่าหลักของระบบ',
		'gallery_img4'     => '',
		'gallery_cap4'     => 'ตัวอย่างสถานะการทำงาน Buy / Sell',

		/* ขั้นตอนใช้งาน */
		'show_steps'     => true,
		'steps_kicker'   => 'Workflow',
		'steps_title'    => 'เริ่มใช้งานใน 4 ขั้นตอน',
		'steps_subtitle' => 'จากศูนย์จนระบบเริ่มทำงาน โดยมีทีมงานช่วยดูแลตลอดทาง',
		'step1_title'    => 'ติดตั้ง MT5',
		'step1_desc'     => 'เตรียมบัญชีเทรดและติดตั้งแพลตฟอร์ม MetaTrader 5 บนเครื่องหรือ VPS',
		'step2_title'    => 'ติดตั้ง EA2000',
		'step2_desc'     => 'ทีมงานช่วยแนะนำการติดตั้งและตั้งค่าเบื้องต้นจนระบบพร้อมทำงาน',
		'step3_title'    => 'ตั้งค่าความเสี่ยง',
		'step3_desc'     => 'เลือก Lot ทุนเริ่มต้น และระดับความเสี่ยงให้เหมาะสมกับพอร์ตของคุณ',
		'step4_title'    => 'ติดตามผลผ่าน Dashboard',
		'step4_desc'     => 'ดูสถานะระบบและผลการทำงานได้อย่างเป็นระบบ โปร่งใส ตรวจสอบได้',

		/* ผลการทดสอบ (ค่าที่ขึ้นต้นด้วย "ระบุ" / "เช่น" = placeholder จะถูกซ่อนจนกว่าจะกรอกจริง · ปิดไว้ หน้าแรกใหม่ใช้บล็อก tests_* แทน) */
		'show_perf'         => false,
		'perf_kicker'       => 'Evidence',
		'perf_title'        => 'ผลการทดสอบระบบ',
		'perf_subtitle'     => 'ความโปร่งใสคือสิ่งที่เราให้ความสำคัญ ข้อมูลการทดสอบทุกชุดระบุเงื่อนไขไว้ชัดเจน',
		'stat1_label'       => 'ช่วงเวลาทดสอบ',
		'stat1_value'       => 'ระบุช่วงเวลา',
		'stat2_label'       => 'คู่เงินที่ทดสอบ',
		'stat2_value'       => 'ระบุคู่เงินที่ทดสอบ',
		'stat3_label'       => 'Timeframe',
		'stat3_value'       => 'เช่น M15',
		'stat4_label'       => 'ทุนเริ่มต้น',
		'stat4_value'       => 'ระบุทุนทดสอบ',
		'stat5_label'       => 'Max Drawdown',
		'stat5_value'       => 'ระบุ %',
		'stat6_label'       => 'จำนวนออเดอร์',
		'stat6_value'       => 'ระบุจำนวน',
		'perf_image'        => '',
		'perf_image_caption'=> 'กราฟผลการทดสอบระบบ (Backtest / Forward Test)',
		'perf_note'         => 'หมายเหตุ: ผลการทดสอบขึ้นอยู่กับ Spread, Commission และ Slippage ของแต่ละโบรกเกอร์',
		'perf_disclaimer'   => 'ผลการทดสอบใช้เพื่อประกอบการศึกษาเท่านั้น ผลลัพธ์ในอดีตไม่ได้รับประกันผลลัพธ์ในอนาคต ผู้ใช้งานควรเข้าใจความเสี่ยงก่อนตัดสินใจใช้งานระบบ',

		/* ผลทดสอบ Backtest / Forward Test · บล็อก 6 (tests_*) · ไม่มีตัวเลข ตัวเลขจริงอยู่ที่หน้า /backtest/ และ /forward-test/ */
		'show_tests'        => true,
		'tests_kicker'      => 'การทดสอบ',
		'tests_tab_bt_label' => 'ทดสอบย้อนหลัง',
		'tests_tab_fw_label' => 'ทดสอบเดินหน้า',
		'tests_title'       => 'ผลทดสอบ Backtest และ Forward Test ของ EA2000',
		'tests_intro'       => 'การทดสอบ EA MT5 มี 2 แบบ: Backtest คือรันระบบกับข้อมูลราคาในอดีต ส่วน Forward Test คือรันกับตลาดจริงแบบเรียลไทม์บนบัญชีจริงหรือเดโม ตัวเลขจะแสดงเมื่อทีมงานมีข้อมูลจริงเท่านั้น ไม่มีการใส่ตัวเลขสมมติ',
		'tests_bt_title'    => 'Backtest',
		'tests_bt_text'     => 'Backtest คือการรัน EA2000 ใน Strategy Tester ของ MT5 กับข้อมูลราคาย้อนหลัง เพื่อดูพฤติกรรมของระบบภายใต้เงื่อนไขที่กำหนด สิ่งที่ควรอ่านคือ Profit Factor, Max Drawdown และจำนวนเทรด ข้อจำกัดคือผลขึ้นกับคุณภาพข้อมูล Spread และ Slippage ที่ใช้ทดสอบ จึงมักดูดีกว่าของจริง',
		'tests_bt_btn'      => 'ดูรายละเอียด Backtest',
		'tests_bt_img'      => '',
		'tests_bt_img_alt'  => 'กราฟผล Backtest ของ EA2000 จาก MT5 Strategy Tester',
		'tests_bt_img_note' => 'รูปที่ต้องใส่: ภาพรายงาน Backtest จาก MT5 Strategy Tester ใส่เมื่อมีผลจริงเท่านั้น · แนะนำ 1280x720 px',
		'tests_fw_title'    => 'Forward Test',
		'tests_fw_text'     => 'Forward Test คือการรัน EA2000 บนบัญชีจริงหรือเดโมกับตลาดปัจจุบัน จึงสะท้อน Spread, Commission และ Slippage ของโบรกเกอร์จริง ควรดูช่วงเวลาที่ทดสอบ ทุนเริ่มต้น และ Max Drawdown ควบคู่กับผลตอบแทน ลิงก์จากบริการติดตามผลภายนอกตรวจสอบได้ดีกว่าภาพหน้าจอ',
		'tests_fw_btn'      => 'ดูรายละเอียด Forward Test',
		'tests_fw_img'      => '',
		'tests_fw_img_alt'  => 'กราฟผล Forward Test ของ EA2000 บนบัญชีจริงหรือเดโม',
		'tests_fw_img_note' => 'รูปที่ต้องใส่: ภาพบัญชี Forward Test จริงพร้อมลิงก์ตรวจสอบ ใส่เมื่อมีข้อมูลจริงเท่านั้น · แนะนำ 1280x720 px',
		'tests_note'        => 'ตัวเลขผลทดสอบจะแสดงเมื่อทีมงานกรอกข้อมูลจริงเท่านั้น ผลในอดีตไม่รับประกันผลในอนาคต',

		/* ติดตั้งใน 3 ขั้น · บล็อก 7 (install_*) · install_intro ใช้ร่วมกับหน้า /how-to-install/ (ประกาศไว้ในชุดหน้าย่อยด้านล่าง) */
		'show_install'          => true,
		'install_kicker'        => 'การติดตั้ง',
		'install_step_label'    => 'ขั้น', // คำนำหน้าเลขขั้นใน ledger เช่น ขั้น 01
		'install_title'         => 'ติดตั้ง EA2000 บน MT5 ใน 3 ขั้น',
		'install_step1_title'   => 'เตรียมบัญชี MT5 และดาวน์โหลดไฟล์',
		'install_step1_desc'    => 'เปิดบัญชีกับโบรกเกอร์ที่รองรับ MetaTrader 5 ติดตั้งโปรแกรม MT5 บนคอมพิวเตอร์หรือ VPS แล้วดาวน์โหลดไฟล์ EA2000 ที่ได้รับหลังสั่งซื้อ',
		'install_step1_img'     => $install_assets . 'guide-01.webp',
		'install_step1_img_alt' => 'ขั้นที่ 1 · เตรียมบัญชี MT5 และดาวน์โหลดไฟล์ EA2000',
		'install_step1_img_note' => 'รูปที่ต้องใส่: ภาพหน้าจอขั้นเตรียมบัญชี MT5 และดาวน์โหลดไฟล์ · แนะนำ 1280x720 px',
		'install_step2_title'   => 'วางไฟล์ในโฟลเดอร์ Experts และเปิด Algo Trading',
		'install_step2_desc'    => 'ใน MT5 ไปที่ File แล้ว Open Data Folder เข้า MQL5 และ Experts วางไฟล์ลงไป รีสตาร์ต MT5 แล้วกดปุ่ม Algo Trading ให้เป็นสีเขียว',
		'install_step2_img'     => $install_assets . 'guide-02.webp',
		'install_step2_img_alt' => 'ขั้นที่ 2 · วางไฟล์ EA2000 ในโฟลเดอร์ Experts ของ MT5 และเปิด Algo Trading',
		'install_step2_img_note' => 'รูปที่ต้องใส่: ภาพโฟลเดอร์ Experts ใน MT5 และปุ่ม Algo Trading · แนะนำ 1280x720 px',
		'install_step3_title'   => 'ตั้งค่าตาม Preset และตรวจสถานะ',
		'install_step3_desc'    => 'ลาก EA2000 ขึ้นกราฟคู่เงินที่ต้องการ โหลดไฟล์ Preset ตามระดับความเสี่ยง แล้วตรวจแผง Dashboard และแท็บ Experts ว่าระบบทำงานปกติ',
		'install_step3_img'     => $install_assets . 'guide-03.webp',
		'install_step3_img_alt' => 'ขั้นที่ 3 · ตั้งค่า EA2000 ตาม Preset และตรวจสถานะบน Dashboard',
		'install_step3_img_note' => 'รูปที่ต้องใส่: ภาพ EA2000 บนกราฟพร้อมแผง Dashboard · แนะนำ 1280x720 px',
		'install_mobile_note'   => 'ใช้ MT5 บนมือถือดูผลได้ แต่ตัว EA ต้องรันบนคอมพิวเตอร์หรือ VPS ที่เปิดตลอด',
		'install_btn'           => 'อ่านคู่มือติดตั้งฉบับเต็ม',
		'install_btn_url'       => '/how-to-install/',

		/* เหมาะกับใคร */
		'show_fit'       => true,
		'fit_title'      => 'EA2000 เหมาะกับใคร?',
		'fit_subtitle'   => 'เราอยากให้คุณตัดสินใจจากข้อมูลจริง ไม่ใช่ความคาดหวังเกินจริง',
		'fit_good_title' => 'เหมาะกับ',
		'fit_good_items' => "คนที่อยากเทรดอย่างเป็นระบบ มีแบบแผนชัดเจน\nคนที่ต้องการลดการใช้อารมณ์ในการเทรด\nคนที่เข้าใจว่าการเทรดมีความเสี่ยง\nคนที่มีเวลาเรียนรู้การตั้งค่าระบบ\nคนที่มองว่า EA คือเครื่องมือช่วย ไม่ใช่เครื่องการันตีกำไร",
		'fit_bad_title'  => 'ไม่เหมาะกับ',
		'fit_bad_items'  => "คนที่หวังกำไรเร็วหรือรวยทางลัด\nคนที่รับความเสี่ยงและการขาดทุนไม่ได้\nคนที่ไม่ต้องการศึกษาการใช้งานเลย\nคนที่คิดว่า EA จะทำเงินให้ได้ตลอดเวลา\nคนที่ไม่ได้ใช้เงินเย็นในการเทรด",

		/* แพ็กเกจ (ราคาและเงื่อนไขรอเจ้าของยืนยัน) */
		'show_pricing'      => true,
		'pricing_title'     => 'แพ็กเกจการใช้งาน',
		'pricing_subtitle'  => 'เลือกแพ็กเกจที่เหมาะกับระดับการใช้งานของคุณ หรือทักมาปรึกษาทีมงานก่อนตัดสินใจได้',
		'pricing_mode'      => 'price',
		'pricing_confirmed' => false, // เปิดเมื่อเจ้าของยืนยันราคาแล้วเท่านั้น · ควบคุมว่าจะประกาศราคาให้ Google (Offer/AggregateOffer) หรือไม่
		'pricing_btn_text'  => 'สอบถามแพ็กเกจนี้',
		'pricing_note'      => 'ราคาและเงื่อนไขเป็นไปตามแพ็กเกจที่เลือก สอบถามรายละเอียดล่าสุดและความเหมาะสมกับบัญชีของคุณได้ทาง LINE',
		/* ตัวเลือกแพ็กเกจบนหน้าแรก (บล็อก 7 Tier selector) */
		'pricing_kicker'            => 'แพ็กเกจ',
		'pricing_recommended_label' => 'แนะนำ', // คำ monospace หลังชื่อแพ็กเกจที่ติ๊ก featured
		'pricing_more_text'         => 'ดูรายละเอียดแพ็กเกจทั้งหมด',
		'pricing_contact_text'      => 'สอบถามราคา', // แสดงแทนตัวเลขเมื่อ pricing_mode = contact หรือราคาว่าง
		'pkg1_name'        => 'Starter',
		'pkg1_tag'         => 'สำหรับสอบถามข้อมูลและเริ่มประเมินความเหมาะสม',
		'pkg1_price'       => 'Free',
		'pkg1_period'      => 'สอบถามทีมงาน',
		'pkg1_features'    => "ปรึกษาความเหมาะสมก่อนเริ่มใช้งาน\nดูภาพรวมระบบและแนวทางตั้งค่าเบื้องต้น\nรับคำแนะนำเรื่องทุน โบรกเกอร์ และบัญชี MT5\nสอบถามรายละเอียดผ่าน LINE Official",
		'pkg1_featured'    => false,
		'pkg2_name'        => 'Pro',
		'pkg2_tag'         => 'สำหรับใช้งานจริงต่อเนื่อง',
		'pkg2_price'       => '6,990',
		'pkg2_period'      => 'บาท',
		'pkg2_features'    => "ใช้งาน EA2000 สำหรับบัญชี MT5\nคู่มือการติดตั้งและตั้งค่าอย่างเป็นขั้นตอน\nไฟล์ Preset และแนวทางตั้งค่าตามระดับความเสี่ยง\nอัปเดตระบบตามรอบเวอร์ชัน\nSupport ผ่าน LINE Official",
		'pkg2_featured'    => true,
		'pkg3_name'        => 'VIP',
		'pkg3_tag'         => 'สำหรับคนที่อยากให้ทีมดูแลใกล้ชิด',
		'pkg3_price'       => '9,990',
		'pkg3_period'      => 'บาท',
		'pkg3_features'    => "รวมสิทธิ์ใช้งาน EA2000 ตามแพ็กเกจ\nทีมงานช่วยติดตั้งบนเครื่องหรือ VPS\nช่วยตรวจการตั้งค่าเบื้องต้นก่อนเริ่มใช้งาน\nแนะนำแนวคิดบริหารความเสี่ยงให้เหมาะกับทุน\nSupport แบบใกล้ชิดผ่าน LINE Official",
		'pkg3_featured'    => false,

		/* รีวิว (ปิดไว้ก่อน จนกว่าจะมีรีวิวจริง · ห้ามใส่รีวิวสมมติ) */
		'show_reviews'     => false,
		'reviews_title'    => 'เสียงจากผู้ใช้งานจริง',
		'reviews_subtitle' => 'รีวิวจากลูกค้าที่ใช้งาน EA2000',
		'rev1_text'        => '',
		'rev1_name'        => '',
		'rev2_text'        => '',
		'rev2_name'        => '',
		'rev3_text'        => '',
		'rev3_name'        => '',

		/* FAQ · บล็อก 9 (8 ข้อตาม docs/landing-page-plan.md ข้อ 5 · ข้อ 9 และ 10 เว้นว่าง) */
		'show_faq'     => true,
		'faq_kicker'   => 'คำถามที่พบบ่อย',
		'faq_title'    => 'คำถามที่พบบ่อยเกี่ยวกับ EA2000 และ EA MT5',
		'faq_subtitle' => 'คำตอบสั้น ๆ สำหรับคำถามที่ถูกถามบ่อยก่อนตัดสินใจใช้ EA เทรด forex',
		'faq1_q'       => 'EA เทรด คืออะไร ต่างจากโรบอทเทรดหรือบอทเทรดไหม',
		'faq1_a'       => 'ไม่ต่างกัน EA ย่อจาก Expert Advisor คือโปรแกรมที่ติดตั้งบน MetaTrader แล้วส่งคำสั่งซื้อขายตามกฎที่เขียนไว้ คนไทยเรียกทั้งโรบอทเทรด บอทเทรด หรือ EA เทรด แต่หมายถึงสิ่งเดียวกัน EA2000 คือ EA สำหรับ MetaTrader 5',
		'faq2_q'       => 'ใช้ EA เทรด forex ดีไหม เหมาะกับใคร',
		'faq2_a'       => 'เหมาะกับคนที่มีแผนเทรดแต่ทำตามไม่สม่ำเสมอ ไม่มีเวลาเฝ้าจอ หรืออยากคุม Lot และความเสี่ยงให้เป็นระบบ ไม่เหมาะกับคนที่หวังกำไรเร็ว รับการขาดทุนไม่ได้ หรือไม่อยากศึกษาการตั้งค่า เพราะ EA เป็นเครื่องมือ ไม่ใช่ทางลัด',
		'faq3_q'       => 'EA2000 ใช้กับโบรกเกอร์ไหนได้บ้าง',
		'faq3_a'       => 'ใช้ได้กับโบรกเกอร์ที่ให้บริการ MetaTrader 5 โดยทั่วไป ทั้งบัญชีจริงและบัญชีเดโม Spread, Commission, ประเภทบัญชี และ Leverage ของแต่ละโบรกเกอร์มีผลต่อการตั้งค่า ก่อนเริ่มแนะนำให้แจ้งชื่อโบรกเกอร์และประเภทบัญชีกับทีมงานทาง LINE',
		'faq4_q'       => 'EA2000 เทรดคู่เงินอะไร',
		'faq4_a'       => 'EA2000 ออกแบบให้ใช้กับหลายคู่เงินบน MT5 ไม่ผูกกับสินทรัพย์ใดสินทรัพย์หนึ่ง คู่เงินที่แนะนำและค่าที่เหมาะกับแต่ละคู่อยู่ในไฟล์ Preset และคู่มือตามแพ็กเกจ ควรเริ่มจากคู่ที่ Spread ต่ำและทดสอบบนบัญชีเดโมก่อน',
		'faq5_q'       => 'ต้องเปิดคอมตลอดไหม ใช้ EA MT5 บนมือถือได้ไหม',
		'faq5_a'       => 'ตัว EA ต้องรันบน MetaTrader 5 บนคอมพิวเตอร์ที่เปิดอยู่ตลอดช่วงที่ให้ระบบทำงาน ส่วนใหญ่จึงใช้ VPS แทนการเปิดเครื่องที่บ้าน MT5 บนมือถือรัน EA ไม่ได้ แต่ล็อกอินบัญชีเดียวกันเพื่อดูและปิดออเดอร์เองได้',
		'faq6_q'       => 'EA2000 ต่างจาก EA แจกฟรีทั่วไปอย่างไร',
		'faq6_a'       => 'สิ่งที่ต่างคือการดูแล: คู่มือติดตั้งภาษาไทย ไฟล์ Preset ตามระดับความเสี่ยง อัปเดตตามรอบเวอร์ชัน และทีมงานตอบคำถามผ่าน LINE ตามแพ็กเกจ EA แจกฟรีมักไม่มีผู้รับผิดชอบเมื่อมีปัญหา เราไม่อ้างว่าให้ผลกำไรมากกว่า',
		'faq7_q'       => 'มีให้ทดลองบนบัญชี demo ก่อนไหม',
		'faq7_a'       => 'มี ตามเงื่อนไขของแต่ละแพ็กเกจ แนะนำให้รัน EA2000 บนบัญชีเดโมของโบรกเกอร์ที่จะใช้จริงก่อน เพื่อดูพฤติกรรมของระบบกับ Spread และเงื่อนไขบัญชีของตัวเอง ทักทีมงานทาง LINE เพื่อสอบถามวิธีขอทดลอง',
		'faq8_q'       => 'ใช้ EA2000 แล้วการันตีกำไรไหม',
		'faq8_a'       => 'ไม่ ไม่มี EA ตัวใดการันตีผลกำไรได้ และเราไม่อ้างตัวเลขที่ยืนยันไม่ได้ ผลการเทรดขึ้นกับสภาวะตลาด การตั้งค่า ทุน และเงื่อนไขของโบรกเกอร์ คุณอาจขาดทุนบางส่วนหรือทั้งหมด โปรดอ่านหน้าประกาศความเสี่ยงก่อนตัดสินใจ',
		'faq9_q'       => '',
		'faq9_a'       => '',
		'faq10_q'      => '',
		'faq10_a'      => '',

		/* คำเตือนความเสี่ยง · บล็อก 9 Hazard band (ห้ามลดทอน) */
		'show_risk'        => true,
		'risk_kicker'      => 'ประกาศความเสี่ยง',
		'risk_label'       => 'NOTICE', // stamp อังกฤษ 1 ใน 2 คำที่อนุญาตบนหน้าแรก (อีกคำคือ prompt ของเทอร์มินัล)
		'risk_more_text'   => 'อ่านประกาศความเสี่ยงฉบับเต็ม',
		'risk_margin_note' => 'ผลในอดีตไม่รับประกันผลในอนาคต · การเทรดมีความเสี่ยง โปรดอ่านประกาศฉบับเต็มก่อนตัดสินใจ', // บรรทัดเตือนใต้ตัวเลือกแพ็กเกจ
		'risk_title' => 'ก่อนตัดสินใจ อ่านความเสี่ยงก่อน',
		'risk_text'  => 'การเทรด Forex, CFD หรือสินทรัพย์ทางการเงินอื่น ๆ มีความเสี่ยงสูง ผู้ใช้งานอาจขาดทุนได้ทั้งบางส่วนหรือทั้งหมดของเงินทุน ผลการทดสอบหรือผลลัพธ์ในอดีตไม่ได้รับประกันผลลัพธ์ในอนาคต EA2000 เป็นเครื่องมือช่วยเทรดตามเงื่อนไขที่กำหนด ไม่ใช่ระบบรับประกันผลกำไร ผู้ใช้งานควรศึกษาข้อมูล เข้าใจข้อจำกัดของระบบ และบริหารความเสี่ยงให้เหมาะสมกับตนเองก่อนใช้งานจริง',

		/* CTA ปิดท้าย · รวมอยู่ในบล็อก 10 · ปุ่ม LINE ปุ่มเดียว */
		'show_cta'     => true,
		'cta_title'    => 'พร้อมคุยเรื่อง EA2000 กับทีมงานแล้วหรือยัง',
		'cta_subtitle' => 'สอบถามแพ็กเกจ การติดตั้ง และความเหมาะสมกับทุนของคุณได้ทาง LINE ไม่มีข้อผูกมัด',
		'cta_btn_text' => 'สอบถามทาง LINE',

		/* Footer v2 "Console" (footer.php) · key ชุดเก่า footer_kicker / footer_cta_title / footer_cta_text / footer_tagline คงไว้เพื่อ REST แต่เทมเพลตไม่อ่านแล้ว */
		'footer_console_label'  => 'ติดต่อทีมงาน',
		'footer_headline'       => '', // ว่าง = ใช้ cta_title
		'footer_sub'            => '', // ว่าง = ใช้ cta_subtitle
		'footer_line_qr_img'    => '', // QR ของ LINE OA · ว่าง = แอดมินเห็นข้อความเตือนให้อัปโหลด
		'footer_line_qr_alt'    => 'QR สำหรับเพิ่มเพื่อน LINE Official Account ของ EA2000',
		'footer_line_qr_note'   => 'อัปโหลด QR ของ LINE OA ขนาด 600x600 px พื้นขาว ที่ ปรับแต่ง : Footer',
		'footer_qr_toggle_text' => 'แสดง QR',
		'footer_hours_title'    => 'เวลาตอบแชท',
		'footer_hours_text'     => '', // ว่าง = ซ่อนแถว · เจ้าของกรอกเอง ไม่มีสัญญาบริการฝังในโค้ด
		'show_footer_console'   => true,
		'footer_console_prompt' => 'ea2000@line:~$',
		'footer_console_lines'  => "ทีมงานตอบแชทด้วยตัวเอง\nแจ้งเวอร์ชัน MT5 และโบรกเกอร์ที่ใช้ เพื่อให้ช่วยติดตั้งได้เร็วขึ้น\nอ่านประกาศความเสี่ยงก่อนตัดสินใจทุกครั้ง",
		'footer_index_title'    => 'ดัชนีหน้า',
		'footer_channels_title' => 'ช่องทาง',
		'footer_docs_title'     => 'เอกสาร',
		'footer_spec_title'     => 'ข้อมูลระบบ',
		'footer_spec_items'     => "แพลตฟอร์ม|MetaTrader 5\nระบบปฏิบัติการ|Windows หรือ VPS\nสินทรัพย์|หลายคู่เงิน\nการส่งมอบ|ไฟล์ EA และคู่มือ",
		'show_footer_watermark' => true,
		'footer_watermark_text' => 'EA2000',
		'footer_status_text'    => 'Expert Advisor สำหรับ MetaTrader 5',
		'footer_copyright_text' => 'สงวนลิขสิทธิ์',
		'show_footer_clock'     => true,
		'footer_clock_label'    => 'เวลาไทย',
		'footer_backtop_text'   => 'กลับด้านบน',
		'show_footer_spotlight' => true,
		'show_footer_signal'    => true,

		/* Footer · key ชุดเดิม */
		'footer_kicker'       => 'EA2000',
		'footer_cta_title'    => 'พร้อมเริ่มต้นใช้งาน EA2000?',
		'footer_cta_text'     => 'สอบถามการติดตั้ง เงื่อนไขการใช้งาน และความเหมาะสมกับทุนของคุณได้ทาง LINE',
		'footer_line_text'    => 'ทัก LINE Official Account',
		'footer_facebook_text' => 'Facebook Page',
		'footer_email_text'    => 'Email Support',
		'footer_prep_title'    => 'ก่อนทัก LINE',
		'footer_prep_text'     => 'เตรียมข้อมูลสั้น ๆ เพื่อให้ทีมช่วยแนะนำได้ตรงขึ้น',
		'footer_prep_items'    => "ทุนที่ต้องการใช้กับระบบ\nโบรกเกอร์และประเภทบัญชี MT5\nเป้าหมาย: ติดตั้ง / สอบถามราคา / ตรวจความเหมาะสม\nช่วงเวลาที่สะดวกให้ทีมติดต่อกลับ",
		'footer_tagline'      => "EA2000 ถูกออกแบบให้เป็นผู้ช่วยจัดระบบการเทรดบน MetaTrader 5 สำหรับผู้ที่ต้องการลดการตัดสินใจตามอารมณ์ และให้การทำงานเป็นไปตามแผนที่กำหนดไว้อย่างมีวินัย\n\nแนวทางของระบบให้ความสำคัญกับการใช้งานภายใต้กรอบความเสี่ยงที่ชัดเจน ช่วยให้ผู้ใช้พิจารณาความเหมาะสมของทุน การตั้งค่า และเงื่อนไขการใช้งานก่อนเริ่มต้นจริง",
		'footer_risk_link'    => 'คำเตือนความเสี่ยง',
	);

	/* ===== เนื้อหาหน้าย่อย (multipage) ===== */
	$d = array_merge(
		$d,
		array(

			/* หน้าแรก · แถบไฮไลต์ */
			'show_highlight' => false,
			'highlight1'     => 'ทำงานบน MetaTrader 5',
			'highlight2'     => 'ลดการเทรดด้วยอารมณ์',
			'highlight3'     => 'บริหารความเสี่ยงได้',
			'highlight4'     => 'มีทีมช่วยติดตั้ง',

			/* หน้าแรก · แถบสถานะ / Control center */
			'show_live_status'     => false,
			'live_status_kicker'   => 'Live System Flow',
			'live_status_items'    => "EA2000 บน MetaTrader 5\nRisk-first setup\nBacktest และ Forward Test\nตั้งค่าตามทุนและความเสี่ยง\nLINE Support ภาษาไทย",
			'show_control_center'  => false,
			'control_kicker'       => 'EA2000 Control Center',
			'control_title'        => 'ภาพรวมก่อนเริ่มใช้งาน EA2000',
			'control_subtitle'     => 'ดูขั้นตอนสำคัญของระบบ ตั้งแต่ความพร้อมของ MetaTrader 5 การตั้งค่าความเสี่ยง ไปจนถึงการติดตามผลผ่าน Dashboard โดยไม่ต้องเดาเอง',
			'control_panel_title'  => 'System Readiness',
			'control_panel_status' => 'พร้อมประเมินความเหมาะสม',
			'control_badge'        => 'MT5',
			'control_panel_text'   => 'ก่อนเริ่มใช้งาน ทีมจะช่วยเช็กข้อมูลหลักที่ส่งผลต่อการตั้งค่า เช่น ประเภทบัญชี โบรกเกอร์ ทุนที่ใช้ และระดับความเสี่ยงที่รับได้',
			'control_metric1_label' => 'Platform',
			'control_metric1_value' => 'MetaTrader 5',
			'control_metric2_label' => 'Mode',
			'control_metric2_value' => 'EA Setup',
			'control_metric3_label' => 'Focus',
			'control_metric3_value' => 'Risk-first',
			'control_list_title'   => 'สิ่งที่ควรเตรียมก่อนเริ่ม',
			'control_list_items'   => "บัญชี MetaTrader 5 และโบรกเกอร์ที่ใช้งาน\nทุนที่ต้องการนำมาใช้กับระบบ\nระดับความเสี่ยงที่รับได้\nเป้าหมายการใช้งาน: ติดตั้ง / ทดลอง / ปรับพอร์ต",

			/* หน้าแรก · การ์ดนำทาง (explore hub · ปิดไว้บนหน้าแรกแบบ 10 บล็อก) */
			'show_explore'     => false,
			'home_cards_title' => 'ดูข้อมูลเชิงลึกต่อ',
			'home_cards_sub'   => 'เราแยกข้อมูลเป็นหมวด เพื่อให้คุณศึกษาได้ละเอียดก่อนตัดสินใจ',
			'card1_title'      => 'ผล Backtest',
			'card1_desc'       => 'ผลการทดสอบย้อนหลังพร้อมเงื่อนไขการทดสอบที่ชัดเจน',
			'card1_url'        => '/backtest/',
			'card2_title'      => 'ผล Forward Test',
			'card2_desc'       => 'ผลการทดสอบบนบัญชีจริง/เดโมแบบเรียลไทม์',
			'card2_url'        => '/forward-test/',
			'card3_title'      => 'แพ็กเกจ & ราคา',
			'card3_desc'       => 'เปรียบเทียบแพ็กเกจและสิ่งที่ได้รับในแต่ละระดับ',
			'card3_url'        => '/pricing/',
			'card4_title'      => 'วิธีติดตั้ง',
			'card4_desc'       => 'คู่มือติดตั้ง EA บน MT5 ทีละขั้นตอน',
			'card4_url'        => '/how-to-install/',
			'card5_title'      => 'คำเตือนความเสี่ยง',
			'card5_desc'       => 'ข้อมูลความเสี่ยงที่ควรอ่านก่อนเริ่มใช้งานจริง',
			'card5_url'        => '/risk-disclosure/',

			/* หน้า Backtest */
			'backtest_sub'        => 'ผลการทดสอบย้อนหลัง (Historical Backtest)',
			'backtest_intro'      => 'Backtest คือการนำกลยุทธ์ของ EA มาทดสอบกับข้อมูลราคาในอดีต เพื่อดูพฤติกรรมของระบบภายใต้เงื่อนไขที่กำหนด ทีมงานจะระบุพารามิเตอร์การทดสอบไว้อย่างชัดเจนเพื่อความโปร่งใส',
			'bt_stat1_label'      => 'ช่วงเวลาทดสอบ',
			'bt_stat1_value'      => 'ระบุช่วงเวลา',
			'bt_stat2_label'      => 'คู่เงิน / สินทรัพย์',
			'bt_stat2_value'      => 'ระบุคู่เงินที่ทดสอบ',
			'bt_stat3_label'      => 'Timeframe',
			'bt_stat3_value'      => 'เช่น M15',
			'bt_stat4_label'      => 'ทุนเริ่มต้น',
			'bt_stat4_value'      => 'ระบุทุน',
			'bt_stat5_label'      => 'กำไรสุทธิ (Net Profit)',
			'bt_stat5_value'      => 'ระบุผล',
			'bt_stat6_label'      => 'Profit Factor',
			'bt_stat6_value'      => 'ระบุค่า',
			'bt_stat7_label'      => 'Max Drawdown',
			'bt_stat7_value'      => 'ระบุ %',
			'bt_stat8_label'      => 'จำนวนเทรดทั้งหมด',
			'bt_stat8_value'      => 'ระบุจำนวน',
			'backtest_img'        => '',
			'backtest_img_caption'=> 'กราฟ Equity / รายงานผล Backtest จาก MT5',
			'backtest_note'       => 'หมายเหตุ: ผลขึ้นอยู่กับคุณภาพข้อมูลราคา Spread, Commission และ Slippage ที่ใช้ในการทดสอบ',
			'backtest_disclaimer' => 'ผลการทดสอบย้อนหลังใช้เพื่อการศึกษาเท่านั้น ไม่ได้รับประกันผลลัพธ์ในอนาคต และไม่ใช่คำแนะนำในการลงทุน',

			/* หน้า Forward Test */
			'forward_sub'        => 'ผลการทดสอบบนบัญชีจริง / เดโม (Forward Test)',
			'forward_intro'      => 'Forward Test คือการรันระบบกับสภาวะตลาดจริงแบบเรียลไทม์ สะท้อนสภาพการเทรดจริงได้ดีกว่าการทดสอบย้อนหลัง ข้อมูลด้านล่างจะอัปเดตตามรอบการทดสอบ',
			'fw_stat1_label'     => 'ช่วงเวลาทดสอบ',
			'fw_stat1_value'     => 'ระบุช่วงเวลา',
			'fw_stat2_label'     => 'ประเภทบัญชี',
			'fw_stat2_value'     => 'เช่น Real / Demo',
			'fw_stat3_label'     => 'คู่เงิน / สินทรัพย์',
			'fw_stat3_value'     => 'ระบุคู่เงินที่ทดสอบ',
			'fw_stat4_label'     => 'ทุนเริ่มต้น',
			'fw_stat4_value'     => 'ระบุทุน',
			'fw_stat5_label'     => 'ผลตอบแทนสะสม',
			'fw_stat5_value'     => 'ระบุ %',
			'fw_stat6_label'     => 'Max Drawdown',
			'fw_stat6_value'     => 'ระบุ %',
			'forward_img'        => '',
			'forward_img_caption'=> 'ภาพผลการทดสอบจากบัญชี MT5 หรือบริการติดตามผลที่ตรวจสอบได้',
			'forward_link_label' => '',
			'forward_link_url'   => '',
			'forward_note'       => 'หมายเหตุ: ผลในช่วงเวลาหนึ่งไม่ได้บ่งบอกถึงผลในอีกช่วงเวลาหนึ่ง',
			'forward_disclaimer' => 'ผลการทดสอบบนบัญชีจริง/เดโมสะท้อนช่วงเวลาที่ทดสอบเท่านั้น ผลในอดีตไม่ได้รับประกันผลลัพธ์ในอนาคต และไม่ใช่คำแนะนำในการลงทุน',

			/* หน้า How to Install (ภาพขั้นตอนเป็น placeholder รอเจ้าของใส่ภาพหน้าจอจริง) */
			'install_sub'       => 'คู่มือติดตั้งและเริ่มใช้งานทีละขั้นตอน',
			'install_intro'     => 'ติดตั้ง EA2000 บน MetaTrader 5 ได้ด้วยตัวเองตามขั้นตอนด้านล่าง หากติดขั้นตอนไหน ทักทีมงานทาง LINE ได้ทันที', // ใช้ร่วมกันระหว่างบล็อกติดตั้งบนหน้าแรกและหน้า /how-to-install/
			'install_req'       => "บัญชีเทรดของโบรกเกอร์ที่รองรับ MetaTrader 5\nโปรแกรม MetaTrader 5 (PC หรือ VPS)\nไฟล์ EA2000 ที่ได้รับหลังสั่งซื้อ\nแนะนำใช้ VPS เพื่อให้ระบบทำงานต่อเนื่อง 24 ชม.",
			'inst_step1_title'  => 'ติดตั้ง MetaTrader 5 / เตรียม VPS',
			'inst_step1_desc'   => 'ดาวน์โหลดและติดตั้ง MT5 จากโบรกเกอร์ของคุณ หากต้องการให้ระบบรันตลอด 24 ชม. แนะนำให้เช่า VPS แล้วติดตั้ง MT5 บน VPS แทนเครื่องส่วนตัว',
			'inst_step1_img'    => $install_assets . 'guide-01.webp',
			'inst_step2_title'  => 'เปิดโฟลเดอร์ Experts แล้วนำไฟล์ EA เข้า',
			'inst_step2_desc'   => 'ใน MT5 ไปที่เมนู File → Open Data Folder → MQL5 → Experts จากนั้นวางไฟล์ EA2000 ลงในโฟลเดอร์นี้ แล้วปิด-เปิด MT5 หรือกด Refresh',
			'inst_step2_img'    => $install_assets . 'guide-02.webp',
			'inst_step3_title'  => 'ลาก EA ขึ้นกราฟและตั้งค่า',
			'inst_step3_desc'   => 'เปิดกราฟคู่เงินที่ต้องการ แล้วลาก EA2000 จากหน้าต่าง Navigator ขึ้นกราฟ ตั้งค่าพารามิเตอร์ตามคำแนะนำ เช่น Lot และระดับความเสี่ยงให้เหมาะกับทุน',
			'inst_step3_img'    => $install_assets . 'guide-03.webp',
			'inst_step4_title'  => 'เปิด AutoTrading',
			'inst_step4_desc'   => 'กดปุ่ม AutoTrading (Algo Trading) ด้านบนให้เป็นสีเขียว และตรวจสอบว่ามีชื่อ EA พร้อมไอคอนหมวกสีน้ำเงินที่มุมขวาบนของกราฟ แสดงว่า EA พร้อมทำงาน',
			'inst_step4_img'    => $install_assets . 'guide-04.webp',
			'inst_step5_title'  => 'ตรวจสอบการทำงานผ่าน Dashboard',
			'inst_step5_desc'   => 'สังเกตสถานะระบบบนกราฟและแท็บ Experts/Journal ว่าทำงานปกติ ติดตามผลและเงื่อนไขการเทรดได้จาก Dashboard ของระบบ',
			'inst_step5_img'    => $install_assets . 'guide-05.webp',
			'inst_step6_title'  => 'ปรับความเสี่ยงให้เหมาะกับตัวเอง',
			'inst_step6_desc'   => 'ทบทวนการตั้งค่าความเสี่ยงเป็นระยะ ใช้เงินเย็น และปรับ Lot ให้สอดคล้องกับทุน เพื่อให้ Drawdown อยู่ในระดับที่รับได้',
			'inst_step6_img'    => $install_assets . 'guide-06.webp',
			'install_note'      => 'ต้องการให้ทีมงานช่วยติดตั้งให้? ทักมาทาง LINE ได้เลย',

			/* หน้า Pricing (เพิ่มเติม) */
			'pricing_sub'   => 'เลือกแพ็กเกจที่เหมาะกับการใช้งานของคุณ',
			'compare_title' => 'ตารางเปรียบเทียบแพ็กเกจ',
			'compare_rows'  => "รายการ | Starter | Pro | VIP\nจำนวนบัญชีที่ใช้ได้ | 1 | 1-2 | ตามตกลง\nไฟล์ Preset ตั้งค่าพร้อมใช้ | ✗ | ✓ | ✓\nทีมช่วยติดตั้ง / VPS | ✗ | ✗ | ✓\nอัปเดตระบบ | ✓ | ✓ | ✓\nระดับการ Support | พื้นฐาน | ส่วนตัว | ใกล้ชิด",

			/* หน้า Risk Disclosure */
			'riskpage_sub'     => 'ข้อมูลความเสี่ยงที่ควรอ่านก่อนเริ่มใช้งาน',
			'riskpage_intro'   => 'โปรดอ่านและทำความเข้าใจข้อมูลความเสี่ยงต่อไปนี้อย่างละเอียดก่อนตัดสินใจใช้งาน EA2000 หรือทำการเทรดใด ๆ เนื้อหานี้จัดทำขึ้นเพื่อให้ผู้ใช้งานเห็นข้อจำกัดของระบบ ความเสี่ยงของตลาด และความรับผิดชอบของผู้ใช้งานอย่างชัดเจน',
			'riskpage_image'   => '',
			'riskpage_image_caption' => 'ตัวอย่างแนวทางตั้งค่าความเสี่ยงและ Lot Size ให้เหมาะสมกับทุน',
			'rp_block1_title'  => 'ความเสี่ยงของการเทรด',
			'rp_block1_text'   => 'การเทรด Forex, CFD และสินทรัพย์ทางการเงินอื่น ๆ มีความเสี่ยงสูงต่อเงินทุนของคุณ ราคาอาจเคลื่อนไหวผันผวนรุนแรงในระยะเวลาสั้น โดยเฉพาะช่วงข่าวสำคัญ สภาพคล่องต่ำ หรือภาวะตลาดผิดปกติ คุณอาจสูญเสียเงินลงทุนบางส่วนหรือทั้งหมด จึงควรใช้เฉพาะเงินเย็นที่พร้อมรับความเสี่ยงได้เท่านั้น',
			'rp_block2_title'  => 'ไม่มีการรับประกันผลกำไร',
			'rp_block2_text'   => 'EA2000 เป็นเครื่องมือช่วยเทรดที่ทำงานตามเงื่อนไขที่กำหนดไว้ ไม่ใช่ระบบที่รับประกันผลกำไร ไม่ใช่สัญญาว่าจะทำกำไรได้ทุกวัน และไม่สามารถป้องกันการขาดทุนได้ในทุกสภาวะตลาด ผลการเทรดขึ้นอยู่กับสภาวะตลาด การตั้งค่า ทุน ระดับความเสี่ยง Spread, Commission, Slippage และเงื่อนไขของโบรกเกอร์ที่ใช้งาน',
			'rp_block3_title'  => 'ผลในอดีตไม่ได้บ่งบอกอนาคต',
			'rp_block3_text'   => 'ผลการทดสอบย้อนหลัง (Backtest) และผลการทดสอบบนบัญชีจริงหรือบัญชีเดโม (Forward Test) สะท้อนเฉพาะช่วงเวลาที่ทดสอบและเงื่อนไขที่ใช้ทดสอบเท่านั้น ไม่ได้รับประกันว่าผลในอนาคตจะเป็นไปในทิศทางเดียวกัน ตลาดสามารถเปลี่ยนแปลงได้ตลอดเวลา และระบบที่เคยทำงานได้ดีในอดีตอาจให้ผลลัพธ์แตกต่างออกไปในอนาคต',
			'rp_block4_title'  => 'ความรับผิดชอบของผู้ใช้งาน',
			'rp_block4_text'   => 'ผู้ใช้งานเป็นผู้รับผิดชอบการตัดสินใจเทรดและการตั้งค่าระบบด้วยตนเอง รวมถึงการเลือกบัญชี การเลือกโบรกเกอร์ การกำหนด Lot Size ระดับความเสี่ยง การเปิดปิดระบบ และการดูแลบัญชีของตนเอง ก่อนใช้งานจริงควรทดลอง ตั้งค่าด้วยความระมัดระวัง และตรวจสอบว่าเข้าใจผลกระทบของแต่ละพารามิเตอร์แล้ว',
			'rp_block5_title'  => 'ไม่ใช่คำแนะนำการลงทุน',
			'rp_block5_text'   => 'ข้อมูลทั้งหมดบนเว็บไซต์นี้จัดทำขึ้นเพื่อให้ข้อมูลเกี่ยวกับเครื่องมือและวิธีใช้งานเท่านั้น ไม่ถือเป็นคำแนะนำทางการเงิน การลงทุน กฎหมาย หรือภาษี ทีมงานไม่ได้ทำหน้าที่เป็นที่ปรึกษาการลงทุนส่วนบุคคล หากต้องการคำแนะนำเฉพาะบุคคล ควรปรึกษาผู้เชี่ยวชาญที่ได้รับอนุญาตตามกฎหมาย',
			'rp_block6_title'  => 'ความเสี่ยงด้านเทคนิคและการเชื่อมต่อ',
			'rp_block6_text'   => 'การใช้งาน EA จำเป็นต้องพึ่งพาแพลตฟอร์ม MetaTrader 5, อินเทอร์เน็ต, VPS หรือคอมพิวเตอร์ที่เปิดใช้งานระบบ รวมถึงเซิร์ฟเวอร์ของโบรกเกอร์ ปัญหาทางเทคนิค เช่น อินเทอร์เน็ตหลุด VPS ดับ โปรแกรมค้าง คำสั่งส่งไม่สำเร็จ หรือโบรกเกอร์มีข้อจำกัดบางอย่าง อาจส่งผลต่อการทำงานของระบบและผลการเทรด ผู้ใช้งานควรตรวจสอบระบบเป็นระยะและมีแผนรับมือความเสี่ยงเหล่านี้',
			'riskpage_updated' => 'ระบุวันที่ปรับปรุงล่าสุด',
		)
	);

	/* ===== ส่วนเสริมความเชื่อใจหน้าแรก (ทีมงาน / ความมั่นใจ / Mid CTA / Pricing teaser / ลิงก์ผล verified) ===== */
	$d = array_merge(
		$d,
		array(

			/* ทีมงาน / ใครอยู่เบื้องหลัง (ปิดไว้ก่อน จนกว่าเจ้าของจะกรอกข้อมูลจริง) */
			'show_team'   => false,
			'team_kicker' => 'Who We Are',
			'team_title'  => 'ทีมที่อยู่เบื้องหลัง EA2000',
			'team_text'   => "EA2000 พัฒนาและดูแลโดยทีมงานที่ติดตามตลาดและการเทรดบน MetaTrader 5 เราตั้งใจสร้างเครื่องมือที่ช่วยให้การเทรดเป็นระบบและตรวจสอบได้ พร้อมดูแลผู้ใช้งานผ่าน LINE หลังเริ่มใช้งานจริง",
			'team_points' => "ดูแลผู้ใช้งานผ่าน LINE ภาษาไทย\nให้ความสำคัญกับการบริหารความเสี่ยง\nอัปเดตระบบตามรอบเวอร์ชัน",
			'team_img'    => '',

			/* ความมั่นใจก่อนเริ่มใช้งาน */
			'show_assurance'     => false,
			'assurance_kicker'   => 'Before You Start',
			'assurance_title'    => 'สบายใจก่อนเริ่มใช้งาน',
			'assurance_subtitle' => 'เราอยากให้คุณเข้าใจระบบและมั่นใจก่อนตัดสินใจ ไม่ใช่เร่งให้รีบซื้อ',
			'assurance_items'    => "ทดลองแนวคิดระบบบนบัญชี Demo ก่อนได้\nมีทีมไทยช่วยแนะนำการติดตั้งจนระบบทำงานได้จริง\nสอบถามและปรึกษาทีมก่อนตัดสินใจได้\nอัปเดตระบบตามรอบเวอร์ชันอย่างต่อเนื่อง",

			/* Mid CTA (แถบทัก LINE คั่นกลางหน้า) */
			'show_mid_cta'  => false,
			'mid_cta_title' => 'ยังไม่แน่ใจว่าเหมาะกับทุนของคุณไหม?',
			'mid_cta_text'  => 'ทักมาให้ทีมช่วยประเมินความเหมาะสมก่อนตัดสินใจได้ ไม่มีข้อผูกมัด',

			/* Pricing teaser หน้าแรก (ใช้แพ็กเกจร่วมกับหมวด 10) */
			'show_pricing_home'  => true,
			'pricing_home_title' => 'แพ็กเกจและราคา EA2000',
			'pricing_home_sub'   => 'เลือกแพ็กเกจ EA MT5 ได้ 3 ระดับตามการดูแลที่ต้องการ ดูตารางเปรียบเทียบและเงื่อนไขทั้งหมดได้ที่หน้าแพ็กเกจ',

			/* ปุ่มลิงก์ผลที่ตรวจสอบได้ (บริการติดตามผลภายนอก) บนหน้าแรก · เว้นว่าง = ซ่อนปุ่ม */
			'verified_link_label' => 'ดูผลแบบเรียลไทม์',
			'verified_link_url'   => '',

			/* บทความล่าสุดบนหน้าแรก (ซ่อนอัตโนมัติเมื่อยังไม่มีบทความที่เผยแพร่) */
			'show_blog'      => true,
			'blog_kicker'    => 'Articles',
			'blog_title'     => 'บทความและความรู้',
			'blog_subtitle'  => 'รวมบทความเกี่ยวกับ EA การเทรดอัตโนมัติ และการบริหารความเสี่ยงบน MetaTrader 5',
			'blog_all_label' => 'ดูบทความทั้งหมด',
		)
	);

	return $d;
}

/**
 * อ่านค่า theme mod พร้อม fallback เป็นค่าเริ่มต้น
 */
function ea2000_mod( $key ) {
	$defaults = ea2000_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
	$value    = get_theme_mod( $key, $default );

	return $value;
}

/**
 * แปลง textarea เป็น array รายการ (บรรทัดละ 1 รายการ)
 */
function ea2000_lines( $text ) {
	$lines = preg_split( '/\r\n|\r|\n/', (string) $text );
	$lines = array_map( 'trim', $lines );
	return array_values( array_filter( $lines, 'strlen' ) );
}

/**
 * ตรวจว่าค่ายังเป็น placeholder (ยังไม่กรอกจริง) หรือไม่
 * ใช้ซ่อนสถิติที่ยังขึ้นต้นด้วย "ระบุ" / "เช่น" ไม่ให้หน้าแรกดูเหมือนยังทำไม่เสร็จ
 */
function ea2000_is_placeholder( $value ) {
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return true;
	}
	foreach ( array( 'ระบุ', 'เช่น' ) as $needle ) {
		if ( 0 === mb_strpos( $value, $needle ) ) {
			return true;
		}
	}
	return false;
}

/**
 * แปลงลิงก์ที่ตั้งค่าได้ ให้รองรับทั้ง URL เต็ม, anchor และ slug ภายในเว็บ
 */
function ea2000_link_url( $url ) {
	$url = trim( (string) $url );

	if ( '' === $url ) {
		return '#';
	}

	if ( '#' === $url || 0 === strpos( $url, '#' ) || preg_match( '#^(https?:)?//#i', $url ) || preg_match( '#^(mailto|tel):#i', $url ) ) {
		return $url;
	}

	return home_url( '/' . ltrim( $url, '/' ) );
}

/**
 * URL โลโก้ (ใช้โลโก้ที่อัปโหลดเอง ถ้าไม่มีใช้โลโก้ที่ฝังมากับธีม)
 */
function ea2000_logo_url() {
	$logo_id = get_theme_mod( 'custom_logo' );
	if ( $logo_id ) {
		$url = wp_get_attachment_image_url( $logo_id, 'full' );
		if ( $url ) {
			return $url;
		}
	}
	return get_template_directory_uri() . '/assets/img/logo-mark.webp';
}

/**
 * ALT ของภาพเด่นจาก Media Library พร้อม fallback เป็นชื่อบทความ
 *
 * @param int $post_id Post ID. Defaults to the current post.
 * @return string
 */
function ea2000_featured_image_alt( $post_id = 0 ) {
	$post_id      = $post_id ? absint( $post_id ) : get_the_ID();
	$thumbnail_id = get_post_thumbnail_id( $post_id );
	$alt          = $thumbnail_id ? trim( (string) get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true ) ) : '';

	if ( '' === $alt ) {
		$alt = get_the_title( $post_id );
	}

	return $alt;
}

/**
 * ตรวจว่ามีลิงก์ LINE ที่ใช้งานได้จริงหรือไม่ (ไม่ว่างและไม่ใช่ #)
 */
function ea2000_has_line_url() {
	$url = trim( (string) ea2000_mod( 'line_url' ) );

	return '' !== $url && '#' !== $url;
}

/**
 * เมนูสำรอง กรณียังไม่ได้สร้างเมนูใน WordPress
 * ชี้ไปยังหน้าย่อยตาม slug ที่แนะนำ (ปรับเมนูจริงได้ที่ รูปแบบ → เมนู)
 */
function ea2000_fallback_menu() {
	echo '<ul class="nav-list">';
	echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">หน้าแรก</a></li>';
	echo '<li class="menu-item-has-children"><a href="' . esc_url( home_url( '/forward-test/' ) ) . '">ผลทดสอบ</a>';
	echo '<ul class="sub-menu">';
	echo '<li><a href="' . esc_url( home_url( '/backtest/' ) ) . '">Backtest</a></li>';
	echo '<li><a href="' . esc_url( home_url( '/forward-test/' ) ) . '">Forward Test</a></li>';
	echo '</ul></li>';
	echo '<li><a href="' . esc_url( home_url( '/how-to-install/' ) ) . '">วิธีติดตั้ง</a></li>';
	echo '<li><a href="' . esc_url( home_url( '/pricing/' ) ) . '">แพ็กเกจ</a></li>';
	echo '<li><a href="' . esc_url( home_url( '/articles/' ) ) . '">บทความ</a></li>';
	echo '<li><a href="' . esc_url( home_url( '/risk-disclosure/' ) ) . '">ความเสี่ยง</a></li>';
	echo '<li class="menu-item-ea2000-contact"><a href="' . esc_url( home_url( '/go/' ) ) . '">ติดต่อ</a></li>';
	echo '</ul>';
}

/**
 * เพิ่มลิงก์ "ติดต่อ" (link hub /go/) ต่อท้ายเมนูหลัก แม้จะใช้เมนูที่สร้างเองใน WordPress
 * เพิ่มเฉพาะเมื่อมีหน้า slug "go" เผยแพร่แล้ว และข้ามเมื่อเมนูมีลิงก์ /go/ รายการชื่อ "ติดต่อ" หรือลิงก์ตรงไป LINE OA อยู่แล้ว
 */
function ea2000_add_contact_to_primary_menu( $items, $args ) {
	if ( empty( $args->theme_location ) || 'primary' !== $args->theme_location ) {
		return $items;
	}

	$go_page = get_page_by_path( 'go' );
	if ( ! $go_page || 'publish' !== $go_page->post_status ) {
		return $items;
	}

	$go_url = home_url( '/go/' );
	if ( false !== strpos( $items, $go_url ) || false !== strpos( $items, esc_url( $go_url ) ) || false !== strpos( $items, '/go/' ) ) {
		return $items;
	}

	if ( false !== strpos( $items, '>ติดต่อ' ) ) {
		return $items;
	}

	if ( ea2000_has_line_url() ) {
		$line_url = trim( (string) ea2000_mod( 'line_url' ) );
		if ( false !== strpos( $items, $line_url ) || false !== strpos( $items, esc_url( $line_url ) ) ) {
			return $items;
		}
	}

	return $items . '<li class="menu-item menu-item-ea2000-contact"><a href="' . esc_url( $go_url ) . '">ติดต่อ</a></li>';
}
add_filter( 'wp_nav_menu_items', 'ea2000_add_contact_to_primary_menu', 10, 2 );

/**
 * Language switcher slot.
 *
 * This prefers multilingual plugins for real translated URLs, hreflang, SEO,
 * and Elementor compatibility. The manual fallback is only a visible starter
 * until a plugin such as TranslatePress, Polylang, or WPML owns translations.
 *
 * GTranslate is deliberately not supported: it translates on the client only,
 * so it creates no indexable URLs, and its widget is injected by JavaScript
 * with inline styles that the theme can only fight with !important. Those CSS
 * rules were removed on 8 Sep 2026 (see the note in style.css), so the theme
 * falls through to its own switcher even when that plugin is active.
 */
function ea2000_language_switcher() {
	if ( ! ea2000_mod( 'show_language_switcher' ) ) {
		return;
	}

	$plugin_markup = '';

	if ( shortcode_exists( 'language-switcher' ) ) {
		$plugin_markup = do_shortcode( '[language-switcher]' );
	} elseif ( function_exists( 'pll_the_languages' ) ) {
		$plugin_markup = pll_the_languages(
			array(
				'echo'          => 0,
				'show_flags'    => 0,
				'show_names'    => 1,
				'hide_if_empty' => 0,
			)
		);
	} elseif ( function_exists( 'icl_get_languages' ) ) {
		$wpml_languages = icl_get_languages( 'skip_missing=0&orderby=code' );

		if ( is_array( $wpml_languages ) && $wpml_languages ) {
			$plugin_markup = '<ul class="language-switcher-list">';
			foreach ( $wpml_languages as $language ) {
				if ( empty( $language['url'] ) || empty( $language['native_name'] ) ) {
					continue;
				}

				$plugin_markup .= sprintf(
					'<li><a class="%1$s" href="%2$s">%3$s</a></li>',
					! empty( $language['active'] ) ? 'is-active' : '',
					esc_url( $language['url'] ),
					esc_html( $language['native_name'] )
				);
			}
			$plugin_markup .= '</ul>';
		}
	}

	if ( $plugin_markup ) {
		echo '<div class="language-switcher language-switcher--plugin" aria-label="' . esc_attr__( 'Language switcher', 'ea2000' ) . '">';
		echo wp_kses_post( $plugin_markup );
		echo '</div>';
		return;
	}

	$languages = array();
	foreach ( ea2000_lines( ea2000_mod( 'language_fallback_items' ) ) as $language_line ) {
		$parts = array_map( 'trim', explode( '|', $language_line ) );
		if ( count( $parts ) < 3 ) {
			continue;
		}

		$code = sanitize_key( $parts[0] );
		if ( ! $code ) {
			continue;
		}

		if ( count( $parts ) >= 4 ) {
			$flag  = $parts[1];
			$short = $parts[2];
			$label = $parts[3];
		} else {
			$flag  = '';
			$short = $parts[1];
			$label = $parts[2];
		}

		$languages[ $code ] = array(
			'flag'  => $flag,
			'short' => $short,
			'label' => $label,
		);
	}

	if ( ! $languages ) {
		$languages = array(
			'th' => array(
				'flag'  => '🇹🇭',
				'short' => 'TH',
				'label' => 'ไทย',
			),
			'en' => array(
				'flag'  => '🇬🇧',
				'short' => 'EN',
				'label' => 'English',
			),
		);
	}

	$current = substr( get_locale(), 0, 2 );
	if ( isset( $_GET['lang'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$current = sanitize_key( wp_unslash( $_GET['lang'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	if ( ! isset( $languages[ $current ] ) ) {
		$current = 'th';
	}
	?>
	<details class="language-switcher language-switcher--fallback">
		<summary aria-label="<?php echo esc_attr__( 'Choose language', 'ea2000' ); ?>">
			<span class="language-switcher-current">
				<?php if ( ! empty( $languages[ $current ]['flag'] ) ) : ?>
					<span class="language-switcher-flag" aria-hidden="true"><?php echo esc_html( $languages[ $current ]['flag'] ); ?></span>
				<?php endif; ?>
				<span class="language-switcher-code"><?php echo esc_html( $languages[ $current ]['short'] ); ?></span>
			</span>
		</summary>
		<div class="language-switcher-menu">
			<?php foreach ( $languages as $code => $language ) : ?>
				<a class="<?php echo esc_attr( $code === $current ? 'is-active' : '' ); ?>" href="<?php echo esc_url( add_query_arg( 'lang', $code ) ); ?>">
					<?php if ( ! empty( $language['flag'] ) ) : ?>
						<span class="language-switcher-flag" aria-hidden="true"><?php echo esc_html( $language['flag'] ); ?></span>
					<?php endif; ?>
					<span class="language-switcher-code"><?php echo esc_html( $language['short'] ); ?></span>
					<small><?php echo esc_html( $language['label'] ); ?></small>
				</a>
			<?php endforeach; ?>
		</div>
	</details>
	<?php
}

/**
 * หัวหน้าเพจ (page hero) ใช้ร่วมกันทุกหน้าย่อย
 */
function ea2000_page_hero( $kicker, $title, $subtitle = '' ) {
	?>
	<section class="phero">
		<div class="phero-bg" aria-hidden="true">
			<span class="ember ember-a"></span>
			<span class="ember ember-b"></span>
		</div>
		<div class="container phero-inner reveal">
			<?php if ( $kicker ) : ?>
				<span class="kicker"><?php echo esc_html( $kicker ); ?></span>
			<?php endif; ?>
			<h1 class="phero-title"><?php echo esc_html( $title ); ?></h1>
			<?php if ( $subtitle ) : ?>
				<p class="phero-sub"><?php echo esc_html( $subtitle ); ?></p>
			<?php endif; ?>
		</div>
	</section>
	<?php
}

/**
 * แถบ CTA ทักไลน์ ใช้ปิดท้ายหน้าย่อย
 */
function ea2000_line_cta( $title = '', $sub = '' ) {
	if ( ! ea2000_has_line_url() ) {
		return;
	}

	$title = $title ? $title : 'มีคำถาม? ทักมาคุยกับเราได้เลย';
	$sub   = $sub ? $sub : 'สอบถามรายละเอียด การติดตั้ง และความเหมาะสมกับทุนของคุณได้ทาง LINE';
	?>
	<section class="section cta cta--slim" id="cta">
		<div class="cta-bg" aria-hidden="true"><span class="ember ember-c"></span></div>
		<div class="container container-narrow">
			<div class="cta-inner reveal">
				<h2><?php echo esc_html( $title ); ?></h2>
				<p><?php echo esc_html( $sub ); ?></p>
				<a class="btn btn-line btn-lg" href="<?php echo esc_url( ea2000_mod( 'line_url' ) ); ?>" target="_blank" rel="noopener">
					<?php echo ea2000_icon( 'line' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					ทัก LINE เพื่อสอบถาม
				</a>
			</div>
		</div>
	</section>
	<?php
}

/* --------------------------------------------------------------
 * Inline SVG icons
 * -------------------------------------------------------------- */
function ea2000_icon( $name, $class = 'icon' ) {
	$svg = array(
		'flame'    => '<path d="M12 2c1 4-3 5.5-3 9a3 3 0 0 0 6 0c0-1.2-.6-2.2-1.2-3.1C16.5 9.4 19 11.6 19 15a7 7 0 0 1-14 0c0-5 5-7.5 7-13z"/>',
		'pulse'    => '<path d="M3 12h4l2.5-6 4 12L16 12h5"/>',
		'clock'    => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/>',
		'gauge'    => '<path d="M4.5 19a9 9 0 1 1 15 0"/><path d="M12 13l4-4"/><circle cx="12" cy="14" r="1.6"/>',
		'flag'     => '<path d="M5 21V4"/><path d="M5 5h12l-2.5 3.5L17 12H5"/>',
		'home'     => '<path d="M4 11.5 12 5l8 6.5"/><path d="M6.5 10.5V20h11v-9.5"/><path d="M10 20v-5h4v5"/>',
		'chart'    => '<path d="M4 19V5"/><path d="M4 19h16"/><path d="M7 15l3-3 2.4 2.4L17.5 9"/><path d="M15 9h2.5v2.5"/>',
		'image'    => '<rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="8.5" cy="9.5" r="1.5"/><path d="M21 15l-5-5-7 7"/><path d="M3 17l4-4 3 3"/>',
		'account'  => '<circle cx="9" cy="8" r="3"/><path d="M3.8 19c.7-3 2.8-4.6 5.2-4.6 1.4 0 2.7.5 3.7 1.5"/><circle cx="17" cy="16.5" r="4"/><path d="M17 14.5v4M15 16.5h4"/>',
		'guide'    => '<path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H11v16H6.5A2.5 2.5 0 0 0 4 21V5.5z"/><path d="M20 5.5A2.5 2.5 0 0 0 17.5 3H13v16h4.5A2.5 2.5 0 0 1 20 21V5.5z"/><path d="M7 7h1.6M7 10h1.6M15.4 7H17M15.4 10H17"/>',
		'tag'      => '<path d="M20 12.5 12.5 20 4 11.5V4h7.5L20 12.5z"/><circle cx="8.2" cy="8.2" r="0.8"/>',
		'cpu'      => '<rect x="6" y="6" width="12" height="12" rx="2"/><rect x="10" y="10" width="4" height="4"/><path d="M9 2v3M15 2v3M9 19v3M15 19v3M2 9h3M2 15h3M19 9h3M19 15h3"/>',
		'candles'  => '<path d="M7 6v3M7 15v3M7 9h0a1.5 1.5 0 0 1 1.5 1.5v3A1.5 1.5 0 0 1 7 15h0a1.5 1.5 0 0 1-1.5-1.5v-3A1.5 1.5 0 0 1 7 9zM17 3v3M17 13v4M17 6h0a1.5 1.5 0 0 1 1.5 1.5v4A1.5 1.5 0 0 1 17 13h0a1.5 1.5 0 0 1-1.5-1.5v-4A1.5 1.5 0 0 1 17 6z"/><path d="M3 21h18"/>',
		'layout'   => '<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18M9 9v11"/>',
		'shield'   => '<path d="M12 3l7 3v5c0 4.6-3 8.4-7 10-4-1.6-7-5.4-7-10V6l7-3z"/><path d="M9 12l2 2 4-4"/>',
		'moon'     => '<path d="M20 14.5A8 8 0 1 1 9.5 4 6.5 6.5 0 0 0 20 14.5z"/>',
		'headset'  => '<path d="M4 13a8 8 0 0 1 16 0"/><rect x="3" y="13" width="4" height="6" rx="1.6"/><rect x="17" y="13" width="4" height="6" rx="1.6"/><path d="M19 19a3 3 0 0 1-3 3h-3"/>',
		'check'    => '<path d="M4 12.5l5 5L20 6.5"/>',
		'x'        => '<path d="M6 6l12 12M18 6L6 18"/>',
		'warn'     => '<path d="M12 3.5l9.5 16.5h-19L12 3.5z"/><path d="M12 10v4.2"/><circle cx="12" cy="17" r="0.4"/>',
		'chat'     => '<path d="M21 12a8 8 0 0 1-8 8c-1.2 0-2.4-.25-3.4-.7L4 21l1.4-4.2A8 8 0 1 1 21 12z"/><path d="M8.5 11h.01M12 11h.01M15.5 11h.01"/>',
		'arrow'    => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'quote'    => '<path d="M7.5 11c-1.7 0-3 1.3-3 3s1.3 3 3 3 3-1.3 3-3V9.5C10.5 7 9 5.5 7 5M17.5 11c-1.7 0-3 1.3-3 3s1.3 3 3 3 3-1.3 3-3V9.5C20.5 7 19 5.5 17 5"/>',
		'mail'     => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>',
		'download' => '<path d="M12 4v10M7.5 10.5L12 15l4.5-4.5"/><path d="M5 19h14"/>',
		'link'     => '<path d="M9.5 14.5l5-5"/><path d="M11.5 6.5l1-1a4 4 0 0 1 5.7 5.7l-2 2"/><path d="M12.5 17.5l-1 1a4 4 0 0 1-5.7-5.7l2-2"/>',
		'windows'  => '<path d="M3.5 5.5 10.5 4.5v6.8h-7z"/><path d="M13 4.1 20.5 3v8.3H13z"/><path d="M3.5 12.7h7v6.8l-7-1z"/><path d="M13 12.7h7.5V21L13 19.9z"/>',
		'android'  => '<path d="M8 8.5h8a3 3 0 0 1 3 3V17a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-5.5a3 3 0 0 1 3-3z"/><path d="M8.5 5 7 2.8M15.5 5 17 2.8"/><path d="M9 19v2M15 19v2M3 12v4M21 12v4"/><path d="M9.2 12h.01M14.8 12h.01"/>',
		'apple'    => '<path d="M16.6 13.1c0-2.1 1.7-3.1 1.8-3.2-1-1.5-2.5-1.7-3-1.7-1.3-.1-2.5.7-3.1.7-.7 0-1.7-.7-2.8-.7-1.5 0-2.9.9-3.6 2.2-1.6 2.7-.4 6.6 1.1 8.8.7 1.1 1.6 2.3 2.8 2.2 1.1 0 1.5-.7 2.8-.7 1.3 0 1.7.7 2.8.7 1.2 0 1.9-1.1 2.6-2.2.8-1.2 1.1-2.3 1.2-2.4-.1 0-2.6-1-2.6-3.7z"/><path d="M14.7 5.9c.6-.8 1-1.8.9-2.9-.9.1-1.9.6-2.5 1.3-.6.7-1.1 1.7-.9 2.8.9.1 1.9-.5 2.5-1.2z"/>',
		'macos'    => '<rect x="4" y="4.5" width="16" height="11" rx="1.8"/><path d="M8.5 19.5h7"/><path d="M12 15.5v4"/><path d="M9.8 9.7c-.45 0-.82-.37-.82-.83 0-.45.37-.82.82-.82"/><path d="M14.2 9.7c.45 0 .82-.37.82-.83 0-.45-.37-.82-.82-.82"/><path d="M9.9 12.1c1.2.75 3 .75 4.2 0"/>',
		'users'    => '<path d="M16.5 19.5c0-2.1-1.8-3.8-4.5-3.8s-4.5 1.7-4.5 3.8"/><circle cx="12" cy="9" r="3.2"/><path d="M20.5 18.5c0-1.7-1.2-3-3-3.4"/><path d="M16.8 6.3a2.6 2.6 0 0 1 0 5.1"/><path d="M3.5 18.5c0-1.7 1.2-3 3-3.4"/><path d="M7.2 6.3a2.6 2.6 0 0 0 0 5.1"/>',
	);

	// แบรนด์ไอคอน LINE (โลโก้จริง) · เป็น path แบบ fill ไม่ใช่ stroke จึง render แยก.
	if ( 'line' === $name ) {
		return '<svg class="' . esc_attr( $class ) . '" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true" focusable="false"><path d="M19.365 9.863c.349 0 .63.285.63.631 0 .345-.281.63-.63.63H17.61v1.125h1.755c.348 0 .63.283.63.63 0 .344-.282.629-.63.629h-2.386c-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63h2.386c.346 0 .627.285.627.63 0 .349-.281.63-.63.63H17.61v1.125h1.755zm-3.855 3.016c0 .27-.174.51-.432.596-.064.021-.133.031-.199.031-.211 0-.391-.09-.51-.25l-2.443-3.317v2.94c0 .344-.279.629-.631.629-.346 0-.626-.285-.626-.629V8.108c0-.27.173-.51.43-.595.06-.023.136-.033.194-.033.195 0 .375.104.495.254l2.462 3.33V8.108c0-.345.282-.63.63-.63.345 0 .63.285.63.63v4.771zm-5.741 0c0 .344-.282.629-.631.629-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63.346 0 .628.285.628.63v4.771zm-2.466.629H4.917c-.345 0-.63-.285-.63-.629V8.108c0-.345.285-.63.63-.63.348 0 .63.285.63.63v4.141h1.756c.348 0 .629.283.629.63 0 .344-.282.629-.629.629M24 10.314C24 4.943 18.615.572 12 .572S0 4.943 0 10.314c0 4.811 4.27 8.842 10.035 9.608.391.082.923.258 1.058.59.12.301.079.766.038 1.08l-.164 1.02c-.045.301-.24 1.186 1.049.645 1.291-.539 6.916-4.078 9.436-6.975C23.176 14.393 24 12.458 24 10.314"/></svg>';
	}

	// แบรนด์ไอคอนแบบ fill (Facebook / Instagram / TikTok / YouTube) trusted.
	$ea2000_brand = array(
		'facebook'  => '<path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>',
		'instagram' => '<path d="M12 2.163c3.204 0 3.584.012 4.85.07 1.366.062 2.633.336 3.608 1.311.975.975 1.249 2.242 1.311 3.608.058 1.266.07 1.646.07 4.85s-.012 3.584-.07 4.85c-.062 1.366-.336 2.633-1.311 3.608-.975.975-2.242 1.249-3.608 1.311-1.266.058-1.646.07-4.85.07s-3.584-.012-4.85-.07c-1.366-.062-2.633-.336-3.608-1.311-.975-.975-1.249-2.242-1.311-3.608-.058-1.266-.07-1.646-.07-4.85s.012-3.584.07-4.85c.062-1.366.336-2.633 1.311-3.608.975-.975 2.242-1.249 3.608-1.311 1.266-.058 1.646-.07 4.85-.07M12 0C8.741 0 8.332.015 7.052.073 5.197.158 3.355.673 2.014 2.014.673 3.355.158 5.197.073 7.052.015 8.332 0 8.741 0 12c0 3.259.015 3.668.073 4.948.085 1.855.6 3.697 1.941 5.038 1.341 1.341 3.183 1.856 5.038 1.941C8.332 23.985 8.741 24 12 24s3.668-.015 4.948-.073c1.855-.085 3.697-.6 5.038-1.941 1.341-1.341 1.856-3.183 1.941-5.038.058-1.28.073-1.689.073-4.948s-.015-3.668-.073-4.948c-.085-1.855-.6-3.697-1.941-5.038C20.645.673 18.803.158 16.948.073 15.668.015 15.259 0 12 0z"/><path d="M12 5.838A6.162 6.162 0 1 0 18.162 12 6.162 6.162 0 0 0 12 5.838zM12 16a4 4 0 1 1 4-4 4 4 0 0 1-4 4z"/><circle cx="18.406" cy="5.594" r="1.44"/>',
		'tiktok'    => '<path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.08-.14 1.62.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>',
		'youtube'   => '<path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>',
	);
	if ( isset( $ea2000_brand[ $name ] ) ) {
		return '<svg class="' . esc_attr( $class ) . '" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true" focusable="false">' . $ea2000_brand[ $name ] . '</svg>';
	}

	if ( ! isset( $svg[ $name ] ) ) {
		return '';
	}

	return '<svg class="' . esc_attr( $class ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $svg[ $name ] . '</svg>';
}

/**
 * แถวไอคอนโซเชียล (ใช้ทั้ง footer และเมนูมือถือ)
 * แสดงเฉพาะลิงก์ที่กรอกแล้วใน Customizer · ถ้ายังไม่มีลิงก์เลยจะไม่พิมพ์อะไรออกมา
 * ป้าย aria-label ของ Facebook อ่านจาก footer_facebook_text · อีเมลติดต่อ (contact_email) แสดงเป็นไอคอน mailto
 * พร้อมป้าย footer_email_text เฉพาะเมื่อกรอกแล้วและผ่าน is_email() (ที่อยู่ถูกเข้ารหัสด้วย antispambot)
 */
function ea2000_social_row( $class = 'social-row' ) {
	$ea2000_facebook_label = trim( (string) ea2000_mod( 'footer_facebook_text' ) );
	$ea2000_email_label    = trim( (string) ea2000_mod( 'footer_email_text' ) );
	$ea2000_socials        = array(
		'facebook'  => array( ea2000_mod( 'facebook_url' ), '' !== $ea2000_facebook_label ? $ea2000_facebook_label : 'Facebook' ),
		'instagram' => array( ea2000_mod( 'instagram_url' ), 'Instagram' ),
		'tiktok'    => array( ea2000_mod( 'tiktok_url' ), 'TikTok' ),
		'youtube'   => array( ea2000_mod( 'youtube_url' ), 'YouTube' ),
	);
	$ea2000_links = array();
	foreach ( $ea2000_socials as $ea2000_icon_name => $ea2000_s ) {
		$ea2000_url = trim( (string) $ea2000_s[0] );
		if ( '' === $ea2000_url || '#' === $ea2000_url ) {
			continue;
		}
		$ea2000_links[ $ea2000_icon_name ] = array( $ea2000_url, $ea2000_s[1] );
	}
	$ea2000_email = trim( (string) ea2000_mod( 'contact_email' ) );
	if ( '' !== $ea2000_email && is_email( $ea2000_email ) ) {
		$ea2000_links['mail'] = array( $ea2000_email, '' !== $ea2000_email_label ? $ea2000_email_label : 'Email' );
	}
	if ( empty( $ea2000_links ) ) {
		return;
	}
	echo '<div class="' . esc_attr( $class ) . '">';
	foreach ( $ea2000_links as $ea2000_icon_name => $ea2000_s ) {
		if ( 'mail' === $ea2000_icon_name ) {
			echo '<a class="social-link" href="mailto:' . esc_attr( antispambot( $ea2000_s[0] ) ) . '" aria-label="' . esc_attr( $ea2000_s[1] ) . '">';
		} else {
			echo '<a class="social-link" href="' . esc_url( $ea2000_s[0] ) . '" target="_blank" rel="noopener" aria-label="' . esc_attr( $ea2000_s[1] ) . '">';
		}
		echo ea2000_icon( $ea2000_icon_name ); // phpcs:ignore WordPress.Security.EscapeOutput
		echo '</a>';
	}
	echo '</div>';
}

/* --------------------------------------------------------------
 * Post views (ตัวนับยอดเข้าชมบทความ)
 * -------------------------------------------------------------- */
function ea2000_get_post_views( $post_id ) {
	return (int) get_post_meta( $post_id, 'ea2000_views', true );
}

function ea2000_increment_post_views( $post_id ) {
	if ( ! $post_id ) {
		return;
	}
	update_post_meta( $post_id, 'ea2000_views', ea2000_get_post_views( $post_id ) + 1 );
}

/* นับเฉพาะผู้เข้าชมหน้าบทความเดี่ยว (ข้ามแอดมิน เพื่อไม่ให้ตัวเลขเพี้ยน)
   หมายเหตุ: ถ้าใช้ปลั๊กแคชหน้า ตัวเลขอาจนับไม่ครบทุกครั้ง */
add_action(
	'wp_head',
	function () {
		if ( is_singular( 'post' ) && ! current_user_can( 'edit_posts' ) ) {
			ea2000_increment_post_views( get_queried_object_id() );
		}
	}
);

/* --------------------------------------------------------------
 * Open Graph / Twitter meta (รูปแชร์ LINE/Facebook)
 * ปิดอัตโนมัติถ้ามีปลั๊ก SEO (Yoast/Rank Math) เพื่อไม่ให้แท็กซ้ำ
 * -------------------------------------------------------------- */
function ea2000_open_graph() {
	$site        = get_bloginfo( 'name' );
	$default_img = ea2000_mod( 'og_default_image' );
	$img_alt     = '';
	if ( ! $default_img ) {
		$default_img = ea2000_logo_url();
	}

	if ( is_singular() ) {
		$title = get_the_title();
		$desc  = has_excerpt() ? get_the_excerpt() : wp_trim_words( wp_strip_all_tags( strip_shortcodes( get_the_content() ) ), 40, '…' );
		$url   = get_permalink();
		$img   = get_the_post_thumbnail_url( get_the_ID(), 'full' );
		if ( ! $img ) {
			$img = $default_img;
		}
		$type    = is_singular( 'post' ) ? 'article' : 'website';
		$img_alt = ea2000_featured_image_alt( get_the_ID() );
	} else {
		$title = is_front_page() ? $site : wp_strip_all_tags( wp_get_document_title() );
		$desc  = ea2000_mod( 'og_default_description' );
		$url   = home_url( '/' );
		$img   = $default_img;
		$type  = 'website';
	}

	if ( '' === $img_alt ) {
		$img_alt = $title;
	}

	$desc = trim( (string) $desc );
	if ( '' === $desc ) {
		$desc = trim( (string) ea2000_mod( 'og_default_description' ) );
	}

	if ( '' !== $desc ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $desc ) );
	}

	$tags = array(
		'og:site_name'   => $site,
		'og:locale'      => get_locale(),
		'og:type'        => $type,
		'og:title'       => $title,
		'og:description' => $desc,
		'og:url'         => $url,
		'og:image'       => $img,
	);

	foreach ( $tags as $property => $value ) {
		if ( '' === (string) $value ) {
			continue;
		}
		printf( '<meta property="%1$s" content="%2$s">' . "\n", esc_attr( $property ), esc_attr( $value ) );
	}

	if ( '' !== (string) $img ) {
		printf( '<meta property="og:image:alt" content="%s">' . "\n", esc_attr( $img_alt ) );
	}

	if ( 'article' === $type ) {
		printf( '<meta property="article:published_time" content="%s">' . "\n", esc_attr( get_the_date( 'c' ) ) );
		printf( '<meta property="article:modified_time" content="%s">' . "\n", esc_attr( get_the_modified_date( 'c' ) ) );
	}

	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );
	if ( '' !== $desc ) {
		printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $desc ) );
	}
	if ( '' !== (string) $img ) {
		printf( '<meta name="twitter:image" content="%s">' . "\n", esc_attr( $img ) );
		printf( '<meta name="twitter:image:alt" content="%s">' . "\n", esc_attr( $img_alt ) );
	}
}
if ( ! defined( 'WPSEO_VERSION' ) && ! class_exists( 'RankMath' ) && ! defined( 'SEOPRESS_VERSION' ) ) {
	add_action( 'wp_head', 'ea2000_open_graph', 5 );
}

/* --------------------------------------------------------------
 * Structured data (JSON-LD)
 * ไม่มีปลั๊ก SEO : Organization + WebSite (ทั้งเว็บ) + FAQPage (หน้าแรก) + สินค้า
 * มีปลั๊ก SEO   : ออกเฉพาะ SoftwareApplication ของสินค้า เพราะ WebPage / WebSite /
 *                Organization / BreadcrumbList ปลั๊กออกให้อยู่แล้ว ห้ามออกซ้ำ
 * -------------------------------------------------------------- */

/**
 * แปลงราคาแพ็กเกจเป็นตัวเลขจริง
 * คืน null ถ้าเป็นข้อความ (เช่น Free หรือ ทัก LINE) เพื่อไม่ประกาศราคาที่ไม่มีอยู่จริง
 *
 * @param string $key คีย์ราคาใน ea2000_defaults() เช่น pkg2_price.
 * @return float|null
 */
function ea2000_numeric_price( $key ) {
	$raw = trim( (string) ea2000_mod( $key ) );
	if ( '' === $raw ) {
		return null;
	}

	$clean = str_replace( array( ',', ' ', "\xc2\xa0" ), '', $raw );

	/* ตัดหน่วยเงินหน้า/ท้ายค่า (เช่น 6,990 บาท หรือ ฿6,990) ก่อนตรวจว่าเป็นตัวเลข
	   เพื่อไม่ให้ราคาที่เจ้าของพิมพ์พร้อมหน่วยหลุดหายไปจาก offers เงียบ ๆ */
	$stripped = preg_replace( '/^(?:฿|thb|baht)|(?:บาท|thb|baht|฿)$/iu', '', $clean );
	if ( null !== $stripped ) {
		$clean = trim( $stripped );
	}

	if ( ! is_numeric( $clean ) || (float) $clean <= 0 ) {
		return null;
	}

	return (float) $clean;
}

/**
 * ฟอร์แมตราคาให้อยู่ในรูปที่ schema.org รับ (ตัวเลขล้วน ไม่มีคอมมา)
 *
 * @param float $price ราคา.
 * @return string
 */
function ea2000_schema_price( $price ) {
	$decimals = ( floor( $price ) === $price ) ? 0 : 2;

	return number_format( $price, $decimals, '.', '' );
}

/**
 * ราคาแพ็กเกจสำหรับข้อมูลโครงสร้าง
 * เหมือน ea2000_numeric_price แต่ถือว่าแพ็กเกจฟรีคือราคา 0 เพื่อให้จำนวน offer
 * ตรงกับจำนวนแพ็กเกจที่ผู้เข้าชมเห็นบนหน้าเว็บ (Google กำหนดให้ตรงกัน)
 * คืน null เมื่อเป็นข้อความที่ไม่ใช่ราคา เช่น "ทัก LINE"
 *
 * @param string $key คีย์ราคาใน ea2000_defaults() เช่น pkg1_price.
 * @return float|null
 */
function ea2000_offer_price( $key ) {
	$raw = trim( (string) ea2000_mod( $key ) );
	if ( '' === $raw ) {
		return null;
	}

	/* strtolower พอ เพราะแปลงเฉพาะตัวอักษร ASCII ไม่แตะไบต์ภาษาไทย */
	$free = array( 'free', '0', 'ฟรี', 'ฟรี!', 'ไม่มีค่าใช้จ่าย' );
	if ( in_array( strtolower( $raw ), $free, true ) ) {
		return 0.0;
	}

	return ea2000_numeric_price( $key );
}

/**
 * offers ของสินค้า
 * ออกให้ Google เฉพาะเมื่อครบสามข้อ
 * 1) โหมดราคา (pricing_mode = price)
 * 2) เจ้าของยืนยันราคาแล้ว (setting pricing_confirmed หรือฟิลเตอร์ ea2000_emit_offers)
 * 3) แปลงราคาเป็นตัวเลขได้ครบทุกแพ็กเกจที่แสดงอยู่บนหน้าเว็บ (อ่านได้ไม่ครบ = ไม่ประกาศเลย)
 * นอกนั้นคืน null เพื่อให้ node ไม่มีคีย์ offers เลย ซึ่งยังเป็น schema.org ที่ถูกต้อง
 * เหตุผลของข้อ 2: ราคาที่ประกาศผ่านข้อมูลโครงสร้างจะไปโผล่ในผลค้นหา แก้ทีหลังช้ากว่าแก้หน้าเว็บ
 *
 * @return array|null
 */
function ea2000_product_offers() {
	if ( 'price' !== ea2000_mod( 'pricing_mode' ) ) {
		return null;
	}

	if ( ! (bool) apply_filters( 'ea2000_emit_offers', (bool) ea2000_mod( 'pricing_confirmed' ) ) ) {
		return null;
	}

	/* สกุลเงินของราคาบนหน้าเว็บ (ค่าเริ่มต้นบาท) เปลี่ยนได้ด้วยฟิลเตอร์ถ้าย้ายไปสกุลอื่น */
	$currency  = (string) apply_filters( 'ea2000_schema_price_currency', 'THB' );
	$offer_url = home_url( '/pricing/' );
	$offers    = array();
	$prices    = array();
	$visible   = 0;

	foreach ( array( 'pkg1', 'pkg2', 'pkg3' ) as $ea2000_pkg ) {
		/* แพ็กเกจที่ไม่มีชื่อจะไม่ถูกแสดงใน template-pricing.php จึงไม่นับว่ามองเห็น */
		$ea2000_pkg_name = trim( (string) ea2000_mod( $ea2000_pkg . '_name' ) );
		if ( '' === $ea2000_pkg_name ) {
			continue;
		}

		++$visible;

		$ea2000_price = ea2000_offer_price( $ea2000_pkg . '_price' );
		if ( null === $ea2000_price ) {
			continue;
		}

		$prices[] = $ea2000_price;

		$offers[] = array(
			'@type'         => 'Offer',
			'name'          => wp_strip_all_tags( $ea2000_pkg_name ),
			'price'         => ea2000_schema_price( $ea2000_price ),
			'priceCurrency' => $currency,
			'availability'  => 'https://schema.org/InStock',
			'url'           => $offer_url,
		);
	}

	/* ถ้าอ่านราคาได้ไม่ครบทุกแพ็กเกจที่แสดงบนหน้าเว็บ ให้ไม่ประกาศราคาเลย
	   ดีกว่าประกาศบางส่วนแล้วข้อมูลโครงสร้างขัดกับสิ่งที่ผู้เข้าชมเห็น */
	if ( empty( $offers ) || count( $offers ) < $visible ) {
		return null;
	}

	if ( 1 === count( $offers ) ) {
		return $offers[0];
	}

	sort( $prices, SORT_NUMERIC );

	return array(
		'@type'         => 'AggregateOffer',
		'lowPrice'      => ea2000_schema_price( $prices[0] ),
		'highPrice'     => ea2000_schema_price( end( $prices ) ),
		'offerCount'    => count( $offers ),
		'priceCurrency' => $currency,
		'offers'        => $offers,
	);
}

/**
 * Organization node ของแบรนด์ (ไม่ใส่ @context เพื่อให้ฝังในกราฟอื่นได้)
 * ใช้ทั้งเป็น node เดี่ยวตอนไม่มีปลั๊ก SEO และเป็น publisher แบบเต็มของ node สินค้า
 *
 * @return array
 */
function ea2000_organization_node() {
	$org = array(
		'@type' => 'Organization',
		'@id'   => home_url( '/' ) . '#organization',
		'name'  => get_bloginfo( 'name' ),
		'url'   => home_url( '/' ),
		'logo'  => ea2000_logo_url(),
	);

	$ea2000_same = array();
	foreach ( array( 'facebook_url', 'instagram_url', 'tiktok_url', 'youtube_url', 'line_url' ) as $ea2000_same_key ) {
		$ea2000_same_url = trim( (string) ea2000_mod( $ea2000_same_key ) );
		if ( '' === $ea2000_same_url || '#' === $ea2000_same_url || ! preg_match( '#^https?://#i', $ea2000_same_url ) ) {
			continue;
		}
		$ea2000_same[] = $ea2000_same_url;
	}
	if ( ! empty( $ea2000_same ) ) {
		$org['sameAs'] = array_values( array_unique( $ea2000_same ) );
	}

	return $org;
}

/**
 * publisher ของ node สินค้า
 *
 * อ้าง @id เฉย ๆ ได้เฉพาะตอนธีมเป็นคนออก Organization node เอง · เมื่อมีปลั๊ก SEO
 * ธีมจะไม่ออก node นั้น และไม่มีอะไรรับประกันว่าปลั๊กอินจะออก Organization ให้
 * (Yoast ออกให้ต่อเมื่อตั้ง Site representation = Organization ถ้าตั้งเป็น Person
 * จะได้ node ชนิด Person คนละ @id) จึงฝัง Organization แบบเต็มไปเลย
 * @id เดียวกันทำให้กราฟรวมเป็นตัวตนเดียว ไม่เกิด node ซ้ำ
 *
 * @return array
 */
function ea2000_publisher_ref() {
	if ( ea2000_has_seo_plugin() ) {
		return ea2000_organization_node();
	}

	return array(
		'@id' => home_url( '/' ) . '#organization',
	);
}

/**
 * หน้าที่ควรออก node ของสินค้า: หน้าแรกและหน้าแพ็กเกจ
 *
 * @return bool
 */
function ea2000_is_product_page() {
	return is_front_page() || is_page_template( 'template-pricing.php' );
}

/**
 * SoftwareApplication node ของ EA2000 (ออกทั้งตอนมีและไม่มีปลั๊ก SEO)
 *
 * ข้อควรรู้เรื่อง rich result: Google ให้ผลพิเศษกับ SoftwareApplication ก็ต่อเมื่อมี name
 * บวกอย่างน้อยหนึ่งใน offers / aggregateRating / review · เว็บนี้จะไม่มี aggregateRating
 * และ review จนกว่าจะมีรีวิวจริง (ห้ามแต่งขึ้นมา) ดังนั้น node นี้จะได้ rich result
 * เฉพาะตอนโหมดราคาและเจ้าของยืนยันราคาแล้วเท่านั้น · ในโหมด contact node ยังถูกต้อง
 * ตาม schema.org แต่ไม่เข้าเงื่อนไข rich result ซึ่งยอมรับได้ เพราะยังช่วยระบุตัวตนแบรนด์
 * (ชื่อ EA2000 ชนกับหูฟัง SIMGOT EA2000 และ Energy Absolute)
 *
 * @return array
 */
function ea2000_product_schema() {
	$name = trim( (string) ea2000_mod( 'product_name' ) );
	if ( '' === $name ) {
		$name = trim( (string) ea2000_mod( 'brand_name' ) );
	}
	if ( '' === $name ) {
		$name = get_bloginfo( 'name' );
	}

	/* @id และ url คงที่ทุกหน้า เพื่อให้เป็นตัวตนเดียวของสินค้าทั้งเว็บ
	   ส่วนหน้าที่กำลังแสดง node นี้อยู่ ใส่ไว้ใน mainEntityOfPage */
	$product_url = home_url( '/' );
	$page_url    = $product_url;
	if ( ! is_front_page() ) {
		$permalink = get_permalink();
		if ( $permalink ) {
			$page_url = $permalink;
		}
	}

	$node = array(
		'@context'            => 'https://schema.org',
		'@type'               => 'SoftwareApplication',
		'@id'                 => home_url( '/' ) . '#product',
		'name'                => wp_strip_all_tags( $name ),
		'applicationCategory' => 'FinanceApplication',
		'url'                 => $product_url,
		'mainEntityOfPage'    => $page_url,
		'publisher'           => ea2000_publisher_ref(),
	);

	/* operatingSystem คือระบบปฏิบัติการที่รันโปรแกรม ส่วน MT5 เป็นแพลตฟอร์มจึงอยู่ใน softwareRequirements */
	$os = trim( (string) ea2000_mod( 'product_os' ) );
	if ( '' !== $os ) {
		$node['operatingSystem'] = wp_strip_all_tags( $os );
	}

	$requirements = trim( (string) ea2000_mod( 'product_requirements' ) );
	if ( '' !== $requirements ) {
		$node['softwareRequirements']   = wp_strip_all_tags( $requirements );
		$node['applicationSubCategory'] = 'Trading';
	}

	$version = trim( (string) ea2000_mod( 'product_version' ) );
	if ( '' !== $version ) {
		$node['softwareVersion'] = wp_strip_all_tags( $version );
	}

	$image = trim( (string) ea2000_mod( 'og_default_image' ) );
	if ( '' !== $image ) {
		$node['image'] = esc_url_raw( $image );
	}

	$desc = trim( (string) ea2000_mod( 'og_default_description' ) );
	if ( '' !== $desc ) {
		$node['description'] = wp_strip_all_tags( $desc );
	}

	$offers = ea2000_product_offers();
	if ( null !== $offers ) {
		$node['offers'] = $offers;
	}

	return $node;
}

/**
 * FAQPage node จาก FAQ บนหน้าแรก (faq1..faq10 ที่กรอกทั้งคำถามและคำตอบ)
 * ออกเฉพาะหน้าแรกและเมื่อเปิด show_faq · คืน null เมื่อไม่มีข้อไหนกรอก
 * Yoast ไม่ออก FAQPage ให้หน้านี้ (ไม่ได้ใช้บล็อก FAQ ของ Yoast) จึงออกจากธีมแม้มีปลั๊กอิน SEO
 */
function ea2000_faq_schema() {
	if ( ! is_front_page() || ! ea2000_mod( 'show_faq' ) ) {
		return null;
	}

	$faqs = array();
	for ( $i = 1; $i <= 10; $i++ ) {
		$q = trim( wp_strip_all_tags( (string) ea2000_mod( 'faq' . $i . '_q' ) ) );
		$a = trim( wp_strip_all_tags( (string) ea2000_mod( 'faq' . $i . '_a' ) ) );
		if ( '' === $q || '' === $a ) {
			continue;
		}
		$faqs[] = array(
			'@type'          => 'Question',
			'name'           => $q,
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => $a,
			),
		);
	}

	if ( ! $faqs ) {
		return null;
	}

	return array(
		'@context'   => 'https://schema.org',
		'@type'      => 'FAQPage',
		'@id'        => home_url( '/' ) . '#faq',
		'mainEntity' => $faqs,
	);
}

function ea2000_schema_jsonld() {
	$blocks = array();

	if ( ! ea2000_has_seo_plugin() ) {
		/* @id ตรงกับที่ node สินค้าอ้างถึงใน publisher (รูปแบบเดียวกับที่ Yoast ใช้) */
		$blocks[] = array_merge(
			array( '@context' => 'https://schema.org' ),
			ea2000_organization_node()
		);

		$blocks[] = array(
			'@context'  => 'https://schema.org',
			'@type'     => 'WebSite',
			'@id'       => home_url( '/' ) . '#website',
			'name'      => get_bloginfo( 'name' ),
			'url'       => home_url( '/' ),
			'publisher' => array(
				'@id' => home_url( '/' ) . '#organization',
			),
		);
	}

	if ( ea2000_is_product_page() ) {
		$blocks[] = ea2000_product_schema();
	}

	/* FAQPage ออกนอก guard ปลั๊กอิน SEO เพื่อให้พิมพ์คู่กับ SoftwareApplication แม้ Yoast ทำงานอยู่ */
	$faq_block = ea2000_faq_schema();
	if ( null !== $faq_block ) {
		$blocks[] = $faq_block;
	}

	foreach ( $blocks as $ea2000_block ) {
		echo '<script type="application/ld+json">' . wp_json_encode( $ea2000_block, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}
add_action( 'wp_head', 'ea2000_schema_jsonld', 6 );

/* --------------------------------------------------------------
 * SEO เสริม: canonical (archive), robots, sitemap, verification,
 * preconnect ฟอนต์ และ BreadcrumbList helper
 * -------------------------------------------------------------- */
function ea2000_has_seo_plugin() {
	return defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' ) || defined( 'SEOPRESS_VERSION' );
}

/* preconnect ฟอนต์ Google ลด RTT ให้โหลดเร็วขึ้นบนมือถือ */
function ea2000_resource_hints( $hints, $relation ) {
	if ( 'preconnect' === $relation ) {
		$hints[] = 'https://fonts.googleapis.com';
		$hints[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}
	return $hints;
}
add_filter( 'wp_resource_hints', 'ea2000_resource_hints', 10, 2 );

/* meta ยืนยันความเป็นเจ้าของเว็บ (Google Search Console / Bing) แสดงเมื่อกรอกเท่านั้น และปิดถ้ามีปลั๊ก SEO / Site Kit จัดการเอง */
function ea2000_verification_meta() {
	if ( ea2000_has_seo_plugin() ) {
		return;
	}
	$gsc  = trim( (string) ea2000_mod( 'search_console_verify' ) );
	$bing = trim( (string) ea2000_mod( 'bing_verify' ) );
	if ( '' !== $gsc ) {
		printf( '<meta name="google-site-verification" content="%s">' . "\n", esc_attr( $gsc ) );
	}
	if ( '' !== $bing ) {
		printf( '<meta name="msvalidate.01" content="%s">' . "\n", esc_attr( $bing ) );
	}
}
add_action( 'wp_head', 'ea2000_verification_meta', 1 );

/* canonical สำหรับหน้า archive (หมวด/แท็ก) ที่ WP core ไม่ออกให้ ปิดถ้ามีปลั๊ก SEO */
function ea2000_archive_canonical() {
	if ( ea2000_has_seo_plugin() ) {
		return;
	}
	$url = '';
	if ( is_category() || is_tag() || is_tax() ) {
		$term = get_queried_object();
		if ( $term && ! is_wp_error( $term ) ) {
			$link = get_term_link( $term );
			if ( ! is_wp_error( $link ) ) {
				$url = $link;
			}
		}
	} elseif ( is_post_type_archive() ) {
		$url = get_post_type_archive_link( get_post_type() );
	}
	if ( ! $url ) {
		return;
	}
	$paged = max( (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
	if ( $paged > 1 ) {
		$url = trailingslashit( $url ) . 'page/' . $paged . '/';
	}
	printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $url ) );
}
add_action( 'wp_head', 'ea2000_archive_canonical' );

/* noindex หน้าบาง/ซ้ำ (ค้นหา, ผู้เขียน, วันที่) ไม่ให้แย่ง crawl budget */
function ea2000_robots_noindex( $robots ) {
	if ( is_search() || is_author() || is_date() ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
	}
	return $robots;
}
add_filter( 'wp_robots', 'ea2000_robots_noindex' );

/* ชี้ sitemap ของ WP core ใน robots.txt (เฉพาะตอนไม่มีปลั๊ก SEO ที่จัดการ sitemap เอง)
   WP core เองก็เติมบรรทัดนี้ผ่าน WP_Sitemaps::add_robots() อยู่แล้ว จึงต้องเช็กก่อนว่ามีหรือยัง
   ไม่งั้นได้บรรทัด Sitemap ซ้ำสองบรรทัด · esc_url_raw เพราะ robots.txt เป็น plain text
   (esc_url จะแปลง & เป็น &#038;) */
function ea2000_robots_txt( $output, $public ) {
	if ( '1' !== (string) $public || ea2000_has_seo_plugin() ) {
		return $output;
	}

	if ( false !== strpos( (string) $output, 'wp-sitemap.xml' ) ) {
		return $output;
	}

	if ( '' !== trim( (string) $output ) && "\n" !== substr( $output, -1 ) ) {
		$output .= "\n";
	}

	$output .= 'Sitemap: ' . esc_url_raw( home_url( '/wp-sitemap.xml' ) ) . "\n";

	return $output;
}
add_filter( 'robots_txt', 'ea2000_robots_txt', 10, 2 );

/* BreadcrumbList JSON-LD เรียกจาก single.php / page.php ปิดถ้ามีปลั๊ก SEO */
function ea2000_breadcrumb_jsonld( $items ) {
	if ( ea2000_has_seo_plugin() || empty( $items ) ) {
		return;
	}
	$list = array();
	$pos  = 1;
	foreach ( $items as $it ) {
		$entry = array(
			'@type'    => 'ListItem',
			'position' => $pos,
			'name'     => wp_strip_all_tags( $it['name'] ),
		);
		if ( ! empty( $it['url'] ) ) {
			$entry['item'] = $it['url'];
		}
		$list[] = $entry;
		$pos++;
	}
	$schema = array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $list,
	);
	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE ) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput
}

/* ห่อ <table> ในเนื้อหาโพสต์ด้วย div ที่เลื่อนแนวนอนได้ (กันตารางล้นจอมือถือ) */
function ea2000_wrap_content_tables( $content ) {
	if ( ! is_singular() || false === strpos( $content, '<table' ) ) {
		return $content;
	}
	$content = str_replace( '<table', '<div class="table-scroll"><table', $content );
	$content = str_replace( '</table>', '</table></div>', $content );
	return $content;
}
add_filter( 'the_content', 'ea2000_wrap_content_tables', 20 );

/* --------------------------------------------------------------
 * Table of Contents · เก็บหัวข้อ H2/H3 จากเนื้อหาบทความ + ใส่ id ให้ลิงก์
 * ($GLOBALS['ea2000_toc'] ถูกเติมตอน the_content ถูกประมวลผล)
 * -------------------------------------------------------------- */
function ea2000_collect_toc( $content ) {
	if ( ! ( is_singular( 'post' ) && is_main_query() && in_the_loop() ) ) {
		return $content;
	}

	$GLOBALS['ea2000_toc'] = array();
	$index                = 0;

	return preg_replace_callback(
		'/<(h[23])([^>]*)>(.*?)<\/\1>/is',
		function ( $matches ) use ( &$index ) {
			$index++;
			$tag   = strtolower( $matches[1] );
			$attrs = $matches[2];
			$inner = $matches[3];

			if ( preg_match( '/\bid=["\']([^"\']+)["\']/', $attrs, $id_match ) ) {
				$id = $id_match[1];
			} else {
				$id     = 'toc-' . $index;
				$attrs .= ' id="' . $id . '"';
			}

			$GLOBALS['ea2000_toc'][] = array(
				'level' => (int) substr( $tag, 1 ),
				'text'  => trim( wp_strip_all_tags( $inner ) ),
				'id'    => $id,
			);

			return '<' . $tag . $attrs . '>' . $inner . '</' . $tag . '>';
		},
		$content
	);
}
add_filter( 'the_content', 'ea2000_collect_toc', 20 );

/* ตัดคำนำหน้า "หมวดหมู่:" / "ป้ายกำกับ:" ออกจากหัวข้อหน้า archive */
add_filter( 'get_the_archive_title_prefix', '__return_empty_string' );

/* --------------------------------------------------------------
 * การ์ดบทความ (ใช้ร่วมกันที่ index และ AJAX โหลดเพิ่ม)
 * -------------------------------------------------------------- */
function ea2000_post_card() {
	$cats = get_the_category();
	$cat  = ! empty( $cats ) ? $cats[0] : null;
	?>
	<article <?php post_class( 'post-card' ); ?>>
		<?php if ( has_post_thumbnail() ) : ?>
			<a class="post-card-thumb" href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail( 'medium_large', array( 'alt' => esc_attr( ea2000_featured_image_alt() ) ) ); ?>
				<?php if ( $cat ) : ?>
					<span class="post-card-cat"><?php echo esc_html( $cat->name ); ?></span>
				<?php endif; ?>
			</a>
		<?php endif; ?>
		<div class="post-card-body">
			<span class="post-meta"><?php echo esc_html( get_the_date() ); ?></span>
			<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
			<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
			<span class="post-card-more">อ่านต่อ <?php echo ea2000_icon( 'arrow', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		</div>
	</article>
	<?php
}

/* --------------------------------------------------------------
 * AJAX โหลดบทความเพิ่ม (ปุ่ม "โหลดเพิ่ม")
 * -------------------------------------------------------------- */
function ea2000_load_more() {
	check_ajax_referer( 'ea2000_load_more', 'nonce' );

	$page = isset( $_POST['page'] ) ? max( 1, (int) $_POST['page'] ) : 1;

	$incoming = array();
	if ( isset( $_POST['query'] ) ) {
		$decoded = json_decode( wp_unslash( $_POST['query'] ), true ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( is_array( $decoded ) ) {
			$incoming = $decoded;
		}
	}

	// รับเฉพาะ query var ที่หน้าเว็บส่งมาได้จริง + sanitize ทีละค่า (กัน inject meta_query/tax_query หนัก ๆ)
	$query = array();
	if ( isset( $incoming['category_name'] ) ) {
		$query['category_name'] = sanitize_text_field( $incoming['category_name'] );
	}
	if ( isset( $incoming['cat'] ) ) {
		$query['cat'] = (int) $incoming['cat'];
	}
	if ( isset( $incoming['tag'] ) ) {
		$query['tag'] = sanitize_text_field( $incoming['tag'] );
	}
	if ( isset( $incoming['author'] ) ) {
		$query['author'] = (int) $incoming['author'];
	}
	if ( isset( $incoming['author_name'] ) ) {
		$query['author_name'] = sanitize_text_field( $incoming['author_name'] );
	}
	if ( isset( $incoming['s'] ) ) {
		$query['s'] = sanitize_text_field( $incoming['s'] );
	}

	// บังคับค่าที่ปลอดภัย ไม่ให้ฝั่ง client กำหนดเอง
	$query['paged']               = $page;
	$query['post_type']           = 'post';
	$query['post_status']         = 'publish';
	$query['posts_per_page']      = (int) get_option( 'posts_per_page' );
	$query['ignore_sticky_posts'] = true;

	$loop = new WP_Query( $query );
	if ( $loop->have_posts() ) {
		while ( $loop->have_posts() ) {
			$loop->the_post();
			ea2000_post_card();
		}
	}
	wp_reset_postdata();
	wp_die();
}
add_action( 'wp_ajax_ea2000_load_more', 'ea2000_load_more' );
add_action( 'wp_ajax_nopriv_ea2000_load_more', 'ea2000_load_more' );

/* --------------------------------------------------------------
 * Production security hardening
 * -------------------------------------------------------------- */

/**
 * Disable comments and pingbacks even if an individual post is misconfigured.
 */
function ea2000_disable_discussion_support() {
	remove_post_type_support( 'post', 'comments' );
	remove_post_type_support( 'post', 'trackbacks' );
	remove_post_type_support( 'page', 'comments' );
	remove_post_type_support( 'page', 'trackbacks' );
}
add_action( 'init', 'ea2000_disable_discussion_support', 100 );
add_filter( 'comments_open', '__return_false', 20, 2 );
add_filter( 'pings_open', '__return_false', 20, 2 );
add_filter( 'comments_array', '__return_empty_array', 10, 2 );

/**
 * Remove comment management links from the dashboard.
 */
function ea2000_remove_comments_admin_menu() {
	remove_menu_page( 'edit-comments.php' );
}
add_action( 'admin_menu', 'ea2000_remove_comments_admin_menu', 100 );

function ea2000_remove_comments_admin_bar( $wp_admin_bar ) {
	$wp_admin_bar->remove_node( 'comments' );
}
add_action( 'admin_bar_menu', 'ea2000_remove_comments_admin_bar', 100 );

/**
 * Disable XML-RPC and remove its public discovery links.
 */
add_filter( 'xmlrpc_enabled', '__return_false' );
add_filter( 'xmlrpc_methods', '__return_empty_array' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );

/**
 * กู้ header Authorization บนโฮสต์ที่ตัดทิ้ง (CGI/FastCGI/LiteSpeed)
 * เพื่อให้ Application Passwords ใช้กับ REST API ได้ · ไม่มีผลถ้า PHP เห็น header อยู่แล้ว
 */
function ea2000_restore_authorization_header() {
	if ( ! empty( $_SERVER['HTTP_AUTHORIZATION'] ) || ! empty( $_SERVER['PHP_AUTH_USER'] ) ) {
		return;
	}

	$header = '';
	if ( ! empty( $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ) ) {
		$header = $_SERVER['REDIRECT_HTTP_AUTHORIZATION']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	} elseif ( function_exists( 'apache_request_headers' ) ) {
		$headers = apache_request_headers();
		if ( is_array( $headers ) ) {
			foreach ( $headers as $name => $value ) {
				if ( 'authorization' === strtolower( $name ) ) {
					$header = $value;
					break;
				}
			}
		}
	}

	// ทางเลือกสุดท้าย: ไคลเอนต์ส่งมาในชื่อ X-Authorization (โฮสต์บางเจ้าตัดเฉพาะชื่อ Authorization).
	if ( '' === $header && ! empty( $_SERVER['HTTP_X_AUTHORIZATION'] ) ) {
		$header = $_SERVER['HTTP_X_AUTHORIZATION']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	}

	if ( '' === $header ) {
		return;
	}

	$_SERVER['HTTP_AUTHORIZATION'] = $header;
	if ( function_exists( 'wp_populate_basic_auth_from_authorization_header' ) ) {
		wp_populate_basic_auth_from_authorization_header();
	}
}
ea2000_restore_authorization_header();

/**
 * จุดตรวจว่า header Authorization มาถึง PHP หรือไม่ · คืนเฉพาะ true/false ไม่มีข้อมูลลับ
 * GET /wp-json/ea2000/v1/authcheck
 */
function ea2000_register_authcheck_route() {
	register_rest_route(
		'ea2000/v1',
		'/authcheck',
		array(
			'methods'             => 'GET',
			'permission_callback' => '__return_true',
			'callback'            => 'ea2000_authcheck_response',
		)
	);
}
add_action( 'rest_api_init', 'ea2000_register_authcheck_route' );

function ea2000_authcheck_response() {
	$apache_keys = array();
	if ( function_exists( 'apache_request_headers' ) ) {
		$apache_keys = array_map( 'strtolower', array_keys( (array) apache_request_headers() ) );
	}

	return array(
		'http_authorization'     => ! empty( $_SERVER['HTTP_AUTHORIZATION'] ),
		'redirect_authorization' => ! empty( $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ),
		'x_authorization'        => ! empty( $_SERVER['HTTP_X_AUTHORIZATION'] ),
		'apache_authorization'   => in_array( 'authorization', $apache_keys, true ),
		'php_auth_user_set'      => isset( $_SERVER['PHP_AUTH_USER'] ),
		'logged_in_user_id'      => get_current_user_id(),
	);
}

/**
 * REST สำหรับผู้ดูแล: อ่าน/แก้ค่า Customizer (theme_mod) เฉพาะ key ที่มีใน ea2000_defaults()
 * GET  /wp-json/ea2000/v1/mods                      → ค่าปัจจุบันทุก key
 * POST /wp-json/ea2000/v1/mods {"key":"value",...}  → บันทึก (ส่ง null = ล้างกลับเป็นค่าเริ่มต้น)
 * ต้องมีสิทธิ์ edit_theme_options (ผู้ดูแลระบบ) เท่านั้น
 */
function ea2000_register_mods_route() {
	register_rest_route(
		'ea2000/v1',
		'/mods',
		array(
			array(
				'methods'             => 'GET',
				'permission_callback' => 'ea2000_mods_permission',
				'callback'            => 'ea2000_mods_get',
			),
			array(
				'methods'             => 'POST',
				'permission_callback' => 'ea2000_mods_permission',
				'callback'            => 'ea2000_mods_update',
			),
		)
	);
}
add_action( 'rest_api_init', 'ea2000_register_mods_route' );

function ea2000_mods_permission() {
	return current_user_can( 'edit_theme_options' );
}

function ea2000_mods_get() {
	$out = array();
	foreach ( array_keys( ea2000_defaults() ) as $key ) {
		$out[ $key ] = ea2000_mod( $key );
	}
	return $out;
}

function ea2000_mods_sanitize( $key, $value, $default ) {
	if ( 'pricing_mode' === $key && function_exists( 'ea2000_sanitize_pricing_mode' ) ) {
		return ea2000_sanitize_pricing_mode( $value );
	}
	if ( is_bool( $default ) ) {
		return in_array( $value, array( true, 1, '1', 'true', 'on' ), true );
	}
	if ( preg_match( '/(_url|_img|_image|_logo|_wordmark)$/', $key ) ) {
		return esc_url_raw( (string) $value );
	}
	return sanitize_textarea_field( (string) $value );
}

function ea2000_mods_update( WP_REST_Request $request ) {
	$defaults = ea2000_defaults();
	$data     = $request->get_json_params();

	if ( ! is_array( $data ) || empty( $data ) ) {
		return new WP_Error( 'ea2000_mods_empty', 'ส่ง JSON object {"key":"value"} อย่างน้อย 1 รายการ', array( 'status' => 400 ) );
	}

	$saved    = array();
	$rejected = array();
	foreach ( $data as $key => $value ) {
		if ( ! array_key_exists( $key, $defaults ) ) {
			$rejected[] = $key;
			continue;
		}
		if ( null === $value ) {
			remove_theme_mod( $key );
		} else {
			set_theme_mod( $key, ea2000_mods_sanitize( $key, $value, $defaults[ $key ] ) );
		}
		$saved[ $key ] = ea2000_mod( $key );
	}

	return array(
		'saved'    => $saved,
		'rejected' => $rejected,
	);
}

/**
 * Block the public REST user directory while keeping it available to editors.
 *
 * @param mixed           $result  Response to replace, or null.
 * @param WP_REST_Server  $server  REST server instance.
 * @param WP_REST_Request $request Current REST request.
 * @return mixed
 */
function ea2000_restrict_public_user_rest_api( $result, $server, $request ) {
	unset( $server );

	if ( is_user_logged_in() ) {
		return $result;
	}

	if ( 0 === strpos( $request->get_route(), '/wp/v2/users' ) ) {
		return new WP_Error(
			'rest_user_directory_forbidden',
			__( 'The user directory is not public.', 'ea2000' ),
			array( 'status' => 403 )
		);
	}

	return $result;
}
add_filter( 'rest_pre_dispatch', 'ea2000_restrict_public_user_rest_api', 10, 3 );

/**
 * Remove unnecessary disclosure headers and disable sensitive browser APIs.
 *
 * @param array $headers Response headers.
 * @return array
 */
function ea2000_harden_response_headers( $headers ) {
	unset( $headers['X-Pingback'] );
	$headers['Permissions-Policy'] = 'camera=(), microphone=(), geolocation=()';
	return $headers;
}
add_filter( 'wp_headers', 'ea2000_harden_response_headers', 20 );

function ea2000_remove_powered_by_header() {
	if ( function_exists( 'header_remove' ) ) {
		header_remove( 'X-Powered-By' );
	}
}
add_action( 'send_headers', 'ea2000_remove_powered_by_header', 100 );

/* --------------------------------------------------------------
 * ลดขนาดหน้าเว็บ: ตัด CSS/JS ที่ธีมนี้ไม่ได้ใช้ (block editor และอีโมจิ)
 * ทำเฉพาะฝั่งผู้ชมเท่านั้น หน้าแอดมินและตัวแก้ไขบล็อกไม่ถูกแตะ
 * -------------------------------------------------------------- */

/**
 * หน้านี้ต้องใช้ CSS ของบล็อก Gutenberg หรือไม่
 * เทมเพลตของธีมไม่ได้ใช้บล็อก แต่ถ้าเนื้อหาเพจหรือบทความเขียนด้วยบล็อก ต้องคง CSS ไว้ ไม่งั้นเลย์เอาต์เพี้ยน
 *
 * @return bool
 */
function ea2000_needs_block_styles() {
	if ( is_singular() && function_exists( 'has_blocks' ) && has_blocks( get_queried_object_id() ) ) {
		return true;
	}

	/* เปิดคืนได้ด้วยฟิลเตอร์ ถ้าอนาคตมีหน้าที่ต้องใช้ CSS ของบล็อกจริง */
	return (bool) apply_filters( 'ea2000_keep_block_styles', false );
}

/**
 * ถอด stylesheet ของ block editor และอีโมจิออกจากหน้าเว็บฝั่งผู้ชม
 */
function ea2000_trim_front_assets() {
	if ( is_admin() ) {
		return;
	}

	/* สไตล์อีโมจิไม่ได้ใช้ในทุกกรณี */
	wp_dequeue_style( 'wp-emoji-styles' );

	if ( ea2000_needs_block_styles() ) {
		return;
	}

	$handles = array( 'wp-block-library', 'wp-block-library-theme', 'global-styles', 'classic-theme-styles' );
	foreach ( $handles as $ea2000_handle ) {
		wp_dequeue_style( $ea2000_handle );
	}
}
add_action( 'wp_enqueue_scripts', 'ea2000_trim_front_assets', 100 );

/**
 * ปิดสคริปต์ตรวจอีโมจิของ WordPress เฉพาะฝั่งผู้ชม
 * ค้น priority จริงด้วย has_action() เพื่อให้ยังทำงานแม้ WordPress ย้าย hook ในเวอร์ชันใหม่
 */
function ea2000_disable_emoji() {
	if ( is_admin() ) {
		return;
	}

	$hooks     = array( 'wp_head', 'wp_footer', 'wp_print_styles', 'wp_print_scripts', 'wp_print_head_scripts', 'wp_print_footer_scripts', 'wp_enqueue_scripts' );
	$callbacks = array( 'print_emoji_detection_script', 'wp_print_emoji_detection_script', 'wp_enqueue_emoji_detection_script', 'print_emoji_styles', 'wp_enqueue_emoji_styles' );

	foreach ( $hooks as $ea2000_hook ) {
		foreach ( $callbacks as $ea2000_callback ) {
			$ea2000_priority = has_action( $ea2000_hook, $ea2000_callback );
			if ( false !== $ea2000_priority ) {
				remove_action( $ea2000_hook, $ea2000_callback, $ea2000_priority );
			}
		}
	}

	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
}
add_action( 'init', 'ea2000_disable_emoji', 20 );

/* --------------------------------------------------------------
 * Customizer
 * -------------------------------------------------------------- */
require get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/seo.php';
