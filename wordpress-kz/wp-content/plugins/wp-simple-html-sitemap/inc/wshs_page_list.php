<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if (!function_exists('wshs_page_list')) {

    function wshs_page_list() {
        global $wpdb;
        $atts = array();
        $default_title = 'Page sitemap';

        $id = 0;
        // Sanitize and validate input
        // phpcs:disable WordPress.Security.NonceVerification.Recommended -- only reading GET params to fetch records
        if(isset($_GET['id'])){ 
            if(sanitize_text_field( wp_unslash($_GET['id'])) != ""){
                $id = sanitize_text_field( wp_unslash($_GET['id']));
            }
        }
        // phpcs:enable WordPress.Security.NonceVerification.Recommended

        if($id != 0) {

            $id = intval( $id);
            
            // Using $wpdb directly because this is a custom table without a core API.
            // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            // phpcs:disable WordPress.DB.DirectDatabaseQuery
            $table = esc_sql($wpdb->prefix . 'wshs_saved_code');
            $cache_key = 'saved_code_row_' . $id;
            $row = wp_cache_get( $cache_key, 'wshs' );
            if ( false === $row ) {               
                
                $row = $wpdb->get_row(
                    $wpdb->prepare(
                        "SELECT * FROM {$table} WHERE id = %d",
                        $id
                    )
                );

                // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                // phpcs:enable WordPress.DB.DirectDatabaseQuery

                wp_cache_set( $cache_key, $row, 'wshs', 3600 ); // Cache for 1 hour
            }

            if($row){
                $default_title = $row->title;
                $atts = shortcode_parse_atts($row->attributes);
            }
        }
        $options = get_option('wshs_disable_plugin_styles');
        ?>
        <script> var existing_atts = <?php echo wp_json_encode($atts); ?>; </script>
        <div class="wrap wtl-main">
            <h1 class="wp-heading-inline"><?php echo esc_html(get_admin_page_title()); ?></h1>
            <hr class="wp-header-end">
            <div id="post-body" class="metabox-holder columns-3">
                <!-- Top Navigation -->
                <div class="sitemap-wordpress">
                    <h2 class="nav-tab-wrapper">
                        <a href="?page=wshs_page_list" class="nav-tab nav-tab-active"><?php echo esc_html__("Pages","wp-simple-html-sitemap"); ?></a>
                        <a href="?page=wshs_post_list" class="nav-tab "><?php echo esc_html__("Posts","wp-simple-html-sitemap"); ?></a>
                        <a href="?page=wshs_saved" class="nav-tab "><?php echo esc_html__("Saved Shortcodes","wp-simple-html-sitemap"); ?></a>
                        <a href="?page=wshs_documentation" class="nav-tab"><?php echo esc_html__("Documentation","wp-simple-html-sitemap"); ?></a>
                    </h2>
                    <div class="sitemap-pages">
                        <div class="shortcode-container">
                            <!-- Get all registered post types -->
                            <?php $allposttypes = get_post_types(array('show_in_nav_menus' => 1)); ?>
                            <!-- Admin page sitemap -->
                            <div class="admin-field-section admin-page">
                                <fieldset>
                                    <label><?php echo esc_html("1. Select page","wp-simple-html-sitemap"); ?></label>
                                    <select name="wshs_select_type" class="wshs_select_type_page wshs-field" id="wshs_select_type">
                                        <option value=""><?php echo esc_html("Select page","wp-simple-html-sitemap"); ?></option>
                                        <?php
                                        $postsarray = array();
                                        foreach ($allposttypes as $slug => $title) :
                                            $postdetails = get_post_type_object($slug);
                                            $postsarray[$slug] = array('title' => $postdetails->label, 'hierarchical' => $postdetails->hierarchical);
                                            if ($title == 'page') :
                                                ?>
                                                <option data-title="Page Sitemap" value="<?php echo esc_attr($slug); ?>"><?php echo esc_html($postdetails->label); ?></option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                </fieldset>
                                <fieldset>
                                    <label><?php echo esc_html("2. Order by","wp-simple-html-sitemap"); ?></label>
                                    <select name="wshs_select_order" class="wshs-field" id="wshs_select_order_page" disabled>
                                        <option value="date"><?php echo esc_html("Date","wp-simple-html-sitemap"); ?></option>
                                        <option value="title"><?php echo esc_html("Title","wp-simple-html-sitemap"); ?></option>
                                        <option value="menu_order"><?php echo esc_html("Menu Order","wp-simple-html-sitemap"); ?></option>
                                    </select>
                                </fieldset>
                                <fieldset>
                                    <label><?php echo esc_html("3. Order","wp-simple-html-sitemap"); ?></label>
                                    <select name="wshs_select_order_asc" class="wshs-field" id="wshs_select_order_asc_page" disabled>
                                        <option value="asc"><?php echo esc_html("ASC","wp-simple-html-sitemap"); ?></option>
                                        <option value="desc">D<?php echo esc_html("ESC","wp-simple-html-sitemap"); ?></option>
                                    </select>
                                </fieldset>
                                <fieldset>
                                    <label><?php echo esc_html("4. Display only childs of","wp-simple-html-sitemap"); ?></label>
                                    <select name="wshs_select_parent" class="wshs-field" id="wshs_select_parent" disabled>
                                        <option value=""><?php echo esc_html("Select parent","wp-simple-html-sitemap"); ?></option>
                                    </select>
                                </fieldset>
                                <fieldset>
                                    <label><?php echo esc_html("5. Display depth","wp-simple-html-sitemap"); ?></label>
                                    <select name="wshs_select_depth" class="wshs_select_depth wshs-field" id="wshs_select_depth" disabled>
                                        <option value=""><?php echo esc_html("Select depth","wp-simple-html-sitemap"); ?></option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8">8</option>
                                        <option value="9">9</option>
                                        <option value="10">10</option>
                                    </select>
                                </fieldset>
                                <fieldset>
                                    <label><?php echo esc_html("6. Column layout","wp-simple-html-sitemap"); ?></label>
                                    <select name="wshs_select_column_page" class="wshs_select_column_page wshs-field" id="wshs_select_column_page" disabled>
                                        <option value="" data-title=""><?php echo esc_html("Select column","wp-simple-html-sitemap"); ?></option>
                                        <option data-title="single-column" value="full"><?php echo esc_html("Single column","wp-simple-html-sitemap"); ?></option>
                                        <option data-title="two-columns" value="half"><?php echo esc_html("Two columns","wp-simple-html-sitemap"); ?></option>
                                    </select>
                                </fieldset>
                                <fieldset class="position-page">
                                    <label><?php echo esc_html("Column position","wp-simple-html-sitemap"); ?></label>
                                    <select name="wshs_select_column_position_page" class="wshs_select_column_position_page wshs-field" id="wshs_select_column_position_page">
                                        <option value=""><?php echo esc_html("Select position","wp-simple-html-sitemap"); ?></option>
                                        <option value="left"><?php echo esc_html("Left","wp-simple-html-sitemap"); ?></option>
                                        <option value="right"><?php echo esc_html("Right","wp-simple-html-sitemap"); ?></option>
                                    </select>
                                </fieldset>
                                <fieldset class="field-checkbox">
                                    <label><?php echo esc_html("6. Display image","wp-simple-html-sitemap"); ?> </label>
                                    <input type="checkbox" name="wshs_display_image" id="wshs_display_image_page" disabled>
                                    <div class="wshs_image_size_page">
                                        <input type="number" min="60" name="wshs_image_width_page" placeholder="Width"> X <input placeholder="Height" type="number" min="60" name="wshs_image_height_page">
                                    </div>
                                </fieldset>
                                <fieldset>
                                    <label><?php echo esc_html("7. Display excerpt","wp-simple-html-sitemap"); ?> </label>
                                    <input type="checkbox" name="wshs_display_excerpt_page" id="wshs_display_excerpt_page" disabled>
                                    <div class="wshs_excerpt_limit_page">
                                        <input min="10" type="number" name="wshs_excerpt_length_page" value="100">
                                    </div>
                                </fieldset>
                                <fieldset>
                                    <label><?php echo esc_html("8. Show date","wp-simple-html-sitemap"); ?> </label>
                                    <input type="checkbox" name="wshs_display_date_page" id="wshs_display_date_page" disabled>
                                    <div class="wshs_show_date_view_page">
                                        <select name="wshs_show_date_format_page" class="wshs-field-select" id="wshs_show_date_format_page">
                                            <option value="F j, Y">November 6, 2010</option>
                                            <option value="F, Y">November, 2010</option>
                                            <option value="l, F jS, Y">Saturday, November 6th, 2010</option>
                                            <option value="F j, Y g:i a">November 6, 2010 12:50 am</option>
                                            <option value="M j, Y">Nov 6, 2010</option>
                                        </select>
                                    </div>
                                </fieldset>
                                <fieldset>
                                    <label><?php echo esc_html("9. Limit page","wp-simple-html-sitemap"); ?> </label>
                                    <input type="checkbox" name="wshs_display_limit_page" id="wshs_display_limit_page" disabled>
                                    <div class="wshs_post_limit_page">
                                        <input min="1" type = "number" name="wshs_post_limit_page" value = "10">
                                    </div>
                                </fieldset>
                                <div class="loading-sitemap">
                                    <img src="<?php echo esc_url(WSHS_PLUGIN_URL . '/images/loader.gif'); ?>">
                                </div>
                                  <div class="disable_style">
                                    <label>Disable plugin style </label>
                                    <input type="checkbox" name="disable_plugin_styles" id="disable_plugin_styles" value="1" >
                                </div>

                            </div>
                            <div class="short-code-main">
                                <div id="wshs_shortcode"></div>
                                <div class="short-code-action">
                                    <input type="text" id="wshs_code_title" name="wshs_code_title" value="<?php echo esc_attr(htmlspecialchars($default_title)); ?>">
                                    <a href="javascript:void(0);" class="short-code-save-btn button" data-type="page" data-id="<?php echo esc_attr($get_id); ?>">Save</a>
                                    <a href="javascript:void(0);" class="short-code-copy-btn button"><?php echo esc_html("Copy","wp-simple-html-sitemap"); ?></a>
                                </div>
                            </div>
                            <div id="wshs_admin_post_list">
                                <div class="sitemap-exclude-post"><?php echo esc_html("Exclude","wp-simple-html-sitemap"); ?></div>
                                <ul></ul>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <!-- Sidebar Advertisement -->
                    <?php require_once WSHS_PLUGIN_PATH . '/inc/wshs_sidebar.php'; ?>
                    <!-- Sidebar Advertisement -->
                </div>
            </div>
        </div>
        <?php
    }
}
