<?php
/**
 * การ์ดความยินยอมคุกกี้ (PDPA) และรหัสติดตาม
 *
 * หลักการ
 * - พิมพ์ HTML ชุดเดียวกันทุกหน้าผ่าน wp_footer (หน้า /go/ ไม่โหลด footer.php แต่เรียก wp_footer จึงได้การ์ดด้วย)
 * - PHP ไม่อ่านคุกกี้ความยินยอมเลย เพราะแคชหน้าเว็บของโฮสต์เสิร์ฟ HTML เดียวให้ทุกคน · main.js เป็นคนตัดสินใจว่าจะแสดงการ์ดไหม
 * - ไม่มีแท็ก Google Analytics หรือ Meta Pixel ในหน้า · main.js โหลดทีละหมวดหลังผู้ใช้ยินยอมหมวดนั้นเท่านั้น
 * - การ์ดขึ้นเองเฉพาะเมื่อกรอกรหัส GA4 หรือ Pixel ที่รูปแบบถูกต้อง · ถ้าไม่มีเลย ลิงก์ "ตั้งค่าคุกกี้" จะบอกว่าใช้แค่คุกกี้ที่จำเป็น
 * - ข้อความทุกคำเป็น setting ใน Customizer หมวด 16
 *
 * @package ea2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * รหัส Google Analytics 4 ที่รูปแบบถูกต้อง (G- ตามด้วยตัวอักษรหรือตัวเลข) · ผิดรูปแบบคืนค่าว่าง
 */
function ea2000_sanitize_ga_id( $value ) {
	$value = strtoupper( trim( (string) $value ) );
	return preg_match( '/^G-[A-Z0-9]{4,20}$/', $value ) ? $value : '';
}

/**
 * รหัส Meta Pixel ที่รูปแบบถูกต้อง (ตัวเลข 10 ถึง 20 หลัก) · ผิดรูปแบบคืนค่าว่าง
 */
function ea2000_sanitize_pixel_id( $value ) {
	$value = preg_replace( '/\s+/', '', (string) $value );
	return preg_match( '/^\d{10,20}$/', $value ) ? $value : '';
}

/**
 * แจ้งแอดมินใน Customizer ทันทีเมื่อกรอกรหัสผิดรูปแบบ แทนการเงียบแล้วไม่เก็บข้อมูลอะไรเลย
 */
function ea2000_validate_tracking_id( $validity, $value, $setting ) {
	if ( '' === trim( (string) $value ) ) {
		return $validity;
	}
	if ( 'ga_measurement_id' === $setting->id && '' === ea2000_sanitize_ga_id( $value ) ) {
		$validity->add( 'ea2000_bad_ga_id', 'รหัส Google Analytics 4 ต้องขึ้นต้นด้วย G- เช่น G-ABC123XYZ9 (ดูได้ที่ Admin > Data streams ของ GA4)' );
	}
	if ( 'fb_pixel_id' === $setting->id && '' === ea2000_sanitize_pixel_id( $value ) ) {
		$validity->add( 'ea2000_bad_pixel_id', 'รหัส Meta Pixel ต้องเป็นตัวเลขล้วน 10 ถึง 20 หลัก (ดูได้ที่ Events Manager ของ Meta)' );
	}
	return $validity;
}

/**
 * รหัสติดตามที่ใช้ได้จริง
 *
 * @return array { ga: string, pixel: string }
 */
function ea2000_tracking_ids() {
	return array(
		'ga'    => ea2000_sanitize_ga_id( ea2000_mod( 'ga_measurement_id' ) ),
		'pixel' => ea2000_sanitize_pixel_id( ea2000_mod( 'fb_pixel_id' ) ),
	);
}

/**
 * รุ่นของความยินยอม · เพิ่มเลขใน Customizer เมื่อเปลี่ยนการใช้คุกกี้ ผู้เข้าชมทุกคนจะถูกถามใหม่
 */
function ea2000_consent_version() {
	return max( 1, absint( ea2000_mod( 'consent_version' ) ) );
}

/**
 * ส่งค่าให้ main.js · เป็นค่าคงที่เหมือนกันทุกคน จึงแคชได้
 */
function ea2000_consent_script_data() {
	if ( ! wp_script_is( 'ea2000-main', 'enqueued' ) ) {
		return;
	}
	$ids = ea2000_tracking_ids();
	wp_localize_script(
		'ea2000-main',
		'ea2000Consent',
		array(
			'version' => (string) ea2000_consent_version(),
			'ga'      => $ids['ga'],
			'pixel'   => $ids['pixel'],
		)
	);
}
add_action( 'wp_enqueue_scripts', 'ea2000_consent_script_data', 20 );

