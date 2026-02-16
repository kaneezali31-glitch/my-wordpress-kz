<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 *
 * @decription List of post-type Page, Post and Custom Post Type (CPT) from admin side.
 * 
 */
function wshs_get_posts_by_type() {

    if (!current_user_can( 'manage_options' ) ) {
        return wp_send_json( array( 'result' => 'Authentication error' ) );
    }

    check_ajax_referer('ajax-nonce', 'security');

    if(isset($_POST['type'])){
        $type = sanitize_text_field(wp_unslash($_POST['type']));
    } else {
        $type = "";
    }

    if(isset($_POST['orderby'])){
        $orderby = sanitize_text_field(wp_unslash($_POST['orderby']));
    } else {
        $orderby = "";
    }

    if(isset($_POST['order'])){
        $order = sanitize_text_field(wp_unslash($_POST['order']));
    } else {
        $order = "";
    }

    if(isset($_POST['dateformate'])){
        $dateformate = sanitize_text_field(wp_unslash($_POST['dateformate']));
    } else {
        $dateformate = "";
    }

    if(isset($_POST['taxonomyslug'])){
        $taxonomyname = sanitize_text_field(wp_unslash($_POST['taxonomyslug']));
    } else {
        $taxonomyname = "";
    }

    if(isset($_POST['termsslug'])){
        $termsname = sanitize_text_field(wp_unslash($_POST['termsslug']));
    } else {
        $termsname = "";
    }

    if(isset($_POST['post_limit'])){
        $post_limit = sanitize_text_field(wp_unslash($_POST['post_limit']));
    } else {
        $post_limit = "";
    }

    if (!post_type_exists($type)) {
        wp_send_json_error('Invalid post type');
    }

    $type = $GLOBALS['wpdb']->prepare('%s', $type);

    if ($termsname != '') {
        /* Taxonomy name & Terms name */
        $taxquery = array(
            array(
                'taxonomy' => $taxonomyname,
                'field' => 'slug',
                'terms' => $termsname,
            ),
        );
    } else {
        /* Taxonomy name */
        $custom_taxonomy = get_terms($taxonomyname);
        foreach ($custom_taxonomy as $customTaxonomy) {
            $taxonomyslug = $customTaxonomy->slug;
            $taxonomyarray[] = $taxonomyslug;
        }
        $taxonomyslugarray = implode(', ', $taxonomyarray);

        if ($taxonomyname != '') {
            $taxquery = array(
                array(
                    'taxonomy' => $taxonomyname,
                    'field' => 'slug',
                    'terms' => explode(', ', $taxonomyslugarray),
                ),
            );
        }
    }

    $args = array(
        'post_type' => $type,
        'post_status' => 'publish',
        'posts_per_page' => $post_limit,
        'orderby' => $orderby,
        'order' => $order,
        'tax_query' => $taxquery, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
        'ignore_custom_sort' => true,
    );
    
    $query = new WP_Query($args);

    $typeallposts = array();
    if ($query->post_count > 0) {
        foreach ($query->posts as $typepost):
            $featureimg = wp_get_attachment_image_src(get_post_thumbnail_id($typepost->ID), 'full');
            $exp = get_the_excerpt($typepost->ID);
            $contentpost = $typepost->post_content;
            $typeallposts[] = array(
                'title' => esc_html($typepost->post_title),
                'ID' => $typepost->ID,
                'post_parent' => $typepost->post_parent,
                'post_date' => date_i18n($dateformate, strtotime($typepost->post_date)),
                'post_excerpt' => esc_html(wshs_truncate_value(wp_strip_all_tags($exp), 100, ' ')),
                'post_content' => esc_html(wshs_truncate_value(wp_strip_all_tags(preg_replace('#\[[^\]]+\]#', '', $contentpost)), 100, ' ')),
                'post_image' => esc_url($featureimg[0]),
            );
        endforeach;
    }
    $typeallposts = wshs_build_tree($typeallposts);
    wp_send_json($typeallposts);
}

add_action('wp_ajax_wshs_get_posts_by_type', 'wshs_get_posts_by_type');

/**
 * 
 * @description List of post-type Taxonomy.
 */
