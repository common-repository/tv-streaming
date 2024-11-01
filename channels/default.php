<?php

if( function_exists("register_tv_channels") ){
	$rcti_rand = mt_rand(1,2);
	register_tv_channels(

		//channel group info
		array(
			"code" => "default",			//channel group slug (use country code for local channel. eg: id_ID, en_US).
			"name" => "Default Channels"	//channel group name/title
		),

		//channels data
		array(
			//channel slug must be unique since it will reserved as the key
			"rcti"=>array(
				//
				"stream"	=> "rcti",

				//cannel name/title
				"title"		=> "RCTI",
				
				//the embed flash code.
				"rcti"		=> "<object id='flash-%ID%' classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' width='%WIDTH%' height='%HEIGHT%'><param name='movie' value='http://indoweb.tv/tools/player/player.swf' /><param name='quality' value='low' /><param name='allowfullscreen' value='true' /><param name='allowscriptaccess' value='always' /><param name='menu' value='false' /><param name='wmode' value='opaque' /><param name='flashvars' value='file=rcti_".$rcti_rand."&amp;type=video&amp;screencolor=000000&amp;logo=%LOGO%&amp;streamer=rtmp://202.147.200.196/live/stream_rcti&amp;autostart=%AUTO%&amp;stretching=exactfit&amp;controlbar=over&amp;smoothing=false&amp;volume=100' /><!--[if !IE]>--><object type='application/x-shockwave-flash' data='http://indoweb.tv/tools/player/player.swf' width='%WIDTH%' height='%HEIGHT%'><param name='movie' value='http://indoweb.tv/tools/player/player.swf' /><param name='quality' value='low' /><param name='allowfullscreen' value='true' /><param name='allowscriptaccess' value='always' /><param name='menu' value='false' /><param name='wmode' value='opaque' /><param name='flashvars' value='file=rcti_".$rcti_rand."&amp;type=video&amp;screencolor=000000&amp;logo=%LOGO%&amp;streamer=rtmp://202.147.200.196/live/stream_rcti&amp;autostart=%AUTO%&amp;stretching=exactfit&amp;controlbar=over&amp;smoothing=false&amp;volume=100' /><!--<![endif]-->%MESSAGE%<!--[if !IE]>--></object><!--<![endif]--></object>"
			),
			"sctv"=>array(
				"stream"=>"sctv",
				"title"=>"SCTV"
			),
			"trans"=>array(
				"stream"=>"trans",
				"title"=>"Trans TV"
			),
			"indosiar"=>array(
				"stream"=>"indosiar",
				"title"=>"Indosiar"
			),
			"antv"=>array(
				"stream"=>"antv",
				"title"=>"ANTV"
			),
			"global"=>array(
				"stream"=>"global",
				"title"=>"Global TV"
			),
			"tvone"=>array(
				"stream"=>"tv1",
				"title"=>"TV One"
			),
			"metro"=>array(
				"stream"=>"metro",
				"title"=>"Metro TV"
			),
			"spacetoon"=>array(
				"stream"=>"spt",
				"title"=>"Spacetoon"
			),
			"mnc"=>array(
				"stream"=>"tpi",
				"title"=>"MNC Network"
			),
			"daai"=>array(
				"stream"=>"daai",
				"title"=>"DAAI TV"
			),
			"lantabur"=>array(
				"stream"=>"m1x",
				"title"=>"Lantabur TV"
			),
			"laatahzan"=>array(
				"stream"=>"myStream",
				"title"=>"Laatahzan TV"
			),
			"pjtv"=>array(
				"stream"=>"pjtv",
				"title"=>"PJTV"
			),
			"fajar"=>array(
				"stream"=>"2",
				"title"=>"Fajar TV Makassar"
			),
			"jaktv"=>array(
				"stream" => "jaktv",
				"title"  => "JakTV",
				"jaktv"		=> "<object id='flash-%ID%' classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' width='%WIDTH%' height='%HEIGHT%'><param name='movie' value='http://indoweb.tv/tools/player/player.swf' /><param name='quality' value='low' /><param name='allowfullscreen' value='true' /><param name='allowscriptaccess' value='always' /><param name='menu' value='false' /><param name='wmode' value='opaque' /><param name='flashvars' value='file=jaktv&amp;type=video&amp;screencolor=000000&amp;logo=%LOGO%&amp;streamer=rtmp://live.jak-tv.com:5119/live/&amp;autostart=%AUTO%&amp;stretching=exactfit&amp;controlbar=over&amp;smoothing=false&amp;volume=100' /><!--[if !IE]>--><object type='application/x-shockwave-flash' data='http://indoweb.tv/tools/player/player.swf' width='%WIDTH%' height='%HEIGHT%'><param name='movie' value='http://indoweb.tv/tools/player/player.swf' /><param name='quality' value='low' /><param name='allowfullscreen' value='true' /><param name='allowscriptaccess' value='always' /><param name='menu' value='false' /><param name='wmode' value='opaque' /><param name='flashvars' value='file=jaktv&amp;type=video&amp;screencolor=000000&amp;logo=%LOGO%&amp;streamer=rtmp://live.jak-tv.com:5119/live/&amp;autostart=%AUTO%&amp;stretching=exactfit&amp;controlbar=over&amp;smoothing=false&amp;volume=100' /><!--<![endif]-->%MESSAGE%<!--[if !IE]>--></object><!--<![endif]--></object>"
			)
		
		)//end-channels data

	); //end-register_tv_channels
}

?>