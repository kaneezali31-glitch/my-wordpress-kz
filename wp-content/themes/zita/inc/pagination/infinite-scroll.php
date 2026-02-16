<?php 
//**********************/
// Inifinite pagination
//**********************/
function zita_scrolling_ajax(){
global $wp_query;
?>
<div class="infinite-loader"><span class="inifiniteLoader"></span></div>
<div class="scroll-error"></div>

<?php
if(is_category()){
$queried_category = get_term( get_query_var('cat'), 'category' ); 
echo'<a href="#" id="loadMore"  data-page="1" data-url="'.esc_url(admin_url("admin-ajax.php")).'"  data-ppp="'.get_option('posts_per_page').'" data-total="'.esc_attr($wp_query->max_num_pages).'" data-catslug="'.esc_attr($queried_category->term_id).'" ></a>';
}elseif(is_author()){
  $post = get_post( $wp_query->id);
  echo'<a href="#" id="loadMore"  data-page="1" data-url="'.esc_url(admin_url("admin-ajax.php")).'"  data-ppp="'.get_option('posts_per_page').'" data-total="'.esc_attr($wp_query->max_num_pages).'" data-authorid="'.esc_attr($post->post_author).'" ></a>';
}elseif(is_date()){
     echo'<a href="#" id="loadMore"  data-page="1" data-url="'.esc_url(admin_url("admin-ajax.php")).'"  data-ppp="'.get_option('posts_per_page').'" data-total="'.esc_attr($wp_query->max_num_pages).'" data-year="'.esc_attr(get_the_date( 'Y')).'"  data-month="'.esc_attr(get_the_date( 'F')).'"></a>';
}else{
echo'<a href="#" id="loadMore"  data-page="1" data-url="'.esc_url(admin_url("admin-ajax.php")).'"  data-ppp="'.get_option('posts_per_page').'" data-total="'.esc_attr($wp_query->max_num_pages).'" ></a>';
}

}
/*
 * load more script ajax hooks
 */
add_action('wp_ajax_nopriv_zita_ajax_script_load_more', 'zita_ajax_script_load_more');
add_action('wp_ajax_zita_ajax_script_load_more', 'zita_ajax_script_load_more');
/*
 * enqueue js script
 */
add_action( 'wp_enqueue_scripts', 'zita_ajax_enqueue_script' );
/*
 * enqueue js script call back
 */
function zita_ajax_enqueue_script(){
wp_enqueue_script( 'script_ajax', get_parent_theme_file_uri() . '/inc/pagination/js/infinite-scroll.js', array( 'jquery' ), '0.1', true );
wp_localize_script('script_ajax', 'my_ajax_object', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'ajax_nonce' => wp_create_nonce('zita_load_more_nonce'),
    ]);
}

function zita_ajax_script_load_more() {
    check_ajax_referer('zita_load_more_nonce', 'security'); // 🔐 verify nonce

    $offset = isset($_POST["offset"]) ? intval($_POST["offset"]) : 0;
    $paged  = isset($_POST["paged"]) ? intval($_POST["paged"]) : 1;
    $ppp    = isset($_POST["ppp"]) ? intval($_POST["ppp"]) : 10;

    $cat    = isset($_POST["catslug"]) ? sanitize_text_field($_POST["catslug"]) : '';
    $author = isset($_POST["authorid"]) ? intval($_POST["authorid"]) : 0;
    $year   = isset($_POST["yearid"]) ? intval($_POST["yearid"]) : 0;
    $month  = isset($_POST["monthid"]) ? intval($_POST["monthid"]) : 0;

    $args = [
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => $ppp,
        'paged'          => $paged,
    ];

    if ($cat) {
        $args['category_name'] = $cat;
    }
    if ($author) {
        $args['author'] = $author;
    }
    if ($year || $month) {
        $args['date_query'] = [
            [
                'year'  => $year ?: null,
                'month' => $month ?: null,
            ]
        ];
    }

    $loop = new WP_Query($args);

    if ($loop->have_posts()) {
        while ($loop->have_posts()) : $loop->the_post();
            get_template_part('template-parts/content', get_post_format());
        endwhile;
    }

    wp_reset_postdata();
    wp_die(); // use wp_die for ajax
}
add_action('wp_ajax_nopriv_zita_ajax_script_load_more', 'zita_ajax_script_load_more');
add_action('wp_ajax_zita_ajax_script_load_more', 'zita_ajax_script_load_more');
