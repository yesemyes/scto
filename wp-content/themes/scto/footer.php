<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Scto
 */

?>

	</div><!-- #content -->
<div class="right-sidebar flexbox box-md-3">
    <?php
        get_sidebar();
    ?>
</div>
	<footer id="colophon" class="site-footer flexbox center-xs">
		<div class="site-info">
<!--			<a href="--><?php //echo esc_url( __( 'https://wordpress.org/', 'scto' ) ); ?><!--">--><?php
//				/* translators: %s: CMS name, i.e. WordPress. */
//				printf( esc_html__( 'Proudly powered by %s', 'scto' ), 'WordPress' );
//			?><!--</a>-->
<!--			<span class="sep"> | </span>-->
			<?php
				/* translators: 1: Theme name, 2: Theme author. */
				printf( esc_html__( 'Theme: %1$s by %2$s.', 'scto' ), 'scto', '<a href="http://underscores.me/">Medical Network</a>' );
			?>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
