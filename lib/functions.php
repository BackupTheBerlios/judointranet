<?php








/**
 * setzt einige standardeinstellungen fuer das laufende skript und liest die konfig ein
 */
function init() {
	
	// sicherung der skripte definieren
	define("SICHERUNG", 'Sicherung');
	
	// konfiguration einlesen
	$GLOBALS['konfig'] = new NBkonfig('cnf/config.ini');
	$GC = $GLOBALS['konfig'];
	
	// locale fuer datum auf deutsch setzen
	setlocale(LC_ALL, 'de_DE@euro');
	
	// zeitzone setzen
	date_default_timezone_set($GC->return_konfig('global','timezone'));
	
	// admin-button-beschriftungen setzen
	$GC->add_to_konfig('intern','submit_name','submit');
	$GC->add_to_konfig('intern','submit_wert','Speichern');
	
	// login-button-beschriftungen setzen
	$GC->add_to_konfig('intern','submit_login_name','submit_login');
	$GC->add_to_konfig('intern','submit_login_wert','Login');
}






/**
 * laed die klassendefinition der uebergebenen klasse
 */
function __autoload($klasse) {
	
	// klassen laden
	$dateiname = 'class_'.substr($klasse,2).'.php';
	include('lib/classes/'.$dateiname);
}






?>