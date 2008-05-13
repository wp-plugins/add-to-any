<?
/*
Plugin Name: Add to Any: Share/Save/Bookmark Button
Plugin URI: http://www.addtoany.com/
Description: Lets readers share, save, and bookmark your posts and pages using any service.  [<a href="options-general.php?page=add-to-any.php">Settings</a>]
Version: .9.2
Author: MicroPat
Author URI: http://www.addtoany.com/contact/
*/


// Returns the utf string corresponding to the unicode value (from php.net, courtesy - romans@void.lv)
if (!function_exists('A2A_SHARE_SAVE_code2utf')) {
	function A2A_SHARE_SAVE_code2utf($num)
	{
		if ($num < 128) return chr($num);
		if ($num < 2048) return chr(($num >> 6) + 192) . chr(($num & 63) + 128);
		if ($num < 65536) return chr(($num >> 12) + 224) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
		if ($num < 2097152) return chr(($num >> 18) + 240) . chr((($num >> 12) & 63) + 128) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
		return '';
	}
}
// Since UTF-8 does not work in PHP4 ( http://us2.php.net/manual/en/function.html-entity-decode.php ) :
if (!function_exists('A2A_SHARE_SAVE_html_entity_decode_utf8')) {
	function A2A_SHARE_SAVE_html_entity_decode_utf8($string)
	{
		static $trans_tbl;
	   
		// replace numeric entities
		$string = preg_replace('~&#x([0-9a-f]+);~ei', 'A2A_SHARE_SAVE_code2utf(hexdec("\\1"))', $string);
		$string = preg_replace('~&#([0-9]+);~e', 'A2A_SHARE_SAVE_code2utf(\\1)', $string);
	
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

function ADDTOANY_SHARE_SAVE_BUTTON($output_buffering=false) {
	
	if($output_buffering)ob_start();
	
	$sitename_enc	= rawurlencode( get_bloginfo('name') );
	$siteurl_enc	= rawurlencode( trailingslashit( get_bloginfo('url') ) );
	$linkname		= A2A_SHARE_SAVE_html_entity_decode_utf8( get_the_title() );
	$linkname_enc	= rawurlencode( $linkname );
	$linkurl		= get_permalink($post->ID);
	$linkurl_enc	= rawurlencode( $linkurl );
	
	if( !get_option('A2A_SHARE_SAVE_button') ) {
		$button_fname	= 'share_save_120_16.gif';
		$button_width	= '120';
		$button_height	= "16";
	} else {
		$button_attrs	= explode( '|', get_option('A2A_SHARE_SAVE_button') );
		$button_fname	= $button_attrs[0];
		$button_width	= $button_attrs[1];
		$button_height	= $button_attrs[2];
	}
	if( $button_attrs[0] == 'favicon.png' ) {
		$style_bg		= 'background:url('.trailingslashit(get_option('siteurl')).PLUGINDIR.'/add-to-any/'.$button_fname.') no-repeat scroll 0px 0px';
		$style_bg		= ';' . $style_bg . ' !important;';
		$style			= ' style="'.$style_bg.'padding:1px 5px 5px 22px"';
		$button			= 'Share/Save';
	} else 
		$button			= '<img src="'.trailingslashit(get_option('siteurl')).PLUGINDIR.'/add-to-any/'.$button_fname.'" width="'.$button_width.'" height="'.$button_height.'" border="0" alt="Share/Save/Bookmark"/>';
	?>

    <a name="a2a_dd" onmouseover="a2a_show_dropdown(this)" onmouseout="a2a_onMouseOut_delay()" href="http://www.addtoany.com/bookmark?sitename=<?=$sitename_enc?>&amp;siteurl=<?=$siteurl_enc?>&amp;linkname=<?=$linkname_enc?>&amp;linkurl=<?=$linkurl_enc?>"<?=$style?>><?=$button?></a>
    <script type="text/javascript">
		a2a_linkname="<?=str_replace('"', '\\"', $linkname)?>";
		a2a_linkurl="<?=$linkurl?>";
		<?=(get_option('A2A_SHARE_SAVE_hide_embeds')=='-1') ? 'a2a_hide_embeds=0;' : '' ?>
		<?=(get_option('A2A_SHARE_SAVE_show_title')=='1') ? 'a2a_show_title=1;' : '' ?>
    </script>
    <script type="text/javascript" src="http://www.addtoany.com/menu/page.js"></script>

	<?
	if($output_buffering) {
		$button = ob_get_contents();
		ob_end_clean();
		return $button;
	}
}

function A2A_SHARE_SAVE_to_bottom_of_content($content) {
	if ( 
		( (strpos($content, '<!--sharesave-->')===false) ) && (														// <!--sharesave-->
			( !is_page() && get_option('A2A_SHARE_SAVE_display_in_posts')=='-1' ) || 								// All posts
			( !is_page() && !is_single() && get_option('A2A_SHARE_SAVE_display_in_posts_on_front_page')=='-1' ) ||  // Front page posts
			( is_page() && get_option('A2A_SHARE_SAVE_display_in_pages')=='-1' ) ||									// Pages
			( (strpos($content, '<!--nosharesave-->')!==false ) )													// <!--nosharesave-->
		)
	)	
		return $content;
	
	$content .= '<p class="addtoany_share_save">'.ADDTOANY_SHARE_SAVE_BUTTON(true).'</p>';
	return $content;
}

add_action('the_content', 'A2A_SHARE_SAVE_to_bottom_of_content');






/*****************************
		OPTIONS
******************************/


function A2A_SHARE_SAVE_options_page() {

    if( $_POST[ 'A2A_SHARE_SAVE_submit_hidden' ] == 'Y' ) {

        update_option( 'A2A_SHARE_SAVE_display_in_posts_on_front_page', ($_POST['A2A_SHARE_SAVE_display_in_posts_on_front_page']=='1') ? '1':'-1' );
		update_option( 'A2A_SHARE_SAVE_display_in_posts', ($_POST['A2A_SHARE_SAVE_display_in_posts']=='1') ? '1':'-1' );
		update_option( 'A2A_SHARE_SAVE_display_in_pages', ($_POST['A2A_SHARE_SAVE_display_in_pages']=='1') ? '1':'-1' );
		update_option( 'A2A_SHARE_SAVE_hide_embeds', ($_POST['A2A_SHARE_SAVE_hide_embeds']=='1') ? '1':'-1' );
		update_option( 'A2A_SHARE_SAVE_show_title', ($_POST['A2A_SHARE_SAVE_show_title']=='1') ? '1':'-1' );
		update_option( 'A2A_SHARE_SAVE_button', $_POST['A2A_SHARE_SAVE_button'] );
		
		?>
    	<div class="updated fade"><p><strong><?php _e('Settings saved.', 'A2A_SHARE_SAVE_trans_domain' ); ?></strong></p></div>
		<?
		
    }

    ?>
    
    <div class="wrap">

	<h2><?=__( 'Add to Any: Share/Save Settings', 'A2A_SHARE_SAVE_trans_domain' )?></h2>

    <form method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
    
	<?php wp_nonce_field('update-options'); ?>
    
    	<input type="hidden" name="A2A_SHARE_SAVE_submit_hidden" value="Y">
    
        <table class="form-table">
        	<tr valign="top">
            <th scope="row">Button</th>
            <td>
            	<label>
                	<input name="A2A_SHARE_SAVE_button" value="favicon.png|16|16" type="radio"<? if(get_option('A2A_SHARE_SAVE_button')=='favicon.png|16|16') echo ' checked="checked"'; ?>
                    	 style="margin:9px 0">
                    <img src="<?=trailingslashit(get_option('siteurl')).PLUGINDIR.'/add-to-any/favicon.png'?>" width="16" height="16" border="0" style="padding:9px" alt="+ Share/Save" title="+ Share/Save"
                    	onclick="this.parentNode.firstChild.checked=true"/>
                </label><br>
                <label>
                	<input name="A2A_SHARE_SAVE_button" value="share_save_120_16.gif|120|16" type="radio"<? if( !get_option('A2A_SHARE_SAVE_button') || get_option('A2A_SHARE_SAVE_button' )=='share_save_120_16.gif|120|16') echo ' checked="checked"'; ?>
                    	style="margin:9px 0">
                    <img src="<?=trailingslashit(get_option('siteurl')).PLUGINDIR.'/add-to-any/share_save_120_16.gif'?>" width="120" height="16" border="0" style="padding:9px"
                    	onclick="this.parentNode.firstChild.checked=true"/>
                </label><br>
                <label>
                	<input name="A2A_SHARE_SAVE_button" value="share_save_171_16.gif|171|16" type="radio"<? if(get_option('A2A_SHARE_SAVE_button')=='share_save_171_16.gif|171|16') echo ' checked="checked"'; ?>
                    	style="margin:9px 0">
                    <img src="<?=trailingslashit(get_option('siteurl')).PLUGINDIR.'/add-to-any/share_save_171_16.gif'?>" width="171" height="16" border="0" style="padding:9px"
                    	onclick="this.parentNode.firstChild.checked=true"/>
                </label><br>
                <label>
                	<input name="A2A_SHARE_SAVE_button" value="share_save_256_24.gif|256|24" type="radio"<? if(get_option('A2A_SHARE_SAVE_button')=='share_save_256_24.gif|256|24') echo ' checked="checked"'; ?>
                    	style="margin:9px 0">
                    <img src="<?=trailingslashit(get_option('siteurl')).PLUGINDIR.'/add-to-any/share_save_256_24.gif'?>" width="256" height="24" border="0" style="padding:9px"
                    	onclick="this.parentNode.firstChild.checked=true"/>
				</label>
            </td>
            </tr>
            <tr valign="top">
            <th scope="row">Placement</th>
            <td>
                <label>
                	<input name="A2A_SHARE_SAVE_display_in_posts" 
                    	onclick="e=getElementsByName('A2A_SHARE_SAVE_display_in_posts_on_front_page')[0];if(!this.checked){e.checked=false;e.disabled=true}else{e.checked=true;e.disabled=false}"
                        onchange="e=getElementsByName('A2A_SHARE_SAVE_display_in_posts_on_front_page')[0];if(!this.checked){e.checked=false;e.disabled=true}else{e.checked=true;e.disabled=false}"
                        type="checkbox"<? if(get_option('A2A_SHARE_SAVE_display_in_posts')!='-1') echo ' checked="checked"'; ?> value="1"/>
                	Display Share/Save button at the bottom of posts <strong>*</strong>
                </label><br/>
                <label>
                	&nbsp; &nbsp; &nbsp; <input name="A2A_SHARE_SAVE_display_in_posts_on_front_page" type="checkbox"<? 
						if(get_option('A2A_SHARE_SAVE_display_in_posts_on_front_page')!='-1') echo ' checked="checked"';
						if(get_option('A2A_SHARE_SAVE_display_in_posts')=='-1') echo ' disabled="disabled"';
						?> value="1"/>
                    Display Share/Save button at the bottom of posts on the front page
				</label><br/>
                <label>
                	<input name="A2A_SHARE_SAVE_display_in_pages" type="checkbox"<? if(get_option('A2A_SHARE_SAVE_display_in_pages')!='-1') echo ' checked="checked"'; ?> value="1"/>
                    Display Share/Save button at the bottom of pages <strong>*</strong>
				</label>
                
                <br/><br/>
                <strong>*</strong> If unchecked, be sure to place the following code in <a href="theme-editor.php">your template pages</a> (within <code>index.php</code>, <code>single.php</code>, and/or <code>page.php</code>):<br/>
                <code>&lt;? if( function_exists('ADDTOANY_SHARE_SAVE_BUTTON') ) { ADDTOANY_SHARE_SAVE_BUTTON(); } ?&gt;</code>
            </td>
            </tr>
            <tr valign="top">
            <th scope="row">Options</th>
            <td>
            	<label>
                	<input name="A2A_SHARE_SAVE_hide_embeds" 
                        type="checkbox"<? if(get_option('A2A_SHARE_SAVE_hide_embeds')!='-1') echo ' checked="checked"'; ?> value="1"/>
                	Hide embedded objects (Flash, video, etc.) when the menu is displayed
                </label><br />
                <label>
                	<input name="A2A_SHARE_SAVE_show_title" 
                        type="checkbox"<? if(get_option('A2A_SHARE_SAVE_show_title')=='1') echo ' checked="checked"'; ?> value="1"/>
                	Show the title of the post within the menu
                </label>
            </td>
            </tr>
        </table>
        
        <p class="submit">
            <input type="submit" name="Submit" value="<?php _e('Save Changes', 'A2A_SHARE_SAVE_trans_domain' ) ?>" />
        </p>
    
    </form>
    </div>

<?
 
}

function A2A_SHARE_SAVE_add_menu_link() {
	if( current_user_can('manage_options') ) {
		add_options_page(
			'Add to Any: Share/Save Settings'
			, 'Share/Save Buttons'
			, 8 
			, basename(__FILE__)
			, 'A2A_SHARE_SAVE_options_page'
		);
	}
}

add_action('admin_menu', 'A2A_SHARE_SAVE_add_menu_link');



?>