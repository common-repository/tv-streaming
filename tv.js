var tv_global_config = new Array();
var tv_custom_channels = new Array();
var tv_default_flash_msie = "<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' width='%WIDTH%' height='%HEIGHT%'>"+
"	<param name='movie' value='http://indoweb.tv/tools/player/player.swf' />"+
"	<param name='quality' value='low' />"+
"	<param name='allowfullscreen' value='true' />"+
"	<param name='allowscriptaccess' value='always' />"+
"	<param name='menu' value='false' />"+
"	<param name='wmode' value='opaque' />"+
"	<param name='flashvars' value='file=%ID%&amp;type=video&amp;screencolor=000000&amp;logo=%LOGO%&amp;streamer=rtmp://stream.indoweb.tv/live&amp;autostart=%AUTO%&amp;stretching=exactfit&amp;controlbar=over&amp;smoothing=false&amp;volume=100' />"+
"		%MESSAGE%"+
"</object>";
var tv_default_flash = "<object type='application/x-shockwave-flash' data='http://indoweb.tv/tools/player/player.swf' width='%WIDTH%' height='%HEIGHT%'>"+
"		<param name='movie' value='http://indoweb.tv/tools/player/player.swf' />"+
"		<param name='quality' value='low' />"+
"		<param name='allowfullscreen' value='true' />"+
"		<param name='allowscriptaccess' value='always' />"+
"		<param name='menu' value='false' />"+
"		<param name='wmode' value='opaque' />"+
"		<param name='flashvars' value='file=%ID%&amp;type=video&amp;screencolor=000000&amp;logo=%LOGO%&amp;streamer=rtmp://stream.indoweb.tv/live&amp;autostart=%AUTO%&amp;stretching=exactfit&amp;controlbar=over&amp;smoothing=false&amp;volume=100' />"+
"		%MESSAGE%"+
"</object>";

jQuery(document).ready(function($){
	$('.tv-streaming .channel a').each(function(i,el){
		
		$parUL = $(el).parent().parent();
		$root = $parUL.parent();
		if( $parUL.hasClass('channels') && $root.hasClass('tv-streaming') ){
			$(el).click(function(e){
				e.preventDefault();
				$channel_title = $(this).html();
				$parUL.find('li.active').removeClass('active');

				$class_names = $(this).parent().attr('class');
				$class_names = $class_names.split(' ');
				$id = '';
				for(i=0; i<$class_names.length; i++){
					if( $class_names[i]!= 'active' && $class_names[i]!='channel' ){
						$id = $class_names[i];
						break;
					}
				}
				$(this).parent().addClass('active');

				//The Real Streaming Embed
				$tv = tv_global_config[$root.attr('id')];

				$str = tv_custom_channels[$id];
				if( $str == '' ){
					if( $.browser.msie ){
						$str = tv_default_flash_msie + "";
					}else{
						$str = tv_default_flash + "";
					}
					$str = $str.replace(/%ID%/gi, $id);

				}else{

					$str = $str.replace(/%ID%/gi, $root.attr('id'));
				}

				$str = $str.replace(/%WIDTH%/gi, $tv.width);
				$str = $str.replace(/%HEIGHT%/gi, $tv.height);
				$str = $str.replace(/%LOGO%/gi, (typeof $tv.logo!='undefined' && typeof $tv.logo!='null' ? $tv.logo:''));
				$str = $str.replace(/%AUTO%/gi, (typeof $tv.auto!='undefined' && typeof $tv.auto!='null' && $tv.auto ? 'true':'false'));

				$altext = $root.find('p.altext:first');
				if( typeof $altext!='undefined' && typeof $altext.html()!='null' ){
					$altext.find('strong:first').html($channel_title);
					$str = $str.replace(/%MESSAGE%/gi, '<p class="altext">'+$altext.html()+'</p>');
				}

				$root.find('.video:first').html($str);
			});
		}
	});
});


function autoplay_tv(id){
	$tv = tv_global_config[id];

	$class_names = jQuery('#'+id).find('li.active:first').attr('class');
	$id = '';
	if( typeof $class_names=='undefined' || typeof $class_names=='null' ){
		//
	}else{
		$class_names = $class_names.split(' ');
		for(i=0; i<$class_names.length; i++){
			if( $class_names[i]!= 'active' && $class_names[i]!='channel' ){
				$id = $class_names[i];
				break;
			}
		}
	}
	if( $id == '' ){
		$class_names = jQuery('#'+id).attr('class');
		$class_names = $class_names.split(' ');
		for(i=0; i<$class_names.length; i++){
			if( $class_names[i]!='tv-streaming' && $class_names[i]!= 'active' && $class_names[i]!='channel' ){
				$id = $class_names[i];
				break;
			}
		}
	}
	$str = tv_custom_channels[$id];
	if( $str == '' ){
		
		if( jQuery.browser.msie ){
			$str = tv_default_flash_msie + "";
		}else{
			$str = tv_default_flash + "";
		}
		$str = $str.replace(/%ID%/gi, $id);
	
	}else{

		$str = $str.replace(/%ID%/gi, id);

	}

	$str = $str.replace(/%WIDTH%/gi, $tv.width);
	$str = $str.replace(/%HEIGHT%/gi, $tv.height);
	$str = $str.replace(/%LOGO%/gi, (typeof $tv.logo!='undefined' && typeof $tv.logo!='null' ? $tv.logo:''));
	$str = $str.replace(/%AUTO%/gi, (typeof $tv.auto!='undefined' && typeof $tv.auto!='null' && $tv.auto ? 'true':'false'));

	$altext = jQuery('#'+id+' p.altext:first');
	if( typeof $altext!='undefined' && typeof $altext.html()!='null' ){
		$str = $str.replace(/%MESSAGE%/gi, '<p class="altext">'+$altext.html()+'</p>');
	}

	jQuery('#'+id).find('.video:first').html($str);
}