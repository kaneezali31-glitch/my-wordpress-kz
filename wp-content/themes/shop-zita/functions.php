<?php
/**
 * WooCommerce Compatibility
 */
if ( ! class_exists( 'Shop_Zita_Woocommerce_Ext' ) ) :
    /**
     * Shop_Zita_Woocommerce_Ext
     *
     * @since 1.0.0
     */
    class Shop_Zita_Woocommerce_Ext{
         /**
         * Member Variable
         *
         * @var object instance
         */
        private static $instance;

        /**
         * Initiator
         */
        public static function get_instance() {
            if ( ! isset( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }
       /**
         * Constructor
         */
        public function __construct(){
         /* Shop page view list and grid view.
         */
         add_action('wp_enqueue_scripts', array($this, 'shop_zita_enq'));


         add_action( 'woocommerce_before_shop_loop', array($this,'shop_zita_shop_content_start'),1);
         add_action( 'woocommerce_after_shop_loop', array($this,'shop_zita_shop_content_end'),1);

         add_action('woocommerce_before_shop_loop', array($this, 'shop_zita_before_shop_loop'), 35);
         add_action('zita_woo_shop_add_to_cart_before', array($this, 'shop_zita_list_after_shop_loop_item'),40);

        }  

                  /**
                   * Thumbnail wrap start.
                   */
                  function shop_zita_shop_content_start(){
                    
                    echo '<div id="shop-product-wrap">';
                  }
              

               

                  /**
                   * Thumbnail wrap start.
                   */
                  function shop_zita_shop_content_end(){
                    
                    echo '</div>';
                  }
               
        public function shop_zita_enq(){
        $themeVersion = wp_get_theme()->get('Version');
        // Enqueue our style.css with our own version
        wp_enqueue_style('shop-zita-styles', get_template_directory_uri() . '/style.css',array(), $themeVersion);
        wp_add_inline_style( 'shop-zita-styles', $this->shop_zita_custom_style());
        wp_enqueue_script('shop-zita-custom-script', get_stylesheet_directory_uri() . '/js/custom.js',array(), $themeVersion,true);
        }


        function shop_zita_before_shop_loop(){
        echo '<div class="zita-list-grid-switcher">';
        echo '<a title="' . esc_attr__('Grid View', 'shop-zita') . '" href="#" data-type="grid" class="zita-grid-view selected"><i class="fa fa-th"></i></a>';

        echo '<a title="' . esc_attr__('List View', 'shop-zita') . '" href="#" data-type="list" class="zita-list-view"><i class="fa fa-bars"></i></a>';

        echo '</div>';
        }
        // shop page content
        function shop_zita_list_after_shop_loop_item(){
        if (is_shop()) { ?>
           <div class="shop-zita-product-excerpt"><?php the_excerpt(); ?></div>
        <?php     }
        }

      public  function shop_zita_custom_style(){
        $shop_zita_style=""; 
        $shop_zita_theme_clr     = get_theme_mod('zita_theme_clr','#006799');  
        $shop_zita_style.=" .zita-list-grid-switcher a.selected, .zita-list-grid-switcher a:hover{
                                background:{$shop_zita_theme_clr}!important;
                                border: 1px solid {$shop_zita_theme_clr}!important;
                            }";
        return $shop_zita_style;
       }
       

    }
endif;  
Shop_Zita_Woocommerce_Ext::get_instance(); 