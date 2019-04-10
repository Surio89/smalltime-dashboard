<?php
	/********************************************************************************
	* Small Time
	/*******************************************************************************
	* Version 0.9.003
	* Author:  IT-Master GmbH
	* www.it-master.ch / info@it-master.ch
	* Copyright (c), IT-Master GmbH, All rights reserved
	********************************************************************************/
	// -----------------------------------------------------------------------------
	// idtime - Stempelzeit via Direkt-URL eintragen, z.B. ID oder
	//          komplette URL von einem Barcode-Scanner
	//
	// Aufruf: SCRIPT_NAME?id=<id>, z.B. http://server/idtime.php?id=f0ab4565d3ead4c9
	//         <id> - SHA-1 aus Benutzer-Login + Benutzer-Passwort-SHA-1
	//                + Blowfish-Hash des Benutzer-Logins,"gesaltet" mit
	//                mit einem "secret" und dem SHA-1 des Benutzer-Passworts 
	// 
	// ACHTUNG: Es wird kein Benutzername oder Passwort abgefragt!
	//          ID-Verfahren weist Sicherheitsmängel auf: Jeder, dem das "secret"
	//          sowie der Passwort-SHA-1 bekannt ist, kann die ID nachbilden!
	//          Wenn das "secret" hier geändert wird, muss es auch in
	//          ./modules/sites_admin/admin04_idtime_generate.php angepasst werden!
	//
	$idtime_secret = 'CHANGEME'; // [./0-9A-Za-z] Mehr als 21 Zeichen führen dazu, dass das Benutzer-Passwort nicht mehr in die ID-Generierung einfliesst.	
	// -----------------------------------------------------------------------------
	// Benutzerdaten in Array ( ID => Pfad ) lesen:
	$_stempelid = array();
	$succss = "leer";
	$realstamp = "leer";
	
	$fp = @fopen('./Data/users.txt', 'r');
	@fgets($fp); // erste Zeile überspringen
	while (($logindata = fgetcsv($fp, 0, ';')) != false) {
		if(isset($_GET['rfid'])) {
			$tempid=trim(@$logindata[3]);
			$tempid = str_ireplace('\r','',$tempid);
			$tempid = str_ireplace('\n','',$tempid);
			if($tempid==@$_GET['rfid']){
				$user = $logindata[0];
			}
		}elseif(isset($_GET['id'])){
			$hash = sha1($logindata[1].$logindata[2].crypt($logindata[1], '$2y$04$'.substr($idtime_secret.$logindata[2], 0, 22)));
			$ID = substr($hash, 0, 16);
			$_stempelid[$ID] = $logindata[0];
			}
	}
	fclose($fp);
	// -----------------------------------------------------------------------------
	// übergebene ID Benutzer zuordnen und Stempelzeit eintragen:
	if (isset($_GET['id'])) {
		$ID = substr($_GET['id'], 0, 16);
		//echo "<div id='usershort' id-usershort='".$_stempelid[$ID]."'></div>";
		if (isset($_stempelid[$ID])) {
			$user = $_stempelid[$ID];
			$_timestamp = time();
			
			
			//Stempelzeit berechnen
			$_w_jahr     = date("Y", time());
			$_w_monat    = date("n", time());
			$_w_tag      = date("j", time());
			$_w_stunde   = date("H", time());
			$_w_minute   = date("i", time());
			$_w_sekunde  = date("s", time());

			$_w_jahr_t   = date("Y", $_timestamp);
			$_w_monat_t  = date("n", $_timestamp);
			$_w_tag_t    = date("j", $_timestamp);
			$_w_stunde_t = date("H", $_timestamp);
			$_w_minute_t = date("i", $_timestamp);
			$_w_sekunde_t= date("s", $_timestamp);

			if($_w_jahr == $_w_jahr_t && $_w_monat == $_w_monat_t && $_w_tag == $_w_tag_t)
			{
				$realstamp = $_w_stunde_t . ":" . $_w_minute_t;
			}			
			#END
			
			
			$_zeilenvorschub= "\r\n";
			$_file = './Data/' . $user . '/Timetable/' . date('Y') . '.' . date('n');
			$fp = fopen($_file, 'a+b') or die("FEHLER - Konnte Stempeldatei nicht &ouml;ffnen!");
			fputs($fp, $_timestamp.$_zeilenvorschub);
			fclose($fp);
			$succss = '<div id="succss" id-status="1"></div>';
			txt("1",$realstamp,$user);
			
			//$_SESSION['time'] = true; // ?
		}
		else txt("0","","");
	}elseif(isset($_GET['rfid'])){
		if(isset($user)){
			$_timestamp = time();
			
	

			//Stempelzeit berechnen
			$_w_jahr     = date("Y", time());
			$_w_monat    = date("n", time());
			$_w_tag      = date("j", time());
			$_w_stunde   = date("H", time());
			$_w_minute   = date("i", time());
			$_w_sekunde  = date("s", time());

			$_w_jahr_t   = date("Y", $_timestamp);
			$_w_monat_t  = date("n", $_timestamp);
			$_w_tag_t    = date("j", $_timestamp);
			$_w_stunde_t = date("H", $_timestamp);
			$_w_minute_t = date("i", $_timestamp);
			$_w_sekunde_t= date("s", $_timestamp);

			if($_w_jahr == $_w_jahr_t && $_w_monat == $_w_monat_t && $_w_tag == $_w_tag_t)
			{
				$realstamp = $_w_stunde_t . ":" . $_w_minute_t;
			}
			#END
			
			
			$_zeilenvorschub= "\r\n";
			$_file = './Data/' . $user . '/Timetable/' . date('Y') . '.' . date('n');
			$fp = fopen($_file, 'a+b') or die("FEHLER - Konnte Stempeldatei nicht &ouml;ffnen!");
			fputs($fp, $_timestamp.$_zeilenvorschub);
			fclose($fp);
			$succss = '<div id="succss" id-status="1"></div>';
			txt("1",$realstamp,"");
						
			//$_SESSION['time'] = true; // ?			
		}else txt("0","","");
	}else{ 
		$succss = '<div id="succss" id-status="0"></div>';
		txt("0","","");	
		
	}
	function txt($ok,$realstamp,$stempelid) {
		//echo '<p style="color:'.($ok?'green':'red').'">' . $txt . '</p>';
		echo json_encode( array( "ok"=>$ok,"time"=>$realstamp,"user"=>$stempelid ) ); 
	}
	

	
?>
