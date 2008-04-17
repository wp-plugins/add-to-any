<?
/*
Plugin Name: Add to Any: Share/Save/Bookmark Button
Plugin URI: http://www.addtoany.com/buttons/
Description: Lets readers share, save and bookmark your posts using any service and browser.
Version: .7
Author: MicroPat
Author URI: http://www.addtoany.com/contact/
*/


// Returns the utf string corresponding to the unicode value (from php.net, courtesy - romans@void.lv)
if (!function_exists('add_to_any_code2utf')) {
	function add_to_any_code2utf($num)
	{
		if ($num < 128) return chr($num);
		if ($num < 2048) return chr(($num >> 6) + 192) . chr(($num & 63) + 128);
		if ($num < 65536) return chr(($num >> 12) + 224) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
		if ($num < 2097152) return chr(($num >> 18) + 240) . chr((($num >> 12) & 63) + 128) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
		return '';
	}
}
// Since UTF-8 does not work in PHP4 ( http://us2.php.net/manual/en/function.html-entity-decode.php ) :
if (!function_exists('add_to_any_html_entity_decode_utf8')) {
	function add_to_any_html_entity_decode_utf8($string)
	{
		static $trans_tbl;
	   
		// replace numeric entities
		$string = preg_replace('~&#x([0-9a-f]+);~ei', 'add_to_any_code2utf(hexdec("\\1"))', $string);
		$string = preg_replace('~&#([0-9]+);~e', 'add_to_any_code2utf(\\1)', $string);
	
		// replace literal entities
		if (!isset($trans_tbl))
		{
			$trans_tbl = array();
		   
			foreach (get_html_translation_table(HTML_ENTITIES) as $val=>$key)
				$trans_tbl[$key] = utf8_encode($val);
		}
	   
		return strtr($string, $trans_tbl);
	}
}

function add_to_any_link() {
	ob_start();
	
	$sitename_enc	= rawurlencode( get_bloginfo('name') );
	$siteurl_enc	= rawurlencode( trailingslashit( get_bloginfo('url') ) );
	$linkname		= add_to_any_html_entity_decode_utf8( get_the_title() );
	$linkname_enc	= rawurlencode( $linkname );
	$linkurl		= get_permalink($post->ID);
	$linkurl_enc	= rawurlencode( $linkurl );
?>

<a name="a2a_dd" onmouseover="a2a_show_dropdown(this)" onmouseout="a2a_onMouseOut_delay()" href="http://www.addtoany.com/bookmark?sitename=<?=$sitename_enc?>&amp;siteurl=<?=$siteurl_enc?>&amp;linkname=<?=$linkname_enc?>&amp;linkurl=<?=$linkurl_enc?>">
	<img src="<?=trailingslashit(get_option('siteurl')).PLUGINDIR.'/add-to-any/share_save_120_16.gif'?>" width="120" height="16" border="0" alt="Share/Save/Bookmark"/>
</a>
<script type="text/javascript">a2a_linkname="<?=str_replace('"', '\\"', $linkname)?>";a2a_linkurl="<?=$linkurl?>";</script>
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