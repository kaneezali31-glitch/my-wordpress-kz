<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if (!function_exists('wshs_documentation')) {

    function wshs_documentation() {
        wp_enqueue_style('wshs_front_css', WSHS_PLUGIN_CSS . 'wshs_style.css',array(),filemtime(WSHS_PLUGIN_CSS . 'wshs_style.css'), false);
        wp_enqueue_style('wshs_fancybox_css', WSHS_PLUGIN_CSS . 'jquery.fancybox.css',array(),filemtime(WSHS_PLUGIN_CSS . 'jquery.fancybox.css'), false);
        wp_enqueue_script('wshs_fancybox_js', WSHS_PLUGIN_JS . 'jquery.fancybox.min.js',array(),filemtime(WSHS_PLUGIN_JS . 'jquery.fancybox.min.js'), true);
        ?>
        <div class="wrap wtl-main">
            <h1 class="wp-heading-inline"><?php echo esc_html("Simple HTML Sitemap","wp-simple-html-sitemap"); ?></h1>
            <hr class="wp-header-end">
            <div id="post-body" class="metabox-holder columns-3">
                <!-- Top Navigation -->
                <div class="sitemap-wordpress">
                    <!-- Pages sitemap -->
                    <h2 class="nav-tab-wrapper">
                        <a href="?page=wshs_page_list" class="nav-tab"><?php echo esc_html("Pages","wp-simple-html-sitemap"); ?></a>
                        <a href="?page=wshs_post_list" class="nav-tab"><?php echo esc_html("Posts","wp-simple-html-sitemap"); ?></a>
                        <a href="?page=wshs_saved" class="nav-tab "><?php echo esc_html("Saved Shortcodes","wp-simple-html-sitemap"); ?></a>
                        <a href="?page=wshs_documentation" class="nav-tab nav-tab-active"><?php echo esc_html("Documentation","wp-simple-html-sitemap"); ?></a>
                    </h2>
                    <div class="sitemap-pages">
                        <div id="postbox-container-1" class="postbox-container">
                            <div class="row column-layout">
                                <div class="col">
                                    <a class="fancybox" href="<?php echo esc_url(WSHS_PLUGIN_URL."/images/page-sitemap.jpg") ?>" data-fancybox="images" title="Page sitemap" data-caption='[wshs_list post_type="page" name="Page Sitemap" order_by="date"]'>
                                        <img src="<?php echo esc_url(WSHS_PLUGIN_URL."/images/page-sitemap.jpg") ?>" alt="Page sitemap" />
                                    </a>
                                    <h2><?php echo esc_html("Page Sitemap","wp-simple-html-sitemap"); ?></h2>
                                </div>
                                <div class="col">
                                    <a class="fancybox" href="<?php echo esc_url(WSHS_PLUGIN_URL."/images/post-sitemap.jpg") ?>" data-fancybox="images" title="Post sitemap" data-caption='[wshs_list post_type="post" name="Post Sitemap" order_by="date"]'>
                                        <img src="<?php echo esc_url(WSHS_PLUGIN_URL."/images/post-sitemap.jpg") ?>" alt="Post sitemap" />
                                    </a>
                                    <h2><?php echo esc_html("Post Sitemap","wp-simple-html-sitemap"); ?></h2>
                                </div>
                                <div class="col">
                                    <a class="fancybox" href="<?php echo esc_url(WSHS_PLUGIN_URL."/images/horizontal-sitemap.jpg") ?>" data-fancybox="images" title="Horizontal view sitemap" data-caption='[wshs_list post_type="page" name="Page Sitemap" order_by="date" horizontal="true" separator="|"]'>
                                        <img src="<?php echo esc_url(WSHS_PLUGIN_URL."/images/horizontal-sitemap.jpg") ?>" alt="Horizontal view sitemap" />
                                    </a>
                                    <h2><?php echo esc_html("Sitemap With Horizontal View","wp-simple-html-sitemap"); ?></h2>
                                </div>
                                <div class="col">
                                    <a class="fancybox" href="<?php echo esc_url(WSHS_PLUGIN_URL."/images/column-sitemap.jpg") ?>" data-fancybox="images" title="Column layout sitemap" data-caption='[wshs_list post_type="page" name="Page Sitemap" order_by="date" layout="Two columns" position="left"]<br /> [wshs_list post_type="post" name="Post Sitemap" order_by="date" layout="Two columns" position="right"]'>
                                        <img src="<?php echo esc_url(WSHS_PLUGIN_URL."/images/column-sitemap.jpg") ?>" alt="Column layout sitemap" />
                                    </a>
                                    <h2><?php echo esc_html("Two Column Layout Sitemap","wp-simple-html-sitemap"); ?></h2>
                                </div>
                                <div class="col">
                                    <a class="fancybox" href="<?php echo esc_url(WSHS_PLUGIN_URL."/images/page-with-image.jpg") ?>" data-fancybox="images" title="Page sitemap with image, excerpt and date" data-caption='[wshs_list post_type="page" name="Page Sitemap" order_by="date" show_image="true" image_width="60" image_height="60" content_limit="100"  show_date="true" date_format="F j, Y"]'>
                                        <img src="<?php echo esc_url(WSHS_PLUGIN_URL."/images/page-with-image.jpg") ?>" alt="Page sitemap with image, excerpt and date" />
                                    </a>
                                    <h2><?php echo esc_html("Page Sitemap With Image, Excerpt And Date","wp-simple-html-sitemap"); ?></h2>
                                </div>
                                <div class="col">
                                    <a class="fancybox" href="<?php echo esc_url(WSHS_PLUGIN_URL."/images/post-with-image.jpg") ?>" data-fancybox="images" title="Post sitemap with image, excerpt and date" data-caption='[wshs_list post_type="post" name="Post Sitemap" order_by="date" show_image="true" image_width="60" image_height="60" content_limit="100"  show_date="true" date_format="F j, Y"]'>
                                        <img src="<?php echo esc_url(WSHS_PLUGIN_URL."/images/post-with-image.jpg") ?>" alt="Post sitemap with image, excerpt and date" />
                                    </a>
                                    <h2><?php echo esc_html("Post Sitemap With Image, Excerpt And Date","wp-simple-html-sitemap"); ?></h2>
                                </div>

                                <div class="col">
                                    <a class="fancybox" href="<?php echo esc_url(WSHS_PLUGIN_URL."/images/custom-post-type.jpg") ?>" data-fancybox="images" title="Custom Post Type and Taxanomy sitemap" data-caption='[wshs_list post_type="portfolio" name="Portfolio Sitemap" order_by="date"] <br> [wshs_list post_type="portfolio" name="Website Design Project" order_by="date" taxonomy="portfolio_category" terms="website-design"]'>
                                        <img src="<?php echo esc_url(WSHS_PLUGIN_URL."/images/custom-post-type.jpg") ?>" alt="Custom Post Type and Taxanomy sitemap" />
                                    </a>
                                    <h2><?php echo esc_html("CPT And Taxonomy Sitemap","wp-simple-html-sitemap"); ?></h2>
                                </div>
                                <div class="col">
                                    <a class="fancybox" href="<?php echo esc_url(WSHS_PLUGIN_URL."/images/category-sitemap.jpg") ?>" data-fancybox="images" title="Category sitemap" data-caption='[wshs_list post_type="post" name="Post Sitemap" order_by="date"] <br> [wshs_list post_type="post" name="Free Download" order_by="date" taxonomy="category" terms="free-download"]'>
                                        <img src="<?php echo esc_url(WSHS_PLUGIN_URL."/images/category-sitemap.jpg") ?>" alt="Category sitemap" />
                                    </a>
                                    <h2><?php echo esc_html("Category Sitemap","wp-simple-html-sitemap"); ?></h2>
                                </div>
                            </div> 
                        </div> 

                        <div class="documentation">
                            <div class="wp-heading-main">
                                <h2 class="wp-heading-inline"><?php echo esc_html("Documentation","wp-simple-html-sitemap"); ?></h2>
                            </div>
                            <div class="instruction">
                                <p><?php echo esc_html("Note: Default values are always used for missing shortcode attributes. i.e. Override only the values you want to change.","wp-simple-html-sitemap"); ?></p>
                                <div class="instruction-shortcode">
                                    <ul>
                                        <li><strong>[wshs_list post_type="page" name="Page Sitemap" order_by="date"]</strong></li>
                                        <li><strong>[wshs_list post_type="post" name="Post Sitemap" order_by="date"]</strong></li>
                                        <li><strong>[wshs_list post_type="page" name="Page Sitemap" order_by="date" horizontal="true" separator="|"]</strong></li>
                                        <li><strong>[wshs_list post_type="post" name="Post Sitemap" order_by="date" horizontal="true" separator="|"]</strong></li>
                                    </ul>
                                </div>
                                <ul class="shortcode-attribute">
                                    <li><code>post_type="page"</code> - <?php echo esc_html("A list of pages, in the order entered.","wp-simple-html-sitemap"); ?></li>
                                    <li><code>post_type="post"</code> - <?php echo esc_html("A list of posts for each post type specified, in the order entered.","wp-simple-html-sitemap"); ?></li>
                                    <li><code>name="Post Sitemap"</code> - <?php echo esc_html("Display post type title.","wp-simple-html-sitemap"); ?></li>
                                    <li><code>child_of=""</code> - <?php echo esc_html("To specify the parent page by adding parent page ID","wp-simple-html-sitemap"); ?></li>                                    
                                    <li><code>orderby="title"</code> - <?php echo esc_html("Pages and Posts will be ordered by title alphabetically in ascending order","wp-simple-html-sitemap"); ?></li>                                    
                                    <li><code>depth="2"</code> -  <?php echo esc_html("Using this option one can control what level of sub-pages should be included in the sitemap.","wp-simple-html-sitemap"); ?></li>
                                    <li><code>show_image="true"</code> - <?php echo esc_html("Optionally show the post or page featured image.","wp-simple-html-sitemap"); ?></li>
                                    <li><code>image_width="30" image_height="30"</code> - <?php echo esc_html("Optionally show the post or page featured image set height and width.","wp-simple-html-sitemap"); ?></li>
                                    <li><code>content_limit="140"</code> - <?php echo esc_html("Optionally show a post excerpt (if defined) under each sitemap item.","wp-simple-html-sitemap"); ?></li>
                                    <li><code>show_date="true"</code> - <?php echo esc_html("Set to true to display sitemap items in post created date.","wp-simple-html-sitemap"); ?></li>
                                    <li><code>date="created"</code> - <?php echo esc_html("Display sitemap items in post created date.","wp-simple-html-sitemap"); ?></li>
                                    <li><code>date_format="F j, Y"</code> - <?php echo esc_html("Display sitemap items date format.","wp-simple-html-sitemap"); ?></li>  
                                    <li><code>layout="single-column"</code> - <?php echo esc_html("To show the sitemap in Single column or in Two column.","wp-simple-html-sitemap"); ?></li>
                                    <li><code>position="left"</code> - <?php echo esc_html("For Two column layout, you can choose to show sitemap in left or right column.","wp-simple-html-sitemap"); ?></li>                   
                                    <li><code>taxonomy="category"</code> - <?php echo esc_html("List of post type for each post type specific taxonomy post list.","wp-simple-html-sitemap"); ?></li>
                                    <li><code>terms="wordpress-plugins"</code> - <?php echo esc_html("List of post type for each post type specific taxonomy by terms post list.","wp-simple-html-sitemap"); ?></li>
                                    <li><code>horizontal="true"</code> - <?php echo esc_html("Set to true to display sitemap items in a flat horizontal list. Great for adding a sitemap to the footer!","wp-simple-html-sitemap"); ?></li>
                                    <li><code>separator=" |"</code> - <?php echo esc_html("The character(s) used to separate sitemap items. Use with the 'horizontal' attribute.","wp-simple-html-sitemap"); ?></li>
                                    <li><code>exclude="100,122,155"</code> - <?php echo esc_html("Comma separated list of post IDs to exclude from the sitemap.","wp-simple-html-sitemap"); ?></li>
                                </ul> 
                            </div>
                        </div>
                    </div>
                    <!-- Sidebar Advertisement -->
                    <?php $sidebar_file = WSHS_PLUGIN_PATH . '/inc/wshs_sidebar.php';
                        if (file_exists($sidebar_file)) {
                            require_once $sidebar_file;
                        } ?>
                    <!-- Sidebar Advertisement -->
                </div>
            </div> 
        </div>  
        <script type="text/javascript">
            jQuery(document).ready(function() {
                jQuery('.fancybox').fancybox({
                    beforeShow: function() {
                        this.title = this.title + " <br/> " + jQuery(this.element).data("caption");
                    }
                });
            });
            <?php if ( wshs_is_admin_page_active( 'wshs_documentation', 'toplevel_page_wshs_page_list' ) ) : ?>
                jQuery(document).ready(function($) {
                    $('#toplevel_page_wshs_page_list').addClass('current');
                });
            <?php endif; ?>
        </script>
        <?php
    }

}