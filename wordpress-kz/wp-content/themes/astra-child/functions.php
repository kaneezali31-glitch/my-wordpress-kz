<?php
// Enqueue parent and child theme styles
function my_child_theme_enqueue_styles() {
    $parent_style = 'twentytwentyfour-style'; // Replace with your parent theme's unique style identifier
    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );
}
add_action( 'wp_enqueue_scripts', 'my_child_theme_enqueue_styles' );

?>