/**
 * การ์ดความยินยอม · ซ่อนไว้ด้วย hidden เสมอ ปิด JS แล้วจะไม่เห็นและไม่มีแท็กใดโหลด
 */
function ea2000_consent_card() {
	if ( is_admin() ) {
		return;
	}

	$privacy     = get_page_by_path( 'privacy-policy' );
	$privacy_url = ( $privacy && 'publish' === $privacy->post_status ) ? get_permalink( $privacy ) . '#cookies' : '';
	$policy      = trim( (string) ea2000_mod( 'consent_policy_label' ) );
	$cats        = array(
		'analytics' => array( ea2000_mod( 'consent_analytics_name' ), ea2000_mod( 'consent_analytics_desc' ) ),
		'marketing' => array( ea2000_mod( 'consent_marketing_name' ), ea2000_mod( 'consent_marketing_desc' ) ),
	);
	?>
<section class="consent" id="cookie-settings" aria-labelledby="consent-title" hidden>
	<p class="consent-kicker mono keep-case"><?php echo esc_html( ea2000_mod( 'consent_kicker' ) ); ?></p>
	<h2 class="consent-title" id="consent-title" tabindex="-1"><?php echo ea2000_text( ea2000_mod( 'consent_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ea2000_text ?></h2>
	<p class="consent-text" data-show="intro"><?php echo esc_html( ea2000_mod( 'consent_text' ) ); ?><?php if ( $privacy_url && '' !== $policy ) : ?> <a href="<?php echo esc_url( $privacy_url ); ?>"><?php echo esc_html( $policy ); ?></a><?php endif; ?></p>
	<div class="consent-prefs" data-show="prefs">
		<ul class="consent-cats">
			<li class="consent-cat">
				<div>
					<p class="consent-cat-name"><?php echo esc_html( ea2000_mod( 'consent_necessary_name' ) ); ?></p>
					<p class="consent-cat-desc"><?php echo esc_html( ea2000_mod( 'consent_necessary_desc' ) ); ?></p>
				</div>
				<span class="consent-always mono keep-case"><?php echo esc_html( ea2000_mod( 'consent_always_label' ) ); ?></span>
			</li>
			<?php foreach ( $cats as $ea2000_cat_key => $ea2000_cat ) : ?>
			<li class="consent-cat" data-cat="<?php echo esc_attr( $ea2000_cat_key ); ?>" hidden>
				<div>
					<p class="consent-cat-name" id="consent-<?php echo esc_attr( $ea2000_cat_key ); ?>-name"><?php echo esc_html( $ea2000_cat[0] ); ?></p>
					<p class="consent-cat-desc" id="consent-<?php echo esc_attr( $ea2000_cat_key ); ?>-desc"><?php echo esc_html( $ea2000_cat[1] ); ?></p>
				</div>
				<label class="consent-switch">
					<input type="checkbox" role="switch" name="<?php echo esc_attr( $ea2000_cat_key ); ?>" aria-labelledby="consent-<?php echo esc_attr( $ea2000_cat_key ); ?>-name" aria-describedby="consent-<?php echo esc_attr( $ea2000_cat_key ); ?>-desc">
					<span class="consent-track" aria-hidden="true"></span>
				</label>
			</li>
			<?php endforeach; ?>
		</ul>
		<p class="consent-note" data-when="none" hidden><?php echo esc_html( ea2000_mod( 'consent_none_text' ) ); ?></p>
		<p class="consent-note"><?php echo esc_html( ea2000_mod( 'consent_prefs_note' ) ); ?><?php if ( $privacy_url && '' !== $policy ) : ?> <a href="<?php echo esc_url( $privacy_url ); ?>"><?php echo esc_html( $policy ); ?></a><?php endif; ?></p>
	</div>
	<div class="consent-actions" data-when="optional" hidden>
		<button type="button" class="consent-btn" data-consent="reject"><?php echo esc_html( ea2000_mod( 'consent_reject_label' ) ); ?></button>
		<button type="button" class="consent-btn" data-consent="accept"><?php echo esc_html( ea2000_mod( 'consent_accept_label' ) ); ?></button>
		<button type="button" class="consent-btn consent-btn--link" data-consent="prefs" data-show="intro"><?php echo esc_html( ea2000_mod( 'consent_prefs_label' ) ); ?></button>
		<button type="button" class="consent-btn consent-btn--save" data-consent="save" data-show="prefs"><?php echo esc_html( ea2000_mod( 'consent_save_label' ) ); ?></button>
	</div>
	<button type="button" class="consent-close" data-consent="close" aria-label="<?php echo esc_attr( ea2000_mod( 'consent_close_label' ) ); ?>"><?php echo ea2000_icon( 'x', 'icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
</section>
	<?php
}
add_action( 'wp_footer', 'ea2000_consent_card', 5 );
