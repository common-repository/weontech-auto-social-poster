<?php
/*
Plugin Name: WeOnTech Auto Social Poster
Plugin URI: http://www.weontech.com
Description: Automatically publishes your new blog content to Social Networks.
Version: 2.0
Author: WeOnTech
Author URI: http://www.weontech.com
*/
require_once "weontech_request.php";

class WeOnTech_Poster
{
	public static function activate()
    {
		global  $wpdb;
                
        $table_logs = $wpdb->prefix . 'weontech_logs';
		$sql = "CREATE TABLE IF NOT EXISTS {$table_logs} (
            log_id int(11) NOT NULL AUTO_INCREMENT,
            post_title text NOT NULL,                        
            post_type varchar(50) NOT NULL DEFAULT 'Bookmarking',
            post_id int(11) NOT NULL,
            log_datetime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (log_id)
        )";
        
        $wpdb->query($sql);
		
		add_option('weontech_key');
		add_option('weontech_project');
		add_option('weontech_spin');
		add_option('weontech_services_bookmark');
		add_option('weontech_services_blog');
    }		
	
	static function addAdminMessage($text, $type)
	{
		$messages = get_transient('wot_messages');
		$messages[] = array($text, $type);
		set_transient('wot_messages', $messages);
	}
	
	public static function post($post_ID)
    {
		if ($_POST['wot_post'] != "on")
        {								
            return;
        }			
		$post = get_post($post_ID);
		$title = $post->post_title;
		$url = get_permalink($post_ID);
						
		$post_thumbnail_id = get_post_thumbnail_id($post_ID);
        $image_url = '';
        if($post_thumbnail_id)
        {
            $image_attributes = wp_get_attachment_image_src($post_thumbnail_id, 'large');
            $image_url = $image_attributes[0];            
        }
		$tags = "";
		$posttags = get_the_tags($post_ID);
		if ($posttags) {
			foreach($posttags as $tag) {
				$tags .= $tag->name . ','; 
			}
		}
		$tags = rtrim($tags,',');
		
		$key = get_option('weontech_key');
		$project_id = get_option('weontech_project');	
		$bookmark_ids = get_option('weontech_services_bookmark');		
		$blog_ids = get_option('weontech_services_blog');
		$spin = get_option('weontech_spin');
		if($bookmark_ids != "") {
			$description = WeOnTech_Poster::clean_up_and_shorten_content($post->post_content, 350, ' ');
			$bookmark = json_decode(wot_createBookmark($key, $url, $title, $description, $image_url, $tags, $project_id, $bookmark_ids, $spin));
			if($bookmark->success) {
				WeOnTech_Poster::insert_log($title, 'Bookmarking', $bookmark->post_id);
				WeOnTech_Poster::addAdminMessage('Sent successfully to WeOnTech Post Bookmark', 'updated');
			}
			else {
				WeOnTech_Poster::addAdminMessage('WeOnTech: ' . $bookmark->error, 'error');
			}
		}
		if($blog_ids != "") {
			$content = $post->post_content;	
			$content = $content."<p>Source: <a href=\"".$url."\">".$title."</a></p>";
			$blogpost = json_decode(wot_createBlogPost($key, $title, $content, $tags, $project_id, $blog_ids, $spin));
			if($blogpost->success) {
				WeOnTech_Poster::insert_log($title, 'Blogging', $blogpost->post_id);
				WeOnTech_Poster::addAdminMessage('Sent successfully to WeOnTech Post Blogging', 'updated');
			}
			else {
				WeOnTech_Poster::addAdminMessage('WeOnTech: ' . $blogpost->error, 'error');
			}
		}
		
		WeOnTech_Poster::maintain_logs();
	}
	
	private static function clean_up_and_shorten_content($content, $length, $ending_char)
    {
        $content = strip_tags($content);
        $content = preg_replace("|(\r\n)+|", " ", $content);
        $content = preg_replace("|(\t)+|", "", $content);
        $content = preg_replace("|\&nbsp\;|", "", $content);
        $content = substr($content, 0, $length);
        
        if(strlen($content) == $length)
        {
            $content = substr($content, 0, strrpos($content, $ending_char));
        }
        return $content;
    }
	
	private static function insert_log($post_title, $post_type, $post_id)
    {
        global  $wpdb;

        $table_logs = $wpdb->prefix . 'weontech_logs';
        
        $wpdb->escape_by_ref($post_title);
        $wpdb->escape_by_ref($post_type);        
        
        $sql="INSERT INTO $table_logs (post_title, post_type, post_id) 
            VALUES ('{$post_title}','{$post_type}','{$post_id}')";
        $wpdb->query($sql);
        
        return true;
    }
	
	private static function maintain_logs()
    {
        global  $wpdb;

        $table_logs = $wpdb->prefix . 'weontech_logs';
        
        $sql="SELECT log_id FROM $table_logs ORDER BY log_id DESC LIMIT 100";
        $rows = $wpdb->get_results($sql);
        if(is_array($rows) && count($rows)==100)
        {
            $log_ids = "(";
            foreach($rows as $row)
            {
                $log_ids .= $row->log_id.",";
            }
            $log_ids = rtrim($log_ids, ",");
            $log_ids .= ")";
            
            $sql="DELETE FROM {$table_logs} WHERE log_id NOT IN {$log_ids}";
            $wpdb->query($sql);
        }
        
        return true;
    }
}

