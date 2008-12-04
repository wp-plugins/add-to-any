<?php
/*
Plugin Name: Add to Any: Share/Save/Bookmark Button
Plugin URI: http://www.addtoany.com/
Description: Helps readers share, save, and bookmark your posts and pages using any service.  [<a href="options-general.php?page=add-to-any.php">Settings</a>]
Version: .9.8.6.5
Author: Add to Any
Author URI: http://www.addtoany.com/contact/
*/


if( !isset($A2A_javascript) )
	$A2A_javascript = '';
if( !isset($A2A_locale) )
	$A2A_locale = '';
	

function A2A_SHARE_SAVE_textdomain() {
		load_plugin_textdomain('add-to-any',
			PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)),
			dirname(plugin_basename(__FILE__)));
}
add_action('init', 'A2A_SHARE_SAVE_textdomain');


function ADDTOANY_SHARE_SAVE_BUTTON( $args = false) {
	
	if( $args )
		extract( $args );
	
	if($output_buffering) ob_start();
	
	$sitename_enc	= rawurlencode( get_bloginfo('name') );
	$siteurl_enc	= rawurlencode( trailingslashit( get_bloginfo('url') ) );
	$linkname		= ( in_the_loop() ) ? get_the_title() : wp_title('-', false,'right').get_bloginfo('name');
	$linkname_enc	= rawurlencode( $linkname );
	$linkurl		= ( in_the_loop() ) ?
						/* Current post (if within loop) or current page */
						get_permalink() : 
						"http" . ( ($_SERVER["HTTPS"]=="on") ? 's' : '' ) . "://" .
							$_SERVER["HTTP_HOST"] .
							( ($_SERVER["SERVER_PORT"] != "80") ? ":".	$_SERVER["SERVER_PORT"] : '') .
							$_SERVER["REQUEST_URI"] ;
	$linkurl_enc	= rawurlencode( $linkurl );
	
	$button_target	= (get_option('A2A_SHARE_SAVE_button_opens_new_window')=='1' && (get_option('A2A_SHARE_SAVE_onclick')!='1')) ? ' target="_blank"' : '';
	
	if( !get_option('A2A_SHARE_SAVE_button') ) {
		$button_fname	= 'share_save_120_16.png';
		$button_width	= ' width="120"';
		$button_height	= ' height="16"';
		$button_src		= trailingslashit(get_option('siteurl')).PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)).'/'.$button_fname;
	} else if( get_option('A2A_SHARE_SAVE_button') == 'CUSTOM' ) {
		$button_src		= get_option('A2A_SHARE_SAVE_button_custom');
		$button_width	= '';
		$button_height	= '';
	} else if( get_option('A2A_SHARE_SAVE_button') == 'TEXT' ) {
		$button_text	= get_option('A2A_SHARE_SAVE_button_text');
	} else {
		$button_attrs	= explode( '|', get_option('A2A_SHARE_SAVE_button') );
		$button_fname	= $button_attrs[0];
		$button_width	= ' width="'.$button_attrs[1].'"';
		$button_height	= ' height="'.$button_attrs[2].'"';
		$button_src		= trailingslashit(get_option('siteurl')).PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)).'/'.$button_fname;
	}
	if( $button_attrs[0] == 'favicon.png' ) {
		$style_bg		= 'background:url('.trailingslashit(get_option('siteurl')).PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)).'/'.$button_fname.') no-repeat scroll 0px 0px';
		$style_bg		= ';' . $style_bg . ' !important;';
		$style			= ' style="'.$style_bg.'padding:1px 5px 5px 22px"';
		$button			= 'Share/Save';
	} else if( $button_text ) {
		$button			= $button_text;
	} else
		$button			= '<img src="'.$button_src.'"'.$button_width.$button_height.' alt="Share/Save/Bookmark"/>';
	?>

    <a class="a2a_dd addtoany_share_save" href="http://www.addtoany.com/share_save?sitename=<?php echo $sitename_enc; ?>&amp;siteurl=<?php echo $siteurl_enc; ?>&amp;linkname=<?php echo $linkname_enc; ?>&amp;linkurl=<?php echo $linkurl_enc; ?>"<?php echo $style . $button_target; ?>><?php echo $button; ?></a>

	<?php
	
	if( !is_feed() ) {
	
		global $A2A_javascript, $A2A_SHARE_SAVE_external_script_called;
		if( $A2A_javascript == '' || !$A2A_SHARE_SAVE_external_script_called ) {
			$external_script_call = '</script><script type="text/javascript" src="http://static.addtoany.com/menu/page.js"></script>';
			$A2A_SHARE_SAVE_external_script_called = true;
		}
		else
			$external_script_call = 'a2a_init("page");</script>';
		$A2A_javascript .= '<script type="text/javascript">' . "\n"
			. A2A_menu_locale()
			. 'a2a_linkname="' . js_escape($linkname) . '";' . "\n"
			. 'a2a_linkurl="' . $linkurl . '";' . "\n"
			. ((get_option('A2A_SHARE_SAVE_onclick')=='1') ? 'a2a_onclick=1;' . "\n" : '')
			. ((get_option('A2A_SHARE_SAVE_hide_embeds')=='-1') ? 'a2a_hide_embeds=0;' . "\n" : '')
			. ((get_option('A2A_SHARE_SAVE_show_title')=='1') ? 'a2a_show_title=1;' . "\n" : '')
			. (($A2A_javascript == '' || !$A2A_SHARE_SAVE_external_script_called) ? stripslashes(get_option('A2A_SHARE_SAVE_additional_js_variables')) . "\n" : '')
			. $external_script_call . "\n\n";
		
		remove_action('wp_footer', 'A2A_menu_javascript');
		add_action('wp_footer', 'A2A_menu_javascript');
	
	}
	
	if($output_buffering) {
		$button = ob_get_contents();
		ob_end_clean();
		return $button;
	}
}

