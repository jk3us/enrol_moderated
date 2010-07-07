<?php

    require("../../config.php");

    // Allow access only to admin 
    require_capability('moodle/legacy:admin', get_context_instance(CONTEXT_SYSTEM), NULL, false);
    
    $navlinks = array();
    $navlinks[] = array('name' => 'Enrolment Requests', 'link' => ".", 'type' => 'misc');
    $navigation = build_navigation($navlinks);
    
    // Show pending requests -> status=0
    $sql = "SELECT em.*, u.id AS uid, u.firstname, u.lastname, u.username, c.shortname
            FROM {$CFG->prefix}enrol_moderated em
            LEFT OUTER JOIN {$CFG->prefix}user u ON u.id = em.userid
            LEFT OUTER JOIN {$CFG->prefix}course c ON c.id = em.courseid
		    WHERE status = 0
            ORDER BY em.created";
    $rows = get_records_sql($sql);
    $output = '<table class="generaltable boxaligncenter" width="95%" cellspacing="1" cellpadding="5">';
    $output .= '<tr>
                <th align="left">'.get_string('name').'</th>
                <th align="left">'.get_string('username').'</th>
                <th align="left">'.get_string('course').'</th>
                <th align="left">'.get_string('date').'</th>
                <th align="left">'.get_string('action').'</th>
                </tr>';
    
    if(!empty($rows)){
        foreach ($rows as $row){
            $url = $CFG->wwwroot.'/enrol/moderated/process_request.php?cid='.$row->courseid.'&uid='.$row->userid;
        	$output .= '<tr>';
            $output .= "<td>$row->firstname $row->lastname</td>";
            $output .= "<td>$row->username</td>";
            $output .= "<td>$row->shortname</td>";
            $output .= "<td>".date('d/m/y',$row->created)."</td>";            
            $output .= "<td><a href='{$url}&action=enrol'>".get_string('aprove','enrol_moderated')."</a> ";
            $output .= "&nbsp;&nbsp;<a href='{$url}&action=delete'>".get_string('reject','enrol_moderated')."</a></td>";
            $output .= '</tr>';    
    	}
    }    
    $output .= '</table>';
    
    print_header(get_string('applicationsenrolment','enrol_moderated'),get_string('applicationsenrolment','enrol_moderated'), $navigation);
    print_box($output);
    print_continue($CFG->wwwroot, $return=false);
    print_footer();
?>