<?
/*
Plugin Name: Add to Any: Bookmark Button
Plugin URI: http://www.addtoany.com/buttons/
Description: Lets readers share, save and bookmark your posts using any service and browser.
Version: .5
Author: MicroPat
Author URI: http://www.addtoany.com/contact/
*/

function add_to_any_link() {
	ob_start();
	
	$sitename_enc	= rawurlencode( get_bloginfo('name') );
	$siteurl_enc	= rawurlencode( trailingslashit( get_bloginfo('url') ) );
	$linkname		= html_entity_decode( get_the_title() , ENT_COMPAT, 'UTF-8');
	$linkname_enc	= rawurlencode( $linkname );
	$linkurl		= get_permalink($post->ID);
	$linkurl_enc	= rawurlencode( $linkurl );
?>

<a name="a2a_dd" onmouseover="a2a_show_dropdown(this)" onmouseout="a2a_onMouseOut_delay()" href="http://www.addtoany.com/bookmark?sitename=<?=$sitename_enc?>&amp;siteurl=<?=$siteurl_enc?>&amp;linkname=<?=$linkname_enc?>&amp;linkurl=<?=$linkurl_enc?>">
	<img src="<?=trailingslashit(get_option('siteurl')).PLUGINDIR.'/add-to-any/bookmark.gif'?>" width="91" height="16" border="0" alt="Bookmark"/>
</a>
<script type="text/javascript">a2a_linkname="<?=$linkname?>";a2a_linkurl="<?=$linkurl?>";</script>
<script type="text/javascript" src="http://www.addtoany.com/js.dropdown.js?type=page"></script>

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