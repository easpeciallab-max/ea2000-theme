<?php
/**
 * Template Name: EA2000 · หน้ารวมบทความ
 *
 * หน้ารวมบทความทั้งหมด: สร้างเพจใหม่ เลือกเทมเพลตนี้ ตั้ง slug = articles แล้วลิงก์เข้าเมนู
 * (ใช้การ์ดบทความ + ปุ่มโหลดเพิ่มชุดเดียวกับหน้า archive)
 *
 * @package ea2000
 */

defined( 'ABSPATH' ) || exit;

get_header();

$ea2000_blog_q = new WP_Query(
	array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => (int) get_option( 'posts_per_page' ),
		'paged'               => 1,
		'ignore_sticky_posts' => true,
	)
);
?>

<main id="main">

	<div class="page-hero archive-hero">
		<div class="container">

			<nav class="breadcrumb breadcrumb-center" aria-label="เส้นทางนำทาง">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">หน้าแรก</a>
				<span class="breadcrumb-sep" aria-hidden="true">›</span>
				<span class="breadcrumb-current"><?php the_title(); ?></span>
			</nav>

			<h1><?php the_title(); ?></h1>
			<p class="muted">ความรู้เรื่อง EA, MT5 และการบริหารความเสี่ยง</p>

			<?php if ( $ea2000_blog_q->found_posts ) : ?>
				<p class="archive-count"><?php echo esc_html( number_format_i18n( $ea2000_blog_q->found_posts ) . ' บทความ' ); ?></p>
			<?php endif; ?>

		</div>
	</div>

	<div class="posts-wrap">
		<div class="container">

			<?php if ( $ea2000_blog_q->have_posts() ) : ?>

				<div class="posts-grid">
					<?php
					while ( $ea2000_blog_q->have_posts() ) :
						$ea2000_blog_q->the_post();
						ea2000_post_card();
					endwhile;
					wp_reset_postdata();
					?>
				</div>

				<?php if ( $ea2000_blog_q->max_num_pages > 1 ) : ?>
					<div class="load-more">
						<button type="button" class="btn btn-ghost load-more-btn"
							data-page="1"
							data-max="<?php echo esc_attr( (string) (int) $ea2000_blog_q->max_num_pages ); ?>"
							data-query="<?php echo esc_attr( wp_json_encode( array() ) ); ?>">
							โหลดเพิ่ม
						</button>
					</div>
				<?php endif; ?>

			<?php else : ?>

				<p class="no-posts">ยังไม่มีบทความในขณะนี้</p>

			<?php endif; ?>

		</div>
	</div>

</main>

<?php
get_footer();