if (!function_exists('A2A_menu_javascript')) {
	function A2A_menu_javascript() {
		global $A2A_javascript;
		echo $A2A_javascript;
	}
}

if (!function_exists('A2A_menu_locale')) {
	function A2A_menu_locale() {
		global $A2A_locale;
		if( $A2A_locale != '' ) return false;
		$A2A_locale = 'a2a_localize = {
	Share: "' . __("Share", "add-to-any") . '",
	Save: "' . __("Save", "add-to-any") . '",
	Subscribe: "' . __("Subscribe", "add-to-any") . '",
	Email: "' . __("E-mail", "add-to-any") . '",
    Bookmark: "' . __("Bookmark", "add-to-any") . '",
	ShowAll: "' . __("Show all", "add-to-any") . '",
	ShowLess: "' . __("Show less", "add-to-any") . '",
	FindServices: "' . __("Find service(s)", "add-to-any") . '",
	FindAnyServiceToAddTo: "' . __("Instantly find any service to add to", "add-to-any") . '",
	PoweredBy: "' . __("Powered by", "add-to-any") . '",
	ShareViaEmail: "' . __("Share via e-mail", "add-to-any") . '",
	SubscribeViaEmail: "' . __("Subscribe via e-mail", "add-to-any") . '",
	BookmarkInYourBrowser: "' . __("Bookmark in your browser", "add-to-any") . '",
	BookmarkInstructions: "' . __("Press Ctrl+D or Cmd+D to bookmark this page", "add-to-any") . '",
	AddToYourFavorites: "' . __("Add to your favorites", "add-to-any") . '",
	SendFromWebOrProgram: "' . __("Send from any e-mail address or e-mail program", "add-to-any") . '",
    EmailProgram: "' . __("E-mail program", "add-to-any") . '"
};
';
		return $A2A_locale;
	}
}


function A2A_SHARE_SAVE_to_bottom_of_content($content) {
	if ( 
		( (strpos($content, '<!--sharesave-->')===false) ) && (														// <!--sharesave-->
			( !is_page() && get_option('A2A_SHARE_SAVE_display_in_posts')=='-1' ) || 								// All posts
			( !is_page() && !is_single() && get_option('A2A_SHARE_SAVE_display_in_posts_on_front_page')=='-1' ) ||  // Front page posts
			( is_page() && get_option('A2A_SHARE_SAVE_display_in_pages')=='-1' ) ||									// Pages
			( (strpos($content, '<!--nosharesave-->')!==false ) ) ||												// <!--nosharesave-->
			( is_feed() && (get_option('A2A_SHARE_SAVE_display_in_feed')=='-1') )									// Display in feed?
		)
	)	
		return $content;
	
	$A2A_SHARE_SAVE_options = array(
		"output_buffering" => true);
	
	$content .= '<p class="addtoany_share_save_container">'.ADDTOANY_SHARE_SAVE_BUTTON( $A2A_SHARE_SAVE_options ).'</p>';
	return $content;
}

