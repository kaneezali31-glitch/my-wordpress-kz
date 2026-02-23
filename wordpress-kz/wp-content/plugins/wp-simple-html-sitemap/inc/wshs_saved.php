<?php 

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

/**
 * 
 * @description Save generated shortcode
 * 
 */
function wshs_save_shortcode() {

    if (!current_user_can( 'manage_options' ) ) {
        return wp_send_json( array( 'result' => 'Authentication error' ) );
    }

    check_ajax_referer('ajax-nonce', 'security');
    global $wpdb;

    if(isset($_POST['code'])){
        $code = sanitize_text_field(wp_unslash($_POST['code']));
    } else {
        $code = "";
    }
    
    if(isset($_POST['type'])){
        $type = sanitize_text_field( wp_unslash($_POST['type']));
    } else {
        $type = "";
    }

    if(isset($_POST['title'])){
        $title = sanitize_text_field( wp_unslash($_POST['title']));
    } else {
        $title = "";
    }

    if(isset($_POST['id'])){
        $id = sanitize_text_field( wp_unslash($_POST['id']));
    } else {
        $id = 0;
    }


    $atts_array = shortcode_parse_atts('[wshs_list post_type="page" name="Page Sitemap" order_by="date" order ="asc"]');
    $post_data = array(
        'title' => $title,
        'attributes' => $code,
        'user_id' => get_current_user_id(),
        'code_type' => $type,
        'updated_at' => gmdate('Y-m-d H:i:s')
    );

    if($id > 0){
        // We are querying a custom plugin table that has no WordPress API equivalent.
        // phpcs:disable WordPress.DB.DirectDatabaseQuery
        $wpdb->update( $wpdb->base_prefix.WSHS_SAVED_CODE_TABLE, $post_data, array( 'id' => intval($id) ), array( '%s','%s','%d','%s','%s'), array( '%d' ) );
        
    } else {
        $post_data['created_at'] = gmdate('Y-m-d H:i:s');
        $format = array('%s','%s','%d','%s','%s','%s');
        $wpdb->insert($wpdb->base_prefix.WSHS_SAVED_CODE_TABLE,$post_data,$format);
        $id = $wpdb->insert_id;
    }

    wp_send_json(array('id' => $id));
}
add_action('wp_ajax_wshs_save_shortcode', 'wshs_save_shortcode');
//add_action('wp_ajax_nopriv_wshs_save_shortcode', 'wshs_save_shortcode');

function wshs_saved(){
    global $wpdb;
    $message = '';
    $table_name = $wpdb->prefix . WSHS_SAVED_CODE_TABLE;
    if(isset($_REQUEST['id'])){
        $id = sanitize_text_field( wp_unslash($_REQUEST['id']));
    } else {
        $id = array();
    }
    //$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
    if ( ! empty( $id ) && isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'delete' ) {
    
        // Verify nonce first
        if ( ! isset( $_REQUEST['wshs_nonce'] ) || 
            ! wp_verify_nonce( sanitize_text_field(wp_unslash($_REQUEST['wshs_nonce'])), 'wshs_delete_' . $id ) ) {
            wp_die( esc_html__( 'Security check failed.', 'wp-simple-html-sitemap' ) );
        }

        // Safe to delete
        $wpdb->delete(
            $table_name,           // Table name
            array( 'id' => $id ),  // WHERE clause
            array( '%d' )          // Value formats
        );

        $message = "Shortcode deleted successfully.";
    }
    $table = new WSHS_Saved_Code_Table();
    ?>
    <div class="wrap wtl-main">
        <h1 class="wp-heading-inline"><?php echo esc_html("Simple HTML Sitemap","wp-simple-html-sitemap"); ?></h1>
        <hr class="wp-header-end">
        <?php if(!empty($message)): ?>
            <div class="updated notice">
                <p><?php echo esc_html($message); ?></p>
            </div>
        <?php endif; ?>
        
        <div id="post-body" class="metabox-holder columns-3">
            <!-- Top Navigation -->
            <div class="sitemap-wordpress">
                <h2 class="nav-tab-wrapper">
                    <a href="?page=wshs_page_list" class="nav-tab"><?php echo esc_html("Pages","wp-simple-html-sitemap"); ?></a>
                    <a href="?page=wshs_post_list" class="nav-tab"><?php echo esc_html("Posts","wp-simple-html-sitemap"); ?></a>
                    <a href="?page=wshs_saved" class="nav-tab nav-tab-active"><?php echo esc_html("Saved Shortcodes","wp-simple-html-sitemap"); ?></a>
                    <a href="?page=wshs_documentation" class="nav-tab"><?php echo esc_html("Documentation","wp-simple-html-sitemap"); ?></a>
                </h2>
                <div class="sitemap-pages">
                    <div class="shortcode-container shortcode-item-list">
                        <?php 
                            // Prepare table
                            $table->prepare_items();
                            // Display table
                            $table->display(); 
                        ?>
                    </div>
                    <div class="clear"></div>
                </div>
                <!-- Sidebar Advertisement -->
                <?php require_once WSHS_PLUGIN_PATH . '/inc/wshs_sidebar.php'; ?>
                <!-- Sidebar Advertisement -->
            </div>
        </div> 
    </div> 
<?php }


