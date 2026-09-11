<?php
/**
 * Footer v2 · "Console" (docs/home-v2-spec.md ข้อ 4)
 * 5 แถว: signal line · launch console · index · watermark · status bar
 * ทุกข้อความมาจาก ea2000_mod() · ปุ่ม LINE ซ่อนหรือ fallback ไป /go/ เมื่อยังไม่กรอก line_url
 *
 * @package ea2000
 */

defined( 'ABSPATH' ) || exit;

$ea2000_line = trim( (string) ea2000_mod( 'line_url' ) );
$ea2000_line = in_array( $ea2000_line, array( '', '#' ), true ) ? '' : $ea2000_line; // ยังไม่กรอก LINE OA: ซ่อนทุกปุ่ม LINE
$ea2000_go_page = get_page_by_path( 'go' );
$ea2000_go_url  = ( $ea2000_go_page && 'publish' === $ea2000_go_page->post_status ) ? get_permalink( $ea2000_go_page ) : ''; // หน้าติดต่อ/ลิงก์รวม ใช้แทนปุ่ม LINE เมื่อยังไม่กรอก line_url
$ea2000_qr       = trim( (string) ea2000_mod( 'footer_line_qr_img' ) );
$ea2000_openchat = trim( (string) ea2000_mod( 'links_openchat_url' ) );
$ea2000_openchat = in_array( $ea2000_openchat, array( '', '#' ), true ) ? '' : $ea2000_openchat;
$ea2000_contact_fallback = trim( (string) ea2000_mod( 'contact_fallback_text' ) );

$ea2000_headline = trim( (string) ea2000_mod( 'footer_headline' ) );
$ea2000_headline = '' !== $ea2000_headline ? $ea2000_headline : (string) ea2000_mod( 'cta_title' );
$ea2000_sub      = trim( (string) ea2000_mod( 'footer_sub' ) );
$ea2000_sub      = '' !== $ea2000_sub ? $ea2000_sub : (string) ea2000_mod( 'cta_subtitle' );

$ea2000_prep          = ea2000_lines( ea2000_mod( 'footer_prep_items' ) );
$ea2000_hours         = ea2000_lines( ea2000_mod( 'footer_hours_text' ) );
$ea2000_console_lines = ea2000_mod( 'show_footer_console' ) ? ea2000_lines( ea2000_mod( 'footer_console_lines' ) ) : array();

/* Spec sheet · บรรทัด "ป้าย|ค่า" · ข้ามบรรทัดที่ไม่มี | และค่าที่อ่านเป็นผลเทรด (ตัวเลขตามด้วย % หรือมีคำว่า กำไร) */
$ea2000_spec_rows = array();
foreach ( ea2000_lines( ea2000_mod( 'footer_spec_items' ) ) as $ea2000_spec_line ) {
	if ( false === strpos( $ea2000_spec_line, '|' ) ) {
		continue;
	}
	list( $ea2000_spec_label, $ea2000_spec_value ) = array_map( 'trim', explode( '|', $ea2000_spec_line, 2 ) );
	if ( '' === $ea2000_spec_label || '' === $ea2000_spec_value || preg_match( '/^[+-]?\d[\d.,]*\s*%/u', $ea2000_spec_value ) || false !== strpos( $ea2000_spec_line, 'กำไร' ) ) { // strpos ปลอดภัยกับ UTF-8 และไม่ต้องพึ่ง mbstring (เหมือน guard ของ HUD ใน functions.php)
		continue;
	}
	$ea2000_spec_rows[] = array( $ea2000_spec_label, $ea2000_spec_value );
}

/* Index · เมนูหลัก (primary) เฉพาะระดับบน · ถ้าไม่มีใช้เมนู footer · ไม่มีทั้งคู่ = รายการหน้าตามแผนเพจ EA2000
   (เมนู footer บนเว็บจริงมีแต่ลิงก์เอกสาร ซึ่งคอลัมน์ "เอกสาร" แสดงอยู่แล้ว) */
