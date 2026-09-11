<?php
/**
 * Template Name: EA2000 · หน้า Link Hub (ยิงแอด)
 *
 * หน้า "ลิงก์รวม" สไตล์ Linktree สำหรับใช้เป็นปลายทางยิงแอด (Facebook/Google/TikTok Ads)
 * เป็นหน้าแบบ standalone: ไม่มีเมนูบน/ฟุตเตอร์เว็บ เพื่อโฟกัสปุ่มเดียว (ทักไลน์)
 * วิธีใช้: สร้างเพจใหม่ เลือกเทมเพลตนี้ ตั้ง slug เช่น go แล้วใช้เป็นลิงก์ในแอด
 * แก้ข้อความ/ปุ่ม/ลิงก์ทั้งหมดได้ที่ ปรับแต่ง → EA2000 → หมวด "หน้า Link Hub"
 * ทุกปุ่ม/การ์ดจะแสดงก็ต่อเมื่อกรอกทั้งข้อความและลิงก์ · เว้นว่างไว้ = ซ่อน
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

$lh_logo = ea2000_mod( 'links_logo' );
if ( ! $lh_logo ) {
	/* กรอบโลโก้ของหน้านี้เป็นวงกลม ใช้ตรากลมของธีม (19 KB) ไม่ใช้โลโก้แนวนอนที่ถูกตัดขาดและหนัก 826 KB */
	$lh_logo = get_template_directory_uri() . '/assets/img/logo-mark.webp';
}

$lh_title  = ea2000_mod( 'links_title' );
$lh_tag    = ea2000_mod( 'links_tagline' );
$lh_badges = ea2000_lines( ea2000_mod( 'links_badges' ) );
$lh_note   = ea2000_mod( 'links_note' );
$lh_line   = trim( (string) ea2000_mod( 'line_url' ) );
$lh_line_label = trim( (string) ea2000_mod( 'links_line_label' ) );
$lh_signup_url   = trim( (string) ea2000_mod( 'links_signup_url' ) );
$lh_signup_label = trim( (string) ea2000_mod( 'links_signup_label' ) );
$lh_account_guide_url   = trim( (string) ea2000_mod( 'links_account_guide_url' ) );
$lh_account_guide_label = trim( (string) ea2000_mod( 'links_account_guide_label' ) );
$lh_mt5_download_url    = trim( (string) ea2000_mod( 'links_mt5_download_url' ) );
$lh_mt5_download_label  = trim( (string) ea2000_mod( 'links_mt5_download_label' ) );
$lh_openchat_url   = trim( (string) ea2000_mod( 'links_openchat_url' ) );
$lh_openchat_label = trim( (string) ea2000_mod( 'links_openchat_label' ) );

$lh_show_line          = $lh_has_url( $lh_line ) && '' !== $lh_line_label;
$lh_show_signup        = $lh_has_url( $lh_signup_url ) && '' !== $lh_signup_label;
$lh_show_account_guide = $lh_has_url( $lh_account_guide_url ) && '' !== $lh_account_guide_label;
$lh_show_mt5_download  = $lh_has_url( $lh_mt5_download_url ) && '' !== $lh_mt5_download_label;
$lh_show_openchat      = $lh_has_url( $lh_openchat_url ) && '' !== $lh_openchat_label;

/* ไอคอนประกอบปุ่มตามลำดับ (ตกแต่ง · เปลี่ยนความหมายปุ่มได้โดยไม่ผูกกับไอคอน) */
$lh_btn_icons = array( 1 => 'guide', 2 => 'tag', 3 => 'download', 4 => 'layout', 5 => 'arrow', 6 => 'arrow' );
$lh_guide_icons = array( 1 => 'guide', 2 => 'windows', 3 => 'android', 4 => 'apple', 5 => 'macos' );

