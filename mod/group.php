<?php



function group_init(&$a) {
	require_once('include/group.php');
	$a->page['aside'] .= group_side();

}



function group_post(&$a) {

	if(! local_user()) {
		notice("Access denied." . EOL);
		return;
	}

	if(($a->argc == 2) && ($a->argv[1] == 'new')) {
		$name = notags(trim($_POST['groupname']));
		$r = group_add($_SESSION['uid'],$name);
		if($r) {
			notice("Group created." . EOL );
			$r = group_byname($_SESSION['uid'],$name);
			if($r)
				goaway($a->get_baseurl() . '/group/' . $r);
		}
		else
			notice("Could not create group." . EOL );	
//		goaway($a->get_baseurl() . '/group');
		return; // NOTREACHED
	}

}

function group_content(&$a) {

	if(! local_user()) {
		notice("Access denied." . EOL);
		return;
	}

	if(($a->argc == 2) && ($a->argv[1] == 'new')) {
		$tpl = file_get_contents('view/group_new.tpl');
		$o .= replace_macros($tpl,array(

		));

	}
		
dbg(2);
	if(($a->argc == 2) && (intval($a->argv[1]))) {
		require_once('view/acl_selectors.php');
		$r = q("SELECT * FROM `group` WHERE `id` = %d AND `uid` = %d LIMIT 1",
			intval($a->argv[1]),
			intval($_SESSION['uid'])
		);
		if(! count($r)) {
			notice("Group not found." . EOL );
			goaway($a->get_baseurl() . '/contacts');
		}
		$ret = group_get_members($r[0]['id']);
		$preselected = array();
		if(count($ret))	{
			foreach($ret as $p)
				$preselected[] = $p['id'];
		}
		$sel = contact_select('group_members_select','group_members_select',$preselected);
	$o .= $sel;	
	}





	return $o;

}