function wshs_get_posts_by_taxonomy() {

    if (!current_user_can( 'manage_options' ) ) {
        return wp_send_json( array( 'result' => 'Authentication error' ) );
    }

    check_ajax_referer('ajax-nonce', 'security');

    if(isset($_POST['type'])){
        $type = sanitize_text_field(wp_unslash($_POST['type']));
    } else {
        $type = "";
    }
    $taxonomies = get_object_taxonomies($type, 'object');    
    $data         = array();
    $data['data'] .= '<option value="">'.esc_html__("Select Taxonomy","wp-simple-html-sitemap").'</option>';
    foreach ($taxonomies as $taxonomy) {
        if ($taxonomy->name != 'post_tag' && $taxonomy->name != 'post_format') {
            $data['data'] .= '<option value="' . esc_attr($taxonomy->name) . '" class="texonomyname">' . esc_html($taxonomy->label) . '</option>';
        }
    }
    wp_send_json($data);
}

add_action('wp_ajax_wshs_get_posts_by_taxonomy', 'wshs_get_posts_by_taxonomy');

/**
 * 
 * @description Particular Taxonomy post list.
 */
function wshs_get_posts_by_taxonomy_post() {
    global $post;
    if (!current_user_can( 'manage_options' ) ) {
        return wp_send_json( array( 'result' => 'Authentication error' ) );
    }

    check_ajax_referer('ajax-nonce', 'security');

    if(isset($_POST['type'])){
        $type = sanitize_text_field(wp_unslash($_POST['type']));
    } else {
        $type = "";
    }

    if(isset($_POST['catslug'])){
        $catslug = sanitize_text_field(wp_unslash($_POST['catslug']));
    } else {
        $catslug = "";
    }

    if(isset($_POST['dateformate'])){
        $dateformate = sanitize_text_field(wp_unslash($_POST['dateformate']));
    } else {
        $dateformate = "";
    }

    if(isset($_POST['orderby'])){
        $orderby = sanitize_text_field(wp_unslash($_POST['orderby']));
    } else {
        $orderby = "";
    }


    if (!taxonomy_exists($catslug)) {
        wp_send_json_error('Invalid post type');
    }
    $taxonomyArray   = array();
    $taxonomySlug    = "";
    $custom_taxonomy = get_terms($catslug);
    foreach ($custom_taxonomy as $customTaxonomy) {
        $taxonomySlug = $customTaxonomy->slug;
        $taxonomyArray[] = $taxonomySlug;
    }
    $taxonomySlugArray = implode(', ', $taxonomyArray);

    if ($catslug != '') {
        $taxquery = array(
            array(
                'taxonomy' => $catslug,
                'field' => 'slug',
                'terms' => explode(', ', $taxonomySlugArray),
            ),
        );
    }

    $args = array
        (
        'post_type' => $type,
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'orderby' => $orderby,
        'order' => "ASC",
        'ignore_custom_sort' => true,
        'tax_query' => $taxquery,  // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
    );

    $loop = new WP_Query($args);
    $typeallposts = array();

    while ($loop->have_posts()) : $loop->the_post();

        $featureimg = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
        $exp = get_the_excerpt($post->ID);
        $contentpost = $post->post_content;
        $typeallposts[] = array
            (
            'title' => esc_html($post->post_title),
            'ID' => $post->ID,
            'post_parent' => $post->post_parent,
            'post_date' => date_i18n($dateformate, strtotime($post->post_date)),
            'post_excerpt' => esc_html(wshs_truncate_value(wp_strip_all_tags($exp), 100, ' ')),
            'post_content' => esc_html(wshs_truncate_value(wp_strip_all_tags(preg_replace('#\[[^\]]+\]#', '', $contentpost)), 100, ' ')),
            'post_image' => esc_url($featureimg[0]),
        );
    endwhile;

    $typeallposts = wshs_build_tree($typeallposts);
    wp_send_json($typeallposts);
}

add_action('wp_ajax_wshs_get_posts_by_taxonomy_post', 'wshs_get_posts_by_taxonomy_post');

/**
 * 
 * @description List of post-type Taxonomy Terms.
 */
