<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * 
 * @param type $atts
 * 
 */
function wshs_front_display_list($atts) {

    if (get_option('wshs_disable_plugin_styles') == '0') {
        wp_enqueue_style('wshs_front_css', WSHS_PLUGIN_CSS . 'wshs_front_style.css',array(),filemtime(WSHS_PLUGIN_CSS . 'wshs_front_style.css'), false);
    }
    if (get_option('wshs_disable_plugin_styles') == '1') {
        wp_dequeue_style( 'wshs_front_css' );
    }

    
    $errors = array();

    // Sanitize attributes
    $atts = array_map('sanitize_text_field', $atts);


    global $wshs_taxquery;

    $atts = shortcode_atts(array(
        'taxonomy' => false,
        'terms' => false,
        'post_type' => 'page',
        'order_by' => 'date',
        'exclude' => '', // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
        'show_image' => false,
        'image_width' => 60,
        'image_height' => 60,
        'content_limit' => false,
        'child_of' => 0,
        'order' => "asc",
        'horizontal' => false,
        'separator' => '|',
        'show_date' => false,
        'date' => 'created',
        'date_format' => 'F j, Y',
        'post_limit' => -1,
        'page_limit' => 0,
        'depth' => -1,
        'layout' => '',
        'position' => '',
        'name' => false,
        'custom_attribute' => '',
        'new_feature' => false,
        ), $atts, 'wshs_list');

    // Whitelist and validate 'order'
    $allowed_orders = array('asc', 'desc');
    $atts['order'] = in_array(strtolower($atts['order']), $allowed_orders) ? strtolower($atts['order']) : 'asc';

    // Whitelist and validate 'orderby'
    $allowed_orderby = array('date', 'title', 'ID', 'author', 'comment_count');
    $atts['order_by'] = in_array(strtolower($atts['order_by']), $allowed_orderby) ? strtolower($atts['order_by']) : 'date';

    // Validate 'post_type'
    if (!post_type_exists($atts['post_type'])) {
        $atts['post_type'] = 'post'; // Revert to a safe default
    }

    // Sanitize 'post_limit' to ensure it is an integer
    $atts['post_limit'] = intval($atts['post_limit']);

    // Sanitize and validate 'exclude'
    $excludePosts = array_map('intval', explode(',', $atts['exclude']));
    $excludePosts = array_filter($excludePosts);


    //$excludePosts   = preg_split('/\s*,\s*/', $atts['exclude'], -1, PREG_SPLIT_NO_EMPTY);
    $customtaxonomy = get_terms($atts['taxonomy']);
    $taxonomyarray = array();

    if ($atts['taxonomy'] == true) {
        foreach ($customtaxonomy as $customtaxonomys) {
            $taxonomyslug = $customtaxonomys->slug;
            $taxonomyarray[] = $taxonomyslug;
        }
    }
    $taxonomyslugarray = implode(', ', $taxonomyarray);

    /* post-type Taxonomy */
    if ($atts['taxonomy'] == true) {
        $wshs_taxquery = array(
            array(
                'taxonomy' => $atts['taxonomy'],
                'field' => 'slug',
                'terms' => explode(', ', $taxonomyslugarray)
            )
        );
    }

    /* post-type Taxonomy Terms */
    if ($atts['taxonomy'] == true && $atts['terms'] == true) {
        $wshs_taxquery = array(
            array(
                'taxonomy' => $atts['taxonomy'],
                'field' => 'slug',
                'terms' => $atts['terms']
            )
        );
    }

    /* Pass argument form post-type post limit, child-of, order by, order, exclude posts, and tax-query. */
    $wshsargs = array(
        'post_type' => $atts['post_type'],
        'posts_per_page' => $atts['post_limit'],
        'post__not_in' => $excludePosts, // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in
        'orderby' => $atts['order_by'],
        'order' => $atts['order'],
        'tax_query' => $wshs_taxquery,  // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
    );
    
    $treeparent = $atts['child_of'];
    if (!in_array($atts['child_of'], $excludePosts)) {
        $wshsargs['wshs_include_parent'] = true;
        $treeparent = $treeparent;
        //$treeparent = 0;
    }
    
    $wshsfrontquery = new WP_Query($wshsargs);
    $typeallposts   = array();
    if ($atts['post_type'] != 'page') {
        if ($wshsfrontquery->post_count > 0) {
            foreach ($wshsfrontquery->posts as $typepost):
                $typeallposts[] = (object) $typepost;
            endforeach;
        }
    }
    
    /* Pass argument form post-type page limit, child-of, order by, order and exclude pages. */
    $postcount = $wshsfrontquery->post_count;
    $wshsargss = array(
        'post_type' => $atts['post_type'],
        'number' => $atts['page_limit'],
        'sort_column' => $atts['order_by'],
        'sort_order' => $atts['order'],
        'exclude' => $excludePosts, // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
        'child_of' => $atts['child_of'],
        
    );

   
    $wshsfrontquerys = get_pages($wshsargss);
    if ($atts['post_type'] == 'page') {
        foreach ($wshsfrontquerys as $typePage):
            $typeallposts[] = (object) $typePage;
        endforeach;
    }

    if ($atts['post_type'] == 'page') {
        $allposts = wshs_build_tree((array)$typeallposts, $treeparent);
    } else {
        $allposts = $typeallposts;
    }

    if ($atts['child_of'] != '') {
        $depth = $atts['depth'];
    } else {
        $depth = $atts['depth'] + 1;
    }

    $columnlayout = $atts['layout'];
    $columnposition = $atts['position'];
    $title = $atts['name'];
    $showimage = $atts['show_image'];
    $imagewidth = $atts['image_width'];
    $imageheight = $atts['image_height'];
    $startdate = $atts['date'];
    $showdate = $atts['show_date'];
    $dateformat = $atts['date_format'];
    $excerptlimit = $atts['content_limit'];
    $separator = $atts['separator'];

    if ($atts['horizontal'] == true) {
        return wshs_simple_list_view_horizontal($allposts, 1, $separator, $title, $columnlayout, $columnposition);
    } elseif ($atts['show_image'] == true || $atts['show_date'] == true || $atts['content_limit'] == true) {
        return wshs_simple_list_view_image($allposts, 1, $showimage, $imagewidth, $imageheight, $excerptlimit, $startdate, $showdate, $dateformat, $title, $depth, $columnlayout, $columnposition);
    } else {
        return wshs_simple_list_view($allposts, 1, $startdate, $showdate, $dateformat, $title, $depth, $columnlayout, $columnposition);
    }
}
add_shortcode('wshs_list', 'wshs_front_display_list');

