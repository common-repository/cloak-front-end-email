<?php
/*
Plugin Name: Cloak Front End Email
Description: Display your email in javascript on your website with short code [email] Or a custom email addresses will use a short code of [email name="cfe-example"]
Author: <a href="https://www.webbernaut.com/">Webbernaut</a>
Version: 1.9.5
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

//Register Scripts & Styles
function cfe_register_script() {
    wp_register_script( 'cloak', plugin_dir_url(__FILE__) . 'cloakfrontendemail.js', array( 'jquery' ), true, false );
    wp_enqueue_script( 'cloak' );
    wp_localize_script( 'cloak', 'cfe_object', array( 'ajaxurl' => admin_url('admin-ajax.php') ) );
}
add_action( 'wp_enqueue_scripts', 'cfe_register_script' );

//Register Admin Scripts & Styles
//Load script if only on the page
if ( isset( $_GET['page'] ) && $_GET['page'] === 'cfe-interface' ) {
    function cfe_admin_register_script() {
        wp_enqueue_script( 'cloak-admin-js', plugin_dir_url(__FILE__) . 'admin/script.js', array( 'jquery' ), true, false );
    }
    add_action( 'admin_enqueue_scripts', 'cfe_admin_register_script' );
}

//=========Ajax Calls=========
//Allow Ajax js_admin_email front end and backend
add_action( 'wp_ajax_cfe_js_admin_email', 'cfe_get_admin_email' );
add_action( 'wp_ajax_nopriv_cfe_js_admin_email', 'cfe_get_admin_email' );
add_action( 'wp_ajax_cfe_remove_email', 'cfe_remove_email' );
add_action( 'wp_ajax_nopriv_cfe_remove_email', 'cfe_remove_email' );
add_action( 'wp_ajax_cfe_get_all_emails', 'cfe_get_all_emails' );
add_action( 'wp_ajax_nopriv_cfe_get_all_emails', 'cfe_get_all_emails' );

//Grab Email PHP
function cfe_get_admin_email() {
    if ( ! isset( $_POST['nouce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['nouce'] ), 'secure_cloak_checker' ) ) {
        die( 'Permission Denied' );
    }
    if ( isset( $_POST['nouce'] ) ) {
        if ( wp_verify_nonce( wp_unslash( $_POST['nouce'] ), 'secure_cloak_checker' ) ) {
            echo esc_attr( get_option( 'admin_email' ) );
        }
    }
    die();
}

function cfe_get_all_emails() {
     if ( ! isset( $_POST['nouce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['nouce'] ), 'secure_cloak_checker' ) ) {
        wp_die( 'Permission Denied' );
    }
    if ( isset( $_POST['nouce'] ) ) {
        if ( wp_verify_nonce( wp_unslash( $_POST['nouce'] ), 'secure_cloak_checker' ) ) {
            global $wpdb;
            $table = $wpdb->prefix . "options";
            $emails = array_map( 'esc_attr', $_POST['emails'] );
            $placeholders = array_fill( 0, count( $emails ), '%s' ); //output mulitple %s for placeholders for prepare statement
            $placeholders = implode( ', ', $placeholders );
            $query = $wpdb->prepare( "SELECT option_name, option_value FROM {$table} WHERE option_name IN ({$placeholders})", ...$emails ); // ...$emails unpacks array for placeholder
            $addresses = $wpdb->get_results( $query, OBJECT );
            echo wp_json_encode( $addresses );
        }
    }
    die();
}

//Delete Email from db
function cfe_remove_email() {
    if ( ! wp_unslash( $_POST['nouce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['nouce'] ), 'secure_cloak_checker' ) ) {
        die( 'Permission Denied' );
    } else {
        if ( wp_verify_nonce( wp_unslash( $_POST['nouce'] ), 'secure_cloak_checker') ) {
            global $wpdb;
            $option_name = esc_html( esc_attr( $_POST['option_name'] ) );
            $wpdb->delete( $wpdb->prefix . 'options', array( 'option_name' => $option_name ) );
            echo $option_name;
        }
    }
    die();
}

//Email JS Shortcode [email]
function cfe_jsEmailShortcode_multi( $atts, $content = null ) {
    $atts = shortcode_atts(
        array(
            'name' => 'cfe-dashboard',
            'subject' => ''
        ), $atts
    );
    return '<span class="cfe-wrapper">
                <span class="cfe-jsemail-' . esc_attr( $atts['name'] ) . '" data-subject="' . esc_attr( esc_html( $atts['subject'] ) ) . '"><a href="#">loading...</a></span>
                <input type="hidden" name="secure-cloak" class="secure-cloak" value="' . wp_create_nonce( "secure_cloak_checker" ) . '">
            </span>';
}
add_shortcode( 'email', 'cfe_jsEmailShortcode_multi' );


//=========Admin Interface=========

// Add settings link on plugin page
function cfe_settings_link( $links ) {
    $settings_link = array(
        '<a href="' . admin_url( 'admin.php?page=cfe-interface' ) . '">Settings</a>',
    );
    return array_merge( $links, $settings_link );
}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'cfe_settings_link' );

function cfe_plugin_meta( $links, $file ) {
    if ( strpos( $file, 'cloak-front-end-email/email.php' ) !== false ) {
        $links = array_merge( $links, array( '<a target="_blank" href="https://www.paypal.me/webbernaut" title="Donate page">Donate</a>' ) );
    }
    return $links;
}
add_filter( 'plugin_row_meta', 'cfe_plugin_meta', 10, 2 );

//Admin Menu
add_action( 'admin_menu', 'cfe_custom_interface' );
function cfe_custom_interface() {
    if ( current_user_can( 'administrator' ) ) {
        add_menu_page( 'Cloak Email', 'Cloak Email', 'read', 'cfe-interface', 'cfe_admin_interface', 'dashicons-lock', 15 );
        add_action( 'admin_init', 'save_cfe_interface_options' );
    }
}

//Save setting options
function save_cfe_interface_options() {
    if ( isset( $_POST['_wpnonce'] ) && isset( $_POST['secure_cloak'] ) && isset( $_POST['action'] ) && $_POST['action'] === 'update' ) {
        // Check the nonce for security
        if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'update-options' ) && ! wp_verify_nonce( $_POST['nouce'], 'secure_cloak_checker' ) ) {
            die( 'Permission Denied' );
        }
        // Sanitize and save email addresses as options
        foreach ( $_POST as $key => $value ) {
            // Check if the key starts with 'cfe-' (which indicates email fields)
            if ( strpos( $key, 'cfe-' ) === 0 ) {
                $key = esc_html( esc_attr( $key ) );
                $sanitized_email = sanitize_email( $value );
                update_option( $key, $sanitized_email );
            }
        }
        wp_redirect( admin_url( 'admin.php?page=cfe-interface' ) );
        exit();
    }
}

//Custom Admin Page
function cfe_admin_interface() {
    //Query database for existing emails
    global $wpdb;
    $table = $wpdb->prefix . "options";
    $query = $wpdb->prepare( "SELECT * FROM {$table} WHERE option_name LIKE %s ORDER BY option_name ASC", "cfe-%" );
    $emails = $wpdb->get_results( $query, OBJECT );
    ?>
    <div class='wrap'>
        <div style="background:#fff; padding:15px; border-bottom:1px #f1f1f1; border-left:solid 4px #46b450; width:28%; float:right; line-height:30px;">Like this plugins? Why not make a <a class="button" target="_blank" href="https://www.paypal.me/webbernaut">Donation</a></div>

        <form method="post" action="options.php">
            <?php wp_nonce_field( 'update-options' ) ?>
            <div class="wrap">
                <h1>Cloak Front End Email</h1>
                <button id="cfe_add" class="button button-primary">+ Add Email</button>
                <p>
                    <strong>WordPress Email ~ <em>shortcode [email]</em></strong><br />
                    <input type="email" size="50" value="<?php echo esc_attr( get_option( 'admin_email' ) ); ?>" disabled />
                </p>
                <?php foreach ( $emails as $email ) { ?>
                    <p><strong><em>shortcode [email name="<?php echo esc_html( esc_attr( $email->option_name ) ); ?>"]</em></strong><br />
                    <input type="email" class="cfe_additional_email" name="<?php echo esc_html( esc_attr( $email->option_name ) ); ?>" size="100" value="<?php echo sanitize_email( $email->option_value ); ?>" />
                    <span class="button button-primary cfe-delete">- Remove</span></p>
                <?php } ?>
                <div id="wrap_cfe_emails"></div>
                <p><?php submit_button(); ?></p>

                <input type="hidden" name="action" value="update" />
                <input type="hidden" name="secure_cloak" id="secure_cloak" value="<?php echo esc_attr( wp_create_nonce( 'secure_cloak_checker' ) ); ?>">
                <!-- <input type="hidden" name="page_options" value="" /> -->
            </div>
        </form>
    </div>
<?php }

//End of Plugin
?>
