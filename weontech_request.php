<?php
	define( "WOT_URL" , "http://www.weontech.com/");
		
	function wot_getInfo($key)
	{
		$url = WOT_URL."api/info?key=".$key;
		$ch    = curl_init();

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);		
		curl_setopt($ch, CURLOPT_URL, $url);		

		$output = curl_exec($ch);
		curl_close($ch);
		
		return $output;
	}
	
	function wot_getProjects($key)
	{
		$url = WOT_URL."api/projects?key=".$key;
		$ch    = curl_init();

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);		
		curl_setopt($ch, CURLOPT_URL, $url);		

		$output = curl_exec($ch);
		curl_close($ch);
		
		return $output;
	}
	function wot_getServices($key, $projectId)
	{
		$url = WOT_URL."api/services?key=".$key."&project_id=".$projectId;
		$ch    = curl_init();

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);		
		curl_setopt($ch, CURLOPT_URL, $url);		

		$output = curl_exec($ch);
		curl_close($ch);
		
		return $output;
	}
	function wot_createBookmark($key, $url, $title, $description, $image_url, $tags, $project_id, $service_ids, $spin)
	{
		$query = WOT_URL."api/add/bookmark";		
		$data = http_build_query(array('key' => $key, 'url' => $url, 'title' => $title, 'description' => $description, 'image_url' => $image_url, 'tags' => $tags, 'project_id' => $project_id, 'service_ids' => $service_ids, 'spin' => $spin));
    
		$ch = curl_init();
	    
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_POST, true);		
		curl_setopt($ch, CURLOPT_URL, $query);		
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
		$output = curl_exec($ch);
		curl_close($ch);
	    
		return $output;
	}
	function wot_createBlogPost($key, $title, $content, $tags, $project_id, $service_ids, $spin)
	{
		$query = WOT_URL."api/add/blogpost";			
		$data = http_build_query(array('key' => $key, 'title' => $title, 'content' => $content, 'tags' => $tags, 'project_id' => $project_id, 'service_ids' => $service_ids, 'spin' => $spin));
    
		$ch = curl_init();
	    
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_POST, true);		
		curl_setopt($ch, CURLOPT_URL, $query);		
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
		$output = curl_exec($ch);
		curl_close($ch);
	    
		return $output;
	}
	function wot_getReport($key, $postId)
	{
		$url = WOT_URL."api/report?key=".$key."&post_id=".$postId;
		$ch    = curl_init();

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);		
		curl_setopt($ch, CURLOPT_URL, $url);		

		$output = curl_exec($ch);
		curl_close($ch);
		
		return $output;
	}
	function wot_checkSpinner($key, $spinner)
	{
		$url = WOT_URL."api/spinner?key=".$key."&s=".$spinner;
		$ch    = curl_init();

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);		
		curl_setopt($ch, CURLOPT_URL, $url);		

		$output = curl_exec($ch);
		curl_close($ch);
		
		return $output;
	}	
?>