function wshs_get_posts_by_taxonomy_terms() {
    if (!current_user_can( 'manage_options' ) ) {
        return wp_send_json( array( 'result' => esc_html__("Authentication error","wp-simple-html-sitemap") ) );
    }

    check_ajax_referer('ajax-nonce', 'security');

    if(isset($_POST['taxonomyname'])){
        $taxonomyName = sanitize_text_field(wp_unslash($_POST['taxonomyname']));
    } else {
        $taxonomyName = "";
    }
    

    // if (!taxonomy_exists($taxonomyname)) {
    //     wp_send_json_error('Invalid taxonomy');
    // }
    $data = array();
    $custom_terms = get_terms($taxonomyName);
    $data['data'] .= '<option value="">'.esc_html__("Select Taxonomy Terms","wp-simple-html-sitemap").'</option>';
    if ($taxonomyName != '') {
        foreach ($custom_terms as $taxonomy) {
            $data['data'] .= '<option value="' . esc_attr($taxonomy->slug) . '">' . esc_html($taxonomy->name) . '</option>';
        }
    }
    wp_send_json($data);
}

add_action('wp_ajax_wshs_get_posts_by_taxonomy_terms', 'wshs_get_posts_by_taxonomy_terms');
//add_action('wp_ajax_nopriv_wshs_get_posts_by_taxonomy_terms', 'wshs_get_posts_by_taxonomy_terms');

/**
 * 
 * @description Particular Taxonomy Terms post list.
 * 
 */
function wshs_get_posts_by_taxonomy_terms_posts() {
    global $post;

    if (!current_user_can( 'manage_options' ) ) {
        return wp_send_json( array( 'result' => esc_html__("Authentication error","wp-simple-html-sitemap") ) );
    }

    check_ajax_referer('ajax-nonce', 'security');

    if(isset($_POST['type'])){
        $type = sanitize_text_field(wp_unslash($_POST['type']));
    } else {
        $type = "";
    }

    if(isset($_POST['taxonomyslug'])){
        $taxonomy_name = sanitize_text_field(wp_unslash($_POST['taxonomyslug']));
    } else {
        $taxonomy_name = "";
    }

    if(isset($_POST['termsslug'])){
        $terms_name = sanitize_text_field(wp_unslash($_POST['termsslug']));
    } else {
        $terms_name = "";
    }

    if(isset($_POST['dateformate'])){
        $dateformate = sanitize_text_field(wp_unslash($_POST['dateformate']));
    } else {
        $dateformate = "";
    }

    if(isset($_POST['orderby'])){
        $orderby = sanitize_text_field(wp_unslash($_POST['orderby']));
    } else {
        $orderby = "";
    }

    if(isset($_POST['order'])){
        $order = sanitize_text_field(wp_unslash($_POST['order']));
    } else {
        $order = "";
    }

    // if (!taxonomy_exists($taxonomyname)) {
    //     wp_send_json_error('Invalid taxonomy');
    // }

    if ($terms_name != '') {
        /* Taxonomy name & Terms name */
        $taxquery = array(
            array(
                'taxonomy' => $taxonomy_name,
                'field' => 'slug',
                'terms' => $terms_name,
            ),
        );
    } else {
        /* Taxonomy name */
        $custom_taxonomy = get_terms($taxonomy_name);
        foreach ($custom_taxonomy as $customtaxonomy) {
            $taxonomyslug = $customtaxonomy->slug;
            $taxonomyarray[] = $taxonomyslug;
        }
        $taxonomyslugarray = implode(', ', $taxonomyarray);

        if ($taxonomy_name != '') {
            /* Taxonomy name */
            $taxquery = array(
                array(
                    'taxonomy' => $taxonomy_name,
                    'field' => 'slug',
                    'terms' => explode(', ', $taxonomyslugarray),
                ),
            );
        }
    }

    $args = array(
        'post_type' => $type,
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'orderby' => esc_html($orderby),
        'order' => esc_html($order),
        'ignore_custom_sort' => true,
        'tax_query' => $taxquery,  // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
    );
    $loop = new WP_Query($args);
    $typeallposts = array();
    
    while ($loop->have_posts()) : $loop->the_post();
    
    $featureimg = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
    $exp = get_the_excerpt($post->ID);
    $contentpost = $post->post_content;
    $typeallposts[] = array(
        'title' => esc_html($post->post_title),
        'ID' => $post->ID,
        'post_parent' => $post->post_parent,
        'post_date' => date_i18n($dateformate, strtotime($post->post_date)),
        'post_excerpt' => esc_html(wshs_truncate_value(wp_strip_all_tags($exp), 100, ' ')),
        'post_content' => esc_html(wshs_truncate_value(wp_strip_all_tags(preg_replace('#\[[^\]]+\]#', '', $contentpost)), 100, ' ')),
        'post_image' => esc_url($featureimg[0]),
    );
endwhile;

// $typeallposts = wshs_build_tree($typeallposts);
    wp_send_json($typeallposts);
}

