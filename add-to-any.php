<?
/*
Plugin Name: Add to Any: Bookmark Button
Plugin URI: http://www.addtoany.com/
Description: Lets readers bookmark your posts using any bookmark manager.
Version: .4
Author: MicroPat
Author URI: http://www.addtoany.com/contact/
*/

function add_to_any_link() {
	ob_start();
?>
<a href="http://www.addtoany.com/?sitename=<? bloginfo('name'); ?>&amp;siteurl=<? bloginfo('siteurl'); ?>&amp;linkname=<? the_title(); ?>&amp;linkurl=<? echo get_permalink($id); ?>&amp;type=page"><img src="http://www.addtoany.com/bookmark.gif" width="91" height="17" border="0" title="Add to any service" alt="Add to any service"/></a>
<?
	$link = ob_get_contents();
	ob_end_clean();
	return $link;
}

function a2a_add_to_any_to_content($content) {
	$content .= '<p class="a2a_link">'.add_to_any_link('return').'</p>';
	return $content;
}

add_action('the_content', 'a2a_add_to_any_to_content');
add_action('the_content_rss', 'a2a_add_to_any_to_content');

?>