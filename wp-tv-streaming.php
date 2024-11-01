<?php
/*
Plugin Name: TV Streaming Plugin for WordPress
Plugin URI: http://www.satublogs.com/tv-streaming-plugin-for-wordpress/
Description: TV Streaming is a WordPress plugin that can integrate live video streaming from various TV channels into your WordPress site.
Version: 1.0.2
Author: Web Design Jakarta
Author URI: http://www.satublogs.com
License: GPL2

Copyright 2011  Web Design Jakarta  (email : bambang@satublogs.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define('TV_STREAMING_VERSION', '1.0.2');
global $TV_STREAMING_CHANNELS, $TV_LOCALE_CHANNELS;

$auto_channel_file = load_locale_channels(FALSE, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/channels/');
$default_channel_file = load_locale_channels('default', FALSE, dirname( plugin_basename( __FILE__ ) ) . '/channels/');
if( false !== $auto_channel_file ){
	require_once($auto_channel_file);
}elseif( false !== $default_channel_file ){
	require_once($default_channel_file);
}else{
	die('CHANNEL FILE NOT FOUND !!!');
}

class TVStreaming {

	var $autoplay;
	var $default_width;
	var $default_height;
	var $seo_friendly;
	var $default_logo;
	var $available_channels;
	var $channel_data;
	var $default_channel;
	var $default_icon;
	var $display_credit;

	function TVStreaming(){
		$this->wp_tv_get_options();
		if( !get_option('tvs_version') ){
			update_option('tvs_version', TV_STREAMING_VERSION);
			$this->wp_tv_save_options();
		}
		return $this;
	}

	function wp_tv_version(){
		if( function_exists('get_option') && get_option('tvs_version') ){
			return get_option('tvs_version');
		}else{
			return TV_STREAMING_VERSION;
		}
	}

	function wp_tv_get_options(){
		global $TV_STREAMING_CHANNELS;
		$this->channel_data = array_merge(array(), $TV_STREAMING_CHANNELS);

		$x = get_option('tvs_width', 280);
		if( is_numeric($x) && intval($x)>0 ){
			$this->default_width = intval($x);
		}else{
			$this->default_width = 280;
		}

		$x = get_option('tvs_height', 200);
		if( is_numeric($x) && intval($x)>0 ){
			$this->default_height = intval($x);
		}else{
			$this->default_height = 200;
		}

		$x = get_option('tvs_seo', 1);
		if( is_numeric($x) && intval($x)>0 ){
			$this->seo_friendly = TRUE;
		}else{
			$this->seo_friendly = FALSE;
		}

		$x = get_option('tvs_icon', 96);
		if( is_numeric($x) && intval($x)>0 && in_array(intval($x), array(96, 64, 48, 32)) ){
			$this->default_icon = intval($x);
		}else{
			$this->default_icon = 96;
		}

		$x = get_option('tvs_logo', '');
		$x = trim($x);
		if( $x!='' ){
			$this->default_logo = $x;
		}else{
			$this->default_logo = '';
		}

		$x = get_option('tvs_channels', 'rcti,sctv,trans,indosiar,antv,global,tvone,metro');
		$x = strtolower( trim($x) );
		if( $x!='' ){
			$this->available_channels = explode(',', $x);
		}else{
			$this->available_channels = array();
		}
		if( empty($this->available_channels) ) $this->available_channels = array('rcti','sctv','trans','indosiar','antv','global','tvone','metro');

		$x = get_option('tvs_default_channel', 'rcti');
		$x = strtolower( trim($x) );
		if( $x!='' ){
			$this->default_channel = $x;
		}else{
			$this->default_channel = '';
		}

		$x = get_option('tvs_auto', 1);
		if( is_numeric($x) && intval($x)>0 ){
			$this->autoplay = TRUE;
		}else{
			$this->autoplay = FALSE;
		}

		if( $this->available_channels ){
			$this->default_channel = ( $this->default_channel!='' && !empty($this->channel_data) && isset($this->channel_data[$this->default_channel]) && !empty($this->available_channels) && in_array($this->default_channel, $this->available_channels) ? $this->default_channel : $this->available_channels[0] );
		}else{
			$this->default_channel = ( $this->default_channel!='' && !empty($this->channel_data) && isset($this->channel_data[$this->default_channel]) ? $this->default_channel : '' );
		}

		$x = get_option('tvs_credit', 1);
		if( is_numeric($x) && intval($x)>0 ){
			$this->display_credit = TRUE;
		}else{
			$this->display_credit = FALSE;
		}
	}

	function wp_tv_save_options(){
		$reload = FALSE;
		if( function_exists('update_option') ){
			if( isset($_POST['TVS_UPDATE']) && $_POST['TVS_UPDATE'] ){
				
				if( isset($_POST['tvs_width']) && is_numeric($_POST['tvs_width']) ){
					update_option('tvs_width', ( intval($_POST['tvs_width'])>0 ? intval($_POST['tvs_width']) : $this->default_width ));
					$reload = TRUE;
				}
				if( isset($_POST['tvs_height']) && is_numeric($_POST['tvs_height']) ){
					update_option('tvs_height', ( intval($_POST['tvs_height'])>0 ? intval($_POST['tvs_height']) : $this->default_height ));
					$reload = TRUE;
				}
				if( isset($_POST['tvs_icon']) && is_numeric($_POST['tvs_icon']) && intval($_POST['tvs_icon'])>0 && in_array(intval($_POST['tvs_icon']), array(96, 64, 48, 32)) ){
					update_option( 'tvs_icon', intval($_POST['tvs_icon']) );
					$reload = TRUE;
				}
				if( isset($_POST['tvs_seo']) && is_numeric($_POST['tvs_seo']) ){
					update_option('tvs_seo', ( intval($_POST['tvs_seo'])>0 ? 1 : 0 ));
					$reload = TRUE;
				}
				if( isset($_POST['tvs_logo']) ){
					update_option('tvs_logo', trim($_POST['tvs_logo']));
					$reload = TRUE;
				}
				if( isset($_POST['tvs_channels']) ){
					$dummy = array();
					if( is_array($_POST['tvs_channels']) ){
						$dummy = $_POST['tvs_channels'];
					}else{
						$dummy = strtolower(trim($_POST['tvs_channels']));
						$dummy = explode(',', $dummy);
					}
					update_option('tvs_channels', implode(',', $dummy));
					$reload = TRUE;
				}
				if( isset($_POST['tvs_default_channel']) ){
					update_option('tvs_default_channel', strtolower(trim($_POST['tvs_default_channel'])));
					$reload = TRUE;
				}
				if( isset($_POST['tvs_auto']) && is_numeric($_POST['tvs_auto']) ){
					update_option('tvs_auto', ( intval($_POST['tvs_auto'])>0 ? 1 : 0 ));
					$reload = TRUE;
				}
				if( isset($_POST['tvs_credit']) && is_numeric($_POST['tvs_credit']) ){
					update_option('tvs_credit', ( intval($_POST['tvs_credit'])>0 ? 1 : 0 ));
					$reload = TRUE;
				}

			}else{
				update_option('tvs_width', $this->default_width);
				update_option('tvs_height', $this->default_height);
				update_option('tvs_icon', $this->default_icon);
				update_option('tvs_seo', ($this->seo_friendly ? 1:0));
				update_option('tvs_logo', $this->default_logo);
				update_option('tvs_channels', implode(',', $this->available_channels));
				update_option('tvs_default_channel', $this->default_channel);
				update_option('tvs_auto', ($this->autoplay ? 1:0));
				update_option('tvs_credit', ($this->display_credit ? 1:0));
			}
		}
		if( $reload ) $this->wp_tv_get_options();
	}

	function wp_tv_url(){
		
		$used_ssl = FALSE;
		if ( function_exists( 'is_ssl' ) ) {
			$used_ssl = is_ssl();
		}else{
			if ( isset($_SERVER['HTTPS']) ) {
				if ( 'on' == strtolower($_SERVER['HTTPS']) ){
					$used_ssl = TRUE;
				}elseif ( '1' == $_SERVER['HTTPS'] ){
					$used_ssl = TRUE;
				}
			} elseif ( isset($_SERVER['SERVER_PORT']) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
				$used_ssl = TRUE;
			}
		}

		if( !$used_ssl ){
			if ( function_exists( 'plugins_url' ) ) return plugins_url( '' , __FILE__ );
			if ( defined( 'WP_PLUGIN_URL' ) ) return WP_PLUGIN_URL . '/' . dirname( plugin_basename( __FILE__ ) ). '/';
		}
		
		if ( version_compare( get_bloginfo( 'version' ) , '3.0' , '<' ) && $used_ssl ) {
			$wp_content_url = str_replace( 'http://' , 'https://' , get_option( 'siteurl' ) );
		} else {
			$wp_content_url = get_option( 'siteurl' );
		}
		$wp_content_url .= '/wp-content';
		$wp_plugin_url = $wp_content_url . '/plugins';

		return $wp_plugin_url . '/' . dirname( plugin_basename( __FILE__ ) ). '/';
	}
	
	function wp_tv_header(){
		global $TV_STREAMING_CHANNELS;
		echo '<script type="text/javascript">/*<![CDATA[*/';
		foreach($TV_STREAMING_CHANNELS as $ch=>$data){
			$s = $data['stream'];
			if( isset($data[$s]) ):
		?>
			tv_custom_channels["m<?php echo $s; ?>x01"] = "<?php echo addcslashes($data[$s], '"'); ?>";
		<?php
			else:
		?>
			tv_custom_channels["m<?php echo $s; ?>x01"] = "";
		<?php
			endif;
		}
		echo '/*]]>*/</script>';
	}

	function wp_tv_get_html($active_channel='', $channels=array(), $width=200, $height=150, $auto=TRUE, $seo=TRUE, $logo='', $icon=96){
		$result = "";
		$id = date("jnYHis");

		$active_channel = strtolower( trim($active_channel) );
		if( empty($active_channel) ){
			if( $channels ){
				$active_channel = $channels[0];
			}else{
				$active_channel = $this->default_channel;
			}
		}elseif( $channels ){
			if( !in_array($active_channel, $channels) ) $active_channel = $channels[0];
		}

		if( is_numeric($icon) && intval($icon)>0 && in_array(intval($icon), array(96, 64, 48, 32)) ){
			$icon = intval($icon);
		}else{
			$icon = $this->default_icon;
		}

		if( $seo ){
			
			$result = '<div id="tv'.$id.'" class="tv-streaming'.(empty($channels) && $active_channel && isset($this->channel_data[$active_channel]) ? ' m'.$this->channel_data[$active_channel]['stream'].'x01':'' ).'"><div class="video">';

			$result .= '<p class="altext">'.sprintf(__('You are trying to watch <strong>%1$s</strong> channel live TV Streaming with %2$s. But, looks like you don\'t have flash player installed with your browser. Please, <a href="%3$s" target="_blank" rel="nofollow">download and install flash player</a>.', 'tvs_lang'), ( $active_channel && isset($this->channel_data[$active_channel]) ? $this->channel_data[$active_channel]['title'] : __('our', 'tvs_lang')), '<a href="http://www.satublogs.com/tv-streaming-plugin-for-wordpress/" target="_blank">'.__('TV Streaming plugin for WordPress', 'tvs_lang').'</a>', 'http://get.adobe.com/flashplayer/').'</p>';

			$result .= '</div>';

			if( $channels ){
				$str_channels = '';
				foreach($channels as $channel){
					$channel = strtolower( trim($channel) );
					if( $channel && isset($this->channel_data[$channel]) )
						$str_channels .= '<li class="m'.$this->channel_data[$channel]['stream'].'x01 channel'.($active_channel == $channel ? ' active':'').'"><a href="#tv'.$id.'" rel="nofollow" title="'.__('Play','tvs_lang').' '.$this->channel_data[$channel]['title'].'">'.$this->channel_data[$channel]['title'].'</a></li>';
				}
				if( $str_channels ) $result .= '<ul class="channels btn-'.$icon.'">'.$str_channels.'</ul>';
			}
			if( $this->display_credit )
				$result .= '<p class="credit">'.__('Powered by','tvs_lang').' <a href="http://www.satublogs.com/tv-streaming-plugin-for-wordpress/" target="_blank" title="'.__('Get your own TV Streaming','tvs_lang').'">TV Streaming '.__('for','tvs_lang').' WordPress</a></p>';
			$result .= '</div>';

		}else{
			
			$result = '<div id="tv'.$id.'" class="tv-streaming'.(empty($channels) && $active_channel && isset($this->channel_data[$active_channel]) ? ' m'.$this->channel_data[$active_channel]['stream'].'x01':'' ).'">';
			$result .= '<div class="video">';

			$msg = '<p class="altext">'.sprintf(__('You are trying to watch <strong>%1$s</strong> channel live TV Streaming with %2$s. But, looks like you don\'t have flash player installed with your browser. Please, <a href="%3$s" target="_blank" rel="nofollow">download and install flash player</a>.', 'tvs_lang'), ( $active_channel && isset($this->channel_data[$active_channel]) ? $this->channel_data[$active_channel]['title'] : __('our', 'tvs_lang')), '<a href="http://www.satublogs.com/tv-streaming-plugin-for-wordpress/" target="_blank">'.__('TV Streaming plugin for WordPress', 'tvs_lang').'</a>', 'http://get.adobe.com/flashplayer/').'</p>';

			$stream_name = $this->channel_data[$active_channel]['stream'];
			if( isset($this->channel_data[$active_channel][$stream_name]) ){
				
				$result .= str_ireplace(array('%ID%', '%WIDTH%', '%HEIGHT%', '%LOGO%', '%AUTO%', '%MESSAGE%'), array($id, $width, $height, $logo, ($auto ? 'true':'false'), $msg), $this->channel_data[$active_channel][$stream_name]);

			}else{
				
				$result .= '<object id="flash-'.$id.'" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="'.$width.'" height="'.$height.'">
					<param name="movie" value="http://indoweb.tv/tools/player/player.swf" />
					<param name="quality" value="low" />
					<param name="allowfullscreen" value="true" />
					<param name="allowscriptaccess" value="always" />
					<param name="menu" value="false" />
					<param name="wmode" value="opaque" />
					<param name="flashvars" value="file=m'.$this->channel_data[$active_channel]['stream'].'x01&amp;type=video&amp;screencolor=000000&amp;logo='.$logo.'&amp;streamer=rtmp://stream.indoweb.tv/live&amp;autostart='.($auto ? 'true':'false').'&amp;stretching=exactfit&amp;controlbar=over&amp;smoothing=false&amp;volume=100" />
					<!--[if !IE]>-->
						<object type="application/x-shockwave-flash" data="http://indoweb.tv/tools/player/player.swf" width="'.$width.'" height="'.$height.'">
						<param name="movie" value="http://indoweb.tv/tools/player/player.swf" />
						<param name="quality" value="low" />
						<param name="allowfullscreen" value="true" />
						<param name="allowscriptaccess" value="always" />
						<param name="menu" value="false" />
						<param name="wmode" value="opaque" />
						<param name="flashvars" value="file=m'.$this->channel_data[$active_channel]['stream'].'x01&amp;type=video&amp;screencolor=000000&amp;logo='.$logo.'&amp;streamer=rtmp://stream.indoweb.tv/live&amp;autostart='.($auto ? 'true':'false').'&amp;stretching=exactfit&amp;controlbar=over&amp;smoothing=false&amp;volume=100" />
					<!--<![endif]-->
						'.$msg.'
					<!--[if !IE]>-->
						</object>
					<!--<![endif]-->
				</object>';

			}
			$result .= '</div>';
			
			if( $channels ){
				$str_channels = '';
				foreach($channels as $channel){
					$channel = strtolower( trim($channel) );
					if( $channel && isset($this->channel_data[$channel]) )
						$str_channels .= '<li class="m'.$this->channel_data[$channel]['stream'].'x01 channel'.($active_channel == $channel ? ' active':'').'"><a href="#tv'.$id.'" rel="nofollow" title="'.__('Play','tvs_lang').' '.$this->channel_data[$channel]['title'].'">'.$this->channel_data[$channel]['title'].'</a></li>';
				}
				if( $str_channels ) $result .= '<ul class="channels btn-'.$icon.'">'.$str_channels.'</ul>';
			}
			if( $this->display_credit )
				$result .= '<p class="credit">'.__('Powered by','tvs_lang').' <a href="http://www.satublogs.com/tv-streaming-plugin-for-wordpress/" target="_blank" title="'.__('Get your own TV Streaming','tvs_lang').'">TV Streaming '.__('for','tvs_lang').' WordPress</a></p>';
			$result .= '</div>';

		}

		$result .= '<script type="text/javascript">/*<![CDATA[*/tv_global_config["tv'.$id.'"] = {"width":'.$width.', "height":'.$height.', "logo":"'.$logo.'", "auto":'.($auto ? 'true':'false').'};';
		if( $seo && $active_channel ){
			$result .= 'jQuery(document).ready(function($){ autoplay_tv("tv'.$id.'"); });';
		}
		$result .= '/*]]>*/</script>';
		return $result;
	}
	
	function wp_tv_streaming_shortcode($atts) {
		$tv = new TVStreaming;
		extract(shortcode_atts(array(
			'channel' => $tv->default_channel,
			'showchannel' => 1,
			'autoplay' => $tv->autoplay,
			'width'=>$tv->default_width,
			'height'=>$tv->default_height,
			'seofriendly'=>$tv->seo_friendly,
			'logo'=>$tv->default_logo,
			'icon'=>$tv->default_icon
		), $atts));
		
		$channel = strtolower(trim($channel));
		$showchannel = strtolower(trim($showchannel));
		
		if( $showchannel=='' || $showchannel=='no' || $showchannel=='false' || (is_numeric($showchannel) && intval($showchannel)<=0) ){
			$showchannel = array();
		}elseif( is_numeric($showchannel) && intval($showchannel)>0 ){
			$showchannel = $tv->available_channels;
		}else{
			$showchannel = explode(',', $showchannel);
			if( empty($showchannel) ) $showchannel = $tv->available_channels;
		}
		
		return $tv->wp_tv_get_html($channel, $showchannel, $width, $height, $autoplay, $seofriendly, $logo, $icon);
	}

	function wp_tv_create_menu() {
		//create new top-level menu
		add_options_page('TV Streaming Plugin Settings', 'TV Streaming', 'manage_options', 'tv-streaming', 'wp_get_tv_settings_page');
	}

	function wp_tv_plugin_options_url() {
		return admin_url( 'options-general.php?page=tv-streaming' );
	}
	
	/**
	 * Add a link to the settings page to the plugins list
	 */
	function wp_tv_add_action_link( $links, $file ) {
		if ( $file == 'tv-streaming/wp-tv-streaming.php' ) {
			$settings_link = '<a href="' . admin_url( 'options-general.php?page=tv-streaming' ) . '">' . __('Settings','tvs_lang') . '</a>';
			array_unshift( $links, $settings_link );
		}
		return $links;
	}

	function wp_tv_settings_page() {

		if( isset($_POST['TVS_UPDATE']) && $_POST['TVS_UPDATE'] ){
			
			if (!current_user_can('manage_options')) die(__('You cannot edit TV Streaming Settings.','tvs_lang'));
			check_admin_referer(basename(__FILE__));

			$width = $this->default_width;
			$height = $this->default_height;
			$seofriendly = ($this->seo_friendly ? 1:0);
			$autoplay = ($this->autoplay ? 1:0);
			$logo = $this->default_logo;
			$channel = $this->default_channel;
			$showchannel = $this->available_channels;
			$icon = $this->default_icon;
			$credit = $this->display_credit;

			if( isset($_POST['tvs_width']) && is_numeric($_POST['tvs_width']) && intval($_POST['tvs_width'])>0 ){
				$width = intval($_POST['tvs_width']);
			}
			if( isset($_POST['tvs_height']) && is_numeric($_POST['tvs_height']) && intval($_POST['tvs_height'])>0 ){
				$height = intval($_POST['tvs_height']);
			}
			if( isset($_POST['tvs_seofriendly']) && is_numeric($_POST['tvs_seofriendly']) && intval($_POST['tvs_seofriendly'])>=0 ){
				$seofriendly = ( intval($_POST['tvs_seofriendly']) > 0 ? 1:0 );
			}
			if( isset($_POST['tvs_autoplay']) && is_numeric($_POST['tvs_autoplay']) && intval($_POST['tvs_autoplay'])>=0 ){
				$autoplay = ( intval($_POST['tvs_autoplay']) > 0 ? 1:0 );
			}
			if( isset($_POST['tvs_credit']) && is_numeric($_POST['tvs_credit']) && intval($_POST['tvs_credit'])>=0 ){
				$credit = ( intval($_POST['tvs_credit']) > 0 ? 1:0 );
			}
			if( isset($_POST['tvs_logo']) ){
				$logo = esc_attr(trim($_POST['tvs_logo']));
			}
			if( isset($_POST['tvs_default_channel']) ){
				$channel = esc_attr(strtolower( trim($_POST['tvs_default_channel']) ));
			}
			if( isset($_POST['tvs_channels']) ){
				if( empty($_POST['tvs_channels']) ){
					$showchannel = array();
				}else{
					if( is_array($_POST['tvs_channels']) ){
						$showchannel = $_POST['tvs_channels'];
					}else{
						$showchannel = explode(',', $_POST['tvs_channels']);
					}
				}
			}
			if( isset($_POST['tvs_icon']) && is_numeric($_POST['tvs_icon']) && intval($_POST['tvs_icon'])>0 ){
				$icon = intval($_POST['tvs_icon']);
			}

			$_POST['tvs_width'] = $width;
			$_POST['tvs_height'] = $height;
			$_POST['tvs_seofriendly'] = $seofriendly;
			$_POST['tvs_autoplay'] = $autoplay;
			$_POST['tvs_logo'] = $logo;
			$_POST['tvs_default_channel'] = $channel;
			$_POST['tvs_channels'] = $showchannel;
			$_POST['tvs_icon'] = $icon;
			$_POST['tvs_credit'] = $credit;

			$this->wp_tv_save_options();

			echo "<div id=\"updatemessage\" class=\"updated fade\"><p>".__('TV Streaming Plugin for WordPress settings updated.','tvs_lang')."</p></div>\n";
			echo "<script type=\"text/javascript\" charset=\"". get_bloginfo('charset'). "\">/*<![CDATA[*/setTimeout(function(){jQuery('#updatemessage').hide('slow');}, 3000);/*]]>*/</script>";

		}

		$selection = array();
		$selection[] = __('No', 'tvs_lang');
		$selection[] = __('Yes', 'tvs_lang');

		$selection_icons = array();
		$selection_icons[96] = __('96 Pixel', 'tvs_lang');
		$selection_icons[64] = __('64 Pixel', 'tvs_lang');
		$selection_icons[48] = __('48 Pixel', 'tvs_lang');
		$selection_icons[32] = __('32 Pixel', 'tvs_lang');
	?>
	<div class="wrap">
	<h2><?php _e('TV Streaming Plugin for WordPress', 'tvs_lang'); ?></h2>

	<form method="post" action="">
		<input type="hidden" name="TVS_UPDATE" value="1" />
		<?php wp_nonce_field(basename(__FILE__)); ?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e('Default Width:','tvs_lang'); ?></th>
				<td><input type="text" name="tvs_width" value="<?php echo $this->default_width; ?>" /></td>
			</tr>
			 
			<tr valign="top">
				<th scope="row"><?php _e('Default Height:','tvs_lang'); ?></th>
				<td><input type="text" name="tvs_height" value="<?php echo $this->default_height; ?>" /></td>
			</tr>
			
			<tr valign="top">
				<th scope="row"><?php _e('Default Channel Buttons Size:','tvs_lang'); ?></th>
				<td><select name="tvs_icon"><?php
			foreach($selection_icons as $k=>$v){
				$selected = '';
				if( $k==$this->default_icon ) $selected = ' selected="selected"';
				echo '<option value="'.$k.'"'.$selected.'>'.$v.'</option>';
			}
		  ?></select></td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('SEO Friendly:','tvs_lang'); ?></th>
				<td><select name="tvs_seo"><?php
			foreach($selection as $k=>$v){
				$selected = '';
				if( $k==($this->seo_friendly ? 1:0) ) $selected = ' selected="selected"';
				echo '<option value="'.$k.'"'.$selected.'>'.$v.'</option>';
			}
		  ?></select></td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('Default Logo:','tvs_lang'); ?></th>
				<td><input type="text" name="tvs_logo" value="<?php echo $this->default_logo; ?>" style="width:80%;" /></td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('Default Visible Channels:','tvs_lang'); ?></th>
				<td><?php
			foreach($this->channel_data as $k=>$v){
				$checked = '';
				if( $this->available_channels && in_array($k, $this->available_channels) ) $checked = 'checked="checked" ';
				echo '<input type="checkbox" name="tvs_channels[]" value="'.$k.'" '.$checked.'/> <span>'.$v['title'].'</span><br />';
			}
		  ?></td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('Default Active Channel:','tvs_lang'); ?></th>
				<td><select name="tvs_default_channel"><?php
			foreach($this->channel_data as $k=>$v){
				$selected = '';
				if( $this->default_channel && $k==$this->default_channel ) $selected = ' selected="selected"';
				echo '<option value="'.$k.'"'.$selected.'>'.$v['title'].'</option>';
			}
		  ?></select></td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('Autoplay:','tvs_lang'); ?></th>
				<td><select name="tvs_auto"><?php
			foreach($selection as $k=>$v){
				$selected = '';
				if( $k==($this->autoplay ? 1:0) ) $selected = ' selected="selected"';
				echo '<option value="'.$k.'"'.$selected.'>'.$v.'</option>';
			}
		  ?></select></td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('Display Credit:','tvs_lang'); ?></th>
				<td><select name="tvs_credit"><?php
			foreach($selection as $k=>$v){
				$selected = '';
				if( $k==($this->display_credit ? 1:0) ) $selected = ' selected="selected"';
				echo '<option value="'.$k.'"'.$selected.'>'.$v.'</option>';
			}
		  ?></select> <span><?php _e("Thank you and we really appreciate your help for keep sharing our FREE plugin to the world.", "tvs_lang"); ?></span></td>
			</tr>

		</table>
		
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes','tvs_lang') ?>" />
		</p>

	</form>
	</div>
	<?php }

	function wp_tv_init(){
		load_plugin_textdomain('tvs_lang', FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		wp_register_script( 'tvs', TVStreaming::wp_tv_url().'/tv-min.js', array('jquery'), TVStreaming::wp_tv_version());
		wp_register_style( 'tvs', TVStreaming::wp_tv_url().'/tv.css', array(), TVStreaming::wp_tv_version());

		if( !is_admin() ) {
			
			add_action( 'wp_head', array('TVStreaming', 'wp_tv_header') );
			add_shortcode( 'tv', array('TVStreaming', 'wp_tv_streaming_shortcode') );
		
			wp_enqueue_script( 'tvs' );
			wp_enqueue_style( 'tvs' );

		}else{
			
			// create custom plugin settings menu
			add_action( 'admin_menu', array('TVStreaming', 'wp_tv_create_menu') );
			add_filter( 'plugin_action_links', array('TVStreaming', 'wp_tv_add_action_link'), 10, 2 );

		}
	}

}