add_filter('posts_where', function ($where, \WP_Query $q) use (&$wpdb) {
    global $wpdb;    
    if (true !== $q->get('wshs_include_parent')):
        return $where;
    endif;

    $postparent = filter_var($q->get('post_parent'), FILTER_VALIDATE_INT);
    if (!$postparent):
        return $where;
    endif;

    $where .= $wpdb->prepare(" OR $wpdb->posts.ID = %d", $postparent);

    return $where;
}, 10, 2);

/**
 * 
 * @description Display a simple list view in the sitemap.
 * @param array $allposts
 * @param int $level
 * @param date $startdate
 * @param boolean $showdate
 * @param string $dateformat
 * @param string $title
 * @param int $depth
 * @param string $columnlayout
 * @param string $columnposition
 * @return array
 */
function wshs_simple_list_view($allposts, $level, $startdate, $showdate, $dateformat, $title, $depth, $columnlayout, $columnposition) {
    $columnhalf = '';
    $date = '';
    if ($columnlayout == 'single-column') {
        $columnclass = 'full-layout';
    } elseif ($columnlayout == 'two-columns') {
        $columnclass = 'half-layout';
        if ($columnposition == 'left') {
            $columnhalf = 'left';
        } elseif ($columnposition == 'right') {
            $columnhalf = 'right';
        } else {
            $columnhalf = '';
        }
    } else {
        $columnclass = '';
    }
    if ($title != '') {
        $titles = '<h2>' . esc_html(ucfirst($title),'wp-simple-html-sitemap') . '</h2>';
    } else {
        $titles = '';
    }
    $returndata = ($level == 1) ? '<div class="wshs-post-simple-list ' . $columnclass . ' ' . $columnhalf . '">' . $titles . '<ul>' : '';
    if(count($allposts) > 0){
    foreach ($allposts as $singlepost) {
        if ($showdate == true) {
            if ($startdate == 'created') {
                $date = mysql2date($dateformat, $singlepost->post_date);
            } else {
                $date = '';
            }
        }
        if ($level <= $depth) {
            //$returndata .= '<li><a href="' . get_permalink($singlepost->ID) . '" title="' . $singlepost->post_title . '">' . $singlepost->post_title . '</a>' . $date;
            $returndata .= '<li><a href="' . esc_url(get_permalink($singlepost->ID)) . '" title="' . esc_attr($singlepost->post_title) . '">' . esc_html($singlepost->post_title) . '</a>' . esc_html($date);

            if (isset($singlepost->children)) {
                if ($depth == $level) {
                    $hidedepthul = "style='display:none'";
                } else {
                    $hidedepthul = "";
                }
                $returndata .= '<ul class="children" ' . $hidedepthul . '>';
                $returndata .= wshs_simple_list_view($singlepost->children, $level + 1, $startdate, $showdate, $dateformat, $title, $depth, $columnlayout, $columnposition);
                $returndata .= '</ul>';
            }
            $returndata .= '</li>';
        } elseif ($depth == -1) {
            $returndata .= '<li><a href="' . get_permalink($singlepost->ID) . '" title="' . $singlepost->post_title . '">' . $singlepost->post_title . '</a>' . $date;
            if (isset($singlepost->children)) {
                $returndata .= '<ul class="children">';
                $returndata .= wshs_simple_list_view($singlepost->children, $level + 1, $startdate, $showdate, $dateformat, $title, $depth = -1, $columnlayout, $columnposition);
                $returndata .= '</ul>';
            }
            $returndata .= '</li>';
        } elseif ($depth == '') {
            $returndata .= '<li><a href="' . get_permalink($singlepost->ID) . '" title="' . $singlepost->post_title . '">' . $singlepost->post_title . '</a>' . $date;
            if (isset($singlepost->children)) {
                $returndata .= '<ul class="children">';
                $returndata .= wshs_simple_list_view($singlepost->children, $level + 1, $startdate, $showdate, $dateformat, $title, $depth = -1, $columnlayout, $columnposition);
                $returndata .= '</ul>';
            }
            $returndata .= '</li>';
        }
    }
	}else{
		$returndata .= "<p><strong>".__('There are currently no posts available for the selected post type. Please consider choosing a different post type to find content.','wp-simple-html-sitemap').'</strong></p>';
	}
    return $returndata .= ($level == 1) ? '</ul></div>' : '';
}

