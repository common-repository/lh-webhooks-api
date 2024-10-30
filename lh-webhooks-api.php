<?php
/**
 * Plugin Name: LH Webhooks Api
 * Plugin URI: https://lhero.org/plugins/lh-webhooks-api/
 * Description: Extends the rest api for easy use of webhooks
 * Author: Peter Shaw
 * Version: 1.00
 * Author URI: https://shawfactor.com/
 * Text Domain: lh_webhooks_api
 * Domain Path: /languages
*/

if (!class_exists('LH_Webhooks_api_plugin')) {


class LH_Webhooks_api_plugin {
    
    var $filename;
    var $options;
    var $opt_name = 'lh_webhooks_api-options';
    var $user_id_field_name = 'lh_webhooks_api-user_id_field';
    var $action_field_name = 'lh_webhooks_api-action_field';
    
    var $namespace = 'lh_webhooks_api';
    private static $instance;
    
private function return_rest_api_url() {

return rest_url( 'lh-webhooks-api/v1');

}

private function return_endpoint_record_by_id($id){
    
    global $wpdb;
    
    $sql = "SELECT meta.umeta_id, users.ID, users.display_name, meta.meta_value  FROM ".$wpdb->users." users, ".$wpdb->usermeta." meta WHERE users.ID = meta.user_id and meta_key = '";

$sql .= $wpdb->prefix.$this->namespace.'-records';

$sql .= "' and meta.umeta_id = '".$id."' limit 1";

$results = $wpdb ->get_results($sql);

if (isset($results[0]->umeta_id)){
    
return json_decode($results[0]->meta_value);
    
} else {

return false;    
    
}
    
    
    }
    

public function return_allowed_methods(){
    
$methods = array();

return apply_filters('lh_webhooks_api_allowed_methods', $methods);

}
    
    // Return all notices
    public function handle_post($request_data) {
        
        
$data = $this->return_endpoint_record_by_id($_GET['endpoint_id']);

if (empty($data)){
    
return new WP_Error( 'endpoint_id_not_recognised', 'The endpoint id was not recognised', array( 'status' => 404 ) );    
    
}

if ($data->token != $_GET['token']){
    
return new WP_Error( 'token_recognised', 'The token was not recognised', array( 'status' => 404 ) );    
    
}

if ($data->method != $request_data['method']){
    
return new WP_Error( 'method_not_matched', 'Method does not match the method defined', array( 'status' => 404 ) );    
    
}




        
$filter = $this->namespace.'_'.$request_data['method'];

$return = apply_filters($filter, false, $request_data);

if (!isset($return) or ($return === false)){
    
return new WP_Error( 'method_not_recognised', 'The method was not recognised', array( 'status' => 404 ) );
    
} else {
    
return $return;
    
}


        
        
    }
    
    
    public function rest_api_init() {
    register_rest_route( 
        'lh-webhooks-api/v1',
        'method/(?P<method>\S+)',
        array(
            'methods' => 'POST',
            'callback' => array( $this, 'handle_post'),
            'args'     => array()
        )
    );
}

public function plugin_options() {

if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	
		        global $wpdb;

   
 // See if the user has posted us some information
    // If they did, the nonce will be set

	if( isset($_POST[ $this->namespace."-backend_nonce" ]) && wp_verify_nonce($_POST[ $this->namespace."-backend_nonce" ], $this->namespace."-backend_nonce" )) {
	    
	    $user = get_user_by( 'id', trim($_POST[ $this->user_id_field_name ]) );
	    
	    if ( ! empty( $user ) ) {
	        
	        print_r($user->ID);
	        

	        
	        $add = array();
	        
	        $add['method'] = trim($_POST[ $this->action_field_name ]);
	        
	        $add['token'] = strtolower(wp_generate_password( 16, false ));
	        
	        $add = wp_json_encode($add);
	        
	       $test = add_user_meta( $user->ID, $wpdb->prefix.$this->namespace.'-records', $add, false );
	       
	       if (isset($test)){
	           
	           ?>
<div class="updated"><p><strong><?php _e('Record Added', $this->namespace ); ?></strong></p></div>
<?php

	           
	           
	       }
	        
	        
	    }


}

    // Now display the settings editing screen

include ('partials/option-settings.php');


}

public function plugin_menu() {
add_options_page(__('LH Webhooks Api Options', $this->namespace ), __('Webhooks Api', $this->namespace ), 'manage_options', $this->filename, array($this,"plugin_options"));

}

 /**
     * add new allowed methods for endpoints.
     * $methods are the existsing methods
     * each method is a associative array, with name, value, and description
     */

public function demonstration_methods_handler($methods){
    
    
    $add = array();
    
    $add['name'] = 'log_via_email';
    $add['label'] = 'Add via email';
    $add['description'] = 'a simple logging function';
    
    $methods[] = $add;
    
    return $methods;
    
}

    /**
     * handle data posted to the endpoint for the log_via_email method.
     * $return is what you return to the rest api
     * $request_data is the rest api request object
     */
    
    
public function demonstration_request_handler($return, $request_data){
    
$admin_email = get_option('admin_email'); 
    
wp_mail( $admin_email, "Request body log", $request_data->get_body());

return $request_data->get_body();
    
}

    /**
     * Gets an instance of our plugin.
     *
     * using the singleton pattern
     */
    public static function get_instance(){
        if (null === self::$instance) {
            self::$instance = new self();
        }
 
        return self::$instance;
    }
    
    
   public function __construct() {
       
        $this->filename = plugin_basename( __FILE__ );
        $this->options = get_option($this->opt_name);
       
//create a custom rest api endpoint to house our responses
    add_action( 'rest_api_init', array( $this, 'rest_api_init') ); 
    
//add the webhooks setting menu
    add_action('admin_menu', array($this,"plugin_menu"));
    
//demonstrate adding the log_via_email method
    add_filter( 'lh_webhooks_api_allowed_methods', array($this,"demonstration_methods_handler"), 10, 1 );

//demonstrate adding a request handler, triggered by the log_via_email method
    add_filter( 'lh_webhooks_api_log_via_email', array($this,"demonstration_request_handler"), 10, 2 );
    

       
       
   }
    
    
}

$lh_webhooks_api_instance = LH_Webhooks_api_plugin::get_instance();


}


?>