add_action('the_content', 'A2A_SHARE_SAVE_to_bottom_of_content', 98);


function A2A_SHARE_SAVE_button_css() {
	?><style type="text/css">.addtoany_share_save img{border:0;}</style>
<?php
}

add_action('wp_head', 'A2A_SHARE_SAVE_button_css');




/*****************************
		OPTIONS
******************************/


function A2A_SHARE_SAVE_options_page() {

    if( $_POST[ 'A2A_SHARE_SAVE_submit_hidden' ] == 'Y' ) {

        update_option( 'A2A_SHARE_SAVE_display_in_posts_on_front_page', ($_POST['A2A_SHARE_SAVE_display_in_posts_on_front_page']=='1') ? '1':'-1' );
		update_option( 'A2A_SHARE_SAVE_display_in_posts', ($_POST['A2A_SHARE_SAVE_display_in_posts']=='1') ? '1':'-1' );
		update_option( 'A2A_SHARE_SAVE_display_in_pages', ($_POST['A2A_SHARE_SAVE_display_in_pages']=='1') ? '1':'-1' );
		update_option( 'A2A_SHARE_SAVE_display_in_feed', ($_POST['A2A_SHARE_SAVE_display_in_feed']=='1') ? '1':'-1' );
		update_option( 'A2A_SHARE_SAVE_hide_embeds', ($_POST['A2A_SHARE_SAVE_hide_embeds']=='1') ? '1':'-1' );
		update_option( 'A2A_SHARE_SAVE_show_title', ($_POST['A2A_SHARE_SAVE_show_title']=='1') ? '1':'-1' );
		update_option( 'A2A_SHARE_SAVE_onclick', ($_POST['A2A_SHARE_SAVE_onclick']=='1') ? '1':'-1' );
		update_option( 'A2A_SHARE_SAVE_button_opens_new_window', ($_POST['A2A_SHARE_SAVE_button_opens_new_window']=='1') ? '1':'-1' );
		update_option( 'A2A_SHARE_SAVE_button', $_POST['A2A_SHARE_SAVE_button'] );
		update_option( 'A2A_SHARE_SAVE_button_custom', $_POST['A2A_SHARE_SAVE_button_custom'] );
		update_option( 'A2A_SHARE_SAVE_button_text', ( trim($_POST['A2A_SHARE_SAVE_button_text']) != '' ) ? $_POST['A2A_SHARE_SAVE_button_text'] : "Share/Save" );
		update_option( 'A2A_SHARE_SAVE_additional_js_variables', trim($_POST['A2A_SHARE_SAVE_additional_js_variables']) );
		
		?>
    	<div class="updated fade"><p><strong><?php _e('Settings saved.'); ?></strong></p></div>
		<?php
		
    }

    ?>
    
    <div class="wrap">

	<h2><?php _e( 'Add to Any: Share/Save ', 'add-to-any' ) . _e( 'Settings' ); ?></h2>

    <form method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
    
	<?php wp_nonce_field('update-options'); ?>
    
    	<input type="hidden" name="A2A_SHARE_SAVE_submit_hidden" value="Y">
    
        <table class="form-table">
        	<tr valign="top">
            <th scope="row"><?php _e("Button", "add-to-any"); ?></th>
            <td><fieldset>
            	<label>
                	<input name="A2A_SHARE_SAVE_button" value="favicon.png|16|16" type="radio"<?php if(get_option('A2A_SHARE_SAVE_button')=='favicon.png|16|16') echo ' checked="checked"'; ?>
                    	 style="margin:9px 0;vertical-align:middle">
                    <img src="<?php echo trailingslashit(get_option('siteurl')).PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)).'/favicon.png'; ?>" width="16" height="16" border="0" style="padding:9px;vertical-align:middle" alt="+ Share/Save" title="+ Share/Save"
                    	onclick="this.parentNode.firstChild.checked=true"/>
                </label><br>
                <label>
                	<input name="A2A_SHARE_SAVE_button" value="share_save_120_16.png|120|16" type="radio"<?php if( !get_option('A2A_SHARE_SAVE_button') || get_option('A2A_SHARE_SAVE_button' )=='share_save_120_16.png|120|16' ) echo ' checked="checked"'; ?>
                    	style="margin:9px 0;vertical-align:middle">
                    <img src="<?php echo trailingslashit(get_option('siteurl')).PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)).'/share_save_120_16.png'; ?>" width="120" height="16" border="0" style="padding:9px;vertical-align:middle"
                    	onclick="this.parentNode.firstChild.checked=true"/>
                </label><br>
                <label>
                	<input name="A2A_SHARE_SAVE_button" value="share_save_171_16.png|171|16" type="radio"<?php if(get_option('A2A_SHARE_SAVE_button')=='share_save_171_16.png|171|16') echo ' checked="checked"'; ?>
                    	style="margin:9px 0;vertical-align:middle">
                    <img src="<?php echo trailingslashit(get_option('siteurl')).PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)).'/share_save_171_16.png'; ?>" width="171" height="16" border="0" style="padding:9px;vertical-align:middle"
                    	onclick="this.parentNode.firstChild.checked=true"/>
                </label><br>
                <label>
                	<input name="A2A_SHARE_SAVE_button" value="share_save_256_24.png|256|24" type="radio"<?php if(get_option('A2A_SHARE_SAVE_button')=='share_save_256_24.png|256|24') echo ' checked="checked"'; ?>
                    	style="margin:9px 0;vertical-align:middle">
                    <img src="<?php echo trailingslashit(get_option('siteurl')).PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)).'/share_save_256_24.png'; ?>" width="256" height="24" border="0" style="padding:9px;vertical-align:middle"
                    	onclick="this.parentNode.firstChild.checked=true"/>
				</label><br>
                <label>
                	<input name="A2A_SHARE_SAVE_button" value="CUSTOM" type="radio"<?php if( get_option('A2A_SHARE_SAVE_button') == 'CUSTOM' ) echo ' checked="checked"'; ?>
                    	style="margin:9px 0;vertical-align:middle">
					<span style="margin:0 9px;vertical-align:middle"><? _e("Image URL"); ?>:</span>
				</label>
  				<input name="A2A_SHARE_SAVE_button_custom" type="text" class="code" size="50" onclick="e=document.getElementsByName('A2A_SHARE_SAVE_button');e[e.length-2].checked=true" style="vertical-align:middle"
                	value="<?php echo get_option('A2A_SHARE_SAVE_button_custom'); ?>" /><br>
				<label>
                	<input name="A2A_SHARE_SAVE_button" value="TEXT" type="radio"<?php if( get_option('A2A_SHARE_SAVE_button') == 'TEXT' ) echo ' checked="checked"'; ?>
                    	style="margin:9px 0;vertical-align:middle">
					<span style="margin:0 9px;vertical-align:middle"><? _e("Text only"); ?>:</span>
				</label>
                <input name="A2A_SHARE_SAVE_button_text" type="text" class="code" size="50" onclick="e=document.getElementsByName('A2A_SHARE_SAVE_button');e[e.length-1].checked=true" style="vertical-align:middle"
                	value="<?php echo ( trim(get_option('A2A_SHARE_SAVE_button_text')) != '' ) ? get_option('A2A_SHARE_SAVE_button_text') : "Share/Save"; ?>" />
                
            </fieldset></td>
            </tr>
            <tr valign="top">
            <th scope="row"><? _e('Button Placement', 'add-to-any'); ?></th>
            <td><fieldset>
                <label>
                	<input name="A2A_SHARE_SAVE_display_in_posts" 
                    	onclick="e=getElementsByName('A2A_SHARE_SAVE_display_in_posts_on_front_page')[0];f=getElementsByName('A2A_SHARE_SAVE_display_in_feed')[0];
                        	if(!this.checked){e.checked=false;e.disabled=true; f.checked=false;f.disabled=true}else{e.checked=true;e.disabled=false; f.checked=true;f.disabled=false}"
                        onchange="e=getElementsByName('A2A_SHARE_SAVE_display_in_posts_on_front_page')[0];f=getElementsByName('A2A_SHARE_SAVE_display_in_feed')[0];
                        	if(!this.checked){e.checked=false;e.disabled=true; f.checked=false;f.disabled=true}else{e.checked=true;e.disabled=false; f.checked=true;f.disabled=false}"
                        type="checkbox"<?php if(get_option('A2A_SHARE_SAVE_display_in_posts')!='-1') echo ' checked="checked"'; ?> value="1"/>
                	<?php _e('Display Share/Save button at the bottom of posts', 'add-to-any'); ?> <strong>*</strong>
                </label><br/>
                <label>
                	&nbsp; &nbsp; &nbsp; <input name="A2A_SHARE_SAVE_display_in_posts_on_front_page" type="checkbox"<?php 
						if(get_option('A2A_SHARE_SAVE_display_in_posts_on_front_page')!='-1') echo ' checked="checked"';
						if(get_option('A2A_SHARE_SAVE_display_in_posts')=='-1') echo ' disabled="disabled"';
						?> value="1"/>
                    <?php _e('Display Share/Save button at the bottom of posts on the front page', 'add-to-any'); ?>
				</label><br/>
                <label>
                	&nbsp; &nbsp; &nbsp; <input name="A2A_SHARE_SAVE_display_in_feed" type="checkbox"<?php 
						if(get_option('A2A_SHARE_SAVE_display_in_feed')!='-1') echo ' checked="checked"'; 
						if(get_option('A2A_SHARE_SAVE_display_in_posts')=='-1') echo ' disabled="disabled"';
						?> value="1"/>
                    <?php _e('Display Share/Save button at the bottom of posts in the feed', 'add-to-any'); ?>
				</label><br/>
                <label>
                	<input name="A2A_SHARE_SAVE_display_in_pages" type="checkbox"<?php if(get_option('A2A_SHARE_SAVE_display_in_pages')!='-1') echo ' checked="checked"'; ?> value="1"/>
                    <?php _e('Display Share/Save button at the bottom of pages', 'add-to-any'); ?> <strong>*</strong>
				</label>
                <br/><br/>
                <strong>*</strong> <?php _e("If unchecked, be sure to place the following code in <a href=\"theme-editor.php\">your template pages</a> (within <code>index.php</code>, <code>single.php</code>, and/or <code>page.php</code>)", "add-to-any"); ?>:<br/>
                <code>&lt;?php if( function_exists('ADDTOANY_SHARE_SAVE_BUTTON') ) { ADDTOANY_SHARE_SAVE_BUTTON(); } ?&gt;</code>
            </fieldset></td>
            </tr>
            <tr valign="top">
            <th scope="row"><?php _e('Menu Style', 'add-to-any'); ?></th>
            <td><fieldset>
                    	<?php _e("Using Add to Any's Menu Styler, you can customize the colors of your Share/Save menu! When you're done, be sure to paste the generated code in the <a href=\"#\" onclick=\"document.getElementById('A2A_SHARE_SAVE_additional_js_variables').focus();return false\">Additional Options</a> box below.", "add-to-any"); ?>
                    <p>
                		<a href="http://www.addtoany.com/buttons/share_save/menu_style/wordpress" class="button-secondary" title="<?php _e("Open the Add to Any Menu Styler in a new window", "add-to-any"); ?>" target="_blank"
                        	onclick="document.getElementById('A2A_SHARE_SAVE_additional_js_variables').focus();
                            	document.getElementById('A2A_SHARE_SAVE_menu_styler_note').style.display='';"><?php _e("Open Menu Styler", "add-to-any"); ?></a>
					</p>
            </fieldset></td>
            </tr>
            <tr valign="top">
            <th scope="row"><?php _e('Menu Options', 'add-to-any'); ?></th>
            <td><fieldset>
            	<label>
                	<input name="A2A_SHARE_SAVE_hide_embeds" 
                        type="checkbox"<?php if(get_option('A2A_SHARE_SAVE_hide_embeds')!='-1') echo ' checked="checked"'; ?> value="1"/>
                	<?php _e('Hide embedded objects (Flash, video, etc.) that intersect with the menu when displayed', 'add-to-any'); ?>
                </label><br />
                <label>
                	<input name="A2A_SHARE_SAVE_show_title" 
                        type="checkbox"<?php if(get_option('A2A_SHARE_SAVE_show_title')=='1') echo ' checked="checked"'; ?> value="1"/>
                	<?php _e('Show the title of the post (or page) within the menu', 'add-to-any'); ?>
                </label><br />
				<label>
                	<input name="A2A_SHARE_SAVE_onclick" 
                        type="checkbox"<?php if(get_option('A2A_SHARE_SAVE_onclick')=='1') echo ' checked="checked"'; ?> value="1"
                        onclick="e=getElementsByName('A2A_SHARE_SAVE_button_opens_new_window')[0];if(this.checked){e.checked=false;e.disabled=true}else{e.disabled=false}"
						onchange="e=getElementsByName('A2A_SHARE_SAVE_button_opens_new_window')[0];if(this.checked){e.checked=false;e.disabled=true}else{e.disabled=false}"/>
                	<?php _e('Only show the menu when the user clicks the Share/Save button', 'add-to-any'); ?>
                </label><br />
				<label>
                	<input name="A2A_SHARE_SAVE_button_opens_new_window" 
                        type="checkbox"<?php if(get_option('A2A_SHARE_SAVE_button_opens_new_window')=='1') echo ' checked="checked"'; ?> value="1"
						<?php if(get_option('A2A_SHARE_SAVE_onclick')=='1') echo ' disabled="disabled"'; ?>/>
                	<?php _e('Open the addtoany.com menu page in a new tab or window if the user clicks the Share/Save button', 'add-to-any'); ?>
                </label>
            </fieldset></td>
            </tr>
            <tr valign="top">
            <th scope="row"><?php _e('Additional Options', 'add-to-any'); ?></th>
            <td><fieldset>
            		<p id="A2A_SHARE_SAVE_menu_styler_note" style="display:none">
                        <label for="A2A_SHARE_SAVE_additional_js_variables">
                            <strong><?php _e("Paste the code from Add to Any's Menu Styler in the box below!", 'add-to-any'); ?></strong>
                        </label>
                    </p>
                    <label for="A2A_SHARE_SAVE_additional_js_variables">
                    	<?php _e('Below you can set special JavaScript variables to apply to each Share/Save menu.', 'add-to-any'); ?>
                    	<?php _e("Advanced users might want to check out the code generated by Add to Any's <a href=\"http://www.addtoany.com/buttons/api/\">JavaScript API</a>.", "add-to-any"); ?>
					</label>
                    <p>
                		<textarea name="A2A_SHARE_SAVE_additional_js_variables" id="A2A_SHARE_SAVE_additional_js_variables" class="code" style="width: 98%; font-size: 12px;" rows="5" cols="50"><?php echo stripslashes(get_option('A2A_SHARE_SAVE_additional_js_variables')); ?></textarea>
					</p>
                    <?php if( get_option('A2A_SHARE_SAVE_additional_js_variables')!='' ) { ?>
                    <label for="A2A_SHARE_SAVE_additional_js_variables"><?php _e("<strong>Note</strong>: If you're adding new code, be careful not to accidentally overwrite any previous code.</label>", 'add-to-any'); ?>
                    <?php } ?>
            </fieldset></td>
            </tr>
        </table>
        
        <p class="submit">
            <input type="submit" name="Submit" value="<?php _e('Save Changes', 'add-to-any' ) ?>" />
        </p>
    
    </form>
    </div>

<?php
 
}

function A2A_SHARE_SAVE_add_menu_link() {
	if( current_user_can('manage_options') ) {
		add_options_page(
			'Add to Any: '. __("Share/Save", "add-to-any"). " " . __("Settings")
			, __("Share/Save Buttons", "add-to-any")
			, 8 
			, basename(__FILE__)
			, 'A2A_SHARE_SAVE_options_page'
		);
	}
}

add_action('admin_menu', 'A2A_SHARE_SAVE_add_menu_link');



?>