<?php

    require("../../config.php");
    
    // Allow access only to admin 
    require_capability('moodle/legacy:admin', get_context_instance(CONTEXT_SYSTEM), NULL, false);
    
    // Get mandatory params
    $cid = required_param('cid', PARAM_INT); // course
    $uid = required_param('uid', PARAM_INT); // user
    $action = required_param('action', PARAM_ACTION); // enrol OR delete
    
    $course = get_record("course", "id", $cid);
    $user = get_record("user", "id", $uid);     
    
    if($action=='delete'){
		delete_records('enrol_moderated','userid',$uid,'courseid',$cid); 
		// Send email to student
		$a->site = $SITE->shortname;
        $a->course = $course->shortname;        

        $subject = get_string('applicationsubject','enrol_moderated',$a);
        $body = get_string('applicationbodyrejected','enrol_moderated');
		email_to_user($user, $SITE->shortname, $subject, $body);
		redirect($CFG->wwwroot.'/enrol/moderated/show_requests.php',get_string('applicationrejected','enrol_moderated'),3);    
    }
     
    if ($action=='enrol'){
        if (!enrol_into_course($course, $user, 'moderated')) {
	       print_error('couldnotassignrole');
        }
		delete_records('enrol_moderated','userid',$uid,'courseid',$cid); 
		// No needo to send email because enrol_into_course() sends it.
		redirect($CFG->wwwroot.'/enrol/moderated/show_requests.php',get_string('applicationaproved','enrol_moderated'),3);    
    }
?>