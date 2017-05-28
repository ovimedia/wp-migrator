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
    
    $wpdb->query("update $wpdb->options set option_value = '$new_url' where option_value like '$old_url'");

    $results = $wpdb->get_results("select option_name from $wpdb->options where option_value like '%$old_url%'");
    
    foreach($results as $option)
    {        
        $value = get_option($option->option_name);

        replace_options($value, $old_url, $new_url);
        
        update_option($option->option_name, $value);
    }

    echo "<p>Enlaces cambiados.</p>";
}

    function replace_options(&$options, $old_url, $new_url)
    {
        if(is_array($options) || is_object($options) )
        {
            foreach($options as $option => &$field)
            {
                if (is_array($field) || is_object($field))
                   $field = replace_options($field, $old_url, $new_url);
                else
                    $field = str_replace($old_url, $new_url, $field);            
            }
        }
        else 
            $options = str_replace($old_url, $new_url, $options);  
    }

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" >
    
    <p><input type="text" placeholder="Antigua URL" name="old_url" id="old_url" style="min-width: 500px" /></p>
    <p><input type="text" placeholder="Nueva URL" name="new_url" id="new_url" style="min-width: 500px"  /></p>
    <p><input type="submit" value="Reemplazar URL´s" /></p>
    
</form>