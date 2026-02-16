<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @package Zita
 * @since 1.0.0
 */ 

?>
<?php do_action( 'zita_before_footer' ); ?>
<!-- Main Footer (rendered via hook) -->
<?php do_action( 'zita_footer' ); ?> <!-- Footer content injected via hook -->
<?php do_action( 'zita_after_footer' ); ?>
<?php wp_footer(); ?>
</body>
</html>