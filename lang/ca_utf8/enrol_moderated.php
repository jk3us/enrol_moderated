<?PHP // $Id$ 
      // enrol_monitored.php 


$string['enrolname'] = 'Matriculació amb moderador';
$string['description'] = 'Matriculació amb moderador:
<ol>
<li>L\'alumne sol·licita la matriculació.</li>
<li>L\'alumne rep un correu de confirmació de la sol·licitut.</li>
<li>El correu especificat rep un correu amb les dades de la sol·licitut.</li>
<li>Un administrador aprova o denega la sol·licitut.</li>
<li>L\'alumne rep un correu de activació.</li>
</ul>';
$string['enrolmentrequest'] = 'Voleu sol·licitar la matriculació a aquest curs?';
$string['enrol_moderated_moderatoremail'] = 'Correu del moderador que rep els correus de sol·licitut. Si es buit s\'utilitza el correu del administrdor.';
$string['application'] = 'La vostre sol·licitut es revisarà el més aviat possible. Us enviarem un correu quan el curs estigui activat.';

$string['applicationsubject'] = '$a->site: Sol·licitut de matriculació per el curs \"$a->course\"';
$string['applicationbody'] = 'La vostre sol·licitut sera revisada el més aviat possible. Rebrà un altre correu quan el curs estigui activat.';
$string['applicationadminbody'] = 'Nova sol·licitut de matriculació per l\'alumne $a->user al curs $a->course. Per processar utilitzeu aquest enllaç: $a->url';
$string['applicationbodyrejected'] = 'La vostre sol·licitut ha sigut denegada.';
$string['applicationrejected'] = 'La sol·licitut ha sigut denegada.';
$string['applicationaproved'] = 'La sol·licitut ha sigut aprovada.';

$string['aprove'] = 'Aprovar';
$string['reject'] = 'Denegar';
$string['applicationsenrolment'] = 'Sol·licituts de matriculació';
$string['showapplications'] = 'Mostrar sol·licituts';

?>
