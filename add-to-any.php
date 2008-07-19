<?php
/*
Plugin Name: Add to Any: Share/Save/Bookmark Button
Plugin URI: http://www.addtoany.com/
Description: Helps readers share, save, and bookmark your posts and pages using any service.  [<a href="options-general.php?page=add-to-any.php">Settings</a>]
Version: .9.4
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

    <a class=".addtoany_share_save" name="a2a_dd" <?php echo (get_option('A2A_SHARE_SAVE_onclick')=='1') ? 'onclick="a2a_show_dropdown(this);return false"' : 'onmouseover="a2a_show_dropdown(this)"'; ?> onmouseout="a2a_onMouseOut_delay()" href="http://www.addtoany.com/bookmark?sitename=<?php echo $sitename_enc; ?>&amp;siteurl=<?php echo $siteurl_enc; ?>&amp;linkname=<?php echo $linkname_enc; ?>&amp;linkurl=<?php echo $linkurl_enc; ?>"<?php echo $style; ?>><?php echo $button; ?></a>
    <script type="text/javascript">
		a2a_linkname="<?php echo str_replace('"', '\\"', $linkname); ?>";
		a2a_linkurl="<?php echo $linkurl; ?>";
		<?php echo (get_option('A2A_SHARE_SAVE_hide_embeds')=='-1') ? 'a2a_hide_embeds=0;' : ''; ?>
		<?php echo (get_option('A2A_SHARE_SAVE_show_title')=='1') ? 'a2a_show_title=1;' : ''; ?>
		<?php echo stripslashes(get_option('A2A_SHARE_SAVE_additional_js_variables')); ?>
    </script>
    <script type="text/javascript" src="http://static.addtoany.com/menu/page.js"></script>

	<?php
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
		update_option( 'A2A_SHARE_SAVE_onclick', ($_POST['A2A_SHARE_SAVE_onclick']=='1') ? '1':'-1' );
		update_option( 'A2A_SHARE_SAVE_button', $_POST['A2A_SHARE_SAVE_button'] );
		update_option( 'A2A_SHARE_SAVE_additional_js_variables', trim($_POST['A2A_SHARE_SAVE_additional_js_variables']) );
		
		?>
    	<div class="updated fade"><p><strong><?php _e('Settings saved.', 'A2A_SHARE_SAVE_trans_domain' ); ?></strong></p></div>
		<?php
		
    }

    ?>
    
    <div class="wrap">

	<h2><?php echo __( 'Add to Any: Share/Save Settings', 'A2A_SHARE_SAVE_trans_domain' ); ?></h2>

    <form method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
    
	<?php wp_nonce_field('update-options'); ?>
    
    	<input type="hidden" name="A2A_SHARE_SAVE_submit_hidden" value="Y">
    
        <table class="form-table">
        	<tr valign="top">
            <th scope="row">Button</th>
            <td><fieldset>
            	<label>
                	<input name="A2A_SHARE_SAVE_button" value="favicon.png|16|16" type="radio"<?php if(get_option('A2A_SHARE_SAVE_button')=='favicon.png|16|16') echo ' checked="checked"'; ?>
                    	 style="margin:9px 0;vertical-align:middle">
                    <img src="<?php echo trailingslashit(get_option('siteurl')).PLUGINDIR.'/add-to-any/favicon.png'; ?>" width="16" height="16" border="0" style="padding:9px;vertical-align:middle" alt="+ Share/Save" title="+ Share/Save"
                    	onclick="this.parentNode.firstChild.checked=true"/>
                </label><br>
                <label>
                	<input name="A2A_SHARE_SAVE_button" value="share_save_120_16.gif|120|16" type="radio"<?php if( !get_option('A2A_SHARE_SAVE_button') || get_option('A2A_SHARE_SAVE_button' )=='share_save_120_16.gif|120|16') echo ' checked="checked"'; ?>
                    	style="margin:9px 0;vertical-align:middle">
                    <img src="<?php echo trailingslashit(get_option('siteurl')).PLUGINDIR.'/add-to-any/share_save_120_16.gif'; ?>" width="120" height="16" border="0" style="padding:9px;vertical-align:middle"
                    	onclick="this.parentNode.firstChild.checked=true"/>
                </label><br>
                <label>
                	<input name="A2A_SHARE_SAVE_button" value="share_save_171_16.gif|171|16" type="radio"<?php if(get_option('A2A_SHARE_SAVE_button')=='share_save_171_16.gif|171|16') echo ' checked="checked"'; ?>
                    	style="margin:9px 0;vertical-align:middle">
                    <img src="<?php echo trailingslashit(get_option('siteurl')).PLUGINDIR.'/add-to-any/share_save_171_16.gif'; ?>" width="171" height="16" border="0" style="padding:9px;vertical-align:middle"
                    	onclick="this.parentNode.firstChild.checked=true"/>
                </label><br>
                <label>
                	<input name="A2A_SHARE_SAVE_button" value="share_save_256_24.gif|256|24" type="radio"<?php if(get_option('A2A_SHARE_SAVE_button')=='share_save_256_24.gif|256|24') echo ' checked="checked"'; ?>
                    	style="margin:9px 0;vertical-align:middle">
                    <img src="<?php echo trailingslashit(get_option('siteurl')).PLUGINDIR.'/add-to-any/share_save_256_24.gif'; ?>" width="256" height="24" border="0" style="padding:9px;vertical-align:middle"
                    	onclick="this.parentNode.firstChild.checked=true"/>
				</label>
            </fieldset></td>
            </tr>
            <tr valign="top">
            <th scope="row">Button Placement</th>
            <td><fieldset>
                <label>
                	<input name="A2A_SHARE_SAVE_display_in_posts" 
                    	onclick="e=getElementsByName('A2A_SHARE_SAVE_display_in_posts_on_front_page')[0];if(!this.checked){e.checked=false;e.disabled=true}else{e.checked=true;e.disabled=false}"
                        onchange="e=getElementsByName('A2A_SHARE_SAVE_display_in_posts_on_front_page')[0];if(!this.checked){e.checked=false;e.disabled=true}else{e.checked=true;e.disabled=false}"
                        type="checkbox"<?php if(get_option('A2A_SHARE_SAVE_display_in_posts')!='-1') echo ' checked="checked"'; ?> value="1"/>
                	Display Share/Save button at the bottom of posts <strong>*</strong>
                </label><br/>
                <label>
                	&nbsp; &nbsp; &nbsp; <input name="A2A_SHARE_SAVE_display_in_posts_on_front_page" type="checkbox"<?php 
						if(get_option('A2A_SHARE_SAVE_display_in_posts_on_front_page')!='-1') echo ' checked="checked"';
						if(get_option('A2A_SHARE_SAVE_display_in_posts')=='-1') echo ' disabled="disabled"';
						?> value="1"/>
                    Display Share/Save button at the bottom of posts on the front page
				</label><br/>
                <label>
                	<input name="A2A_SHARE_SAVE_display_in_pages" type="checkbox"<?php if(get_option('A2A_SHARE_SAVE_display_in_pages')!='-1') echo ' checked="checked"'; ?> value="1"/>
                    Display Share/Save button at the bottom of pages <strong>*</strong>
				</label>
                
                <br/><br/>
                <strong>*</strong> If unchecked, be sure to place the following code in <a href="theme-editor.php">your template pages</a> (within <code>index.php</code>, <code>single.php</code>, and/or <code>page.php</code>):<br/>
                <code>&lt;?php if( function_exists('ADDTOANY_SHARE_SAVE_BUTTON') ) { ADDTOANY_SHARE_SAVE_BUTTON(); } ?&gt;</code>
            </fieldset></td>
            </tr>
            <tr valign="top">
            <th scope="row">Menu Style</th>
            <td><fieldset>
                    	Using Add to Any's Menu Styler, you can customize the colors of your Share/Save menu! When you're done, be sure to paste the generated code in the <a href="#" onclick="document.getElementById('A2A_SHARE_SAVE_additional_js_variables').focus();return false">Additional Options</a> box below.
                    <p>
                		<a href="http://www.addtoany.com/buttons/share_save/menu_style/wordpress" class="button-secondary" title="Open the Add to Any Menu Styler in a new window" target="_blank"
                        	onclick="document.getElementById('A2A_SHARE_SAVE_additional_js_variables').focus();
                            	document.getElementById('A2A_SHARE_SAVE_menu_styler_note').style.display='';">Open Menu Styler</a>
					</p>
            </fieldset></td>
            </tr>
            <tr valign="top">
            <th scope="row">Menu Options</th>
            <td><fieldset>
            	<label>
                	<input name="A2A_SHARE_SAVE_hide_embeds" 
                        type="checkbox"<?php if(get_option('A2A_SHARE_SAVE_hide_embeds')!='-1') echo ' checked="checked"'; ?> value="1"/>
                	Hide embedded objects (Flash, video, etc.) that intersect with the menu when displayed
                </label><br />
                <label>
                	<input name="A2A_SHARE_SAVE_show_title" 
                        type="checkbox"<?php if(get_option('A2A_SHARE_SAVE_show_title')=='1') echo ' checked="checked"'; ?> value="1"/>
                	Show the title of the post (or page) within the menu
                </label><br />
				<label>
                	<input name="A2A_SHARE_SAVE_onclick" 
                        type="checkbox"<?php if(get_option('A2A_SHARE_SAVE_onclick')=='1') echo ' checked="checked"'; ?> value="1"/>
                	Only show the menu when the user clicks the Share/Save button
                </label>
            </fieldset></td>
            </tr>
            <tr valign="top">
            <th scope="row">Additional Options</th>
            <td><fieldset>
            		<p id="A2A_SHARE_SAVE_menu_styler_note" style="display:none">
                        <label for="A2A_SHARE_SAVE_additional_js_variables">
                            <strong>Paste the code from Add to Any's Menu Styler in the box below!</strong>
                        </label>
                    </p>
                    <label for="A2A_SHARE_SAVE_additional_js_variables">
                    	Below you can set special JavaScript variables to apply to each Share/Save menu.
                    	Advanced users might want to check out the code generated by Add to Any's general <a href="http://www.addtoany.com/buttons/share_save">Share/Save button generator</a>.
					</label>
                    <p>
                		<textarea name="A2A_SHARE_SAVE_additional_js_variables" id="A2A_SHARE_SAVE_additional_js_variables" class="code" style="width: 98%; font-size: 12px;" rows="5" cols="50"><?php echo stripslashes(get_option('A2A_SHARE_SAVE_additional_js_variables')); ?></textarea>
					</p>
                    <?php if( get_option('A2A_SHARE_SAVE_additional_js_variables')!='' ) { ?>
                    <label for="A2A_SHARE_SAVE_additional_js_variables"><strong>Note</strong>: If you're adding new code, be careful not to accidentally overwrite any previous code.</label>
                    <?php } ?>
            </fieldset></td>
            </tr>
        </table>
        
        <p class="submit">
            <input type="submit" name="Submit" value="<?php _e('Save Changes', 'A2A_SHARE_SAVE_trans_domain' ) ?>" />
        </p>
    
    </form>
    </div>

<?php
 
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