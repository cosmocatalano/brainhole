<?php
	
//control variables for your embedding pleasure
$stories = 3; //number of stories
$image_width = 'w_100'; //image width in pixels
$image_height = 'h_100'; //image height in pixels
$image_contain = 'c_fill'; //other options at https://cloudinary.com/documentation/image_transformations#resizing_and_cropping_images
$image_quality = 'q_64'; //see above
$image_prog = 'fl_progressive';


//get cloudinary image of desired size
function hack_image($desc_array, $img_w, $img_h, $img_c, $img_q, $img_p) {
	$img_shard = explode('img src="', $desc_array[0]);
	$img_url = explode('"/>',$img_shard[1]);
	$img_slug = explode('c_fit', $img_url[0]);
	$img_id = explode('w_636', $img_url[0]);
	return $img_slug[0].$img_c.','.$img_p.','.$img_w.','.$img_h.$img_id[1];
}

//replacing the Google Analytics info so they know where it came from
function retag_link($url) {
	$new_url = str_replace('=RSS&', '=brainholewidget&', $url);
	$final_url = str_replace('=feeds', '=brainhole', $new_url);
	return $final_url;
}

//let's get three random items; I'm pretty sure this is how Outbrain's algorithm works
$story_index = range(0, 19);
shuffle($story_index);
$selected = array_slice($story_index, 0, $stories);

//converting the RSS feed to an array -- http://stackoverflow.com/questions/2454979/get-rss-feed-into-php-array-possible/2455028#2455028
$feed = 'http://www.clickhole.com/feeds/rss';
$feed_to_array = (array) simplexml_load_file($feed, 'SimpleXMLElement', LIBXML_NOCDATA);

//selecting the data for the three random articles
$articles = array();
foreach($selected as $selector) {
	array_push($articles, $feed_to_array[channel]->item[$selector]);
}


//turn that crank
//this currently generates unstyled HTML. You might want to do something different with it.
foreach ($articles as $article) {
	$image = hack_image($article->description, $image_width, $image_height, $image_contain, $image_quality, $image_prog);
	$link = retag_link($article->link);
	$title = $article->title;
	$linked_image = '<a href="'.$link.'"><img src="'.$image.'"></a>';
	$linked_title = '<a href="'.$link.'"><span>'.mb_convert_encoding($title, "HTML-ENTITIES", "UTF-8").'</span></a>';
	echo $linked_image.$linked_title.'<br/><br/>';
}

?>
