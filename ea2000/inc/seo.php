<?php
/**
 * EA2000 · SEO เสริม: redirect ของเก่า, robots.txt, noindex หน้าที่ยังไม่มีข้อมูลจริง
 * และเปิด meta ของ Yoast ให้แก้ผ่าน REST API ได้
 *
 * ไฟล์นี้แยกจาก functions.php เพื่อให้แก้เรื่อง SEO ได้ที่เดียว
 *
 * @package ea2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* --------------------------------------------------------------
 * 1) 301 redirect ของ URL เก่าสมัยเว็บ EA Special
 * -------------------------------------------------------------- */

/**
 * ตาราง redirect: path เก่า (มี / หน้าและหลัง) => path ใหม่บนเว็บนี้
 *
 * ปรับได้ผ่าน filter 'ea2000_redirect_map' โดยไม่ต้องแก้ไฟล์
 * ค่า '/' หมายถึงหน้าแรก
 *
 * วิธีดูแลตารางนี้
 * - เมื่อเพจเก่าถูกลบจริงในขั้น 7 แล้ว ให้คงบรรทัดไว้เพื่อรับลิงก์จากภายนอก
 * - ถ้าจะเอา slug เดิมกลับมาใช้เป็นหน้าใหม่ ให้ลบบรรทัดนั้นทิ้ง (ตัว guard ใน
 *   ea2000_legacy_redirects() จะยอมให้เพจที่ publish อยู่ชนะตารางอยู่แล้ว
 *   ยกเว้นเพจที่ id อยู่ใน ea2000_legacy_page_ids() ดูคำอธิบายของฟังก์ชันนั้น
 *   แต่การลบบรรทัดออกชัดเจนกว่าและกันความสับสน)
 * - ไม่ใส่ '/about/' ในตารางนี้: แผนข้อ 0.6 ให้เอา page id 27 กลับมาเป็นหน้า About
 *   ของ EA2000 ซึ่งเป็นสัญญาณ E-E-A-T ที่ขาดที่สุด · การ 301 URL ที่เนื้อหาคนละเรื่อง
 *   ไปหน้าแรกยังถูก Google ตีเป็น soft 404 ไม่ได้ค่าอะไรกลับมาด้วย
 *   ระหว่างที่ยังไม่มีหน้า About ปล่อยให้ /about/ เป็น 404 ตามปกติ
 *
 * ข้อตกลงของสองบรรทัดที่เลือกไว้แบบตั้งใจ
 * - '/guides/' ชี้ไป '/how-to-install/' ไม่ใช่ '/articles/' เพราะหน้าเก่าชื่อ
 *   "คู่มือการติดตั้งและใช้งาน" ตรงกับหน้าวิธีติดตั้งโดยตรง ส่วน /articles/ ตอนนี้ยังไม่มี
 *   บทความสักชิ้น (ขึ้นว่า "ยังไม่มีบทความในขณะนี้") การ 301 ไปหน้ารายการเปล่ามีสิทธิ์
 *   ถูกตีเป็น soft 404 · ถ้าวันหนึ่งมีบทความแล้วอยากย้ายปลายทาง ให้แก้ที่นี่ที่เดียว
 * - '/results/' ชี้ไป '/backtest/' ทั้งที่หน้า Backtest ยังถูก noindex อยู่ตอนนี้
 *   (ดูส่วนที่ 3 ของไฟล์นี้) เป็นทางตันชั่วคราวโดยรู้ตัว: ปลายทางถูกเรื่องที่สุด และ
 *   noindex จะหลุดเองทันทีที่เจ้าของกรอกสถิติจริง ทางเลือกอื่น (ชี้ไป /pricing/ หรือ
 *   หน้าแรก) แลกด้วยการส่งคนไปผิดเรื่อง จึงไม่เลือก
 */
function ea2000_redirect_map() {
	$map = array(
		/* ตรงกับ docs/seo-content-plan.md ข้อ 0.8 (results 23 -> /backtest/) */
		'/results/'      => '/backtest/',
		'/guides/'       => '/how-to-install/',
		'/risk-warning/' => '/risk-disclosure/',
		'/ea-products/'  => '/pricing/',
	);

	/**
	 * แก้ตาราง redirect ได้จากภายนอก
	 *
	 * @param array $map path เก่า => path ใหม่
	 */
	$map = apply_filters( 'ea2000_redirect_map', $map );

	return is_array( $map ) ? $map : array();
}

/**
 * id ของเพจเก่าสมัย EA Special ที่ยัง publish อยู่ และต้องโดน 301 ทับ
 *
 * ทำไมต้องมีรายการนี้: guard ใน ea2000_legacy_redirects() ยอมให้ "หน้าที่มีอยู่จริง" ชนะ
 * ตาราง redirect เสมอ เพื่อกันไม่ให้ตารางไปทับหน้าใหม่ที่ใช้ slug เดิม · แต่ ณ วันนี้
 * results 23, guides 25 และ risk-warning 29 ยัง publish อยู่ (ตอบ 200 และยังอยู่ใน
 * page-sitemap.xml) ถ้าไม่มีรายการนี้ ตาราง redirect จะไม่ทำงานเลยสำหรับสาม URL ที่
 * แผนข้อ 0.8 ต้องการจริง ๆ เหลือแค่ /ea-products/ ที่ 404 อยู่แล้วเท่านั้นที่ redirect
 *
 * เมื่อขั้น 7 ลบหรือเปลี่ยนเพจสามหน้านี้เป็น draft แล้ว รายการนี้จะไม่มีผลอะไรอีก ปล่อยไว้ได้
 * ถ้าจะเอา id เดิมกลับมาทำเป็นหน้าใหม่ของ EA2000 ให้เอา id นั้นออก (แก้ไฟล์นี้ หรือใช้
 * filter 'ea2000_legacy_page_ids') แล้วเพจนั้นจะชนะตาราง redirect ทันที
 */
