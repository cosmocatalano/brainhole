<?php
	
//control variables for your embedding pleasure
$stories = 3; //number of stories
$image_dimension = '1x1'; //dimensions of image, string only (bummer); '16x9' also works
$image_width = 100; //image width in pixels

//breaks image url out of partial Clickhole page 
function get_og($html) {
	$og_explode = explode('"og:image" content="', $html);
	$og_url = explode('"/>', $og_explode[1]);
	$og_image_url = $og_url[0];
	return $og_image_url;
}

//changes og:image to your specified dimensions and size
function set_image($img_url, $image_dimension, $image_width) {
	$url_shards = explode('/', $img_url);
	$url_shards[4] = $image_dimension;
	$url_shards[5] = $image_width;
	$new_url = "http://chimg.onionstatic.com/".$url_shards[3].'/'.$url_shards[4].'/'.$url_shards[5].'.jpg';
	return $new_url;
}
//Grabbing part of the target page to get the og:image URL; chose 2048 bytes arbitrarily,
//getting the og:image url, changing it to our desired size
function get_image($link, $image_dimension, $image_width) {
	$target_page = file_get_contents($link, NULL, NULL, 0, 2000);
	$image_url = get_og($target_page);
	$new_image = set_image($image_url, $image_dimension, $image_width);
	return $new_image;
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
$feed_to_array = (array) simplexml_load_file($feed);

//selecting the data for the three random articles
$articles = array();
foreach($selected as $selector) {
	array_push($articles, $feed_to_array[channel]->item[$selector]);
}

//turn that crank
//this currently generates unstyled HTML. You might want to do something different with it.
foreach ($articles as $article) {
	$link = retag_link($article->link);
	$image = get_image($link, $image_dimension, $image_width);
	$title = $article->title;
	$linked_image = '<a href="'.$link.'"><img src="'.$image.'"></a>';
	$linked_title = '<a href="'.$link.'"><span>'.mb_convert_encoding($title, "HTML-ENTITIES", "UTF-8").'</span></a>';
	echo $linked_image.$linked_title.'<br/><br/>';
}

?>
