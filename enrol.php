<?php  // $Id: enrol.php,v 1.0 2009/10/19 19:21:11 skodak Exp $
       // Implements all the main code for the moderated enrolment

require_once("$CFG->dirroot/enrol/enrol.class.php");

class enrolment_plugin_moderated {

var $errormsg;

/**
* Prints the entry form/page for this enrolment
*
* This is only called from course/enrol.php
* Most plugins will probably override this to print payment ...
*
* @param    course  current course object
*/
function print_entry($course) {
    global $CFG, $USER, $SESSION, $THEME, $SITE;

    $strloginto = get_string('loginto', '', $course->shortname);
    $strcourses = get_string('courses');

    $context = get_context_instance(CONTEXT_SYSTEM);

    $navlinks = array();
    $navlinks[] = array('name' => $strcourses, 'link' => ".", 'type' => 'misc');
    $navlinks[] = array('name' => $strloginto, 'link' => null, 'type' => 'misc');
    $navigation = build_navigation($navlinks);

    if (has_capability('moodle/legacy:guest', $context, $USER->id, false)) {
        add_to_log($course->id, 'course', 'guest', 'view.php?id='.$course->id, getremoteaddr());
        return;
    }
    
    if (empty($_GET['confirm']) && empty($_GET['cancel'])) {
        print_header($strloginto, $course->fullname, $navigation);
        echo '<br />';
        notice_yesno(get_string('enrolmentrequest','enrol_moderated'), "enrol.php?id=$course->id&amp;confirm=1",
                                                      "enrol.php?id=$course->id&amp;cancel=1");
        print_footer();
        return;
    }

    if (!empty($_GET['confirm'])) {
        print_header($strloginto, $course->fullname, $navigation);
        print_box(get_string('application','enrol_moderated'));
        print_continue($CFG->wwwroot, $return=false);
        print_footer();
        // Send email to student
        $a->site = $SITE->shortname;
        $a->course = $course->shortname;        
        $subject = get_string('applicationsubject','enrol_moderated',$a);
        $body = get_string('applicationbody','enrol_moderated');
        email_to_user($USER, $SITE->shortname, $subject, $body);
        
        // Send email to admin
        $a->url = $CFG->wwwroot.'/enrol/moderated/show_requests.php';
        $a->user = $USER->username;
        $body = get_string('applicationadminbody','enrol_moderated',$a);
        if (isset($CFG->enrol_moderated_moderatoremail)){
            $dest->email = $CFG->enrol_moderated_moderatoremail;
        } else {
            $dest = get_admin();            
        }
        email_to_user($dest, $SITE->shortname, $subject, $body);

        // Check if user has already requested enrolment for the same course
        if (!get_record('enrol_moderated','userid',$USER->id,'courseid',$course->id)){
                // Create db entry
                $data = new object();
                $data->courseid = $course->id;
                $data->userid = $USER->id;
                $data->created = time();
                $data->updated = time();
                $data->status = 0; // pending 
                insert_record('enrol_moderated', addslashes_object($data), false);		            	
        }        
        return;
    }
            
    if (!empty($_GET['cancel'])) {
        unset($SESSION->wantsurl);
        if (!empty($SESSION->enrolcancel)) {
            $destination = $SESSION->enrolcancel;
            unset($SESSION->enrolcancel);
        } else {
            $destination = $CFG->wwwroot;
        }
        redirect($destination);
    }
}



/**
* The other half to print_entry, this checks the form data
*
* This function checks that the user has completed the task on the
* enrolment entry page and then enrolls them.
*
* @param    form    the form data submitted, as an object
* @param    course  the current course, as an object
*/
function check_entry($form, $course) {
    global $CFG, $USER, $SESSION, $THEME;

    if (empty($form->password)) {
        $form->password = '';
    }

    if (empty($course->password)) {
        // do not allow entry when no course password set
        // automatic login when moderated primary, no login when secondary at all!!
        error('illegal enrolment attempted');
    }

    $groupid = $this->check_group_entry($course->id, $form->password);

    if ((stripslashes($form->password) == $course->password) or ($groupid !== false) ) {

        if (isguestuser()) { // only real user guest, do not use this for users with guest role
            $USER->enrolkey[$course->id] = true;
            add_to_log($course->id, 'course', 'guest', 'view.php?id='.$course->id, getremoteaddr());

        } else {  /// Update or add new enrolment
            if (enrol_into_course($course, $USER, 'moderated')) {
                // force a refresh of mycourses
                unset($USER->mycourses);
                if ($groupid !== false) {
                    if (!groups_add_member($groupid, $USER->id)) {
                        print_error('couldnotassigngroup');
                    }
                }
		// delete request because we just dispached it
                delete_records('enrol_moderated','userid',$USER->id,'courseid',$course->id,status,1); 
            } else {
                print_error('couldnotassignrole');
            }
        }

        if ($SESSION->wantsurl) {
            $destination = $SESSION->wantsurl;
            unset($SESSION->wantsurl);
        } else {
            $destination = "$CFG->wwwroot/course/view.php?id=$course->id";
        }

        redirect($destination);

    } else if (!isset($CFG->enrol_moderated_showhint) or $CFG->enrol_moderated_showhint) {
        $this->errormsg = get_string('enrolmentkeyhint', '', substr($course->password, 0, 1));

    } else {
        $this->errormsg = get_string('enrolmentkeyerror', 'enrol_moderated');
    }
}


/**
* Check if the given enrolment key matches a group enrolment key for the given course
*
* @param    courseid  the current course id
* @param    password  the submitted enrolment key
*/
function check_group_entry ($courseid, $password) {

    if ($groups = groups_get_all_groups($courseid)) {
        foreach ($groups as $group) {
            if ( !empty($group->enrolmentkey) and (stripslashes($password) == $group->enrolmentkey) ) {
                return $group->id;
            }
        }
    }

    return false;
}


/**
* Prints a form for configuring the current enrolment plugin
*
* This function is called from admin/enrol.php, and outputs a
* full page with a form for defining the current enrolment plugin.
*
* @param    frm  an object containing all the data for this page
*/
function config_form($frm) {
    global $CFG;

    if (!isset( $frm->enrol_moderated_keyholderrole )) {
        $frm->enrol_moderated_keyholderrole = '';
    }

    if (!isset($frm->enrol_moderated_showhint)) {
        $frm->enrol_moderated_showhint = 1;
    }

    if (!isset($frm->enrol_moderated_usepasswordpolicy)) {
        $frm->enrol_moderated_usepasswordpolicy = 0;
    }

    if (!isset($frm->enrol_moderated_requirekey)) {
        $frm->enrol_moderated_requirekey = 0;
    }

    include ("$CFG->dirroot/enrol/moderated/config.html");
}


/**
* Processes and stored configuration data for the enrolment plugin
*
* @param    config  all the configuration data as entered by the admin
*/
function process_config($config) {

    $return = true;

    foreach ($config as $name => $value) {
        if (!set_config($name, $value)) {
            $return = false;
        }
    }

    return $return;
}



/**
* Returns the relevant icons for a course
*
* @param    course  the current course, as an object
*/
function get_access_icons($course) {
    global $CFG;

    global $strallowguests;
    global $strrequireskey;

    if (empty($strallowguests)) {
        $strallowguests = get_string('allowguests');
        $strrequireskey = get_string('requireskey');
    }

    $str = '';

    if (!empty($course->guest)) {
        $str .= '<a title="'.$strallowguests.'" href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">';
        $str .= '<img class="accessicon" alt="'.$strallowguests.'" src="'.$CFG->pixpath.'/i/guest.gif" /></a>&nbsp;&nbsp;';
    }
    if (!empty($course->password)) {
        $str .= '<a title="'.$strrequireskey.'" href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">';
        $str .= '<img class="accessicon" alt="'.$strrequireskey.'" src="'.$CFG->pixpath.'/i/key.gif" /></a>';
    }

    return $str;
}

/**
 * Prints the message telling you were to get the enrolment key
 * appropriate for the prevailing circumstances
 * A bit clunky because I didn't want to change the standard strings
 */
function print_enrolmentkeyfrom($course) {
    global $CFG;
    global $USER;

    $context = get_context_instance(CONTEXT_SYSTEM);
    $guest = has_capability('moodle/legacy:guest', $context, $USER->id, false);

    // if a keyholder role is defined we list teachers in that role (if any exist)
    $contactslisted = false;
    $canseehidden = has_capability('moodle/role:viewhiddenassigns', $context);
    if (!empty($CFG->enrol_moderated_keyholderrole)) {
        if ($contacts = get_role_users($CFG->enrol_moderated_keyholderrole, get_context_instance(CONTEXT_COURSE, $course->id),true,'','u.lastname ASC',$canseehidden  )) {
            // guest user has a slightly different message
            if ($guest) {
                print_string('enrolmentkeyfromguest', '', ':<br />' );
            }
            else {
                print_string('enrolmentkeyfrom', '', ':<br />');
            }
            foreach ($contacts as $contact) {
                $contactname = "<a href=\"../user/view.php?id=$contact->id&course=".SITEID."\">".fullname($contact)."</a>.";
                echo "$contactname<br />";
            }
            $contactslisted = true;
        }
    }

    // if no keyholder role is defined OR nobody is in that role we do this the 'old' way
    // (show the first person with update rights)
    if (!$contactslisted) {
        if ($teachers = get_users_by_capability(get_context_instance(CONTEXT_COURSE, $course->id), 'moodle/course:update',
            'u.*', 'u.id ASC', 0, 1, '', '', false, true)) {
            $teacher = array_shift($teachers);
        }
        if (!empty($teacher)) {
            $teachername = "<a href=\"../user/view.php?id=$teacher->id&course=".SITEID."\">".fullname($teacher)."</a>.";
        } else {
            $teachername = strtolower( get_string('defaultcourseteacher') ); //get_string('yourteacher', '', $course->teacher);
        }

        // guest user has a slightly different message
        if ($guest) {
            print_string('enrolmentkeyfromguest', '', $teachername );
        }
        else {
            print_string('enrolmentkeyfrom', '', $teachername);
        }
    }
}

} /// end of class

?>
