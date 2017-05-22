<?php

if(isset($_REQUEST["old_url"]))
{
    require_once( './wp-load.php' );

    global $wpdb;

    $old_url = $_REQUEST["old_url"]; 
    $new_url = $_REQUEST["new_url"];  

    $wpdb->query("update $wpdb->posts set guid = replace(guid, '$old_url', '$new_url'), 
    post_content = replace(post_content, '$old_url', '$new_url')");

    $wpdb->query("update $wpdb->postmeta set meta_value = replace(meta_value, '$old_url', '$new_url')");
    
    $wpdb->query("update $wpdb->options set option_value = '$new_url' where option_name like '$old_url'");

    $results = $wpdb->get_results("select option_name from $wpdb->options where option_value like '%$old_url%'");
    
    foreach($results as $option)
    {        
        $value = get_option($option->option_name);

        $value = json_decode(str_replace($old_url, $new_url, json_encode($value)));
        
        update_option($option->option_name, $value);
    }
    
    echo "<p>Enlaces cambiados.</p>";
}

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" >
    
    <p><input type="text" placeholder="Antigua URL" name="old_url" id="old_url" style="min-width: 500px" /></p>
    <p><input type="text" placeholder="Nueva URL" name="new_url" id="new_url" style="min-width: 500px"  /></p>
    <p><input type="submit" value="Reemplazar URL´s" /></p>
    
</form>