register_activation_hook(__FILE__, array('WeOnTech_Poster', 'activate'));

add_action('publish_post', array('WeOnTech_Poster', 'post'));
add_action('publish_page', array('WeOnTech_Poster', 'post'));

function wot_meta()
{		
	$key = get_option('weontech_key');
	 if ($key  != "")
	{
		$userInfo = json_decode(wot_getInfo($key));
		if ($userInfo != "")
		{
			$bookmark_ids = get_option('weontech_services_bookmark');		
			$blog_ids = get_option('weontech_services_blog');
		
			if(!$userInfo->success)
			{
				?>
				<div style="
					 padding: 5px 35px 5px 14px;
					 margin-bottom: 5px;
					 text-shadow: none;
					 border: 1px solid #fbeed5;
					 -webkit-border-radius: 2px;
					 -moz-border-radius: 2px;
					 border-radius: 2px;
					 font-weight: 300 !important; font-size: 14px !important;
					 font-weight: normal !important;
					 color: rgb(228, 0, 0) !important;
					 background-color: #f2dede;
					 border-color: #eed3d7;
					 margin-top: 10px;
					 ">
					 <?php echo $userInfo->error; ?>					
				</div>
				<?php
			}
			else if($userInfo->account_type == -1)
			{
				?>
				<div style="
					 padding: 5px 35px 5px 14px;
					 margin-bottom: 5px;
					 text-shadow: none;
					 border: 1px solid #fbeed5;
					 -webkit-border-radius: 2px;
					 -moz-border-radius: 2px;
					 border-radius: 2px;
					 font-weight: 300 !important; font-size: 14px !important;
					 font-weight: normal !important;
					 color: rgb(228, 0, 0) !important;
					 background-color: #f2dede;
					 border-color: #eed3d7;
					 margin-top: 10px;
					 ">					 
					Your FREE trial has expired! <a style="color: #f26722; text-decoration: underline;" href="<?php echo WOT_URL; ?>/pricing.aspx" target="_blank">Update Now</a>
				</div>
				<?php
			}
			else if($bookmark_ids == "" and $blog_ids == "")
			{
				?>
				<div style="
					 padding: 5px 35px 5px 14px;
					 margin-bottom: 5px;
					 text-shadow: none;
					 border: 1px solid #fbeed5;
					 -webkit-border-radius: 2px;
					 -moz-border-radius: 2px;
					 border-radius: 2px;
					 font-weight: 300 !important; font-size: 14px !important;
					 font-weight: normal !important;
					 color: rgb(228, 0, 0) !important;
					 background-color: #f2dede;
					 border-color: #eed3d7;
					 margin-top: 10px;
					 ">					 
					You do not have any networks setup.
				</div>
				<?php
			}
			else
			{
				$default_post = true;
				$screen = get_current_screen();
				if($screen->action != 'add')
				{
					$default_post = false;
				}
				?>
				<input type="checkbox" id="wot_post" name="wot_post" <?php if($default_post) echo 'checked="checked"';?> />
				<label for="wot_post">Post this to WeOnTech?</label>
				<?php
			}
		}	
	}
}

function wot_render()
{
		$messages = get_transient('wot_messages');
		if (empty($messages)) return;
		foreach ($messages as $message):
			?>
			<div class="<?php echo $message[1]?>">
				<p><?php _e($message[0], 'wot' ); ?></p>
			</div>
			<?php endforeach;
		$_SESSION['wot_messages'] = array();
		set_transient('wot_messages', array());
}

add_action('admin_notices', 'wot_render');
	
function wot_meta_box()
{
	add_meta_box("weontech-post", "WeOnTech Poster", "wot_meta", "post", "normal", "high");
	add_meta_box("weontech-post", "WeOnTech Poster", "wot_meta", "page", "normal", "high");
}
add_action('admin_menu', 'wot_meta_box');

// Add settings link on plugin page
function weontechposter_plugin_settings_link($links) 
{ 
    $settings_link = '<a href="options-general.php?page=weontechoptions">Settings</a>'; //get_admin_url()
    array_unshift($links, $settings_link); 
    return $links; 
}

$weontechposter_plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_{$weontechposter_plugin}", 'weontechposter_plugin_settings_link');

//Options

require_once "weontech_options.php";

?>