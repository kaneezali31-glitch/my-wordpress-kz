<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
if( ! class_exists( 'WP_Customize_Control' ) ){
	return;
}
add_action( 'customize_preview_init', 'zita_focus_section_enqueue');
add_action( 'customize_controls_init', 'zita_focus_section_helper_script_enqueue' );
function zita_focus_section_enqueue(){
	   wp_enqueue_style( 'zita-focus-section-style',ZITA_THEME_URI . 'customizer/customize-focus-section/css/focus-section.css');
		wp_enqueue_script( 'zita-focus-section-script',ZITA_THEME_URI . 'customizer/customize-focus-section/js/focus-section.js', array('jquery'),'',false);
	}
function zita_focus_section_helper_script_enqueue(){
		wp_enqueue_script( 'zita-focus-section-addon-script', ZITA_THEME_URI . 'customizer/customize-focus-section/js/addon-focus-section.js', array('jquery'),'',false);
	}

