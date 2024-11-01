<?php

require_once "weontech_request.php";

add_action('admin_init', 'weontech_init');
add_action('admin_menu', 'weontech_options');

function weontech_init()
{
    /* Register our script. */
    wp_register_script('weontech-facebox-js-script', plugins_url('/facebox/facebox.js', __FILE__) );
    wp_register_style('weontech-facebox-css-script', plugins_url('/facebox/facebox.css', __FILE__) );
}

function weontech_options()
{    
    add_submenu_page('options-general.php', 'WeOnTech Settings', 'WeOnTech Settings', 'administrator', 'weontechoptions', 'weontech_settings');  
}

function weontech_settings()
{	
	?>	
	<style type="text/css">
		.wot-wrap {padding:10px; background-color:#FCFCFC; border:solid 1px #D6D5D5;overflow:hidden;}
		.wot-header {float:right;text-align:center;}
		.list-choose {clear:both; overflow:hidden;padding:0;margin:7px 0 0 0;}
		.list-choose li {float:left;width:33%;height:20px;}
		.list-choose li a {text-decoration:none;color:#444444;}
		.list-choose li a:hover{text-decoration:underline;}
		.wot-info {padding: 7px;font-size: 14px;font-weight: bold;border-radius: 5px;background: #fff;-webkit-border-radius: 5px;-moz-border-radius: 5px; border: 1px solid #e5e5e5; margin-top:5px;}
		.wot-message {padding: 5px 35px 5px 14px;margin-bottom: 5px;text-shadow: none;border: 1px solid #fbeed5;-webkit-border-radius: 2px;
                 -moz-border-radius: 2px;border-radius: 2px;font-weight: 300 !important; font-size: 14px !important;font-weight: normal !important;
                 color: rgb(228, 0, 0) !important;background-color: #f2dede;border-color: #eed3d7;margin-top: 10px;}
		ul.wot_tabs {margin: 0;padding: 0; margin-top:5px;float: left;list-style: none;height: 32px;border-bottom: 1px solid #999;border-left: 1px solid #999;width: 99%;}
		ul.wot_tabs li {float: left;margin: 0;padding: 0;height: 31px;line-height: 31px;border: 1px solid #999;border-left: none;margin-bottom: -1px;overflow: hidden;position: relative;background: #e0e0e0;}
		ul.wot_tabs li a {text-decoration: none;color: #000; display: block; font-size: 1.2em; padding: 0 20px; border: 1px solid #fff; outline: none;}
		ul.wot_tabs li a:hover { background: #ccc;}
		ul.wot_tabs li.active, ul.wot_tabs li.active a:hover  { background: #fff; border-bottom: 1px solid #fff; }
		.wot_tab_container {border: 1px solid #999; border-top: none; overflow: hidden; clear: both; float: left; width: 99%; background: #fff;}
		.wot_tab_content {padding: 10px;}
		.list-table {border-collapse:collapse;width:100%;}	
		.list-table tr, .table-order tr{background: url("/wp-content/plugins/<?php echo basename(dirname(__FILE__)) ?>/facebox/tr-bg.gif") repeat-x scroll left bottom #F5F5F5;vertical-align: middle;}
		.list-table tr.on, .table-order tr.on {background-color: transparent;}    
		.list-table th {text-align:left;}
		.list-table th, .list-table td {background: url("/wp-content/plugins/<?php echo basename(dirname(__FILE__)) ?>/facebox/td-spacer.gif") repeat-y scroll right 0 transparent;padding:5px 10px;}
		.list-table th.last, .list-table td.last, .table-order th.last, .table-order td.last {background:none;}
		.list-table thead tr{background-color:#E6EEEE;}
	</style>
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script> 
	<?php
        wp_enqueue_script('weontech-facebox-js-script');
        wp_enqueue_style('weontech-facebox-css-script');
    ?>    
	<script>	
		function checkSubmit() 
		{			
			jQuery.get("<?php echo site_url() ?>/wp-content/plugins/<?php echo basename(dirname(__FILE__)) ?>/weontech_proxy.php", { key: $("#weontech_key").val() }, function(data) {
					if (data.success) {		
						var services_bookmark = [];
                        $("input[name='services_bookmark[]']:checked").each(function() {
                            services_bookmark.push(this.value);
                        });
                        $("#weontech_services_bookmark").val(services_bookmark);
						var services_blog = [];
                        $("input[name='services_blog[]']:checked").each(function() {
                            services_blog.push(this.value);
                        });
                        $("#weontech_services_blog").val(services_blog);						
                        document.getElementById("wot_form").submit();
						return true;					
					}
					else {
						alert(data.error);
						return false;
					}
				}, "json");            
		}
		function getServices(obj) {
			var p = $(obj).val();
			var key = $("#weontech_key").val();
			if (p != null) {
				jQuery.get("<?php echo site_url() ?>/wp-content/plugins/<?php echo basename(dirname(__FILE__)) ?>/weontech_proxy.php", { f: "services", key: key, p: p }, function(data) {
					if (data.success) {						
						var html = "<div style='padding-bottom:20px;'><b>Services to bookmark</b>: <a href='javascript://' onclick='CheckAll(this, true);'>Select All</a> | <a href='javascript://' onclick='CheckAll(this, false);'>Deselect All</a>";
						html += "<ul class='list-choose'>";
						for (var i = 0; i < data.bookmarks.length; i++) {
							html += "<li><input type='checkbox' id='services_bookmark' name='services_bookmark[]' value='" + data.bookmarks[i].id + "' /> <img src='" + data.bookmarks[i].image + "' /> <a target='_blank' href='" + data.bookmarks[i].url + "'>" + data.bookmarks[i].name + "</a></li>";
						}
						html += "</ul></div>";
						html += "<div><b>Services to blog</b>: <a href='javascript://' onclick='CheckAll(this, true);'>Select All</a> | <a href='javascript://' onclick='CheckAll(this, false);'>Deselect All</a>";
						html += "<ul class='list-choose'>";
						for (var i = 0; i < data.blogs.length; i++) {
							html += "<li><input type='checkbox' id='services_blog' name='services_blog[]' value='" + data.blogs[i].id + "' /> <img src='" + data.blogs[i].image + "' /> <a target='_blank' href='" + data.blogs[i].url + "'>" + data.blogs[i].name + "</a></li>";
						}
						html += "</ul></div>";
						$('#listSocial').html(html);						
					}
					else {
						$('#listSocial').html("You currently do not have any networks for this project.<br />Click below to add networks now.<br /><a class='submit' href='http://www.weontech.com/services.aspx?p=" + p + "'>Set Up Services</a>");						
					}
				}, "json");
			}
			else {
				$('#listSocial').html("You currently do not have any networks for this project.<br />Click below to add networks now.<br /><a class='submit' href='http://www.weontech.com/services.aspx?p=" + p + "'>Set Up Services</a>");				
			}
		}
		function CheckAll(obj, val) {
			var array = obj.parentNode.getElementsByTagName("input");
			for (var i = 0; i < array.length; i++) {
				array[i].checked = val;
			}
		}
		function DetailReport(val)
		{
			$.facebox.settings.modal = false;
			$.facebox.settings.loadingImage = '/wp-content/plugins/<?php echo basename(dirname(__FILE__)) ?>/facebox/loading.gif';
			$.facebox.settings.closeImage = '/wp-content/plugins/<?php echo basename(dirname(__FILE__)) ?>/facebox/closelabel.png';
			$.facebox(function() {
				jQuery.get("<?php echo site_url() ?>/wp-content/plugins/<?php echo basename(dirname(__FILE__)) ?>/weontech_proxy.php", { f: "report", key: "<?php echo get_option('weontech_key') ?>", p: val }, function(data) {
					if (data.success) {						
						var html = "";
						for (var i = 0; i < data.results.length; i++) {
							html += "<div><a href='" + data.results[i].link + "' target='_blank'>" + data.results[i].link + "</a></div>";
						}						
						$.facebox(html);
					}
					else {
						$.facebox("<div style='color:red;text-align:center;padding:10px;'>" + data.error + "</div>");
					}
				}, "json");
			});
		}
		function changeSpinner(obj)
		{
			if (obj.value == "") {
				return;
			}
			$.facebox.settings.loadingImage = '/wp-content/plugins/<?php echo basename(dirname(__FILE__)) ?>/facebox/loading.gif';
			$.facebox.settings.closeImage = '/wp-content/plugins/<?php echo basename(dirname(__FILE__)) ?>/facebox/closelabel.png';
			$.facebox(function() {        
				jQuery.get("<?php echo site_url() ?>/wp-content/plugins/<?php echo basename(dirname(__FILE__)) ?>/weontech_proxy.php", { f: "spinner", key: "<?php echo get_option('weontech_key') ?>", s: obj.value }, function(data) {
					if (data.setting) {
						$.facebox.settings.modal = true;
						$.facebox("<iframe scrolling='no' frameborder='0' width='550px' height='330px' marginwidth='0' src='http://www.weontech.com/spinnersetting.aspx?key=<?php echo get_option('weontech_key') ?>&t=" + obj.value + "' />");
					}
					else {
						$.facebox.close();
					}
				}, "json");        
			});
		}
		jQuery(document).ready(function() {
			jQuery(".wot_tab_content").hide(); //Hide all content
			jQuery("ul.wot_tabs > li:first-child").addClass("active").show(); //Activate first tab
			jQuery(".wot_tab_container > .wot_tab_content:first-child").show(); //Show first tab content

			//On Click Event
			jQuery("ul.wot_tabs li").click(function() {
			  jQuery(this).parent().children("li").removeClass("active"); //Remove any "active" class
			  jQuery(this).addClass("active"); //Add "active" class to selected tab
			  jQuery(this).parent().parent().children(".wot_tab_container").children(".wot_tab_content").hide(); //Hide all tab content    
			  var activeTab = jQuery(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
			  jQuery(activeTab).show(); //Fade in the active ID content
			  return false;
			});
		});
	</script>
	<div class="wrap wot-wrap">		
		<div class="wot-header">
			<a href="<?php echo WOT_URL; ?>" target="_blank">
				<img alt="www.WeOnTech.com - Content Distribution Platform - 1 Click Posts It All" src="https://lh5.googleusercontent.com/-i7dooZvYwKo/UMswxLiH2UI/AAAAAAAAAZA/Aswa4dhOx4o/w249-h66-no/WeOnTech.png" />
			</a><br />
			<?php			
            $userInfo = "";
			$projects = "";			
			$key = get_option('weontech_key');
            if ($key  != "")
            {
                $userInfo = json_decode(wot_getInfo($key));
                if ($userInfo->success)
                {
                    ?>
					<div class="wot-info">Today Posts Usage: <span style="color: #666666;"><?php echo $userInfo->today_post_used ?> / <?php echo $userInfo->daily_post_limit ?></span></div>    
                    <?php
                }				
            }
            ?>			
		</div>
		<h1>WeOnTech: Auto Social Poster Options</h1>
		<p>The WeOnTech Auto Social Poster WordPress Plugin is the easiest way to integrate your WordPress blog with your WeOnTech account. It's the easiest way to automatically promote your blog posts to the top social networks.</p>
		You can find your key on this page: <a href="http://www.weontech.com/profile.aspx" target="_blank">http://www.weontech.com/profile.aspx</a>
		<br /><br />
		<?php if($userInfo == "") { ?>	
			<div class="wot-message">
                To start using this plugin, please enter your WeOnTech key in the fields below and save changes.
			</div>
		<?php } else if (!$userInfo->success) { ?>
			<div class="wot-message">								
				<?php echo $userInfo->error; ?>
			</div>
		<?php } else  {										
			$selProject = get_option('weontech_project');			
			$projects = json_decode(wot_getProjects($key));		
			if($projects == "") { ?>	
				<div class="wot-message">
					To start using this plugin, please enter your WeOnTech key in the fields below and save changes.
				</div>
			<?php } else if(!$projects->success) { ?>
				<div class="wot-message">					
					<?php echo $projects->error; ?>
				</div>
			<?php } ?>
		<?php } ?>
		<ul class="wot_tabs">
			<li><a href="#wot_tab1">Settings</a></li>
			<li><a href="#wot_tab2">Reports/History</a></li>			
		</ul>
		<div class="wot_tab_container">
		<div id="wot_tab1" class="wot_tab_content">
		<form id="wot_form" method="post" action="options.php" onSubmit="checkSubmit(); return false;">
			<?php wp_nonce_field('update-options'); ?>			
			<table class="form-table">						
				<tr>
					<th><label for="weontech_key">Enter Your Key:</label></th>
					<td>
						<input type="text" class="regular-text code" value="<?php echo $key; ?>" id="weontech_key" name="weontech_key" />							
					</td>
				</tr>	
				<?php if($projects->success) { ?>				
				<tr>
					<th><label for="weontech_project">Project to post:</label></th>
					<td>
						<select id="weontech_project" name="weontech_project" onchange="getServices(this);">							
							<?php foreach ($projects->projects as $project) { ?>
							<option value="<?php echo $project->id; ?>"<?php if ($selProject == $project->id) echo " selected";?>><?php echo $project->name; ?></option>
							<?php } ?>
						</select>						
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div id="listSocial">
						<?php
						if($selProject == "") 
						{ ?>
							<script type="text/javascript">getServices($("#weontech_project"));</script>
						<?php 
						}  
						else 
						{
							$services = json_decode(wot_getServices($key, $selProject));						
							if($services->success) 
							{ ?>						
								<div style="padding-bottom:20px;"><b>Services to bookmark</b>: <a href="javascript://" onclick="CheckAll(this, true);">Select All</a> | <a href="javascript://" onclick="CheckAll(this, false);">Deselect All</a>
								<ul class="list-choose">
								<?php foreach ($services->bookmarks as $bookmark) { ?>
									<li>
										<input type="checkbox" id="services_bookmark" name="services_bookmark[]" 
										<?php
										$temp = explode(",", get_option('weontech_services_bookmark'));
										if (in_array($bookmark->id, $temp))
										{
											echo " checked ";
										}
										?>
										value="<?php echo $bookmark->id; ?>" />
										<img src="<?php echo $bookmark->image; ?>" /> 
										<a href="<?php echo $bookmark->url; ?>" target="_blank"><?php echo $bookmark->name; ?></a>
									</li>
								<?php } ?>
								</ul>
								</div>
								<div><b>Services to blog</b>: <a href="javascript://" onclick="CheckAll(this, true);">Select All</a> | <a href="javascript://" onclick="CheckAll(this, false);">Deselect All</a>
								<ul class="list-choose">
								<?php foreach ($services->blogs as $blog) { ?>
									<li>
										<input type="checkbox" id="services_blog" name="services_blog[]" 
										<?php
										$temp = explode(",", get_option('weontech_services_blog'));
										if (in_array($blog->id, $temp))
										{
											echo " checked ";
										}
										?>
										value="<?php echo $blog->id; ?>" />
										<img src="<?php echo $blog->image; ?>" /> 
										<a href="<?php echo $blog->url; ?>" target="_blank"><?php echo $blog->name; ?></a>
									</li>
								<?php } ?>
								</ul>
								</div>
							<?php } else { echo $services->error; } ?>
						<?php }  ?>
						</div>
					</td>
				</tr>
				<tr>
					<th><label for="weontech_spin">Auto spin with:</label></th>
					<td>
						<select id="weontech_spin" name="weontech_spin" onchange="changeSpinner(this);">			
							<option value="">None</option>
							<option value="1" <?php if(get_option('weontech_spin') == 1) echo "selected"; ?>>The Best Spinner</option>
							<option value="2" <?php if(get_option('weontech_spin') == 2) echo "selected"; ?>>Spin Rewriter</option>														
						</select>						
						<i class="small-message">Make sure you have already set up spinner information at <a target="_blank" href="http://www.weontech.com/profile.aspx">www.WeOnTech.com</a></i>
					</td>
				</tr>
				<?php } ?>
			</table>
			<input type="hidden" name="weontech_services_bookmark" id="weontech_services_bookmark" value="" />
			<input type="hidden" name="weontech_services_blog" id="weontech_services_blog" value="" />
			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="page_options" value="weontech_key,weontech_project,weontech_spin,weontech_services_bookmark,weontech_services_blog" />
			<?php if($userInfo == "" or $userInfo->success == false) { ?>	
			<p class="submit"><input type="submit" value="Save Changes" class="button button-primary"></p>
			<?php } else { ?>
			<p class="submit"><input type="submit" value="Update Options" class="button button-primary"></p>
			<?php } ?>
		</form>
		</div>
		<div id="wot_tab2" class="wot_tab_content">
			<table class="list-table">
				<thead>
					<tr>                 
						<th>Date time</th>
                        <th>Title</th>                                
                        <th>Type</th>
                        <th class="last">Action</th>
                    </tr> 
                </thead>
				<?php
				global  $wpdb;
				$table_logs = $wpdb->prefix . 'weontech_logs';
				$sql="SELECT * FROM $table_logs ORDER BY log_id DESC LIMIT 100";
				$rows = $wpdb->get_results($sql);
				$index = 0;
				foreach($rows as $row):
					$color_class = "";
					if($index%2==0)
					{
						$color_class = "on";
					}				
					$index = $index + 1;
				?>
				<tr class="<?php echo $color_class; ?>">
					<td><?php echo $row->log_datetime; ?></td>
					<td><?php echo $row->post_title; ?></td>
					<td><?php echo $row->post_type; ?></td>
					<td class="last"><a href="javascript://" onclick="DetailReport(<?php echo $row->post_id; ?>)" style="margin-right:10px;">Result</a></td>
				</tr>
				<?php endforeach;?>
			</table>
		</div>
		</div>
	</div>
	<?php
}
?>