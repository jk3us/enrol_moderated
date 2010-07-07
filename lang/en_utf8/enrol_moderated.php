<?PHP // $Id$ 
      // enrol_monitored.php 


$string['enrolname'] = 'Moderated enrolment';
$string['description'] = 'Moderated enrolment:
<ol>
<li>The student applies for enrolment.</li>
<li>The student receives an email confirming the aplication.</li>
<li>The specified email account receives a mail with the application data.</li>
<li>An administrator aproves or denies the enrolment application.</li>
<li>The student receives an activation or denial email.</li>
</ul>';
$string['enrolmentrequest'] = 'Would you like to apply for enrolment in this course ?';
$string['enrol_moderated_moderatoremail'] = 'Email of the moderator where the application is send to. If empty the admin user email will be used.';
$string['application'] = 'Your aplication will be reviewed as soon as possible. We will send you an email when your enrolement is activated.';

$string['applicationsubject'] = '$a->site: Application for enrolment in course \"$a->course\"';
$string['applicationbody'] = 'Your aplication will be reviewed as soon as possible. We will send you another email when your enrolement is activated.';
$string['applicationadminbody'] = 'New application for enrolment from student $a->user to course $a->course. To process use this link: $a->url';
$string['applicationbodyrejected'] = 'Your application has been rejected.';
$string['applicationrejected'] = 'The application has been rejected.';
$string['applicationaproved'] = 'The application has been aproved.';

$string['aprove'] = 'Aprove';
$string['reject'] = 'Reject';
$string['applicationsenrolment'] = 'Applications for enrolment';
$string['showapplications'] = 'Show applications';

?>
