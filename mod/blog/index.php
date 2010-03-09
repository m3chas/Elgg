<?php

/**
 * Elgg blog index page
 */

// Load Elgg engine
require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
		
// Get the current page's owner
$page_owner = page_owner_entity();
if ($page_owner === false || is_null($page_owner)) {
	$page_owner = $_SESSION['user'];
	set_page_owner($_SESSION['guid']);
}

//set breadcrumbs if in a group
//$area1 = elgg_view('elggcampus_layout/breadcrumbs_general', array('object_type' => 'blog'));
	
//set blog header
if(page_owner()== get_loggedin_user()->guid){
	$area1 .= elgg_view('blog/blog_header', array('context' => "own", 'type' => 'blog'));
}elseif($page_owner instanceof ElggGroup){
	$area1 .= elgg_view('groups/blog_header_group');
}else{
	$area1 .= elgg_view('blog/blog_header_visit', array('type' => 'blog'));
}
		
// Get a list of blog posts
set_context('search');
$blogs = list_user_objects($page_owner->getGUID(),'blog',10,false, false);
if(!$blogs && ($page_owner->guid == get_loggedin_user()->guid))
	$blogs = elgg_view('help/blogs');
$area2 = "<div id=\"blogs\">" . $blogs  . "<div class='clearfloat'></div></div>";
set_context('blog');
//if the logged in user is not looking at their stuff, display the ownerblock otherwise
//show the users favourites
if(page_owner()	!= get_loggedin_user()->guid){
	$area3 = elgg_view('blog/ownerblock');
}else{	
	//a view for the favourites plugin to extend
	//$area3 .= elgg_view("blogs/favourite", array("object_type" => 'blog'));
}
//get the latest comments on user's blog posts
$comments = get_annotations(0, "object", "blog", "generic_comment", "", 0, 4, 0, "desc",0,0,page_owner());
$area3 .= elgg_view('page_elements/latest_comments', array('comments' => $comments));
//a view for the favourites plugin to extend
$area3 .= elgg_view("blogs/sidebar_options", array("object_type" => 'blog'));
//display archive
$area3 .= elgg_view("blog/archive");
// Display them in the page
$body = elgg_view_layout("one_column_with_sidebar", $area1.$area2, $area3);
	
// Display page
page_draw(sprintf(elgg_echo('blog:user'),$page_owner->name),$body);