function ea2000_legacy_page_ids() {
	$ids = array( 23, 25, 29 );

	/**
	 * รายการ id ของเพจเก่าที่ให้ตาราง redirect ทับได้แม้ยัง publish อยู่
	 *
	 * @param array $ids id ของเพจ
	 */
	$ids = apply_filters( 'ea2000_legacy_page_ids', $ids );

	return is_array( $ids ) ? array_map( 'intval', $ids ) : array();
}

/**
 * แปลง REQUEST_URI เป็น path ที่เทียบกับตารางได้ (ตัด query, ตัด path ของ subdirectory install)
 */
function ea2000_current_request_path() {
	if ( empty( $_SERVER['REQUEST_URI'] ) ) {
		return '';
	}

	$uri  = wp_unslash( $_SERVER['REQUEST_URI'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$path = (string) wp_parse_url( $uri, PHP_URL_PATH );
	if ( '' === $path ) {
		return '';
	}

	/* ถ้า WordPress ติดตั้งใน subdirectory ให้ตัดส่วนนั้นออกก่อนเทียบ */
	$home = (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH );
	$home = untrailingslashit( $home );
	if ( '' !== $home && 0 === strpos( $path, $home ) ) {
		$path = substr( $path, strlen( $home ) );
	}

	$path = '/' . ltrim( rawurldecode( $path ), '/' );
	$path = strtolower( trailingslashit( $path ) );

	return $path;
}

/**
 * พารามิเตอร์ติดตามผลของ request ปัจจุบัน (ไม่มีเครื่องหมาย ?) ใช้ต่อท้ายปลายทางตอน redirect
 *
 * ทำไมต้องเก็บ: ลิงก์เก่าจากโฆษณา LINE broadcast หรือ QR code มักมี utm_* / gclid / fbclid
 * ถ้าตัดทิ้งตอน 301 ทราฟฟิกจะกลายเป็น direct ที่ไม่รู้ที่มาใน GA4 พอดีช่วงเปิดตัว
 *
 * ทำไมต้องคัดเฉพาะบางคีย์ ไม่ส่งทั้ง query: ชื่อคีย์บางตัวเป็น public query var ของ WordPress
 * เช่น /ea-products/?page_id=23 ถ้าส่งต่อทั้งก้อนจะกลายเป็น /pricing/?page_id=23 ซึ่ง WP_Query
 * ให้ page_id ชนะ pagename ผลคือเนื้อหาของเพจ 23 ไปโผล่ใต้ URL /pricing/ · p, s, feed
 * ก็ทำแบบเดียวกัน จึงส่งต่อเฉพาะพารามิเตอร์ติดตามผลที่ตั้งใจเก็บเท่านั้น
 */
function ea2000_current_request_query() {
	if ( empty( $_SERVER['REQUEST_URI'] ) ) {
		return '';
	}

	$uri   = wp_unslash( $_SERVER['REQUEST_URI'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$query = (string) wp_parse_url( $uri, PHP_URL_QUERY );
	if ( '' === $query ) {
		return '';
	}

	$args = array();
	wp_parse_str( $query, $args );
	if ( ! is_array( $args ) || ! $args ) {
		return '';
	}

	$keep = array();
	foreach ( $args as $key => $value ) {
		if ( ! is_string( $key ) || is_array( $value ) ) {
			continue;
		}
		if ( ! preg_match( '/^(utm_[a-z_]+|gclid|fbclid|msclkid|ttclid|ref)$/i', $key ) ) {
			continue;
		}
		$keep[ $key ] = (string) $value;
	}

	return $keep ? build_query( $keep ) : '';
}

/**
 * ทำ 301 ให้ URL เก่า
 *
 * กันไม่ให้ไปบังหน้าจริง: ถ้า path นั้นมีเนื้อหาที่เผยแพร่อยู่จริง (ตอบ 200) จะไม่ redirect
 * แปลว่าพอเจ้าของเอา slug เดิมกลับมาใช้เป็นหน้าใหม่ ตาราง redirect จะหลบให้เองทันที
 * ข้อยกเว้นเดียวคือเพจที่ id อยู่ใน ea2000_legacy_page_ids() ซึ่งเป็นเพจแบรนด์เก่าที่ยัง
 * publish ค้างอยู่และตั้งใจให้ตาราง redirect ทับ ไม่งั้นตารางจะเป็นโค้ดที่ไม่เคยทำงานเลย
 * และไม่ redirect ถ้าปลายทางเป็น path เดียวกับต้นทาง (กัน loop)
 *
 * ไม่แตะโหมด preview: ลิงก์ Preview ของเพจที่ publish แล้วคือ permalink + ?preview=true
 * ถ้า redirect ตรงนี้ เจ้าของจะเปิดดูตัวอย่างเพจเก่าไม่ได้เลย และ iframe ของ Customizer
 * จะเด้งออกจากหน้าเงียบ ๆ
 */
function ea2000_legacy_redirects() {
	if ( is_admin() || is_robots() || is_feed() || wp_doing_ajax() ) {
		return;
	}
	if ( is_preview() || is_customize_preview() ) {
		return;
	}
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return;
	}
	if ( ! empty( $_SERVER['REQUEST_METHOD'] ) && ! in_array( strtoupper( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) ), array( 'GET', 'HEAD' ), true ) ) {
		return;
	}

	$path = ea2000_current_request_path();
	if ( '' === $path ) {
		return;
	}

	$map = ea2000_redirect_map();
	if ( ! isset( $map[ $path ] ) ) {
		return;
	}

	/*
	 * มีเนื้อหาที่เผยแพร่อยู่จริงที่ path นี้ = หน้าจริง ห้ามทับ
	 *
	 * ไม่ดูจากชื่อไฟล์เทมเพลตที่เก็บใน _wp_page_template เพราะค่านั้นเป็นของธีมเก่า
	 * (EA Special) ก็ได้ และหน้าเนื้อหาปกติอย่าง About ก็ใช้ page.php ซึ่งค่าเป็นค่าว่าง
	 * ดังนั้นเช็กแค่ว่า "ตอนนี้มีหน้าให้ดูอยู่จริงไหม" ตรงไปตรงมากว่าและพลาดยากกว่า
	 *
	 * เช็กครอบทั้ง singular / archive / home ไม่ใช่แค่ is_page() เพราะตารางแก้ได้ผ่าน
	 * filter ถ้าวันหนึ่งมีคนใส่ path ที่เป็นโพสต์ CPT หรือหน้ารวมหมวด เราต้องไม่ 301 ทับ
	 * ส่วนรายการ legacy เช็กเฉพาะตอนเป็นเพจ เพื่อไม่ให้ id ของ term ที่บังเอิญเลขตรงกัน
	 * มาเข้าเงื่อนไขโดยไม่ตั้งใจ
	 */
	$queried_id = (int) get_queried_object_id();
	if ( ! is_404() && $queried_id && ( is_singular() || is_archive() || is_home() ) ) {
		$is_legacy = is_page() && in_array( $queried_id, ea2000_legacy_page_ids(), true );
		if ( ! $is_legacy ) {
			return;
		}
	}

	$target = (string) $map[ $path ];
	if ( '' === $target ) {
		return;
	}

	/* ปลายทางเดียวกับต้นทาง = loop ไม่ต้องทำ */
	if ( strtolower( trailingslashit( $target ) ) === $path ) {
		return;
	}

	$url = ( 0 === strpos( $target, 'http' ) ) ? $target : home_url( $target );

	/*
	 * ปลายทางต้องอยู่โดเมนเดียวกัน: wp_safe_redirect() จะเปลี่ยนปลายทางนอกโดเมนเป็น
	 * admin_url() เงียบ ๆ ซึ่งทำให้คนที่มาจากลิงก์เก่าเด้งไปหน้า wp-admin แทนที่จะเห็นว่าผิด
	 * ถ้ามีคนใส่ปลายทางข้ามโดเมนผ่าน filter ให้ไม่ทำอะไรเลยจะตรงไปตรงมากว่า
	 */
	$target_host = (string) wp_parse_url( $url, PHP_URL_HOST );
	$home_host   = (string) wp_parse_url( home_url( '/' ), PHP_URL_HOST );
	if ( '' !== $target_host && strtolower( $target_host ) !== strtolower( $home_host ) ) {
		return;
	}

	/* พา utm_* / gclid / fbclid ไปด้วย เพื่อไม่ให้เสีย attribution ตอนย้าย URL */
	$query = ea2000_current_request_query();
	if ( '' !== $query ) {
		$url .= ( false === strpos( $url, '?' ) ? '?' : '&' ) . $query;
	}

	wp_safe_redirect( esc_url_raw( $url ), 301 );
	exit;
}
add_action( 'template_redirect', 'ea2000_legacy_redirects', 1 );

/* --------------------------------------------------------------
 * 2) robots.txt: ชี้ sitemap ของ Yoast
 * -------------------------------------------------------------- */

/**
 * เติมบรรทัด Sitemap ของ Yoast ลง robots.txt ถ้ายังไม่มี
 *
 * Yoast ใส่บรรทัดนี้เองในบางเวอร์ชัน จึงเช็กก่อนว่ามีอยู่แล้วหรือยัง จะได้ไม่ซ้ำ
 * ไม่เติมเมื่อเว็บตั้งเป็นไม่ให้ index (public = 0) เพราะ robots.txt ตอนนั้นเป็น Disallow: / ทั้งเว็บ
 *
 * ต้องมี Yoast จริงถึงจะเติม: /sitemap_index.xml เป็น route ของ Yoast เท่านั้น
 * ถ้า Yoast ถูกปิด ea2000_robots_txt() ใน functions.php จะเติม /wp-sitemap.xml ของคอร์ให้แทน
 * ถ้าไม่เช็กตรงนี้ robots.txt จะได้สองบรรทัดและบรรทัด sitemap_index.xml จะเป็น 404
 *
 * หมายเหตุการใช้งานจริง: robots.txt ของ ea2000.co ตอนนี้เสิร์ฟจาก Cloudflare
 * ไม่ใช่ไฟล์เสมือนของ WordPress ฟิลเตอร์นี้จึงเป็นแค่ตัวสำรอง · การเติมบรรทัด Sitemap
 * ตัวจริงอยู่ที่แผนข้อ 0.9 (เจ้าของเติมที่ Cloudflare)
 */
function ea2000_robots_txt_yoast_sitemap( $output, $public ) {
	if ( '1' !== (string) $public ) {
		return $output;
	}
	if ( ! defined( 'WPSEO_VERSION' ) ) {
		return $output;
	}

	$sitemap = home_url( '/sitemap_index.xml' );
	if ( false !== strpos( (string) $output, 'sitemap_index.xml' ) ) {
		return $output;
	}

	if ( '' !== trim( (string) $output ) && "\n" !== substr( $output, -1 ) ) {
		$output .= "\n";
	}

	$output .= 'Sitemap: ' . esc_url_raw( $sitemap ) . "\n";

	return $output;
}
add_filter( 'robots_txt', 'ea2000_robots_txt_yoast_sitemap', 20, 2 );

/* --------------------------------------------------------------
 * 3) noindex ชั่วคราวสำหรับหน้าที่ตัวเลขยังเป็น placeholder
 * -------------------------------------------------------------- */

/**
 * คู่ของ "เทมเพลต => ค่าที่ต้องดูว่ากรอกจริงหรือยัง"
 *
 * prefix/count ใช้สร้างคีย์ค่าสถิติ (bt_stat1_label + bt_stat1_value ... )
 * link       = setting ลิงก์ผลที่ตรวจสอบได้ของหน้านั้น ('' = หน้านั้นไม่มีลิงก์ของตัวเอง)
 * link_label = setting ข้อความบนปุ่มลิงก์ ('' = เทมเพลตไม่ได้บังคับว่าต้องมี)
 * image      = setting ภาพผลการทดสอบของหน้านั้น (เจ้าของอัปโหลดเอง = มีของจริงให้ดู)
 *
 * เงื่อนไขต้องตรงกับที่เทมเพลตใช้จริง ไม่งั้นจะปลด noindex ให้หน้าที่ยังไม่แสดงอะไรเลย
 * - template-backtest.php และ template-forward.php ข้ามช่องสถิติเมื่อ label หรือ value
 *   ช่องใดช่องหนึ่งยังเป็น placeholder จึงต้องเช็กทั้งคู่ ไม่ใช่เช็กแค่ value
 * - template-forward.php แสดงปุ่มลิงก์เมื่อมีทั้ง forward_link_url และ forward_link_label
 *
 * ทำไมไม่นับ backtest_intro / forward_intro เป็นหลักฐาน: ค่า default ของสองคีย์นี้
 * เป็นข้อความอธิบายทั่วไปที่ติดมากับธีม ไม่ใช่ placeholder ตามนิยามของ ea2000_is_placeholder
 * ถ้านับด้วย เงื่อนไข noindex จะไม่มีวันเป็นจริงเลย · ส่วนภาพเป็นค่าว่างมาแต่ต้น
 * จึงเป็นสัญญาณ "เจ้าของใส่ของจริงแล้ว" ที่เชื่อถือได้
 *
 * หมายเหตุ: หน้า Backtest ตั้งใจไม่ผูกกับ verified_link_url ซึ่งเป็น setting ของหน้าแรก
 * (front-page.php ใช้เป็นปุ่ม "ดูผลแบบเรียลไทม์") เพราะการกรอกปุ่มหน้าแรกไม่ควรไปปลด
 * noindex ของหน้า Backtest ที่ตัวเลขยังเป็น placeholder ทั้งหน้า
 * ถ้าอยากได้ลิงก์เฉพาะของหน้า Backtest ต้องเพิ่ม setting backtest_link_url ใน
 * ea2000_defaults() + inc/customizer.php + template-backtest.php แล้วค่อยใส่คีย์ 'link' ที่นี่
 */
function ea2000_placeholder_page_rules() {
	return array(
		'template-backtest.php' => array(
			'prefix'     => 'bt_stat',
			'count'      => 8,
			'link'       => '',
			'link_label' => '',
			'image'      => 'backtest_img',
		),
		'template-forward.php'  => array(
			'prefix'     => 'fw_stat',
			'count'      => 6,
			'link'       => 'forward_link_url',
			'link_label' => 'forward_link_label',
			'image'      => 'forward_img',
		),
	);
}

/**
 * กฎหนึ่งข้อใน ea2000_placeholder_page_rules() ยัง "ไม่มีข้อมูลจริงเลย" หรือไม่
 *
 * แยกออกมาจาก ea2000_page_is_placeholder_only() เพราะค่าทั้งหมดที่ตรวจเป็น theme mod
 * ไม่ได้ผูกกับหน้าใดหน้าหนึ่ง จึงเรียกใช้ตอนสร้าง sitemap (ซึ่งไม่มี main query ให้ถาม
 * is_page() / is_page_template()) ได้ด้วย
 *
 * @param array $rule กฎหนึ่งข้อ
 * @return bool true = ทุกช่องยังเป็น placeholder และไม่มีทั้งลิงก์ผลและภาพผลการทดสอบ
 */
function ea2000_placeholder_rule_is_empty( $rule ) {
	if ( ! is_array( $rule ) || ! function_exists( 'ea2000_is_placeholder' ) || ! function_exists( 'ea2000_mod' ) ) {
		return false;
	}

	/* มีลิงก์ผลที่ตรวจสอบได้ (และมีข้อความปุ่มตามที่เทมเพลตต้องการ) = หน้ามีของจริงให้ดู */
	if ( ! empty( $rule['link'] ) ) {
		$link     = trim( (string) ea2000_mod( $rule['link'] ) );
		$has_link = '' !== $link && '#' !== $link;
		if ( $has_link && ! empty( $rule['link_label'] ) ) {
			$has_link = '' !== trim( (string) ea2000_mod( $rule['link_label'] ) );
		}
		if ( $has_link ) {
			return false;
		}
	}

	/* อัปโหลดภาพผลการทดสอบแล้ว = หน้ามีของจริงให้ดูเช่นกัน */
	if ( ! empty( $rule['image'] ) && '' !== trim( (string) ea2000_mod( $rule['image'] ) ) ) {
		return false;
	}

	for ( $i = 1; $i <= (int) $rule['count']; $i++ ) {
		$label = ea2000_mod( $rule['prefix'] . $i . '_label' );
		$value = ea2000_mod( $rule['prefix'] . $i . '_value' );
		if ( ! ea2000_is_placeholder( $label ) && ! ea2000_is_placeholder( $value ) ) {
			/* มีสถิติที่เทมเพลตแสดงจริงอย่างน้อยหนึ่งช่อง = ปล่อยให้ index ตามปกติ */
			return false;
		}
	}

	return true;
}

/**
 * หน้าที่กำลังแสดงอยู่ "ยังไม่มีข้อมูลจริงเลย" ใช่หรือไม่
 *
 * true = ทุกช่องสถิติยังเป็น placeholder และไม่มีทั้งลิงก์ผลและภาพผลการทดสอบ
 * ใช้ร่วมกันทั้งตอนที่ธีมออก meta robots เองและตอนที่ปลั๊กอิน SEO เป็นคนออก
 */
function ea2000_page_is_placeholder_only() {
	if ( ! is_page() ) {
		return false;
	}

	foreach ( ea2000_placeholder_page_rules() as $template => $rule ) {
		if ( ! is_page_template( $template ) ) {
			continue;
		}

		return ea2000_placeholder_rule_is_empty( $rule );
	}

	return false;
}

/**
 * id ของเพจที่ใช้เทมเพลตในตารางกฎ และตอนนี้ยังไม่มีข้อมูลจริง
 *
 * ใช้ตัดหน้าเหล่านี้ออกจาก sitemap · ต้องหาเป็น id เพราะ sitemap สร้างนอก main query
 * จึงใช้ ea2000_page_is_placeholder_only() ที่พึ่ง is_page() ไม่ได้
 */
function ea2000_placeholder_page_ids() {
	static $cache = null;
	if ( null !== $cache ) {
		return $cache;
	}

	$cache = array();
	foreach ( ea2000_placeholder_page_rules() as $template => $rule ) {
		if ( ! ea2000_placeholder_rule_is_empty( $rule ) ) {
			continue;
		}

		$found = get_posts(
			array(
				'post_type'              => 'page',
				'post_status'            => 'publish',
				'posts_per_page'         => 50,
				'fields'                 => 'ids',
				'meta_key'               => '_wp_page_template', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'             => $template, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			)
		);

		if ( $found ) {
			$cache = array_merge( $cache, array_map( 'intval', $found ) );
		}
	}

	$cache = array_values( array_unique( $cache ) );

	return $cache;
}

/**
 * ตัดหน้าที่ยังเป็น placeholder ออกจาก sitemap ของ Yoast
 *
 * ทำไมต้องมีคู่กับ noindex: Yoast สร้าง sitemap จากตาราง indexable (คอลัมน์ is_robots_noindex
 * ที่เขียนตอนบันทึกโพสต์) ไม่ได้สร้างตอน render หน้า ฟิลเตอร์ wpseo_robots_array จึงไม่มีผล
 * กับ sitemap เลย · ถ้าปล่อยไว้ วันที่ปลด noindex ทั้งเว็บ (แผนข้อ 0.10) Google จะไปเจอ
 * URL ใน sitemap ที่ตอบ noindex กลายเป็น error "Submitted URL marked noindex" ใน
 * Search Console สองหน้าจากแปดหน้าของรอบเปิดตัว
 *
 * เงื่อนไขเดียวกับ noindex ทุกอย่าง จึงเลิกกันเองวันที่เจ้าของกรอกข้อมูลจริง
 */
function ea2000_exclude_placeholder_from_sitemap( $excluded ) {
	$ids = ea2000_placeholder_page_ids();
	if ( ! $ids ) {
		return $excluded;
	}

	$excluded = is_array( $excluded ) ? array_map( 'intval', $excluded ) : array();

	return array_values( array_unique( array_merge( $excluded, $ids ) ) );
}
add_filter( 'wpseo_exclude_from_sitemap_by_post_ids', 'ea2000_exclude_placeholder_from_sitemap' );

/**
 * เงื่อนไขเดียวกันสำหรับ sitemap ของคอร์ (ตอนไม่มีปลั๊กอิน SEO)
 *
 * @param array  $args      อาร์กิวเมนต์ของ WP_Query ที่คอร์ใช้สร้าง sitemap
 * @param string $post_type ประเภทโพสต์ของ sitemap นั้น
 */
function ea2000_exclude_placeholder_from_core_sitemap( $args, $post_type ) {
	if ( 'page' !== $post_type ) {
		return $args;
	}

	$ids = ea2000_placeholder_page_ids();
	if ( ! $ids ) {
		return $args;
	}

	$existing = ( isset( $args['post__not_in'] ) && is_array( $args['post__not_in'] ) ) ? array_map( 'intval', $args['post__not_in'] ) : array();

	$args['post__not_in'] = array_values( array_unique( array_merge( $existing, $ids ) ) );

	return $args;
}
add_filter( 'wp_sitemaps_posts_query_args', 'ea2000_exclude_placeholder_from_core_sitemap', 10, 2 );

/**
 * ใส่ noindex,follow ให้หน้า Backtest / Forward Test "เฉพาะตอนที่ยังไม่มีข้อมูลจริง"
 *
 * เหตุผล: ตอนนี้ค่าสถิติทุกช่องยังเป็น placeholder (ขึ้นต้นด้วย "ระบุ" หรือ "เช่น" ดู
 * ea2000_is_placeholder) และยังไม่มีลิงก์ผลที่ตรวจสอบได้ หน้าจึงแทบไม่มีเนื้อหา
 * ถ้าปล่อยให้ Google เก็บไปตอนนี้จะกลายเป็นหน้า thin content ที่ฉุดคุณภาพทั้งเว็บ
 *
 * เงื่อนไข: จะ noindex ก็ต่อเมื่อ "ไม่มีช่องสถิติที่เทมเพลตแสดงจริงสักช่อง" และ "ไม่มีทั้งลิงก์ผล
 * ที่ตรวจสอบได้และภาพผลการทดสอบ" · พอเจ้าของกรอกทั้งชื่อและตัวเลขของช่องใดช่องหนึ่ง
 * ใส่ลิงก์ผลพร้อมข้อความปุ่ม หรืออัปโหลดภาพ noindex จะหายไปเองทันที ไม่ต้องกลับมาแก้โค้ด
 *
 * ไม่ตั้ง follow/nofollow เอง: ปล่อยตามค่าของคอร์ · เว็บสาธารณะคอร์ให้ follow อยู่แล้ว
 * ส่วนตอนที่เจ้าของปิด Search Engine Visibility คอร์ตั้ง noindex + nofollow ไว้ตั้งใจ
 * ถ้าเราไปเติม follow ทับ แท็กจะออกมาเป็น "noindex, nofollow, follow" ซึ่งขัดกันเอง
 *
 * ตัวนี้หลบให้เฉพาะปลั๊กอินที่เรามีฟิลเตอร์ของมันอยู่แล้ว (Yoast, Rank Math) เพื่อไม่ให้มี
 * meta robots สองแท็ก · ส่วน SEOPress ยังใช้ทางนี้: ถ้า SEOPress ถอด meta robots ของคอร์
 * ออกไป ฟิลเตอร์นี้จะกลายเป็นไม่มีผล ไม่ได้ทำให้แท็กซ้ำ และต้องตั้ง noindex รายหน้าเอง
 */
function ea2000_placeholder_robots( $robots ) {
	if ( defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' ) ) {
		return $robots;
	}
	if ( ea2000_page_is_placeholder_only() ) {
		$robots['noindex'] = true;
	}

	return $robots;
}
add_filter( 'wp_robots', 'ea2000_placeholder_robots', 20 );

/**
 * เงื่อนไขเดียวกันแต่สั่งผ่านปลั๊กอิน SEO (เว็บจริงเปิด Yoast อยู่)
 *
 * ถ้าไม่มีตัวนี้ กลไกด้านบนจะเป็นโค้ดที่ไม่เคยทำงานเลยบน ea2000.co และวันที่เจ้าของ
 * ปลด noindex ทั้งเว็บ (แผนข้อ 0.10) หน้า Backtest / Forward Test จะขึ้น index ทั้งที่
 * ตัวเลขยังเป็น "ระบุ..." ทุกช่อง ซึ่งคือ thin content ที่ตั้งใจกันไว้ตั้งแต่แรก
 *
 * ใช้ฟิลเตอร์ของปลั๊กอินจึงไม่เกิดแท็กซ้ำ และยังยกเลิกเองอัตโนมัติเมื่อมีข้อมูลจริง
 * ถ้าเจ้าของอยากคุมเอง ตั้ง noindex รายหน้าในกล่องของปลั๊กอินได้ตามปกติ (ค่าที่ตั้งเอง
 * เป็น noindex อยู่แล้ว ฟิลเตอร์นี้ไม่ไปปลดให้)
 *
 * ผูกทั้งฟิลเตอร์ของ Yoast (wpseo_robots_array) และ Rank Math (rank_math/frontend/robots)
 * เพราะทั้งสองใช้อาร์เรย์รูปแบบเดียวกัน คือคีย์ 'index' ที่รับค่า 'index' / 'noindex'
 */
function ea2000_placeholder_robots_plugin( $robots ) {
	if ( ! is_array( $robots ) ) {
		return $robots;
	}
	if ( ea2000_page_is_placeholder_only() ) {
		$robots['index'] = 'noindex';
	}

	return $robots;
}
add_filter( 'wpseo_robots_array', 'ea2000_placeholder_robots_plugin', 20 );
add_filter( 'rank_math/frontend/robots', 'ea2000_placeholder_robots_plugin', 20 );

/* --------------------------------------------------------------
 * 4) เปิด meta ของ Yoast ให้ REST API อ่าน/เขียนได้
 * -------------------------------------------------------------- */

/**
 * ลงทะเบียน _yoast_wpseo_title และ _yoast_wpseo_metadesc ให้ show_in_rest
 *
 * ทำไม: Yoast เก็บ SEO title / meta description เป็น post meta ที่ขึ้นต้นด้วย _ (protected)
 * ซึ่งปกติ REST API จะไม่ให้อ่านหรือเขียน ทำให้ต้องเข้าไปแก้ทีละหน้าใน wp-admin
 * ลงทะเบียนตรงนี้แล้วจะแก้ผ่าน REST (PUT /wp/v2/pages/<id> ฟิลด์ meta) ได้
 * ซึ่งเป็นวิธีที่ใช้ดูแลชื่อ/คำอธิบาย SEO ของทุกหน้าในโปรเจกต์นี้
 *
 * ทำไมไม่ใส่ sanitize_callback ตอนที่ Yoast ทำงานอยู่: register_post_meta() ผูก
 * sanitize เป็นแบบระบุ subtype (sanitize_post_meta__yoast_wpseo_title_for_page) และ
 * sanitize_meta() จะเลือกตัวที่ระบุ subtype ก่อนแล้วจบเลย ทำให้ callback ของ Yoast เอง
 * ไม่ได้ทำงาน ทั้งตอนบันทึกผ่าน REST และตอนกดบันทึกในกล่อง Yoast ที่ wp-admin
 * ปล่อยให้เป็น null แล้ว Yoast จัดการค่าของตัวเอง (รวมถึง %%variables%%) ตามปกติ
 * ถ้าไม่มี Yoast ค่อยใช้ sanitize_text_field ของเราเอง
 *
 * ความปลอดภัย: auth_callback บังคับสิทธิ์แก้ไขโพสต์นั้น ๆ จึงไม่เปิดช่องให้ผู้ใช้ทั่วไป
 * หรือผู้ไม่ล็อกอินแก้ค่าได้
 * ใช้ priority 20 บน init เพื่อให้ลงทะเบียนหลังปลั๊กอิน (ค่าของเราเป็นตัวสุดท้าย)
 */
function ea2000_register_yoast_meta_rest() {
	$keys = array(
		'_yoast_wpseo_title',
		'_yoast_wpseo_metadesc',
		'_yoast_wpseo_focuskw',
		'_yoast_wpseo_opengraph-title',
		'_yoast_wpseo_opengraph-description',
		'_yoast_wpseo_twitter-title',
		'_yoast_wpseo_twitter-description',
	);
	$args = array(
		'type'              => 'string',
		'single'            => true,
		'show_in_rest'      => true,
		'sanitize_callback' => defined( 'WPSEO_VERSION' ) ? null : 'sanitize_text_field',
		'auth_callback'     => 'ea2000_can_edit_seo_meta',
	);

	foreach ( array( 'post', 'page' ) as $post_type ) {
		foreach ( $keys as $key ) {
			register_post_meta( $post_type, $key, $args );
		}
	}
}
add_action( 'init', 'ea2000_register_yoast_meta_rest', 20 );

/**
 * สิทธิ์ในการอ่าน/เขียน meta ของ Yoast ผ่าน REST
 *
 * ใช้ลายเซ็นจริงของ auth_callback เพื่อเช็กสิทธิ์กับโพสต์ที่กำลังแก้ ไม่ใช่ capability กว้าง ๆ
 * (contributor มี edit_posts แต่แก้หน้าที่เผยแพร่แล้วของคนอื่นไม่ได้)
 *
 * @param bool   $allowed   ค่าเดิมจากคอร์
 * @param string $meta_key  คีย์ที่กำลังตรวจ
 * @param int    $object_id id ของโพสต์
 * @return bool
 */
function ea2000_can_edit_seo_meta( $allowed, $meta_key, $object_id = 0 ) {
	$object_id = (int) $object_id;
	if ( ! $object_id ) {
		return current_user_can( 'edit_posts' );
	}

	return current_user_can( 'edit_post', $object_id );
}

/**
 * รายการตั้งค่า Yoast ระดับเว็บที่ยอมให้แก้ผ่าน REST ได้
 *
 * ทำไมต้องมี: Yoast 28 ไม่มี REST route สำหรับหน้า Search Appearance ทำให้ต้องนั่งคลิกใน
 * wp-admin ทีละช่อง ซึ่งบน host นี้ปุ่มบันทึกมักไม่ทำงานเมื่อสั่งจากสคริปต์
 * เปิดเฉพาะคีย์ที่ระบุไว้เท่านั้น และเฉพาะผู้ใช้ที่มีสิทธิ์ manage_options
 *
 * **ไม่มีคีย์กลุ่ม noindex / index ในรายการนี้โดยตั้งใจ** การเปิดปิดการเก็บข้อมูลของ
 * เสิร์ชเอนจินเป็นการตัดสินใจของเจ้าของเว็บเท่านั้น endpoint นี้จึงแตะไม่ได้
 *
 * @return array<string,string> key => ชนิดค่า (text|bool)
 */
function ea2000_seo_option_whitelist() {
	return array(
		'separator'            => 'text',
		'title-home-wpseo'     => 'text',
		'metadesc-home-wpseo'  => 'text',
		'title-page'           => 'text',
		'metadesc-page'        => 'text',
		'title-post'           => 'text',
		'metadesc-post'        => 'text',
		'title-author-wpseo'   => 'text',
		'title-archive-wpseo'  => 'text',
		'title-search-wpseo'   => 'text',
		'title-404-wpseo'      => 'text',
		'company_or_person'    => 'text',
		'company_name'         => 'text',
		'company_alternate_name' => 'text',
		'website_name'         => 'text',
		'alternate_website_name' => 'text',
		'rssbefore'            => 'text',
		'rssafter'             => 'text',
		'breadcrumbs-home'     => 'text',
		'breadcrumbs-sep'      => 'text',
		'breadcrumbs-prefix'   => 'text',
		'disable-author'       => 'bool',
		'disable-date'         => 'bool',
		'disable-post_format'  => 'bool',
		'disable-attachment'   => 'bool',
	);
}

/**
 * สิทธิ์สำหรับ endpoint ตั้งค่า Yoast ระดับเว็บ
 *
 * @return bool
 */
function ea2000_seo_options_permission() {
	return current_user_can( 'manage_options' );
}

/**
 * อ่านค่าตั้งค่า Yoast ระดับเว็บเฉพาะคีย์ในรายการอนุญาต
 *
 * @return WP_REST_Response
 */
function ea2000_seo_options_get() {
	$titles = get_option( 'wpseo_titles' );
	$titles = is_array( $titles ) ? $titles : array();
	$out    = array();

	foreach ( ea2000_seo_option_whitelist() as $key => $type ) {
		$out[ $key ] = isset( $titles[ $key ] ) ? $titles[ $key ] : null;
	}

	return new WP_REST_Response(
		array(
			'yoast_active' => defined( 'WPSEO_VERSION' ),
			'options'      => $out,
		),
		200
	);
}

/**
 * เขียนค่าตั้งค่า Yoast ระดับเว็บ (เฉพาะคีย์ในรายการอนุญาต)
 *
 * ใช้ WPSEO_Options::set() เมื่อมี เพื่อให้ผ่าน validation ของ Yoast เอง
 * ถ้าไม่มีคลาสนี้ค่อยเขียนลง option ตรง ๆ
 *
 * @param WP_REST_Request $request คำขอ
 * @return WP_REST_Response|WP_Error
 */
function ea2000_seo_options_post( $request ) {
	$body = $request->get_json_params();
	if ( ! is_array( $body ) || empty( $body ) ) {
		return new WP_Error( 'ea2000_empty', 'ต้องส่ง JSON object ที่มีคีย์อย่างน้อยหนึ่งตัว', array( 'status' => 400 ) );
	}

	$allowed  = ea2000_seo_option_whitelist();
	$applied  = array();
	$rejected = array();
	$titles   = get_option( 'wpseo_titles' );
	$titles   = is_array( $titles ) ? $titles : array();
	$direct   = false;

	foreach ( $body as $key => $value ) {
		if ( ! isset( $allowed[ $key ] ) ) {
			$rejected[] = $key;
			continue;
		}

		if ( 'bool' === $allowed[ $key ] ) {
			$clean = (bool) $value;
		} else {
			$clean = sanitize_text_field( wp_unslash( (string) $value ) );
		}

		if ( class_exists( 'WPSEO_Options' ) && method_exists( 'WPSEO_Options', 'set' ) ) {
			WPSEO_Options::set( $key, $clean );
		} else {
			$titles[ $key ] = $clean;
			$direct         = true;
		}

		$applied[ $key ] = $clean;
	}

	if ( $direct ) {
		update_option( 'wpseo_titles', $titles );
	}

	return new WP_REST_Response(
		array(
			'applied'  => $applied,
			'rejected' => $rejected,
		),
		200
	);
}

/**
 * ลงทะเบียน route ตั้งค่า Yoast ระดับเว็บ
 */
function ea2000_register_seo_options_route() {
	register_rest_route(
		'ea2000/v1',
		'/seo-options',
		array(
			array(
				'methods'             => 'GET',
				'callback'            => 'ea2000_seo_options_get',
				'permission_callback' => 'ea2000_seo_options_permission',
			),
			array(
				'methods'             => 'POST',
				'callback'            => 'ea2000_seo_options_post',
				'permission_callback' => 'ea2000_seo_options_permission',
			),
		)
	);
}
add_action( 'rest_api_init', 'ea2000_register_seo_options_route' );
