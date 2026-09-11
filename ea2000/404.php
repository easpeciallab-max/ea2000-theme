<?php
/**
 * 404 · ไม่พบหน้า
 *
 * @package ea2000
 */

defined( 'ABSPATH' ) || exit;

get_header();

$ea2000_line = trim( (string) ea2000_mod( 'line_url' ) );
$ea2000_line = in_array( $ea2000_line, array( '', '#' ), true ) ? '' : $ea2000_line; // ยังไม่กรอก LINE OA: ซ่อนปุ่ม LINE
?>

<main id="main">
	<section class="error-404">
		<div class="container container-narrow">
			<div class="error-404-inner">

				<p class="error-code">404</p>
				<h1 class="error-title">ไม่พบหน้าที่คุณค้นหา</h1>
				<p class="error-text">หน้านี้อาจถูกย้าย ลบ หรือพิมพ์ลิงก์ผิด ลองค้นหา หรือกลับไปหน้าหลักได้เลย</p>

				<form class="error-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<input type="search" name="s" placeholder="ค้นหาคู่มือ…" aria-label="ค้นหา" spellcheck="false" autocomplete="off" value="<?php echo esc_attr( get_search_query() ); ?>">
					<button type="submit" aria-label="ค้นหา"><?php echo ea2000_icon( 'arrow', 'icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
				</form>

				<div class="error-actions">
					<a class="btn btn-fire" href="<?php echo esc_url( home_url( '/' ) ); ?>">
						<?php echo ea2000_icon( 'home', 'icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						กลับหน้าแรก
					</a>
					<?php if ( $ea2000_line ) : ?>
					<a class="btn btn-line" href="<?php echo esc_url( $ea2000_line ); ?>" target="_blank" rel="noopener">
						<?php echo ea2000_icon( 'line' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						ทัก LINE
					</a>
					<?php endif; ?>
				</div>

				<nav class="error-links" aria-label="ลิงก์ด่วน">
					<a href="<?php echo esc_url( home_url( '/forward-test/' ) ); ?>">การทดสอบ</a>
					<a href="<?php echo esc_url( home_url( '/pricing/' ) ); ?>">แพ็กเกจ</a>
					<a href="<?php echo esc_url( home_url( '/how-to-install/' ) ); ?>">วิธีติดตั้ง</a>
					<a href="<?php echo esc_url( home_url( '/risk-disclosure/' ) ); ?>">คำเตือนความเสี่ยง</a>
				</nav>

			</div>
		</div>
	</section>
</main>

<?php
get_footer();