//TVStreaming_Widget
class TVStreaming_Widget extends WP_Widget {

	var $tv;

	function TVStreaming_Widget() {
		$this->tv = new TVStreaming();
		
		// Instantiate the parent object
		parent::WP_Widget(false, $name = 'TVStreaming_Widget');
	}

	function widget( $args, $instance ) {
		// Widget output
		extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        ?>
              <?php echo $before_widget; ?>
                  <?php if ( $title ) echo $before_title . $title . $after_title; ?>
                  <?php
					//output HTML Widget
					$showchannel = array();
					if( isset($instance['tvs_showchannel']) && !empty($instance['tvs_showchannel']) ){
						if( is_array($instance['tvs_showchannel']) ){
							$showchannel = $instance['tvs_showchannel'];
						}else{
							$showchannel = explode(',', $instance['tvs_showchannel']);
						}
					}

					echo $this->tv->wp_tv_get_html($instance['tvs_channel'], $showchannel, $instance['tvs_width'], $instance['tvs_height'], $instance['tvs_autoplay'], $instance['tvs_seofriendly'], $instance['tvs_logo'], $instance['tvs_icon']);

				  ?>
              <?php echo $after_widget; ?>
        <?php
	}

	function update( $new_instance, $old_instance ) {
		// Save widget options
		$instance = $old_instance;

		$instance['title'] = strip_tags($new_instance['title']);
		
		if( isset($new_instance['tvs_width']) && is_numeric($new_instance['tvs_width']) && intval($new_instance['tvs_width'])>0 ){
			$width = intval($new_instance['tvs_width']);
		}elseif( isset($old_instance['tvs_width']) && is_numeric($old_instance['tvs_width']) && intval($old_instance['tvs_width'])>0 ){
			$width = intval($old_instance['tvs_width']);
		}else{
			$width = $this->tv->default_width;
		}
		$instance['tvs_width'] = $width;

		
		
		if( isset($new_instance['tvs_height']) && is_numeric($new_instance['tvs_height']) && intval($new_instance['tvs_height'])>0 ){
			$height = intval($new_instance['tvs_height']);
		}elseif( isset($old_instance['tvs_height']) && is_numeric($old_instance['tvs_height']) && intval($old_instance['tvs_height'])>0 ){
			$height = intval($old_instance['tvs_height']);
		}else{
			$height = $this->tv->default_height;
		}
		$instance['tvs_height'] = $height;



		if( isset($new_instance['tvs_seofriendly']) && is_numeric($new_instance['tvs_seofriendly']) && intval($new_instance['tvs_seofriendly'])>=0 ){
			$seofriendly = ( intval($new_instance['tvs_seofriendly']) > 0 ? 1:0 );
		}elseif( isset($old_instance['tvs_seofriendly']) && is_numeric($old_instance['tvs_seofriendly']) && intval($old_instance['tvs_seofriendly'])>=0 ){
			$seofriendly = ( intval($old_instance['tvs_seofriendly']) > 0 ? 1:0 );
		}else{
			$seofriendly = ($this->tv->seo_friendly ? 1:0);
		}
		$instance['tvs_seofriendly'] = $seofriendly;


		
		if( isset($new_instance['tvs_autoplay']) && is_numeric($new_instance['tvs_autoplay']) && intval($new_instance['tvs_autoplay'])>=0 ){
			$autoplay = ( intval($new_instance['tvs_autoplay']) > 0 ? 1:0 );
		}elseif( isset($old_instance['tvs_autoplay']) && is_numeric($old_instance['tvs_autoplay']) && intval($old_instance['tvs_autoplay'])>=0 ){
			$autoplay = ( intval($old_instance['tvs_autoplay']) > 0 ? 1:0 );
		}else{
			$autoplay = ($this->tv->autoplay ? 1:0);
		}
		$instance['tvs_autoplay'] = $autoplay;



		if( isset($new_instance['tvs_logo']) ){
			$logo = strip_tags(trim($new_instance['tvs_logo']));
		}elseif( isset($old_instance['tvs_logo']) ){
			$logo = strip_tags(trim($old_instance['tvs_logo']));
		}else{
			$logo = $this->tv->default_logo;
		}
		$instance['tvs_logo'] = $logo;


		if( isset($new_instance['tvs_channel']) ){
			$channel = strip_tags(strtolower( trim($new_instance['tvs_channel']) ));
		}elseif( isset($old_instance['tvs_channel']) ){
			$channel = strip_tags(strtolower( trim($old_instance['tvs_channel']) ));
		}else{
			$channel = $this->tv->default_channel;
		}
		$instance['tvs_channel'] = $channel;


		
		if( isset($new_instance['tvs_showchannel']) ){
			if( empty($new_instance['tvs_showchannel']) ){
				$showchannel = array();
			}else{
				if( is_array($new_instance['tvs_showchannel']) ){
					$showchannel = $new_instance['tvs_showchannel'];
				}else{
					$showchannel = explode(',', $new_instance['tvs_showchannel']);
				}
			}
		}elseif( isset($old_instance['tvs_showchannel']) ){
			if( empty($old_instance['tvs_showchannel']) ){
				$showchannel = array();
			}else{
				if( is_array($old_instance['tvs_showchannel']) ){
					$showchannel = $old_instance['tvs_showchannel'];
				}else{
					$showchannel = explode(',', $old_instance['tvs_showchannel']);
				}
			}
		}else{
			$showchannel = $this->tv->available_channels;
		}
		$instance['tvs_showchannel'] = $showchannel;


		if( isset($new_instance['tvs_icon']) && is_numeric($new_instance['tvs_icon']) && intval($new_instance['tvs_icon'])>0 ){
			$icon = intval($new_instance['tvs_icon']);
		}elseif( isset($old_instance['tvs_icon']) && is_numeric($old_instance['tvs_icon']) && intval($old_instance['tvs_icon'])>0 ){
			$icon = intval($old_instance['tvs_icon']);
		}else{
			$icon = $this->tv->default_icon;
		}
		$instance['tvs_icon'] = $icon;
        
		return $instance;
	}