add_action('wp_ajax_wshs_get_posts_by_taxonomy_terms_posts', 'wshs_get_posts_by_taxonomy_terms_posts');
//add_action('wp_ajax_nopriv_wshs_get_posts_by_taxonomy_terms_posts', 'wshs_get_posts_by_taxonomy_terms_posts');

/**
 * 
 * @param array $elements
 * @param int $parentid
 * @return array
 */
function wshs_build_tree(array $elements, $parentid = 0) {
    
    if (!is_array($elements) || !is_int((int) $parentid)) {
        return array();
    }
    
    $branch = array();
    foreach ($elements as $element) {
        if (!is_object($element)) {
            if ($element['post_parent'] == $parentid) {
                $children = wshs_build_tree($elements, $element['ID']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        } else {
            if ($element->post_parent == $parentid) {
                $children = wshs_build_tree($elements, $element->ID);
                if ($children) {
                    $element->children = $children;
                }
                $branch[] = (object) $element;
            }
        }
    }
    return $branch;
}

/**
 * 
 * @description Set content limit of the string.
 * @param string $string
 * @param int $limit
 * @param string $break
 * @param string $pad
 * @return string
 */
function wshs_truncate_value($string, $limit, $break = ".", $pad = "...") {

    if (!is_string($string) || !is_int($limit) || !is_string($break) || !is_string($pad)) {
        return ''; // or handle error as needed
    }

    /* Return with no change if string is shorter than $limit */
    if (strlen($string) <= $limit) {
        /* remove visual composer shortcode */
        $shotcodes_tags = array('vc_row', 'vc_column', 'vc_column', 'vc_column_text', 'vc_message', 'vc_section');
        $string = preg_replace('/\[(\/?(' . implode('|', $shotcodes_tags) . ').*?(?=\]))\]/', ' ', $string);
        return $string;
    }
    /* Is $break present between $limit and the end of the string? */
    if (false !== ($breakpoint = strpos($string, $break, $limit))) {
        if ($breakpoint < strlen($string) - 1) {
            /* Remove visual composer shortcode */
            $shotcodes_tags = array('vc_row', 'vc_column', 'vc_column', 'vc_column_text', 'vc_message', 'vc_section');
            $string = preg_replace('/\[(\/?(' . implode('|', $shotcodes_tags) . ').*?(?=\]))\]/', ' ', $string);
            $string = substr($string, 0, $breakpoint) . $pad;
        }
    }
    return $string;
}


// Handle AJAX request
function wshs_disable_plugin_styles_ajax() {

	check_ajax_referer( 'wshs_disable_styles', 'security' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die(
			esc_html__( 'You do not have sufficient permissions to access this page.', 'wp-simple-html-sitemap' )
		);
	}

	if ( isset( $_POST['option_value'] ) ) {
		$value = ( 'true' === $_POST['option_value'] ) ? '1' : '0';
		update_option( 'wshs_disable_plugin_styles', $value );
		wp_send_json_success( $value );
	}

	$value = get_option( 'wshs_disable_plugin_styles', '0' );
	wp_send_json_success( $value );
}

add_action( 'wp_ajax_wshs_disable_plugin_styles', 'wshs_disable_plugin_styles_ajax' );
