<?php
/**
 * Template Name: EA2000 · Elementor Full Width
 * Template Post Type: page
 *
 * @package ea2000
 */

get_header();
?>

<main id="main" class="elementor-page-shell elementor-page-shell--full">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article <?php post_class( 'elementor-entry' ); ?>>
			<?php the_content(); ?>
		</article>
	<?php endwhile; ?>
</main>

<?php
get_footer();
