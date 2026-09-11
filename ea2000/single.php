<?php
/**
 * Single post · บทความ (ออกแบบสำหรับคอนเทนต์ SEO)
 *
 * @package ea2000
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$ea2000_cats  = get_the_category();
	$ea2000_cat   = ! empty( $ea2000_cats ) ? $ea2000_cats[0] : null;
	$ea2000_thumb = get_the_post_thumbnail_url( get_the_ID(), 'full' );
	?>

	<div class="reading-progress" aria-hidden="true"><span></span></div>

	<main id="main">
		<article <?php post_class( 'single-article' ); ?>>

			<header class="article-hero">
				<div class="container container-narrow">
					<nav class="breadcrumb" aria-label="เส้นทางนำทาง">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>">หน้าแรก</a>
						<?php if ( $ea2000_cat ) : ?>
							<span class="breadcrumb-sep" aria-hidden="true">›</span>
							<a href="<?php echo esc_url( get_category_link( $ea2000_cat->term_id ) ); ?>"><?php echo esc_html( $ea2000_cat->name ); ?></a>
						<?php endif; ?>
						<span class="breadcrumb-sep" aria-hidden="true">›</span>
						<span class="breadcrumb-current"><?php the_title(); ?></span>
					</nav>

					<?php if ( $ea2000_cat ) : ?>
						<a class="article-cat" href="<?php echo esc_url( get_category_link( $ea2000_cat->term_id ) ); ?>"><?php echo esc_html( $ea2000_cat->name ); ?></a>
					<?php endif; ?>

					<h1 class="article-title"><?php the_title(); ?></h1>

					<div class="article-meta">
						<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
						<span class="article-meta-sep" aria-hidden="true">·</span>
						<span><?php echo esc_html( 'เข้าชม ' . number_format_i18n( ea2000_get_post_views( get_the_ID() ) ) . ' ครั้ง' ); ?></span>
					</div>
				</div>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<div class="container container-narrow">
					<figure class="article-thumb"><?php the_post_thumbnail( 'large', array( 'alt' => esc_attr( ea2000_featured_image_alt() ) ) ); ?></figure>
				</div>
			<?php endif; ?>

			<div class="container container-narrow">
				<?php
				$GLOBALS['ea2000_toc'] = array();
				$ea2000_content        = apply_filters( 'the_content', get_the_content() );
				$ea2000_content        = str_replace( ']]>', ']]&gt;', $ea2000_content );
				$ea2000_toc            = isset( $GLOBALS['ea2000_toc'] ) ? $GLOBALS['ea2000_toc'] : array();
				?>

				<?php if ( count( $ea2000_toc ) >= 1 ) : ?>
					<nav class="toc" aria-label="สารบัญ">
						<p class="toc-title"><?php echo ea2000_icon( 'layout', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?> สารบัญ</p>
						<ul>
							<?php foreach ( $ea2000_toc as $ea2000_h ) : ?>
								<li class="toc-l<?php echo esc_attr( $ea2000_h['level'] ); ?>"><a href="#<?php echo esc_attr( $ea2000_h['id'] ); ?>"><?php echo esc_html( $ea2000_h['text'] ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</nav>
				<?php endif; ?>

				<div class="entry-content">
					<?php echo $ea2000_content; // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>

				<div class="article-share">
					<span class="article-share-label">แชร์บทความนี้</span>
					<a class="share-btn share-line" href="<?php echo esc_url( 'https://social-plugins.line.me/lineit/share?url=' . rawurlencode( get_permalink() ) ); ?>" target="_blank" rel="noopener" aria-label="แชร์ไป LINE"><?php echo ea2000_icon( 'line' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
					<a class="share-btn share-fb" href="<?php echo esc_url( 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode( get_permalink() ) ); ?>" target="_blank" rel="noopener" aria-label="แชร์ไป Facebook"><?php echo ea2000_icon( 'facebook' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
					<button class="share-btn share-copy" type="button" data-url="<?php echo esc_url( get_permalink() ); ?>" aria-label="คัดลอกลิงก์"><?php echo ea2000_icon( 'link' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
				</div>

				<?php
				wp_link_pages(
					array(
						'before' => '<div class="page-links">หน้า: ',
						'after'  => '</div>',
					)
				);

				$ea2000_tags = get_the_tag_list( '<ul class="article-tags"><li>', '</li><li>', '</li></ul>' );
				if ( $ea2000_tags ) {
					echo wp_kses_post( $ea2000_tags );
				}
				?>

				<div class="article-disclaimer">
					<?php echo ea2000_icon( 'warn', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<p>
						<?php echo esc_html( ea2000_mod( 'risk_text' ) ); ?>
						<a href="<?php echo esc_url( home_url( '/risk-disclosure/' ) ); ?>">อ่านคำเตือนฉบับเต็ม</a>
					</p>
				</div>
			</div>

		</article>

		<?php ea2000_line_cta(); ?>

		<?php
		if ( $ea2000_cat ) :
			$ea2000_related = new WP_Query(
				array(
					'category__in'        => array( $ea2000_cat->term_id ),
					'post__not_in'        => array( get_the_ID() ),
					'posts_per_page'      => 9,
					'no_found_rows'       => true,
					'ignore_sticky_posts' => true,
				)
			);
			if ( $ea2000_related->have_posts() ) :
				?>
				<section class="section section-alt related-section" aria-label="บทความที่เกี่ยวข้อง">
					<div class="container">
						<div class="related-head">
							<h2>บทความที่เกี่ยวข้อง</h2>
							<div class="rail-nav">
								<button type="button" class="rail-btn rail-prev" data-dir="-1" aria-label="เลื่อนซ้าย"><?php echo ea2000_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
								<button type="button" class="rail-btn rail-next" data-dir="1" aria-label="เลื่อนขวา"><?php echo ea2000_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
							</div>
						</div>
						<div class="related-rail">
							<?php
							while ( $ea2000_related->have_posts() ) :
								$ea2000_related->the_post();
								?>
								<article <?php post_class( 'post-card' ); ?>>
									<?php if ( has_post_thumbnail() ) : ?>
										<a class="post-card-thumb" href="<?php the_permalink(); ?>">
											<?php the_post_thumbnail( 'medium_large', array( 'alt' => esc_attr( ea2000_featured_image_alt() ) ) ); ?>
										</a>
									<?php endif; ?>
									<div class="post-card-body">
										<span class="post-meta"><?php echo esc_html( get_the_date() ); ?></span>
										<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
									</div>
								</article>
								<?php
							endwhile;
							wp_reset_postdata();
							?>
						</div>
					</div>
				</section>
				<?php
			endif;
		endif;
		?>

	</main>

	<?php
	if ( ! ea2000_has_seo_plugin() ) :
	$ea2000_schema = array(
		'@context'         => 'https://schema.org',
		'@type'            => 'BlogPosting',
		'headline'         => get_the_title(),
		'datePublished'    => get_the_date( 'c' ),
		'dateModified'     => get_the_modified_date( 'c' ),
		'author'           => array(
			'@type' => 'Person',
			'name'  => get_the_author(),
		),
		'publisher'        => array(
			'@type' => 'Organization',
			'name'  => get_bloginfo( 'name' ),
			'logo'  => array(
				'@type' => 'ImageObject',
				'url'   => ea2000_logo_url(),
			),
		),
		'mainEntityOfPage' => get_permalink(),
	);
	if ( $ea2000_thumb ) {
		$ea2000_schema['image'] = $ea2000_thumb;
	}
	echo '<script type="application/ld+json">' . wp_json_encode( $ea2000_schema, JSON_UNESCAPED_UNICODE ) . '</script>'; // phpcs:ignore WordPress.Security.EscapeOutput
	endif;

	ea2000_breadcrumb_jsonld(
		array_values(
			array_filter(
				array(
					array( 'name' => 'หน้าแรก', 'url' => home_url( '/' ) ),
					$ea2000_cat ? array( 'name' => $ea2000_cat->name, 'url' => get_category_link( $ea2000_cat->term_id ) ) : null,
					array( 'name' => get_the_title(), 'url' => get_permalink() ),
				)
			)
		)
	);
	?>

	<?php
endwhile;

get_footer();
