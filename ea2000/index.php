<?php
/**
 * Blog index / archive (รายการบทความ + หน้าหมวดหมู่/ป้ายกำกับ/ค้นหา)
 *
 * @package ea2000
 */

defined( 'ABSPATH' ) || exit;

get_header();

$ea2000_desc  = get_the_archive_description();
$ea2000_found = (int) $GLOBALS['wp_query']->found_posts;
?>

<main id="main">

	<div class="page-hero archive-hero">
		<div class="container">

			<nav class="breadcrumb breadcrumb-center" aria-label="เส้นทางนำทาง">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">หน้าแรก</a>
				<span class="breadcrumb-sep" aria-hidden="true">›</span>
				<span class="breadcrumb-current">
					<?php
					if ( is_search() ) {
						echo 'ค้นหา';
					} elseif ( is_archive() ) {
						the_archive_title();
					} else {
						echo 'บทความ';
					}
					?>
				</span>
			</nav>

			<h1>
				<?php
				if ( is_home() && ! is_front_page() ) {
					$ea2000_posts_page = (int) get_option( 'page_for_posts' );
					echo $ea2000_posts_page ? esc_html( get_the_title( $ea2000_posts_page ) ) : 'บทความ';
				} elseif ( is_search() ) {
					echo 'ผลการค้นหา: ' . esc_html( get_search_query() );
				} elseif ( is_archive() ) {
					the_archive_title();
				} else {
					echo 'บทความ';
				}
				?>
			</h1>

			<?php if ( $ea2000_desc ) : ?>
				<div class="archive-desc"><?php echo wp_kses_post( $ea2000_desc ); ?></div>
			<?php elseif ( is_home() ) : ?>
				<p class="muted">ความรู้เรื่อง EA, MT5 และการบริหารความเสี่ยง</p>
			<?php endif; ?>

			<?php if ( $ea2000_found ) : ?>
				<p class="archive-count"><?php echo esc_html( number_format_i18n( $ea2000_found ) . ' บทความ' ); ?></p>
			<?php endif; ?>

		</div>
	</div>

	<div class="posts-wrap">
		<div class="container">

			<?php if ( have_posts() ) : ?>

				<div class="posts-grid">
					<?php
					while ( have_posts() ) :
						the_post();
						ea2000_post_card();
					endwhile;
					?>
				</div>

				<?php if ( $GLOBALS['wp_query']->max_num_pages > 1 ) : ?>
					<div class="load-more">
						<button type="button" class="btn btn-ghost load-more-btn"
							data-page="<?php echo esc_attr( (string) max( 1, (int) get_query_var( 'paged' ) ) ); ?>"
							data-max="<?php echo esc_attr( (string) (int) $GLOBALS['wp_query']->max_num_pages ); ?>"
							data-query="<?php echo esc_attr( wp_json_encode( $GLOBALS['wp_query']->query ) ); ?>">
							โหลดเพิ่ม
						</button>
					</div>
					<noscript>
						<div class="pagination">
							<?php
							echo paginate_links( // phpcs:ignore WordPress.Security.EscapeOutput
								array(
									'prev_text' => '&larr; ก่อนหน้า',
									'next_text' => 'ถัดไป &rarr;',
								)
							);
							?>
						</div>
					</noscript>
				<?php endif; ?>

			<?php else : ?>

				<?php if ( is_search() ) : ?>
					<p class="no-posts">ไม่พบบทความที่ตรงกับคำค้นหา ลองใช้คำที่สั้นลง หรือดูคู่มือด้านล่าง</p>
				<?php else : ?>
					<p class="no-posts">กำลังเตรียมบทความอยู่ ระหว่างนี้อ่านคู่มือที่เขียนไว้แล้วได้เลย</p>
				<?php endif; ?>

				<?php
				$ea2000_hub = array();
				foreach ( array(
					'how-to-install'   => 'ติดตั้ง EA2000 บน MT5 ทีละขั้น',
					'backtest'         => 'ทดสอบย้อนหลังและอ่านผลให้เป็น',
					'forward-test'     => 'Forward Test ต่างจาก Backtest อย่างไร',
					'open-mt5-account' => 'สมัครบัญชี Zaurix เพื่อรับสิทธิ์ใช้ EA2000',
					'mt5-login'        => 'ติดตั้ง MT5 และล็อกอินเข้าบัญชี',
					'vps-windows'      => 'เชื่อมต่อ VPS จากคอมพิวเตอร์ Windows',
					'vps-android'      => 'เข้าดู VPS จากมือถือ Android',
					'vps-ios'          => 'เข้าดู VPS จาก iPhone หรือ iPad',
					'risk-disclosure'  => 'ความเสี่ยงที่ต้องรู้ก่อนเริ่ม',
				) as $ea2000_hub_slug => $ea2000_hub_desc ) {
					$ea2000_hub_page = get_page_by_path( $ea2000_hub_slug );
					if ( $ea2000_hub_page && 'publish' === $ea2000_hub_page->post_status ) {
						$ea2000_hub[] = array( get_the_title( $ea2000_hub_page ), get_permalink( $ea2000_hub_page ), $ea2000_hub_desc );
					}
				}
				?>

				<?php if ( ! empty( $ea2000_hub ) ) : ?>
					<h2 class="guide-more-title">คู่มือที่อ่านได้ตอนนี้</h2>
					<ul class="guide-more">
						<?php foreach ( $ea2000_hub as $ea2000_idx => $ea2000_row ) : ?>
							<li class="guide-more-item">
								<a href="<?php echo esc_url( $ea2000_row[1] ); ?>">
									<span class="guide-more-idx mono keep-case"><?php echo esc_html( str_pad( (string) ( $ea2000_idx + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
									<span class="guide-more-body">
										<span class="guide-more-name"><?php echo ea2000_text( $ea2000_row[0] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ea2000_text ?></span>
										<span class="guide-more-sub"><?php echo ea2000_text( $ea2000_row[2] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ea2000_text ?></span>
									</span>
									<?php echo ea2000_icon( 'arrow', 'icon icon-sm key-arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

			<?php endif; ?>

		</div>
	</div>

</main>

<?php
get_footer();