$lh_socials = array();
foreach ( array(
	'facebook'  => array( 'facebook_url', 'Facebook' ),
	'instagram' => array( 'instagram_url', 'Instagram' ),
	'tiktok'    => array( 'tiktok_url', 'TikTok' ),
	'youtube'   => array( 'youtube_url', 'YouTube' ),
) as $lh_name => $lh_s ) {
	$lh_surl = trim( (string) ea2000_mod( $lh_s[0] ) );
	if ( $lh_has_url( $lh_surl ) ) {
		$lh_socials[ $lh_name ] = array( $lh_surl, $lh_s[1] );
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
			<?php if ( $lh_logo ) : ?>
				<img src="<?php echo esc_url( $lh_logo ); ?>" alt="<?php echo esc_attr( $lh_title ); ?>" width="84" height="84">
			<?php endif; ?>
		</div>

		<h1 class="lh-title"><?php echo esc_html( $lh_title ); ?></h1>

		<?php if ( $lh_tag ) : ?>
			<p class="lh-tagline"><?php echo nl2br( esc_html( $lh_tag ) ); ?></p>
		<?php endif; ?>

		<?php if ( $lh_badges ) : ?>
			<ul class="lh-badges">
				<?php foreach ( $lh_badges as $lh_b ) : ?>
					<li><?php echo esc_html( $lh_b ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<?php if ( $lh_show_signup || $lh_show_account_guide || $lh_show_mt5_download ) : ?>
			<div class="lh-account-actions">
				<?php if ( $lh_show_signup ) : ?>
					<a class="lh-btn lh-btn-signup" href="<?php echo esc_url( ea2000_link_url( $lh_signup_url ) ); ?>">
						<span class="lh-ic lh-account-ic"><?php echo ea2000_icon( 'account' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<span class="lh-signup-copy">
							<span class="lh-lbl"><?php echo esc_html( $lh_signup_label ); ?></span>
						</span>
						<span class="lh-ar" aria-hidden="true">&rsaquo;</span>
					</a>
				<?php endif; ?>

				<?php if ( $lh_show_account_guide || $lh_show_mt5_download ) : ?>
					<div class="lh-mini-actions">
						<?php if ( $lh_show_account_guide ) : ?>
							<a class="lh-mini-btn" href="<?php echo esc_url( ea2000_link_url( $lh_account_guide_url ) ); ?>">
								<span class="lh-ic"><?php echo ea2000_icon( 'guide' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
								<span><?php echo esc_html( $lh_account_guide_label ); ?></span>
							</a>
						<?php endif; ?>
						<?php if ( $lh_show_mt5_download ) : ?>
							<a class="lh-mini-btn" href="<?php echo esc_url( ea2000_link_url( $lh_mt5_download_url ) ); ?>">
								<span class="lh-ic"><?php echo ea2000_icon( 'download' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
								<span><?php echo esc_html( $lh_mt5_download_label ); ?></span>
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $lh_show_line ) : ?>
			<a class="lh-btn lh-btn-line" href="<?php echo esc_url( $lh_line ); ?>" target="_blank" rel="noopener" data-line-pos="go-top">
				<span class="lh-ic"><?php echo ea2000_icon( 'line' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<span class="lh-lbl"><?php echo esc_html( $lh_line_label ); ?></span>
			</a>
		<?php endif; ?>

		<?php if ( $lh_show_openchat ) : ?>
			<a class="lh-btn lh-btn-openchat" href="<?php echo esc_url( $lh_openchat_url ); ?>" target="_blank" rel="noopener">
				<span class="lh-ic"><?php echo ea2000_icon( 'users' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<span class="lh-lbl"><?php echo esc_html( $lh_openchat_label ); ?></span>
			</a>
		<?php endif; ?>

		<?php if ( ea2000_mod( 'links_fast_enabled' ) && $lh_has_url( ea2000_mod( 'links_fast_url' ) ) ) : ?>
			<?php
			/*
			 * การ์ดดาวน์โหลดเด่น: ใช้ภาพจาก links_fast_img (ถ้าว่างใช้ links_feature_img)
			 * ถ้ายังไม่ตั้งลิงก์ดาวน์โหลด จะแสดงเป็นการ์ดเฉย ๆ ไม่มีลิงก์ ไม่มีไฟล์สำรอง
			 */
			$lh_fast_img   = trim( (string) ea2000_mod( 'links_fast_img' ) );
			$lh_fast_url   = trim( (string) ea2000_mod( 'links_fast_url' ) );
			$lh_fast_alt   = trim( (string) ea2000_mod( 'links_fast_alt' ) );
			$lh_fast_cap   = trim( (string) ea2000_mod( 'links_feature_caption' ) );
			if ( '' === $lh_fast_img ) {
				$lh_fast_img = trim( (string) ea2000_mod( 'links_feature_img' ) );
			}
			$lh_fast_has_url = $lh_has_url( $lh_fast_url );
			$lh_fast_href    = $lh_fast_has_url ? ea2000_link_url( $lh_fast_url ) : '';
			/* ใส่ download เฉพาะเมื่อลิงก์เป็นไฟล์ · ถ้าเป็นหน้าเว็บ/หน้าแชร์ไฟล์ให้เปิดตามปกติ */
			$lh_fast_is_file = (bool) preg_match( '/\.(zip|ex5|mq5|set|pdf)([?#]|$)/i', $lh_fast_href );
			$lh_fast_text    = '' !== $lh_fast_alt ? $lh_fast_alt : ( '' !== $lh_fast_cap ? $lh_fast_cap : $lh_title );
			?>
			<?php if ( '' !== $lh_fast_img || '' !== $lh_fast_text ) : ?>
				<div class="lh-feature lh-feature-fast">
					<?php if ( $lh_fast_has_url ) : ?>
					<a class="lh-feature-frame" href="<?php echo esc_url( $lh_fast_href ); ?>"<?php echo $lh_fast_is_file ? ' download' : ''; ?>>
					<?php else : ?>
					<div class="lh-feature-frame">
					<?php endif; ?>
						<?php if ( '' !== $lh_fast_img ) : ?>
							<img src="<?php echo esc_url( $lh_fast_img ); ?>" alt="<?php echo esc_attr( $lh_fast_text ); ?>" width="1200" height="630" loading="lazy">
						<?php else : ?>
							<span class="lh-feature-placeholder"><?php echo esc_html( $lh_fast_text ); ?></span>
						<?php endif; ?>
					<?php if ( $lh_fast_has_url ) : ?>
					</a>
					<?php else : ?>
					</div>
					<?php endif; ?>
					<?php if ( '' !== $lh_fast_cap ) : ?>
						<span class="lh-feature-cap"><?php echo esc_html( $lh_fast_cap ); ?></span>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		<?php elseif ( ! ea2000_mod( 'links_fast_enabled' ) ) : ?>
			<?php
			$lh_feat_img     = trim( (string) ea2000_mod( 'links_feature_img' ) );
			$lh_feat_url     = trim( (string) ea2000_mod( 'links_feature_url' ) );
			$lh_feat_caption = trim( (string) ea2000_mod( 'links_feature_caption' ) );
			$lh_feat_has_url = $lh_has_url( $lh_feat_url );
			/* รูปการ์ดเริ่มต้นเป็นภาพรอไฟล์ จึงแสดงเฉพาะเมื่อมีลิงก์จริง */
			if ( '' !== $lh_feat_img && $lh_feat_has_url ) :
				?>
				<div class="lh-feature">
					<?php if ( $lh_feat_has_url ) : ?>
					<a class="lh-feature-frame" href="<?php echo esc_url( ea2000_link_url( $lh_feat_url ) ); ?>">
					<?php else : ?>
					<div class="lh-feature-frame">
					<?php endif; ?>
						<img src="<?php echo esc_url( $lh_feat_img ); ?>" alt="<?php echo esc_attr( '' !== $lh_feat_caption ? $lh_feat_caption : $lh_title ); ?>" loading="lazy">
					<?php if ( $lh_feat_has_url ) : ?>
					</a>
					<?php else : ?>
					</div>
					<?php endif; ?>
					<?php if ( '' !== $lh_feat_caption ) : ?>
						<span class="lh-feature-cap"><?php echo esc_html( $lh_feat_caption ); ?></span>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php
			$lh_feat2_img         = trim( (string) ea2000_mod( 'links_feature2_img' ) );
			$lh_feat2_url         = trim( (string) ea2000_mod( 'links_feature2_url' ) );
			$lh_feat2_caption     = trim( (string) ea2000_mod( 'links_feature2_caption' ) );
			$lh_feat2_placeholder = trim( (string) ea2000_mod( 'links_feature2_placeholder' ) );
			$lh_feat2_has_url     = $lh_has_url( $lh_feat2_url );
			/* ข้อความรอรูป (เช่น 1200 × 630) ให้เห็นเฉพาะแอดมิน ผู้เข้าชมไม่ควรเห็นกล่องว่าง */
			if ( '' !== $lh_feat2_img || ( '' !== $lh_feat2_placeholder && current_user_can( 'customize' ) ) ) :
				?>
				<div class="lh-feature lh-feature-secondary">
					<?php if ( $lh_feat2_has_url ) : ?>
					<a class="lh-feature-frame" href="<?php echo esc_url( ea2000_link_url( $lh_feat2_url ) ); ?>">
					<?php else : ?>
					<div class="lh-feature-frame">
					<?php endif; ?>
						<?php if ( '' !== $lh_feat2_img ) : ?>
							<img src="<?php echo esc_url( $lh_feat2_img ); ?>" alt="<?php echo esc_attr( '' !== $lh_feat2_caption ? $lh_feat2_caption : $lh_title ); ?>" loading="lazy">
						<?php else : ?>
							<span class="lh-feature-placeholder"><?php echo esc_html( $lh_feat2_placeholder ); ?></span>
						<?php endif; ?>
					<?php if ( $lh_feat2_has_url ) : ?>
					</a>
					<?php else : ?>
					</div>
					<?php endif; ?>
					<?php if ( '' !== $lh_feat2_caption ) : ?>
						<span class="lh-feature-cap"><?php echo esc_html( $lh_feat2_caption ); ?></span>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		<?php endif; ?>

		<?php
		for ( $lh_i = 1; $lh_i <= 6; $lh_i++ ) :
			$lh_label = trim( (string) ea2000_mod( 'links_btn' . $lh_i . '_label' ) );
			$lh_url   = trim( (string) ea2000_mod( 'links_btn' . $lh_i . '_url' ) );
			if ( '' === $lh_label || ! $lh_has_url( $lh_url ) ) {
				continue;
			}

			$lh_icon = isset( $lh_btn_icons[ $lh_i ] ) ? $lh_btn_icons[ $lh_i ] : 'arrow';
			?>
			<a class="lh-btn" href="<?php echo esc_url( ea2000_link_url( $lh_url ) ); ?>">
				<span class="lh-ic"><?php echo ea2000_icon( $lh_icon ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<span class="lh-lbl"><?php echo esc_html( $lh_label ); ?></span>
				<span class="lh-ar" aria-hidden="true">&rsaquo;</span>
			</a>
			<?php
		endfor;

		$lh_guides = array();

		for ( $lh_i = 1; $lh_i <= 5; $lh_i++ ) {
			$lh_label = trim( (string) ea2000_mod( 'links_guide' . $lh_i . '_label' ) );
			$lh_url   = trim( (string) ea2000_mod( 'links_guide' . $lh_i . '_url' ) );

			if ( '' === $lh_label || ! $lh_has_url( $lh_url ) ) {
				continue;
			}

			$lh_guides[] = array(
				'label' => $lh_label,
				'url'   => $lh_url,
				'icon'  => isset( $lh_guide_icons[ $lh_i ] ) ? $lh_guide_icons[ $lh_i ] : 'arrow',
			);
		}

		$lh_setup_guides = array();
		$lh_vps_guides   = array();
		foreach ( $lh_guides as $lh_guide ) {
			$lh_guide_url_norm = strtolower( rtrim( $lh_guide['url'], '/' ) );
			if ( false !== strpos( $lh_guide_url_norm, 'vps-' ) || false !== stripos( $lh_guide['label'], 'VPS' ) ) {
				$lh_vps_guides[] = $lh_guide;
			} else {
				$lh_setup_guides[] = $lh_guide;
			}
		}
		?>
		<?php foreach ( $lh_setup_guides as $lh_guide ) : ?>
			<a class="lh-btn" href="<?php echo esc_url( ea2000_link_url( $lh_guide['url'] ) ); ?>">
				<span class="lh-ic"><?php echo ea2000_icon( $lh_guide['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<span class="lh-lbl"><?php echo esc_html( $lh_guide['label'] ); ?></span>
				<span class="lh-ar" aria-hidden="true">&rsaquo;</span>
			</a>
		<?php endforeach; ?>

		<?php if ( $lh_vps_guides ) : ?>
			<section class="lh-vps" aria-label="เลือกอ่านคู่มือการใช้งาน VPS ตามระบบ">
				<h2 class="lh-section-title">เลือกอ่านคู่มือการใช้งาน VPS ตามระบบ</h2>
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
						} elseif ( false !== stripos( $lh_vps_label, 'macOS' ) || false !== stripos( $lh_vps_label, 'Mac' ) ) {
							$lh_vps_label = 'macOS';
							$lh_guide['icon'] = 'macos';
						}
						?>
						<a class="lh-vps-item" href="<?php echo esc_url( ea2000_link_url( $lh_guide['url'] ) ); ?>">
							<span class="lh-vps-ic"><?php echo ea2000_icon( $lh_guide['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
							<span class="lh-vps-lbl"><?php echo esc_html( $lh_vps_label ); ?></span>
						</a>
					<?php endforeach; ?>
				</div>
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
		$lh_doc = '';
		if ( function_exists( 'ea2000_page_sections' ) ) {
			ob_start();
			ea2000_page_sections( 'linksdoc', 4, false );
			$lh_doc = trim( (string) ob_get_clean() );
		}
		if ( '' !== $lh_doc ) :
			?>
			<section class="lh-doc entry-content doc-body">
				<?php echo $lh_doc; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in ea2000_page_sections ?>
			</section>
		<?php endif; ?>

		<?php if ( $lh_show_line ) : ?>
			<a class="lh-btn lh-btn-line lh-btn-line-bottom" href="<?php echo esc_url( $lh_line ); ?>" target="_blank" rel="noopener" data-line-pos="go-bottom">
				<span class="lh-ic"><?php echo ea2000_icon( 'line' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<span class="lh-lbl"><?php echo esc_html( $lh_line_label ); ?></span>
			</a>
		<?php endif; ?>

		<?php if ( $lh_note ) : ?>
			<p class="lh-note"><?php echo esc_html( $lh_note ); ?></p>
		<?php endif; ?>

	</div>
</main>

<?php wp_footer(); ?>
</body>
</html>
