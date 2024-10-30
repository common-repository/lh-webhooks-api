<h1><?php echo esc_html(get_admin_page_title()); ?></h1>

<?php



$sql = "SELECT meta.umeta_id, users.ID, users.display_name, meta.meta_value  FROM ".$wpdb->users." users, ".$wpdb->usermeta." meta WHERE users.ID = meta.user_id and meta_key = '";

$sql .= $wpdb->prefix.$this->namespace.'-records';

$sql .= "'";

$results = $wpdb ->get_results($sql);

?>

<table>
<thead>
<tr>
    <td>Endpoint ID</td>
    <td>User ID</td>
    <td>User Name</td>
    <td>Method</td>
    <td>Token</td>
    <td>Link</td>
    </tr>    
</thead>
<tbody>
<?php foreach ($results as $result){ 
    
$data = json_decode($result->meta_value);

$link = add_query_arg( 'token', $data->token, add_query_arg( 'endpoint_id', $result->umeta_id, $this->return_rest_api_url().'/method/'.$data->method.'/'));


?>
<tr>
<td><?php echo $result->umeta_id; ?></td>
<td><?php echo $result->ID; ?></td>
<td><?php echo $result->display_name; ?></td>
<td><?php echo $data->method; ?></td>
<td><?php echo $data->token; ?></td>
<td><a target="_blank" href="<?php echo $link; ?>">Link</a></td>
</tr> 
<?php } ?>    
</tbody>
</table>
<form name="lh_login_page-backend_form" method="post" action="">
<?php wp_nonce_field( $this->namespace."-backend_nonce", $this->namespace."-backend_nonce", false ); ?>
<table class="form-table">
<tr valign="top">
<th scope="row"><label for="<?php echo $this->user_id_field_name; ?>"><?php _e("User ID;", $this->namespace ); ?></label></th>
<td><input type="number" name="<?php echo $this->user_id_field_name; ?>" id="<?php echo $this->user_id_field_name; ?>" value="" size="10" /></td>
</tr>
<tr valign="top">
<th scope="row"><label for="<?php echo $this->action_field_name; ?>"><?php _e("Permitted Action:", $this->namespace ); ?></label></th>
<td>
<select name="<?php echo $this->action_field_name; ?>" id="<?php echo $this->action_field_name; ?>">
<?php 

$methods = $this->return_allowed_methods();

 foreach ($methods as $method){ ?>
<option value="<?php echo $method['name']; ?>"><?php echo $method['label']; ?></option>     
<?php } ?>
</select></td>
</tr>
</table>
<?php submit_button( 'Save Changes' ); ?>
</form>

To test endpoint you can send manual post requests to them using <a href="https://www.hurl.it/">Hurl.it/</a>