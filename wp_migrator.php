<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if(isset($_REQUEST["old_url"]))
{
    require_once( './wp-load.php' );
    require_once('./wp-includes/option.php');

    global $wpdb;

    $old_url = $_REQUEST["old_url"]; 
    $new_url = $_REQUEST["new_url"];  

    $wpdb->query("update $wpdb->posts set guid = replace(guid, '$old_url', '$new_url'), post_content = replace(post_content, '$old_url', $new_url')");

    $wpdb->query("update $wpdb->postmeta set meta_value = replace(meta_value, '$old_url', '$new_url')");

    $wpdb->query("update $wpdb->options set option_value = '$new_url' where option_value like '$old_url'");
    
    $results = $wpdb->get_results("select option_name from $wpdb->options where option_value like '%$old_url%'");
    
    foreach($results as $option)
    {        
        $value = get_option($option->option_name);

        $value = json_decode(str_replace($old_url, $new_url, json_encode($value)));
        
        update_option($option->option_name, $value);
    }
}
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" >
    
    <p><input type="text" placeholder="Old URL" name="old_url" id="old_url" style="min-width: 500px" /></p>
    <p><input type="text" placeholder="New URL" name="new_url" id="new_url" style="min-width: 500px"  /></p>
    <p><input type="submit" value="Reemplazar URL´s" /></p>
    
</form>