$ea2000_index = array();
$ea2000_menu_locations = get_nav_menu_locations();
foreach ( array( 'primary', 'footer' ) as $ea2000_index_location ) {
	if ( ! empty( $ea2000_index ) || empty( $ea2000_menu_locations[ $ea2000_index_location ] ) ) {
		continue;
	}
	$ea2000_menu_items = wp_get_nav_menu_items( (int) $ea2000_menu_locations[ $ea2000_index_location ] );
	if ( is_array( $ea2000_menu_items ) ) {
		foreach ( $ea2000_menu_items as $ea2000_menu_item ) {
			if ( (int) $ea2000_menu_item->menu_item_parent > 0 || '' === trim( (string) $ea2000_menu_item->url ) || '#' === $ea2000_menu_item->url ) {
				continue;
			}
			$ea2000_index[] = array( (string) $ea2000_menu_item->title, (string) $ea2000_menu_item->url );
		}
	}
}
if ( empty( $ea2000_index ) ) {
	$ea2000_index = array(
		array( 'หน้าแรก', home_url( '/' ) ),
		array( 'การทดสอบ', home_url( '/forward-test/' ) ),
		array( 'แพ็กเกจ', home_url( '/pricing/' ) ),
		array( 'วิธีติดตั้ง', home_url( '/how-to-install/' ) ),
	);
	$ea2000_posts_page = (int) get_option( 'page_for_posts' );
	if ( $ea2000_posts_page > 0 && 'publish' === get_post_status( $ea2000_posts_page ) ) {
		$ea2000_index[] = array( 'บทความ', get_permalink( $ea2000_posts_page ) );
	}
	if ( $ea2000_go_url ) {
		$ea2000_index[] = array( 'ติดต่อ', $ea2000_go_url );
	}
}

/* Channels · เฉพาะช่องทางที่กรอกแล้ว · ป้าย Facebook/อีเมล มาจาก setting ที่เหลือคงที่ */
$ea2000_channels = array();
if ( $ea2000_line ) {
	$ea2000_channels[] = array( 'url' => $ea2000_line, 'label' => ea2000_mod( 'footer_line_text' ), 'pos' => 'footer-index' );
}
if ( $ea2000_openchat ) {
	$ea2000_channels[] = array( 'url' => $ea2000_openchat, 'label' => ea2000_mod( 'links_openchat_label' ) );
}
$ea2000_facebook_label = trim( (string) ea2000_mod( 'footer_facebook_text' ) );
foreach ( array(
	'facebook_url'  => '' !== $ea2000_facebook_label ? $ea2000_facebook_label : 'Facebook',
	'instagram_url' => 'Instagram',
	'tiktok_url'    => 'TikTok',
	'youtube_url'   => 'YouTube',
) as $ea2000_social_key => $ea2000_social_label ) {
	$ea2000_social_url = trim( (string) ea2000_mod( $ea2000_social_key ) );
	if ( '' === $ea2000_social_url || '#' === $ea2000_social_url ) {
		continue;
	}
	$ea2000_channels[] = array( 'url' => $ea2000_social_url, 'label' => $ea2000_social_label );
}
$ea2000_email = trim( (string) ea2000_mod( 'contact_email' ) );
if ( '' !== $ea2000_email && is_email( $ea2000_email ) ) {
	$ea2000_email_label = trim( (string) ea2000_mod( 'footer_email_text' ) );
	$ea2000_channels[]  = array( 'mailto' => $ea2000_email, 'label' => '' !== $ea2000_email_label ? $ea2000_email_label : 'Email' );
}

/* Docs · เพจของ EA2000 ที่เผยแพร่แล้วเท่านั้น · risk-disclosure แสดงเสมอ */
$ea2000_docs = array();
foreach ( array(
	'about'          => 'เกี่ยวกับเรา',
	'privacy-policy' => 'นโยบายความเป็นส่วนตัว',
	'terms-of-use'   => 'เงื่อนไขการใช้บริการ',
	'data-deletion'  => 'ขอลบข้อมูล',
) as $ea2000_doc_slug => $ea2000_doc_label ) {
	$ea2000_doc_page = get_page_by_path( $ea2000_doc_slug );
	if ( $ea2000_doc_page && 'publish' === $ea2000_doc_page->post_status ) {
		$ea2000_docs[] = array( $ea2000_doc_label, get_permalink( $ea2000_doc_page ) );
	}
}
$ea2000_docs[] = array( (string) ea2000_mod( 'footer_risk_link' ), home_url( '/risk-disclosure/' ) ); // slug ตามแผนเพจ EA2000 · ยังไม่มี setting เฉพาะสำหรับ URL นี้
/* เปิดการตั้งค่าคุกกี้ (inc/consent.php) · main.js ดักลิงก์ที่ลงท้ายด้วย #cookie-settings ถอนความยินยอมได้จากทุกหน้า */
if ( '' !== trim( (string) ea2000_mod( 'footer_cookie_link' ) ) ) {
	$ea2000_docs[] = array( (string) ea2000_mod( 'footer_cookie_link' ), '#cookie-settings' );
}

