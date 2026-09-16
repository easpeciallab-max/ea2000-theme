<?php
/**
 * Template Name: EA2000 · หน้า Link Hub (ยิงแอด)
 *
 * หน้า "ลิงก์รวม" สไตล์ Linktree สำหรับใช้เป็นปลายทางยิงแอด (Facebook/Google/TikTok Ads)
 * เป็นหน้าแบบ standalone: ไม่มีเมนูบน/ฟุตเตอร์เว็บ
 * โครงหน้า (ตามแบบ Link Hub ของเว็บในเครือ 16 ก.ย. 2026):
 *   ติดขั้นตอนไหน ทักทีม (LINE, OpenChat) → เริ่มใช้งานเป็นขั้นตอนมีเลข → ข้อมูลก่อนตัดสินใจ → โซเชียล → คำเตือนและลิงก์เอกสาร
 * ขั้นตอนที่ไม่มีปุ่มจะถูกซ่อน และเลขขั้นตอนเรียงใหม่อัตโนมัติ
 * ปุ่มที่มีข้อความแต่ยังไม่มีลิงก์ (หน้าที่ยังไม่สร้าง) แสดงเป็นปุ่มจาง "เร็ว ๆ นี้" กดไม่ได้
 * แก้ข้อความ/ปุ่ม/ลิงก์ทั้งหมดได้ที่ ปรับแต่ง → EA2000 → หมวด "หน้า Link Hub"
 *
 * @package ea2000
 */

defined( 'ABSPATH' ) || exit;

/**
 * ลิงก์ใช้งานได้จริงหรือไม่ (ว่างหรือ '#' = ยังไม่ตั้งค่า)
 */
$lh_has_url = static function ( $url ) {
	$url = trim( (string) $url );
	return '' !== $url && '#' !== $url;
};

$lh_mod = static function ( $key ) {
	return trim( (string) ea2000_mod( $key ) );
};

$lh_logo = $lh_mod( 'links_logo' );
if ( '' === $lh_logo ) {
	/* กรอบโลโก้ของหน้านี้เป็นวงกลม ใช้ตรากลมของธีม (19 KB) */
	$lh_logo = get_template_directory_uri() . '/assets/img/logo-mark.webp';
}

$lh_title      = $lh_mod( 'links_title' );
$lh_tag        = $lh_mod( 'links_tagline' );
$lh_badges     = ea2000_lines( ea2000_mod( 'links_badges' ) );
$lh_note       = $lh_mod( 'links_note' );
$lh_line       = $lh_mod( 'line_url' );
$lh_has_line   = $lh_has_url( $lh_line );
$lh_pending    = $lh_mod( 'links_pending_label' );

/* ไอคอนประกอบปุ่มกลุ่มข้อมูลก่อนตัดสินใจ (ตกแต่ง) */
$lh_btn_icons   = array( 1 => 'chart', 2 => 'tag', 3 => 'guide', 4 => 'layout', 5 => 'arrow', 6 => 'arrow' );
$lh_guide_icons = array( 1 => 'download', 2 => 'windows', 3 => 'android', 4 => 'apple', 5 => 'macos' );

/**
 * ปุ่มมีลิงก์ = <a> · มีข้อความแต่ไม่มีลิงก์ = ปุ่มจางพร้อมป้าย "เร็ว ๆ นี้" (หน้านั้นยังไม่สร้าง)
 *
 * @param string $class  คลาสปุ่ม (lh-btn หรือ lh-mini-btn)
 * @param string $label  ข้อความ
 * @param string $url    ลิงก์
 * @param string $icon   ชื่อไอคอน
 * @param string $extra  attribute เพิ่ม (ต้อง escape มาแล้ว)
 */