/**
 * 
 * @description Display Featured image, Excerpt and Post create date in the sitemap.
 * @param array $allposts
 * @param int $level
 * @param boolean $showimage
 * @param int $imagewidth
 * @param int $imageheight
 * @param int $excerptlimit
 * @param date $startdate
 * @param boolean $showdate
 * @param string $dateformat
 * @param string $title
 * @param int $depth
 * @param string $columnlayout
 * @param string $columnposition
 * @return array
 */
function wshs_simple_list_view_image($allposts, $level, $showimage, $imagewidth, $imageheight, $excerptlimit, $startdate, $showdate, $dateformat, $title, $depth, $columnlayout, $columnposition) {
    $columnhalf = '';
    $date = '';

    // Validate and sanitize inputs
    $imagewidth = absint($imagewidth);
    $imageheight = absint($imageheight);
    $excerptlimit = (bool) $excerptlimit;
    
    // Escape output to prevent XSS
    $liststyleclass = ($showimage == true) ? esc_attr("list-style wshs-image-listing") : "";
    $columnclass = in_array($columnlayout, array('single-column', 'two-columns')) ? esc_attr($columnlayout) : '';
    $columnhalf = ($columnlayout == 'two-columns') ? esc_attr($columnposition) : '';
    $titles = ($title != '') ? '<h2>' . esc_html(ucfirst($title)) . '</h2>' : '';

    $returndata = ($level == 1) ? '<div class="wshs-post-simple-list '. $liststyleclass . ' ' . $columnclass . ' ' . $columnhalf . '">' . $titles . '<ul>' : '';
    if(count($allposts) > 0){
    foreach ($allposts as $singlepost) {
        $featureimg = wp_get_attachment_image_src(get_post_thumbnail_id($singlepost->ID), 'full');
        if ($showdate == true) {
            if ($startdate == 'created') {
                $date = date_i18n($dateformat, strtotime($singlepost->post_date));
            } else {
                $date = '';
            }
        }
        if ($depth != '' && $level <= $depth) {
            $returndata .= '<li>';
            if ($showimage == true) {
                if (!empty($featureimg) && is_array($featureimg)) {
                    $returndata .= '<img src="' . esc_url($featureimg[0]) . '" width="' . $imagewidth . '" height="' . $imageheight . '" alt="' . esc_attr($singlepost->post_title) . '">';
                } else {
                    $returndata .= '<img src="' . esc_url(plugin_dir_url('').'wp-simple-html-sitemap/images/placeholder.svg') . '" width="' . $imagewidth . '" height="' . $imageheight . '" alt="' . esc_attr($singlepost->post_title) . '">';
                }
            }
            $returndata .= '<a href="' . esc_url(get_permalink($singlepost->ID)) . '" title="' . esc_attr($singlepost->post_title) . '">' . esc_html($singlepost->post_title) . '</a>';
            if ($excerptlimit == true) {
                $excerpt = get_the_excerpt($singlepost->ID);
                $excerpt = wp_strip_all_tags($excerpt);
                $excerpt = wshs_truncate_value($excerpt, $excerptlimit, ' ');
                $returndata .= '<p>' . esc_html($excerpt) . '</p>';
            }
            if ($date != '') {
                $returndata .= '<p><small><i>' . esc_html($date) . '</i></small></p>';
            }
            if (isset($singlepost->children)) {
                if ($depth == $level) {
                    $hidedepthul = "style='display:none'";
                } else {
                    $hidedepthul = "";
                }
                $returndata .= '<ul class="children" ' . $hidedepthul . '>';
                $returndata .= wshs_simple_list_view_image($singlepost->children, $level + 1, $showimage, $imagewidth, $imageheight, $excerptlimit, $startdate, $showdate, $dateformat, $title, $depth, $columnlayout, $columnposition);
                $returndata .= '</ul>';
            }
            $returndata .= '</li>';
        } elseif ($depth == -1 || $depth == '') {
            $returndata .= '<li>';
            if ($showimage == true) {
                if (!empty($featureimg) && is_array($featureimg)) {
                    $returndata .= '<img src="' . esc_url($featureimg[0]) . '" width="' . $imagewidth . '" height="' . $imageheight . '" alt="' . esc_attr($singlepost->post_title) . '">';
                } else {
                    $returndata .= '<img src="' . esc_url(plugin_dir_url('').'wp-simple-html-sitemap/images/placeholder.svg') . '" width="' . $imagewidth . '" height="' . $imageheight . '" alt="' . esc_attr($singlepost->post_title) . '">';
                }
            }
            $returndata .= '<a href="' . esc_url(get_permalink($singlepost->ID)) . '" title="' . esc_attr($singlepost->post_title) . '">' . esc_html($singlepost->post_title) . '</a>';
            if ($excerptlimit == true) {
                $excerpt = get_the_excerpt($singlepost->ID);
                $excerpt = wp_strip_all_tags($excerpt);
                $excerpt = wshs_truncate_value($excerpt, $excerptlimit, ' ');
                $returndata .= '<p>' . esc_html($excerpt) . '</p>';
            }
            if ($date != '') {
                $returndata .= '<p><small><i>' . esc_html($date) . '</i></small></p>';
            }
            if (isset($singlepost->children)) {
                $returndata .= '<ul class="children">';
                $returndata .= wshs_simple_list_view_image($singlepost->children, $level + 1, $showimage, $imagewidth, $imageheight, $excerptlimit, $startdate, $showdate, $dateformat, $title, $depth, $columnlayout, $columnposition);
                $returndata .= '</ul>';
            }
            $returndata .= '</li>';
        }
    }
	}else{
        $returndata .= "<p><strong>".__('There are currently no posts available for the selected post type. Please consider choosing a different post type to find content.','wp-simple-html-sitemap').'</strong></p>';
	}
    return $returndata .= ($level == 1) ? '</ul></div>' : '';
}

