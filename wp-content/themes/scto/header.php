<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Scto
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class('flexbox center-xs'); ?>>
<div id="page" class="site flexbox">

<!--	<header id="masthead" class="site-header">-->
<!--		<div class="site-branding">-->
<!--			--><?php
//			the_custom_logo();
//			if ( is_front_page() && is_home() ) : ?>
<!--				<h1 class="site-title"><a href="--><?php //echo esc_url( home_url( '/' ) ); ?><!--" rel="home">--><?php //bloginfo( 'name' ); ?><!--</a></h1>-->
<!--			--><?php //else : ?>
<!--				<p class="site-title"><a href="--><?php //echo esc_url( home_url( '/' ) ); ?><!--" rel="home">--><?php //bloginfo( 'name' ); ?><!--</a></p>-->
<!--			--><?php
//			endif;
//
//			$description = get_bloginfo( 'description', 'display' );
//			if ( $description || is_customize_preview() ) : ?>
<!--				<p class="site-description">--><?php //echo $description; /* WPCS: xss ok. */ ?><!--</p>-->
<!--			--><?php
//			endif; ?>
<!--		</div><!-- .site-branding -->
<!---->
<!--		<nav id="site-navigation" class="main-navigation">-->
<!--			<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">--><?php //esc_html_e( 'Primary Menu', 'scto' ); ?><!--</button>-->
<!--			--><?php
//				wp_nav_menu( array(
//					'theme_location' => 'menu-1',
//					'menu_id'        => 'primary-menu',
//				) );
//			?>
<!--		</nav><!-- #site-navigation -->
<!--	</header><!-- #masthead -->
 <div class="left-sidebar flexbox box-md-3">
     <div class="logo box-xs-12">
         <div class="site-branding">
             <?php
                the_custom_logo();
            ?>
         </div>
     </div>
     <div class="site-menu">
         <nav id="site-navigation" class="main_navigation flexbox">
            <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e( 'Primary Menu', 'scto' ); ?></button>
            <?php
                wp_nav_menu( array(
                    'theme_location' => 'menu-1',
                    'container_class'=> 'flexbox',
                    'menu_id'        => 'primary_menu',
                    'menu_class'     => 'flexbox start-xs'
                ) );
            ?>
        </nav>
     </div>
 </div>
	<div id="content" class="site-content flexbox box-md-6">
