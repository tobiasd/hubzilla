<?php
require_once('boot.php');
require_once('include/zot.php');
require_once('include/cli_startup.php');
require_once('include/dir_fns.php');


function directory_run($argv, $argc){

	cli_startup();		 

	if($argc != 2)
		return;

	$dirmode = get_config('system','directory_mode');
	if($dirmode === false)
		$dirmode = DIRECTORY_MODE_NORMAL;

	if(($dirmode == DIRECTORY_MODE_PRIMARY) || ($dirmode == DIRECTORY_MODE_STANDALONE)) {
		// syncdirs();
		return;
	}

	$x = q("select * from channel where channel_id = %d limit 1",
		intval($argv[1])
	);
	if(! $x)
		return;

	$channel = $x[0];

	// is channel profile visible to the public?
	// FIXME - remove dir entry if permission is revoked

	if(! perm_is_allowed($channel['channel_id'],null,'view_profile'))
		return;

	$directory = find_upstream_directory($dirmode);

	if($directory) {
		$url = $directory['url'];
	}
	else {
		$url = DIRECTORY_FALLBACK_MASTER . '/post';
	}

	$packet = zot_build_packet($channel,'refresh');
	$z = zot_zot($url,$packet);

	// re-queue if unsuccessful

}

if (array_search(__file__,get_included_files())===0){
  directory_run($argv,$argc);
  killme();
}
