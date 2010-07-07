<?PHP // $Id$ 
      // enrol_monitored.php 


$string['enrolname'] = 'Matriculación con moderador';
$string['description'] = 'Matriculación con moderador:
<ol>
<li>El alumno sollicita la matriculación.</li>
<li>El alumno recibe un correo de confirmación de la solicitud.</li>
<li>El correo especificado recibe un correo con los datos de la solicitud.</li>
<li>Un administrador aprueba o denega la solicitud.</li>
<li>El alumno recibe un correo de activación.</li>
</ul>';
$string['enrolmentrequest'] = '¿Quiere solicitar la matriculación en este curso?';
$string['enrol_moderated_moderatoremail'] = 'Correo del moderador que recibe los correos de solicitut. Si es vacio se usará el correo del administrdor.';
$string['application'] = 'Su solicitud será revisada lo antes posible. Le avisaremos por correo quando el curso este activado.';

$string['applicationsubject'] = '$a->site: Solicitud de matriculación para el curso \"$a->course\"';
$string['applicationbody'] = 'Su solicitud será revisada lo antes posible. Recibirá otro correo quando el curs este activado.';
$string['applicationadminbody'] = 'Nueva solicitud de matriculación para el amulno $a->user al curso $a->course. Para procesar utilize este enlace: $a->url';
$string['applicationbodyrejected'] = 'Su solicitut ha sido rechazada.';
$string['applicationrejected'] = 'La solicitud ha sido denegada.';
$string['applicationaproved'] = 'La solicitud ha sido aprobada.';

$string['aprove'] = 'Aprobar';
$string['reject'] = 'Denegar';
$string['applicationsenrolment'] = 'Solicitud de matriculación';
$string['showapplications'] = 'Mostrar solicitudes';

?>