/* Watermark · แยกส่วนตัวอักษรกับส่วนตั้งแต่ตัวเลขตัวแรก */
$ea2000_wm      = trim( (string) ea2000_mod( 'footer_watermark_text' ) );
$ea2000_wm_ea   = $ea2000_wm;
$ea2000_wm_num  = '';
if ( preg_match( '/^(\D*)(\d.*)$/u', $ea2000_wm, $ea2000_wm_parts ) ) {
	$ea2000_wm_ea  = $ea2000_wm_parts[1];
	$ea2000_wm_num = $ea2000_wm_parts[2];
}

$ea2000_bkk = new DateTimeZone( 'Asia/Bangkok' );

/* Mobile dock · LINE อยู่กลาง (ตำแหน่งที่ 3 ของ 5) เมื่อมี LINE */
$ea2000_mobile_nav = array(
	array(
		'label' => ea2000_mod( 'mobile_nav_home_label' ),
		'url'   => ea2000_link_url( ea2000_mod( 'mobile_nav_home_url' ) ),
		'icon'  => 'home',
	),
	array(
		'label' => ea2000_mod( 'mobile_nav_test_label' ),
		'url'   => ea2000_link_url( ea2000_mod( 'mobile_nav_test_url' ) ),
		'icon'  => 'flask',
	),
);
if ( $ea2000_line ) {
	$ea2000_mobile_nav[] = array(
		'label'        => ea2000_mod( 'mobile_nav_line_label' ),
		'url'          => $ea2000_line,
		'icon'         => 'line',
		'is_action'    => true,
		'target_blank' => true,
		'line_pos'     => 'dock',
	);
}
$ea2000_mobile_nav[] = array(
	'label' => ea2000_mod( 'mobile_nav_price_label' ),
	'url'   => ea2000_link_url( ea2000_mod( 'mobile_nav_price_url' ) ),
	'icon'  => 'tag',
);
$ea2000_mobile_nav[] = array(
	'label' => ea2000_mod( 'mobile_nav_install_label' ),
	'url'   => ea2000_link_url( ea2000_mod( 'mobile_nav_install_url' ) ),
	'icon'  => 'guide',
);
?>