// Extending class
class WSHS_Saved_Code_Table extends WP_List_Table
{
    private $table_data;
    public function get_columns(){
        $columns = array(
                'title'          => esc_html('Name', 'wp-simple-html-sitemap'),
                'attributes'         => esc_html('Shortcode', 'wp-simple-html-sitemap'),
                // 'created_at'   => esc_html('Generated On', 'wp-simple-html-sitemap'),
                'action'   => esc_html('Action', 'wp-simple-html-sitemap'),
        );
        return $columns;
    }

    // Bind table with columns, data and all
    public function prepare_items(){
        $this->table_data = $this->get_table_data();
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = array();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $this->table_data;
    }

    private function get_table_data() {
        global $wpdb;
        $wpdb->wshs_saved_code = $wpdb->prefix . WSHS_SAVED_CODE_TABLE;
        // Now no interpolation needed
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        return $wpdb->get_results( "SELECT * FROM {$wpdb->wshs_saved_code} ORDER BY id DESC", ARRAY_A );

        /*$table = $wpdb->prefix . WSHS_SAVED_CODE_TABLE;
        return $wpdb->get_results( "SELECT * from {$table} ORDER BY ID DESC", ARRAY_A );*/
    }

    function column_default($item, $column_name){
        switch ($column_name) {
        case 'created_at':
            return gmdate('Y-m-d', strtotime($item[$column_name]));
        case 'attributes':
            return '<pre>'. esc_html($item[$column_name]) .'</pre>';
        case 'title':
            return '<strong>'. esc_html($item[$column_name]).'</strong> Date: '.gmdate('d M, Y', strtotime($item['created_at']));
        case 'action':
            if($item['code_type'] == 'page'){
                $edit_url = admin_url( 'admin.php?page=wshs_page_list&id=' . $item['id'] );

                $delete_url = wp_nonce_url(
                    admin_url( 'admin.php?page=wshs_saved&action=delete&id=' . $item['id'] ),
                    'wshs_delete_' . $item['id'], // Action for nonce
                    'wshs_nonce'                  // Nonce field name
                );
                $links  = '<a href="' . esc_url( $edit_url ) . '" class="button">Edit</a>';
                $links .= '<a href="' . esc_url( $delete_url ) . '" class="button">Delete</a>';
                return $links;
                //return '<a href="'.admin_url( 'admin.php?page=wshs_page_list&id='.$item['id']).'" class="button">Edit</a><a href="'.admin_url( 'admin.php?page=wshs_saved&action=delete&id='.$item['id']).'" class="button">Delete</a>';
            }else{
                $edit_url = admin_url( 'admin.php?page=wshs_post_list&id=' . $item['id'] );

                $delete_url = wp_nonce_url(
                    admin_url( 'admin.php?page=wshs_saved&action=delete&id=' . $item['id'] ),
                    'wshs_delete_' . $item['id'], // Action for nonce
                    'wshs_nonce'                  // Nonce field name
                );
                $links  = '<a href="' . esc_url( $edit_url ) . '" class="button">Edit</a>';
                $links .= '<a href="' . esc_url( $delete_url ) . '" class="button">Delete</a>';
                return $links;
                //return '<a href="'.admin_url( 'admin.php?page=wshs_post_list&id='.$item['id']).'" class="button">Edit</a><a href="'.admin_url( 'admin.php?page=wshs_saved&action=delete&id='.$item['id']).'" class="button">Delete</a>';
            }
        default:
            return $item[$column_name];
        }
    }

    public function no_items() {
        esc_html("You don't have any saved shortcode.", 'wp-simple-html-sitemap');
    }
}