$lh_button = static function ( $class, $label, $url, $icon, $extra = '' ) use ( $lh_has_url, $lh_pending ) {
	if ( '' === $label ) {
		return;
	}
	$is_btn = false !== strpos( $class, 'lh-btn' );
	if ( $lh_has_url( $url ) ) {
		$href     = ea2000_link_url( $url );
		$external = (bool) preg_match( '#^https?://#i', $href ) && false === strpos( $href, home_url() );
		?>
		<a class="<?php echo esc_attr( $class ); ?>" href="<?php echo esc_url( $href ); ?>"<?php echo $external ? ' target="_blank" rel="noopener"' : ''; ?><?php echo $extra; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped by caller ?>>
			<span class="lh-ic"><?php echo ea2000_icon( $icon ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			<span class="lh-lbl"><?php echo esc_html( $label ); ?></span>
			<?php if ( $is_btn ) : ?>
				<span class="lh-ar" aria-hidden="true">&rsaquo;</span>
			<?php endif; ?>
		</a>
		<?php
		return;
	}
	?>
	<span class="<?php echo esc_attr( $class ); ?> is-pending" aria-disabled="true">
		<span class="lh-ic"><?php echo ea2000_icon( $icon ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		<span class="lh-lbl"><?php echo esc_html( $label ); ?></span>
		<?php if ( '' !== $lh_pending ) : ?>
			<span class="lh-pending-tag"><?php echo esc_html( $lh_pending ); ?></span>
		<?php endif; ?>
	</span>
	<?php
};

/* ปุ่มที่พาไป LINE OA แทนลิงก์ที่ยังไม่มี (ลิงก์เปิดบัญชีของทีม, ไฟล์ EA) */
$lh_line_button = static function ( $label, $pos ) use ( $lh_line, $lh_has_line ) {
	if ( '' === $label || ! $lh_has_line ) {
		return;
	}
	?>
	<a class="lh-btn lh-btn-line lh-btn-line-step" href="<?php echo esc_url( $lh_line ); ?>" target="_blank" rel="noopener" data-line-pos="<?php echo esc_attr( $pos ); ?>">
		<span class="lh-ic"><?php echo ea2000_icon( 'line' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		<span class="lh-lbl"><?php echo esc_html( $label ); ?></span>
	</a>
	<?php
};

$lh_socials = array();
foreach ( array(
	'facebook'  => array( 'facebook_url', 'Facebook' ),
	'instagram' => array( 'instagram_url', 'Instagram' ),
	'tiktok'    => array( 'tiktok_url', 'TikTok' ),
	'youtube'   => array( 'youtube_url', 'YouTube' ),
) as $lh_name => $lh_s ) {
	$lh_surl = $lh_mod( $lh_s[0] );
	if ( $lh_has_url( $lh_surl ) ) {
		$lh_socials[ $lh_name ] = array( $lh_surl, $lh_s[1] );
	}
}

/* แอป MT5 ตามอุปกรณ์ · ต้องมีทั้งข้อความและลิงก์ */
$lh_mt5_apps = array();
foreach ( array( 'ios' => 'apple', 'android' => 'android', 'windows' => 'windows', 'macos' => 'macos' ) as $lh_os => $lh_os_icon ) {
	$lh_os_url   = $lh_mod( 'links_mt5_' . $lh_os . '_url' );
	$lh_os_label = $lh_mod( 'links_mt5_' . $lh_os . '_label' );
	if ( $lh_has_url( $lh_os_url ) && '' !== $lh_os_label ) {
		$lh_mt5_apps[] = array( 'url' => $lh_os_url, 'label' => $lh_os_label, 'icon' => $lh_os_icon );
	}
}

/* คู่มือ: แยกคู่มือติดตั้ง EA ออกจากคู่มือ VPS · ข้อความที่ไม่มีลิงก์จะแสดงเป็นปุ่ม "เร็ว ๆ นี้" */
$lh_setup_guides = array();
$lh_vps_guides   = array();
for ( $lh_i = 1; $lh_i <= 5; $lh_i++ ) {
	$lh_label = $lh_mod( 'links_guide' . $lh_i . '_label' );
	$lh_url   = $lh_mod( 'links_guide' . $lh_i . '_url' );
	if ( '' === $lh_label ) {
		continue;
	}
	$lh_guide = array(
		'label' => $lh_label,
		'url'   => $lh_url,
		'icon'  => isset( $lh_guide_icons[ $lh_i ] ) ? $lh_guide_icons[ $lh_i ] : 'arrow',
	);
	if ( false !== stripos( $lh_url, 'vps-' ) || false !== stripos( $lh_label, 'VPS' ) ) {
		$lh_vps_guides[] = $lh_guide;
	} else {
		$lh_setup_guides[] = $lh_guide;
	}
}

/*
 * เก็บเนื้อหาแต่ละขั้นตอนไว้ก่อน (output buffer) ขั้นไหนว่างจะไม่แสดง แล้วจึงใส่เลขตามลำดับที่เหลือจริง
 * n คือเลขของ setting (links_stepN_*) ไม่ใช่เลขที่แสดง
 */
$lh_steps = array();

// ขั้นตอน: เปิดบัญชี.
ob_start();
if ( $lh_has_url( $lh_mod( 'links_signup_url' ) ) && '' !== $lh_mod( 'links_signup_label' ) ) :
	?>
	<a class="lh-btn lh-btn-signup" href="<?php echo esc_url( ea2000_link_url( $lh_mod( 'links_signup_url' ) ) ); ?>" target="_blank" rel="noopener">
		<span class="lh-ic lh-account-ic"><?php echo ea2000_icon( 'account' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		<span class="lh-signup-copy"><span class="lh-lbl"><?php echo esc_html( $lh_mod( 'links_signup_label' ) ); ?></span></span>
		<span class="lh-ar" aria-hidden="true">&rsaquo;</span>
	</a>
	<?php
else :
	/* ยังไม่มีลิงก์เปิดบัญชีของทีม · ให้ขอลิงก์ทาง LINE */
	$lh_line_button( $lh_mod( 'links_signup_line_label' ), 'go-signup' );
endif;
$lh_button( 'lh-mini-btn', $lh_mod( 'links_account_guide_label' ), $lh_mod( 'links_account_guide_url' ), 'guide' );
$lh_steps[] = array( 'key' => 'account', 'n' => 1, 'body' => ob_get_clean() );

// ขั้นตอน: ติดตั้ง MT5 และล็อกอิน.
ob_start();
if ( $lh_mt5_apps ) :
	?>
	<div class="lh-vps-grid lh-mt5-grid lh-mt5-grid--<?php echo esc_attr( (string) count( $lh_mt5_apps ) ); ?>">
		<?php foreach ( $lh_mt5_apps as $lh_app ) : ?>
			<a class="lh-vps-item lh-mt5-item" href="<?php echo esc_url( ea2000_link_url( $lh_app['url'] ) ); ?>" target="_blank" rel="noopener">
				<span class="lh-vps-ic"><?php echo ea2000_icon( $lh_app['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<span class="lh-vps-lbl"><?php echo esc_html( $lh_app['label'] ); ?></span>
			</a>
		<?php endforeach; ?>
	</div>
	<?php
endif;
$lh_button( 'lh-mini-btn', $lh_mod( 'links_mt5_download_label' ), $lh_mod( 'links_mt5_download_url' ), 'guide' );
$lh_steps[] = array( 'key' => 'mt5', 'n' => 2, 'body' => ob_get_clean() );

// ขั้นตอน: ฝากเงิน.
ob_start();
if ( $lh_has_url( $lh_mod( 'links_deposit_url' ) ) ) {
	$lh_button( 'lh-btn lh-btn-deposit', $lh_mod( 'links_deposit_label' ), $lh_mod( 'links_deposit_url' ), 'account' );
}
$lh_button( 'lh-mini-btn', $lh_mod( 'links_deposit_guide_label' ), $lh_mod( 'links_deposit_guide_url' ), 'guide' );
$lh_steps[] = array( 'key' => 'deposit', 'n' => 3, 'body' => ob_get_clean() );

// ขั้นตอน: รับไฟล์ EA.
ob_start();
$lh_fast_img = $lh_mod( 'links_fast_img' );
$lh_fast_url = $lh_mod( 'links_fast_url' );
if ( ea2000_mod( 'links_fast_enabled' ) && '' !== $lh_fast_img && $lh_has_url( $lh_fast_url ) ) :
	$lh_fast_href    = ea2000_link_url( $lh_fast_url );
	$lh_fast_is_file = (bool) preg_match( '/\.(zip|ex5|set|pdf)([?#]|$)/i', $lh_fast_href );
	?>
	<div class="lh-feature lh-feature-fast">
		<a class="lh-feature-frame" href="<?php echo esc_url( $lh_fast_href ); ?>"<?php echo $lh_fast_is_file ? ' download' : ''; ?>>
			<img src="<?php echo esc_url( $lh_fast_img ); ?>" alt="<?php echo esc_attr( $lh_mod( 'links_fast_alt' ) ); ?>" width="1200" height="630" loading="lazy">
		</a>
	</div>
	<?php
else :
	/* ยังไม่มีไฟล์ให้ดาวน์โหลด · ทีมงานส่งไฟล์ทาง LINE */
	$lh_line_button( $lh_mod( 'links_download_line_label' ), 'go-download' );
endif;
$lh_steps[] = array( 'key' => 'download', 'n' => 4, 'body' => ob_get_clean() );

// ขั้นตอน: ติดตั้ง EA บน MT5.
ob_start();
foreach ( $lh_setup_guides as $lh_guide ) {
	$lh_button( 'lh-btn', $lh_guide['label'], $lh_guide['url'], $lh_guide['icon'] );
}
$lh_steps[] = array( 'key' => 'install', 'n' => 5, 'body' => ob_get_clean() );

// ขั้นตอน: VPS.
ob_start();
if ( $lh_vps_guides ) :
	?>
	<div class="lh-vps-grid">
		<?php foreach ( $lh_vps_guides as $lh_guide ) : ?>
			<?php
			$lh_vps_label = $lh_guide['label'];
			if ( false !== stripos( $lh_vps_label, 'Windows' ) ) {
				$lh_vps_label = 'Windows';
			} elseif ( false !== stripos( $lh_vps_label, 'Android' ) ) {
				$lh_vps_label = 'Android';
			} elseif ( false !== stripos( $lh_vps_label, 'iPhone' ) || false !== stripos( $lh_vps_label, 'iOS' ) ) {
				$lh_vps_label = 'iOS';
			} elseif ( false !== stripos( $lh_vps_label, 'mac' ) ) {
				$lh_vps_label     = 'macOS';
				$lh_guide['icon'] = 'macos';
			}
			$lh_vps_has_url = $lh_has_url( $lh_guide['url'] );
			?>
			<?php if ( $lh_vps_has_url ) : ?>
			<a class="lh-vps-item" href="<?php echo esc_url( ea2000_link_url( $lh_guide['url'] ) ); ?>">
			<?php else : ?>
			<span class="lh-vps-item lh-vps-item-pending" aria-disabled="true">
			<?php endif; ?>
				<span class="lh-vps-ic"><?php echo ea2000_icon( $lh_guide['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<span class="lh-vps-lbl"><?php echo esc_html( $lh_vps_label ); ?></span>
				<?php if ( ! $lh_vps_has_url && '' !== $lh_pending ) : ?>
					<span class="lh-pending-tag"><?php echo esc_html( $lh_pending ); ?></span>
				<?php endif; ?>
			<?php if ( $lh_vps_has_url ) : ?>
			</a>
			<?php else : ?>
			</span>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
	<?php
endif;
$lh_steps[] = array( 'key' => 'vps', 'n' => 6, 'body' => ob_get_clean() );

$lh_steps = array_values(
	array_filter(
		$lh_steps,
		static function ( $lh_step ) {
			return '' !== trim( $lh_step['body'] );
		}
	)
);

// กลุ่ม: ข้อมูลก่อนตัดสินใจ (ปุ่ม 1 ถึง 6).
ob_start();
for ( $lh_i = 1; $lh_i <= 6; $lh_i++ ) {
	$lh_label = $lh_mod( 'links_btn' . $lh_i . '_label' );
	$lh_url   = $lh_mod( 'links_btn' . $lh_i . '_url' );
	$lh_button( 'lh-btn', $lh_label, $lh_url, isset( $lh_btn_icons[ $lh_i ] ) ? $lh_btn_icons[ $lh_i ] : 'arrow' );
}
$lh_info_body = ob_get_clean();

$lh_steps_title = str_replace( '{n}', (string) count( $lh_steps ), $lh_mod( 'links_steps_title' ) );
$lh_info_title  = $lh_mod( 'links_info_title' );
$lh_help_title  = $lh_mod( 'links_help_title' );
$lh_openchat_url   = $lh_mod( 'links_openchat_url' );
$lh_openchat_label = $lh_mod( 'links_openchat_label' );
$lh_line_label     = $lh_mod( 'links_line_label' );
$lh_show_line      = $lh_has_line && '' !== $lh_line_label;
$lh_show_openchat  = $lh_has_url( $lh_openchat_url ) && '' !== $lh_openchat_label;

/* ลิงก์เอกสารท้ายหน้า · ใช้ชื่อเพจจริงที่เผยแพร่อยู่ */
$lh_legal = array();
foreach ( array( 'privacy-policy', 'terms-of-use', 'risk-disclosure' ) as $lh_slug ) {
	$lh_page = get_page_by_path( $lh_slug );
	if ( $lh_page && 'publish' === $lh_page->post_status ) {
		$lh_legal[] = array( get_permalink( $lh_page ), get_the_title( $lh_page ) );
	}
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class( 'link-hub-page' ); ?>>
<?php wp_body_open(); ?>

<main class="link-hub">
	<div class="lh-card">

		<div class="lh-logo">
			<img src="<?php echo esc_url( $lh_logo ); ?>" alt="<?php echo esc_attr( $lh_title ); ?>" width="84" height="84">
		</div>

		<h1 class="lh-title"><?php echo esc_html( $lh_title ); ?></h1>

		<?php if ( '' !== $lh_tag ) : ?>
			<p class="lh-tagline"><?php echo nl2br( esc_html( $lh_tag ) ); ?></p>
		<?php endif; ?>

		<?php if ( $lh_badges ) : ?>
			<ul class="lh-badges">
				<?php foreach ( $lh_badges as $lh_b ) : ?>
					<li><?php echo esc_html( $lh_b ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<?php if ( $lh_show_line || $lh_show_openchat ) : ?>
			<section class="lh-group lh-group--help"<?php echo '' !== $lh_help_title ? ' aria-labelledby="lh-help-title"' : ''; ?>>
				<?php if ( '' !== $lh_help_title ) : ?>
					<h2 class="lh-section-title" id="lh-help-title"><?php echo esc_html( $lh_help_title ); ?></h2>
				<?php endif; ?>

				<?php if ( $lh_show_line ) : ?>
					<a class="lh-btn lh-btn-line" href="<?php echo esc_url( $lh_line ); ?>" target="_blank" rel="noopener" data-line-pos="go-top">
						<span class="lh-ic"><?php echo ea2000_icon( 'line' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<span class="lh-lbl"><?php echo esc_html( $lh_line_label ); ?></span>
					</a>
				<?php endif; ?>

				<?php if ( $lh_show_openchat ) : ?>
					<a class="lh-btn lh-btn-openchat" href="<?php echo esc_url( $lh_openchat_url ); ?>" target="_blank" rel="noopener" data-line-pos="go-top">
						<span class="lh-ic"><?php echo ea2000_icon( 'users' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<span class="lh-lbl"><?php echo esc_html( $lh_openchat_label ); ?></span>
					</a>
				<?php endif; ?>
			</section>
		<?php endif; ?>

		<?php if ( $lh_steps ) : ?>
			<section class="lh-journey"<?php echo '' !== $lh_steps_title ? ' aria-labelledby="lh-steps-title"' : ''; ?>>
				<?php if ( '' !== $lh_steps_title ) : ?>
					<h2 class="lh-steps-title" id="lh-steps-title"><?php echo esc_html( $lh_steps_title ); ?></h2>
				<?php endif; ?>
				<ol class="lh-steps">
					<?php foreach ( $lh_steps as $lh_idx => $lh_step ) : ?>
						<?php
						$lh_step_title = $lh_mod( 2 === $lh_step['n'] ? 'links_mt5_install_title' : 'links_step' . $lh_step['n'] . '_title' );
						$lh_step_desc  = $lh_mod( 'links_step' . $lh_step['n'] . '_desc' );
						$lh_step_note  = $lh_mod( 'links_step' . $lh_step['n'] . '_note' );
						$lh_step_badge = $lh_mod( 'links_step' . $lh_step['n'] . '_badge' );
						?>
						<li class="lh-step lh-step--<?php echo esc_attr( $lh_step['key'] ); ?>">
							<span class="lh-step-num" aria-hidden="true"><?php echo esc_html( (string) ( $lh_idx + 1 ) ); ?></span>
							<div class="lh-step-head">
								<h3 class="lh-step-title">
									<span class="screen-reader-text"><?php echo esc_html( 'ขั้นที่ ' . ( $lh_idx + 1 ) . ' ' ); ?></span><?php echo esc_html( $lh_step_title ); ?>
									<?php if ( '' !== $lh_step_badge ) : ?>
										<span class="lh-step-badge"><?php echo esc_html( $lh_step_badge ); ?></span>
									<?php endif; ?>
								</h3>
								<?php if ( '' !== $lh_step_desc ) : ?>
									<p class="lh-step-desc"><?php echo esc_html( $lh_step_desc ); ?></p>
								<?php endif; ?>
								<?php if ( '' !== $lh_step_note ) : ?>
									<p class="lh-step-note"><?php echo esc_html( $lh_step_note ); ?></p>
								<?php endif; ?>
							</div>
							<div class="lh-step-body">
								<?php echo $lh_step['body']; // phpcs:ignore WordPress.Security.EscapeOutput -- built above with escaping ?>
							</div>
						</li>
					<?php endforeach; ?>
				</ol>
			</section>
		<?php endif; ?>

		<?php if ( '' !== trim( $lh_info_body ) ) : ?>
			<section class="lh-group lh-group--info"<?php echo '' !== $lh_info_title ? ' aria-labelledby="lh-info-title"' : ''; ?>>
				<?php if ( '' !== $lh_info_title ) : ?>
					<h2 class="lh-section-title" id="lh-info-title"><?php echo esc_html( $lh_info_title ); ?></h2>
				<?php endif; ?>
				<?php echo $lh_info_body; // phpcs:ignore WordPress.Security.EscapeOutput -- built above with escaping ?>
			</section>
		<?php endif; ?>

		<?php if ( $lh_socials ) : ?>
			<div class="lh-socials">
				<?php foreach ( $lh_socials as $lh_name => $lh_s ) : ?>
					<a class="lh-soc lh-soc--<?php echo esc_attr( $lh_name ); ?>" href="<?php echo esc_url( $lh_s[0] ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( $lh_s[1] ); ?>">
						<?php echo ea2000_icon( $lh_name ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php
		/* หัวข้อเนื้อหายาวแบบเดิม (ทักมาแล้วจะได้อะไร ฯลฯ) ปิดไว้ตามแบบใหม่ · เปิดกลับได้ที่ Customizer */
		if ( ea2000_mod( 'links_show_doc' ) && function_exists( 'ea2000_page_sections' ) ) :
			ob_start();
			ea2000_page_sections( 'linksdoc', 4, false );
			$lh_doc = trim( (string) ob_get_clean() );
			if ( '' !== $lh_doc ) :
				?>
				<section class="lh-doc entry-content doc-body">
					<?php echo $lh_doc; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in ea2000_page_sections ?>
				</section>
				<?php
			endif;
		endif;
		?>

		<?php if ( '' !== $lh_note ) : ?>
			<p class="lh-note"><?php echo esc_html( $lh_note ); ?></p>
		<?php endif; ?>

		<?php if ( $lh_legal || '' !== $lh_mod( 'footer_cookie_link' ) ) : ?>
			<p class="lh-legal">
				<?php foreach ( $lh_legal as $lh_doc_link ) : ?>
					<a href="<?php echo esc_url( $lh_doc_link[0] ); ?>"><?php echo esc_html( $lh_doc_link[1] ); ?></a>
				<?php endforeach; ?>
				<?php if ( '' !== $lh_mod( 'footer_cookie_link' ) ) : ?>
					<a href="#cookie-settings"><?php echo esc_html( $lh_mod( 'footer_cookie_link' ) ); ?></a>
				<?php endif; ?>
			</p>
		<?php endif; ?>

	</div>
</main>

<?php wp_footer(); ?>
</body>
</html>