<?php if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) : ?>
<footer class="site-footer console" id="contact"<?php echo ea2000_mod( 'show_footer_spotlight' ) ? ' data-spotlight' : ''; ?>>

	<?php if ( ea2000_mod( 'show_footer_signal' ) ) : ?>
	<svg class="signal watch" viewBox="0 0 1200 40" preserveAspectRatio="none" aria-hidden="true" focusable="false">
		<path vector-effect="non-scaling-stroke" d="M0 30 H230 l16 -22 l14 44 l16 -22 H640 l16 -22 l14 44 l16 -22 H1200"/>
	</svg>
	<?php endif; ?>

	<div class="container console-inner">

		<section class="launch" aria-labelledby="launch-title">
			<div class="launch-main">
				<p class="console-label mono keep-case"><span aria-hidden="true">// </span><?php echo esc_html( ea2000_mod( 'footer_console_label' ) ); ?></p>
				<h2 class="launch-title" id="launch-title"><?php echo ea2000_text( $ea2000_headline ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ea2000_text ?></h2>
				<?php if ( '' !== trim( $ea2000_sub ) ) : ?>
				<p class="launch-sub"><?php echo esc_html( $ea2000_sub ); ?></p>
				<?php endif; ?>
				<div class="launch-keys">
					<div class="keycap-wrap">
						<?php if ( $ea2000_line ) : ?>
						<a class="keycap" href="<?php echo esc_url( $ea2000_line ); ?>" target="_blank" rel="noopener" data-line-pos="footer">
							<?php echo ea2000_icon( 'line' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<span class="keycap-text"><?php echo esc_html( ea2000_mod( 'footer_line_text' ) ); ?></span>
						</a>
						<?php if ( $ea2000_qr ) : ?>
						<div class="qr-flyout" aria-hidden="true"><img src="<?php echo esc_url( $ea2000_qr ); ?>" alt="" width="160" height="160" loading="lazy" decoding="async"></div>
						<details class="qr-mobile">
							<summary><?php echo esc_html( ea2000_mod( 'footer_qr_toggle_text' ) ); ?></summary>
							<img src="<?php echo esc_url( $ea2000_qr ); ?>" alt="<?php echo esc_attr( ea2000_mod( 'footer_line_qr_alt' ) ); ?>" width="200" height="200" loading="lazy" decoding="async">
						</details>
						<?php elseif ( current_user_can( 'customize' ) ) : ?>
						<p class="admin-hint"><?php echo esc_html( ea2000_mod( 'footer_line_qr_note' ) ); ?> (ข้อความนี้เห็นเฉพาะแอดมิน)</p>
						<?php endif; ?>
						<?php elseif ( $ea2000_go_url ) : /* ยังไม่กรอก LINE: ส่งไปหน้าติดต่อ /go/ แทน */ ?>
						<a class="keycap" href="<?php echo esc_url( $ea2000_go_url ); ?>" data-line-pos="footer">
							<?php echo ea2000_icon( 'chat' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<span class="keycap-text"><?php echo esc_html( $ea2000_contact_fallback ); ?></span>
						</a>
						<?php endif; ?>
					</div>
					<?php if ( $ea2000_openchat ) : ?>
					<a class="textlink launch-openchat" href="<?php echo esc_url( $ea2000_openchat ); ?>" target="_blank" rel="noopener"><?php echo esc_html( ea2000_mod( 'links_openchat_label' ) ); ?><?php echo ea2000_icon( 'arrow', 'icon icon-sm key-arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
					<?php endif; ?>
				</div>
			</div>
			<div class="launch-side">
				<?php if ( $ea2000_prep ) : ?>
				<div class="readout">
					<h3 class="readout-title mono"><?php echo esc_html( ea2000_mod( 'footer_prep_title' ) ); ?></h3>
					<?php if ( '' !== trim( (string) ea2000_mod( 'footer_prep_text' ) ) ) : ?>
					<p class="readout-text"><?php echo esc_html( ea2000_mod( 'footer_prep_text' ) ); ?></p>
					<?php endif; ?>
					<ol class="readout-list">
						<?php foreach ( $ea2000_prep as $ea2000_i => $ea2000_item ) : ?>
						<li><span class="mono"><?php echo esc_html( sprintf( '%02d', $ea2000_i + 1 ) ); ?></span><span><?php echo esc_html( $ea2000_item ); ?></span></li>
						<?php endforeach; ?>
					</ol>
				</div>
				<?php endif; ?>
				<?php if ( $ea2000_hours ) : ?>
				<div class="hours">
					<h3 class="mono"><?php echo esc_html( ea2000_mod( 'footer_hours_title' ) ); ?></h3>
					<p><?php echo implode( '<br>', array_map( 'esc_html', $ea2000_hours ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
				</div>
				<?php endif; ?>
				<?php if ( $ea2000_console_lines ) : ?>
				<div class="prompt keep-case" data-prompt data-lines="<?php echo esc_attr( wp_json_encode( array_values( $ea2000_console_lines ) ) ); ?>" data-loops="3">
					<p class="prompt-line" aria-hidden="true"><span class="prompt-prefix mono"><?php echo esc_html( ea2000_mod( 'footer_console_prompt' ) ); ?></span> <span class="prompt-typed mono" data-prompt-out></span><i class="cursor"></i></p>
					<ul class="prompt-static">
						<?php foreach ( $ea2000_console_lines as $ea2000_item ) : ?>
						<li><?php echo esc_html( $ea2000_item ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
				<?php endif; ?>
			</div>
		</section>

		<nav class="index" aria-label="ดัชนีเว็บไซต์">
			<div class="index-col">
				<h3 class="index-title mono"><?php echo esc_html( ea2000_mod( 'footer_index_title' ) ); ?></h3>
				<ol class="index-list">
					<?php foreach ( $ea2000_index as $ea2000_i => $ea2000_entry ) : ?>
					<li><a href="<?php echo esc_url( $ea2000_entry[1] ); ?>"><span class="idx mono"><?php echo esc_html( sprintf( '%02d', $ea2000_i + 1 ) ); ?></span><span class="index-label"><?php echo esc_html( $ea2000_entry[0] ); ?></span></a></li>
					<?php endforeach; ?>
				</ol>
			</div>
			<?php if ( $ea2000_channels ) : ?>
			<div class="index-col">
				<h3 class="index-title mono"><?php echo esc_html( ea2000_mod( 'footer_channels_title' ) ); ?></h3>
				<ul class="index-list channels">
					<?php foreach ( $ea2000_channels as $ea2000_channel ) : ?>
					<li>
						<?php if ( isset( $ea2000_channel['mailto'] ) ) : ?>
						<a class="switch-row" href="mailto:<?php echo esc_attr( antispambot( $ea2000_channel['mailto'] ) ); ?>"><span class="index-label"><?php echo esc_html( $ea2000_channel['label'] ); ?></span><i class="switch" aria-hidden="true"></i></a>
						<?php else : ?>
						<a class="switch-row" href="<?php echo esc_url( $ea2000_channel['url'] ); ?>" target="_blank" rel="noopener"<?php echo ! empty( $ea2000_channel['pos'] ) ? ' data-line-pos="' . esc_attr( $ea2000_channel['pos'] ) . '"' : ''; ?>><span class="index-label"><?php echo esc_html( $ea2000_channel['label'] ); ?></span><i class="switch" aria-hidden="true"></i></a>
						<?php endif; ?>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php endif; ?>
			<div class="index-col">
				<h3 class="index-title mono"><?php echo esc_html( ea2000_mod( 'footer_docs_title' ) ); ?></h3>
				<ul class="index-list">
					<?php foreach ( $ea2000_docs as $ea2000_doc ) : ?>
					<li><a href="<?php echo esc_url( $ea2000_doc[1] ); ?>"><span class="index-label"><?php echo esc_html( $ea2000_doc[0] ); ?></span></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php if ( $ea2000_spec_rows ) : ?>
			<div class="index-col">
				<h3 class="index-title mono"><?php echo esc_html( ea2000_mod( 'footer_spec_title' ) ); ?></h3>
				<dl class="sheet sheet--ink keep-case">
					<?php foreach ( $ea2000_spec_rows as $ea2000_spec_row ) : ?>
					<div class="sheet-row"><dt class="mono"><?php echo esc_html( $ea2000_spec_row[0] ); ?></dt><dd><?php echo esc_html( $ea2000_spec_row[1] ); ?></dd></div>
					<?php endforeach; ?>
				</dl>
			</div>
			<?php endif; ?>
		</nav>

	</div>

	<?php if ( ea2000_mod( 'show_footer_watermark' ) && '' !== $ea2000_wm ) : ?>
	<p class="watermark keep-case watch" aria-hidden="true"><span class="wm-ea"><?php echo esc_html( $ea2000_wm_ea ); ?></span><span class="wm-num"><?php echo esc_html( $ea2000_wm_num ); ?></span></p>
	<?php endif; ?>

	<div class="statusbar">
		<div class="container statusbar-inner">
			<p class="status-copy mono keep-case">&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?> · <?php echo esc_html( ea2000_mod( 'footer_copyright_text' ) ); ?></p>
			<?php if ( '' !== trim( (string) ea2000_mod( 'footer_status_text' ) ) ) : ?>
			<p class="status-text keep-case"><?php echo esc_html( ea2000_mod( 'footer_status_text' ) ); ?></p>
			<?php endif; ?>
			<?php if ( ea2000_mod( 'show_footer_clock' ) ) : ?>
			<p class="status-clock mono keep-case"><span class="clock-label"><?php echo esc_html( ea2000_mod( 'footer_clock_label' ) ); ?></span> <time data-clock-out data-tz="Asia/Bangkok" datetime="<?php echo esc_attr( wp_date( 'c', null, $ea2000_bkk ) ); ?>"><?php echo esc_html( wp_date( 'H:i', null, $ea2000_bkk ) ); ?></time></p>
			<?php endif; ?>
			<a class="status-top mono" href="#top"><?php echo esc_html( ea2000_mod( 'footer_backtop_text' ) ); ?> <span aria-hidden="true">▲</span></a>
		</div>
	</div>
</footer>
<?php endif; ?>

<?php if ( ea2000_mod( 'show_mobile_nav' ) ) : ?>
<?php
/* บาร์ล่างมือถือ · แผงคอนโซล (Console Deck)
   หาแท็บที่ตรงกับหน้าปัจจุบันฝั่ง PHP เพื่อให้ is-active + aria-current + ตำแหน่งไฟชี้
   มาพร้อม HTML ตั้งแต่ตอนเสิร์ฟ ปิด JS ก็ยังถูกต้องครบ
   ใช้ $GLOBALS['wp']->request ไม่ใช่ $_SERVER['REQUEST_URI'] เพราะผ่าน routing ของ WordPress มาแล้ว
   (ไม่มี query string ติดมา ไม่ต้อง sanitize เอง และไม่พังถ้าย้ายไปติดตั้งใน subdirectory)
   ทุกช่องกว้างเท่ากันด้วย grid 1fr ตำแหน่งไฟจึงคิดเป็นเปอร์เซ็นต์ได้ตรงทั้งกรณี 4 และ 5 ช่อง
   หน้าเว็บถูกแคชแยกตาม URL อยู่แล้ว ค่าที่คิดตรงนี้จึงไม่ข้ามหน้ากัน */
$ea2000_dock_req  = isset( $GLOBALS['wp']->request ) ? (string) $GLOBALS['wp']->request : '';
$ea2000_dock_here = wp_parse_url( home_url( $ea2000_dock_req ), PHP_URL_PATH );
$ea2000_dock_here = '/' . trim( is_string( $ea2000_dock_here ) ? $ea2000_dock_here : '', '/' );
$ea2000_dock_host = wp_parse_url( home_url( '/' ), PHP_URL_HOST );

$ea2000_dock_active = -1;
$ea2000_dock_score  = -1;

foreach ( $ea2000_mobile_nav as $ea2000_slot => $ea2000_item ) {
	if ( ! empty( $ea2000_item['is_action'] ) ) {
		continue; // ปุ่ม LINE เป็นการกระทำ ไม่ใช่ตำแหน่งในเว็บ จึงไม่มีวัน active
	}

	$ea2000_dock_url = (string) $ea2000_item['url'];
	if ( '' === $ea2000_dock_url || 0 === strpos( $ea2000_dock_url, '#' ) ) {
		continue;
	}

	$ea2000_dock_link_host = wp_parse_url( $ea2000_dock_url, PHP_URL_HOST );
	if ( $ea2000_dock_link_host && $ea2000_dock_link_host !== $ea2000_dock_host ) {
		continue; // ลิงก์ออกนอกเว็บ ไม่นับเป็นหน้าปัจจุบัน
	}

	$ea2000_dock_path = wp_parse_url( $ea2000_dock_url, PHP_URL_PATH );
	$ea2000_dock_path = '/' . trim( is_string( $ea2000_dock_path ) ? $ea2000_dock_path : '', '/' );

	$ea2000_dock_hit = '/' === $ea2000_dock_path
		? '/' === $ea2000_dock_here
		: ( $ea2000_dock_here === $ea2000_dock_path || 0 === strpos( $ea2000_dock_here, $ea2000_dock_path . '/' ) );

	if ( $ea2000_dock_hit && strlen( $ea2000_dock_path ) > $ea2000_dock_score ) {
		$ea2000_dock_active = (int) $ea2000_slot;
		$ea2000_dock_score  = strlen( $ea2000_dock_path );
	}
}

/* จุดกึ่งกลางของช่องที่เลือก คิดเป็นเปอร์เซ็นต์ของแถวคีย์ · CSS เอาไปวางไฟบนราง */
$ea2000_dock_count = max( 1, count( $ea2000_mobile_nav ) );
$ea2000_dock_lit   = $ea2000_dock_active >= 0;
$ea2000_dock_x     = ( $ea2000_dock_active + 0.5 ) * ( 100 / $ea2000_dock_count );
$ea2000_dock_style = $ea2000_dock_lit ? sprintf( '--dock-x:%s%%', number_format( $ea2000_dock_x, 3, '.', '' ) ) : '';
?>
<nav class="mobile-app-nav dock<?php echo $ea2000_dock_lit ? ' is-lit' : ''; ?>" aria-label="เมนูลัดมือถือ"<?php echo '' !== $ea2000_dock_style ? ' style="' . esc_attr( $ea2000_dock_style ) . '"' : ''; ?>>
	<span class="dock-rail" aria-hidden="true"></span>
	<span class="dock-lamp" aria-hidden="true"></span>
	<div class="dock-keys">
		<?php
		foreach ( $ea2000_mobile_nav as $ea2000_slot => $ea2000_item ) :
			$ea2000_is_action = ! empty( $ea2000_item['is_action'] );
			$ea2000_is_here   = ! $ea2000_is_action && (int) $ea2000_slot === $ea2000_dock_active;
			$ea2000_dock_cls  = 'dock-key';
			if ( $ea2000_is_action ) {
				$ea2000_dock_cls .= ' is-action';
			} elseif ( $ea2000_is_here ) {
				$ea2000_dock_cls .= ' is-active';
			}
			?>
		<a class="<?php echo esc_attr( $ea2000_dock_cls ); ?>" href="<?php echo esc_url( $ea2000_item['url'] ); ?>" data-slot="<?php echo (int) $ea2000_slot; ?>"<?php echo $ea2000_is_here ? ' aria-current="page"' : ''; ?><?php echo ! empty( $ea2000_item['target_blank'] ) ? ' target="_blank" rel="noopener"' : ''; ?><?php echo ! empty( $ea2000_item['line_pos'] ) ? ' data-line-pos="' . esc_attr( $ea2000_item['line_pos'] ) . '"' : ''; ?>>
			<span class="dock-cap">
				<span class="dock-glyph"><?php echo ea2000_icon( $ea2000_item['icon'], 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<span class="dock-led" aria-hidden="true"></span>
			</span>
			<span class="dock-label"><?php echo esc_html( $ea2000_item['label'] ); ?></span>
			<?php if ( ! empty( $ea2000_item['target_blank'] ) ) : ?>
			<span class="sr-only">(เปิดแท็บใหม่)</span>
			<?php endif; ?>
		</a>
		<?php endforeach; ?>
	</div>
</nav>
<?php /* เว้นที่ท้ายหน้าไม่ให้บาร์ทับเนื้อหา · เป็นกล่องจริงในสายเนื้อหา จึงไม่ต้องพึ่ง JS ไม่ต้องพึ่ง :has() และไม่ต้องพึ่ง body class */ ?>
<div class="dock-spacer" aria-hidden="true"></div>
<?php endif; ?>

<?php if ( $ea2000_line && ea2000_mod( 'show_float_line' ) ) : ?>
<a class="float-line key key-line" href="<?php echo esc_url( $ea2000_line ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( ea2000_mod( 'float_line_text' ) ); ?>" data-line-pos="fab">
	<?php echo ea2000_icon( 'line' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	<span><?php echo esc_html( ea2000_mod( 'float_line_text' ) ); ?></span>
</a>
<?php endif; ?>

<?php if ( $ea2000_line && ! ea2000_mod( 'show_float_line' ) ) : ?>
<a class="line-fab" href="<?php echo esc_url( $ea2000_line ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( ea2000_mod( 'float_line_text' ) ); ?>" data-line-pos="fab">
	<?php echo ea2000_icon( 'line' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
</a>
<?php endif; ?>

<?php /* การ์ดความยินยอมคุกกี้พิมพ์ผ่าน wp_footer ใน inc/consent.php เพื่อให้หน้า /go/ ได้ด้วย */ ?>
<?php wp_footer(); ?>
</body>
</html>