/**
 * 
 * @description Display Horizontal view in the sitemap.
 * @param array $allposts
 * @param int $level
 * @param type $separator
 * @param string $title
 * @param string $columnlayout
 * @param string $columnposition
 * @return array
 */

function wshs_simple_list_view_horizontal($allposts, $level, $separator, $title, $columnlayout, $columnposition) {
    $columnhalf = '';
    $separator = sanitize_text_field($separator);

    // Escape output to prevent XSS
    $columnclass = in_array($columnlayout, array('single-column', 'two-columns')) ? esc_attr($columnlayout) : '';
    $columnhalf = ($columnlayout == 'two-columns') ? esc_attr($columnposition) : '';
    $titles = ($title != '') ? '<h2>' . esc_html(ucfirst($title)) . '</h2>' : '';

    $returndata = ($level == 1) ? '<div class="horizontal-view ' . $columnclass . ' ' . $columnhalf . '">' . $titles . '<ul>' : '';
    if(count($allposts) > 0){
		foreach ($allposts as $singlepost) {
			$returndata .= '<li>';
			$returndata .= '<a href="' . esc_url(get_permalink($singlepost->ID)) . '" title="' . esc_attr($singlepost->post_title) . '">' . esc_html($singlepost->post_title) . '</a>';
			$returndata .= '</li><li>' . esc_html($separator) . '</li>';
			if (isset($singlepost->children)) {
				$returndata .= wshs_simple_list_view_horizontal($singlepost->children, $level + 1, $separator, $title, $columnlayout, $columnposition);
			}
		}
    }else{
                 
        $returndata .= "<p><strong>".__('There are currently no posts available for the selected post type. Please consider choosing a different post type to find content.','wp-simple-html-sitemap').'</strong></p>';
	}
    return $returndata .= ($level == 1) ? '</ul></div>' : '';
}