	function form( $instance ) {
		// Output admin widget options form
		$title = esc_attr($instance['title']);

		$width = $this->tv->default_width;
		if( isset($instance['tvs_width']) && is_numeric($instance['tvs_width']) && intval($instance['tvs_width'])>0 ){
			$width = intval($instance['tvs_width']);
		}

		$height = $this->tv->default_height;
		if( isset($instance['tvs_height']) && is_numeric($instance['tvs_height']) && intval($instance['tvs_height'])>0 ){
			$height = intval($instance['tvs_height']);
		}

		$seofriendly = ($this->tv->seo_friendly ? 1:0);
		if( isset($instance['tvs_seofriendly']) && is_numeric($instance['tvs_seofriendly']) && intval($instance['tvs_seofriendly'])>=0 ){
			$seofriendly = ( intval($instance['tvs_seofriendly']) > 0 ? 1:0 );
		}

		$autoplay = ($this->tv->autoplay ? 1:0);
		if( isset($instance['tvs_autoplay']) && is_numeric($instance['tvs_autoplay']) && intval($instance['tvs_autoplay'])>=0 ){
			$autoplay = ( intval($instance['tvs_autoplay']) > 0 ? 1:0 );
		}

		$logo = $this->tv->default_logo;
		if( isset($instance['tvs_logo']) ){
			$logo = esc_attr(trim($instance['tvs_logo']));
		}

		$channel = $this->tv->default_channel;
		if( isset($instance['tvs_channel']) ){
			$channel = esc_attr(strtolower( trim($instance['tvs_channel']) ));
		}

		$showchannel = $this->tv->available_channels;
		if( isset($instance['tvs_showchannel']) ){
			if( empty($instance['tvs_showchannel']) ){
				$showchannel = array();
			}else{
				if( is_array($instance['tvs_showchannel']) ){
					$showchannel = $instance['tvs_showchannel'];
				}else{
					$showchannel = explode(',', $instance['tvs_showchannel']);
				}
			}
		}

		$icon = $this->tv->default_icon;
		if( isset($instance['tvs_icon']) && is_numeric($instance['tvs_icon']) && intval($instance['tvs_icon'])>0 ){
			$icon = intval($instance['tvs_icon']);
		}

		$selection = array();
		$selection[] = __('No', 'tvs_lang');
		$selection[] = __('Yes', 'tvs_lang');

		$selection_icons = array();
		$selection_icons[96] = __('96 Pixel', 'tvs_lang');
		$selection_icons[64] = __('64 Pixel', 'tvs_lang');
		$selection_icons[48] = __('48 Pixel', 'tvs_lang');
		$selection_icons[32] = __('32 Pixel', 'tvs_lang');

		?>
		<p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'tvs_lang'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>

		<p>
          <label for="<?php echo $this->get_field_id('tvs_width'); ?>"><?php _e('Video Width:', 'tvs_lang'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('tvs_width'); ?>" name="<?php echo $this->get_field_name('tvs_width'); ?>" type="text" value="<?php echo $width; ?>" />
        </p>
		
		<p>
          <label for="<?php echo $this->get_field_id('tvs_height'); ?>"><?php _e('Video Height:', 'tvs_lang'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('tvs_height'); ?>" name="<?php echo $this->get_field_name('tvs_height'); ?>" type="text" value="<?php echo $height; ?>" />
        </p>
		
		<p>
          <label><?php _e('Show Channel Button:', 'tvs_lang'); ?></label><br />
		  <?php
			foreach($this->tv->channel_data as $k=>$v){
				$checked = '';
				if( $showchannel && in_array($k, $showchannel) ) $checked = 'checked="checked" ';
				echo '<input type="checkbox" name="'.$this->get_field_name('tvs_showchannel').'[]" value="'.$k.'" '.$checked.'/> <span>'.$v['title'].'</span><br />';
			}
		  ?>
        </p>

		<p>
          <label for="<?php echo $this->get_field_id('tvs_channel'); ?>"><?php _e('Active Channel:', 'tvs_lang'); ?></label>
		  <select name="<?php echo $this->get_field_name('tvs_channel'); ?>" id="<?php echo $this->get_field_id('tvs_channel'); ?>">
		  <?php
			foreach($this->tv->channel_data as $k=>$v){
				$selected = '';
				if( $channel && $k==$channel ) $selected = ' selected="selected"';
				echo '<option value="'.$k.'"'.$selected.'>'.$v['title'].'</option>';
			}
		  ?>
		  </select>
        </p>

		<p>
          <label for="<?php echo $this->get_field_id('tvs_autoplay'); ?>"><?php _e('Autoplay:', 'tvs_lang'); ?></label>
		  <select name="<?php echo $this->get_field_name('tvs_autoplay'); ?>" id="<?php echo $this->get_field_id('tvs_autoplay'); ?>">
		  <?php
			foreach($selection as $k=>$v){
				$selected = '';
				if( $k==$autoplay ) $selected = ' selected="selected"';
				echo '<option value="'.$k.'"'.$selected.'>'.$v.'</option>';
			}
		  ?>
		  </select>
        </p>

		<p>
          <label for="<?php echo $this->get_field_id('tvs_seofriendly'); ?>"><?php _e('SEO Friendly:', 'tvs_lang'); ?></label>
		  <select name="<?php echo $this->get_field_name('tvs_seofriendly'); ?>" id="<?php echo $this->get_field_id('tvs_seofriendly'); ?>">
		  <?php
			foreach($selection as $k=>$v){
				$selected = '';
				if( $k==$seofriendly ) $selected = ' selected="selected"';
				echo '<option value="'.$k.'"'.$selected.'>'.$v.'</option>';
			}
		  ?>
		  </select>
        </p>

		<p>
          <label for="<?php echo $this->get_field_id('tvs_logo'); ?>"><?php _e('Logo:', 'tvs_lang'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('tvs_logo'); ?>" name="<?php echo $this->get_field_name('tvs_logo'); ?>" type="text" value="<?php echo $logo; ?>" />
        </p>

		<p>
          <label for="<?php echo $this->get_field_id('tvs_icon'); ?>"><?php _e('Icon Size:', 'tvs_lang'); ?></label>
		  <select name="<?php echo $this->get_field_name('tvs_icon'); ?>" id="<?php echo $this->get_field_id('tvs_icon'); ?>">
		  <?php
			foreach($selection_icons as $k=>$v){
				$selected = '';
				if( $k==$icon ) $selected = ' selected="selected"';
				echo '<option value="'.$k.'"'.$selected.'>'.$v.'</option>';
			}
		  ?>
		  </select>
        </p>
		<?php
	}
}

function wp_get_tv_settings_page(){
	$tv = new TVStreaming();
	$tv->wp_tv_settings_page();
}

function load_locale_channels( $locale_force = false, $abs_rel_path = false, $plugin_rel_path = false ) {
	$locale = get_locale();
	if( false !== $locale_force ) $locale = $locale_force;
	if ( false !== $plugin_rel_path ) {
		$path = WP_PLUGIN_DIR . '/' . trim( $plugin_rel_path, '/' );
	} else if ( false !== $abs_rel_path ) {
		_deprecated_argument( __FUNCTION__, '2.7' );
		$path = ABSPATH . trim( $abs_rel_path, '/' );
	} else {
		$path = WP_PLUGIN_DIR;
	}

	$filename = $path . '/'. $locale . '.php';
	if( file_exists($filename) ){
		return $filename;
	}else{
		return FALSE;
	}
}
	
function register_tv_channels($info, $data){
	global $TV_STREAMING_CHANNELS, $TV_LOCALE_CHANNELS;
	
	$do_merge_channels = TRUE;
	$local = $info['code'];
	if( isset($TV_LOCALE_CHANNELS) && is_array($TV_LOCALE_CHANNELS) ){
		if( isset($TV_LOCALE_CHANNELS[$local]) ){
			//ignored
			$do_merge_channels = FALSE;
		}
	}else{
		$TV_LOCALE_CHANNELS = array();
	}

	if( $do_merge_channels ){
		$TV_LOCALE_CHANNELS[$local] = array(
			'name' => $info['name'],
			'data' => $data
		);

		if( isset($TV_STREAMING_CHANNELS) && is_array($TV_STREAMING_CHANNELS) ){
			$TV_STREAMING_CHANNELS = array_merge($TV_STREAMING_CHANNELS, $data);
		}else{
			$TV_STREAMING_CHANNELS = array_merge(array(), $data);
		}
	}
}

add_action( 'init', array('TVStreaming', 'wp_tv_init') );
add_action( 'widgets_init', create_function('', 'return register_widget("TVStreaming_Widget");') );
?>