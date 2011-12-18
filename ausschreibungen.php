<?php

/*
 * ausschreibungen
 */
/*
 * lib einlesen
 */
require('lib/functions.php');

/*
 * konfig einlesen
 */
init();

/*
 * session starten
 */
session_start();


/*
 * objekt erzeugen und inhalt anhaengen
 */
$main = new NBmain_ausschreibungen();


$main->inhalt('<h3>Termine</h3>');
$main->inhalt('<p>hier k&ouml;nnen Ausschreibungen und Termine erstellt und bearbeitet werden</p>');

$main->getids('termin','termin',array());
$main->getids('ausschreibung','ausschreibung',array());

$main->to_html();



?>