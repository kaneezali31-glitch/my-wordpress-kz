<?php
/**
 * The template for displaying the header
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Zita
 * @since 1.0.0
 * 
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php endif; ?>
	<?php wp_head(); ?>
</head>
<!-- layout class call -->
<?php 
$zita_default_container    = get_theme_mod('zita_default_container','boxed');
$zita_main_header_layout   = get_theme_mod('zita_main_header_layout','mhdrleft');
$zita_above_header_layout  = get_theme_mod('zita_above_header_layout','abv-two');
$zita_bottom_header_layout = get_theme_mod('zita_bottom_header_layout','abv-two');
// add-pro-feature
$zita_container_site_layout = get_theme_mod('zita_container_site_layout','fullwidth');
?>
<!-- layout class call -->
<body <?php body_class( zita_header_body_classes() ); ?>>
<?php wp_body_open();?>		
<?php if(get_theme_mod('zita_scroll_to_top_disable')==false):?>	
<input type="hidden" id="back-to-top" value="on"/>
<?php endif;?>
<?php if(get_theme_mod('zita_stick_hide_scroll_down')==true):?>	
<input type="hidden" id="header-scroll-down-hide" value="on"/>
<?php endif;?>
<?php zita_preloader();?>
<div id="page" class="zita-site">
<?php do_action( 'zita_before_header' ); ?>
<?php do_action( 'zita_header'); ?> 
<?php do_action( 'zita_after_header' ); ?>
<?php
//Page Header 
if ( isset($post->ID) ) {
$id=$post->ID;
$zita_disable_page_header_dyn = get_post_meta($id, 'zita_disable_page_header_dyn', true );
$zita_page_header_enable = get_theme_mod('zita_page_header_enable',false);

if ($zita_page_header_enable == '1' && $zita_disable_page_header_dyn !=='on') {
zita_page_header_markup($id);
}
}
else{ ?>
	<div class="zita-pageheader">
		<?php zita_page_header_image_for_archive(); ?>
	</div>
<?php }
