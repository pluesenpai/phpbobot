<?php

function ut_ut_recv_go($sck) {
	$rawdata = fgets($sck, 512);
	$rawdata = str_replace("\xFF\xFF\xFF\xFF", "", $rawdata);
	$data = $rawdata;
	$data = str_replace("\r", "", $data);
	$data = str_replace("\n", "", $data);
	if(strlen($data) > 90) {
		$print_data = substr($data, 0, 90) . "...";
	} else {
		$print_data = "$data";
	}
	echo " <<---   $print_data\n";
	return $rawdata;
}

function ut_recv($sck) {
	$rd = "";
	do {
		$rawd = ut_ut_recv_go($sck);
		$rd = "$rd$rawd";
	} while(strlen($rawd) > 1);
	$rd = str_replace("\r", "\n", $rd);
	$rd = str_replace("\n\n", "\n", $rd);
	return $rd;
}

function ut_send($sck, $preut, $data) {
	if(strlen($data) > 90) {
		$print_data = substr($data, 0, 90) . "...";
	} else {
		$print_data = "$data";
	}
	echo "   --->> $print_data\n";
	if($preut == 1) {
		$outdata = "\xFF\xFF\xFF\xFF$data";
	} else {
		$outdata = "$data";
	}
	fwrite($sck, "$outdata\n");
}

function utstatus($socket, $channel, $sender, $msg, $infos) {
	$ut_server = "127.0.0.1";
	$ut_port = 27960;
	$ut_psw = "A9b7h23m1"; #Metti qui la psw di RCon
	$ut = fsockopen("udp://$ut_server", $ut_port, $ut_errno, $ut_errstr);
	if(!$ut) {
		echo "ERRORE!!!!\n\n";
		die("$ut_errstr ($ut_errno)\n");
	}
	ut_send($ut, 1, "rcon $ut_psw status");
	$du = explode("\n", ut_recv($ut));
	$urt_utenti = 0;
	if(count($du) > 4) {
		$rigaa = array();
		$urt_utenti_nick = array();
		for($l=4; $l<(count($du)-1); $l++) {
			$rigaa = $du[$l];
			$riga = $rigaa;
			do {
				$riga_old = $riga;
				$riga = str_replace("  ", " ", $riga);
			} while($riga != $riga_old);
			$urt_utente = explode(" ", $riga);
			$urt_utenti_nick[$l-4] = substr($urt_utente[4], 0, (strlen($urt_utente[4])-2));
			$urt_utenti++;
		}
		$output_utenti = "";
		for($m=0; $m<count($urt_utenti_nick); $m++) {
			if($m == 0) {
				$output_utenti = $urt_utenti_nick[$m];
			} else {
				$output_utenti = $output_utenti . ", " . $urt_utenti_nick[$m];
			}
		}
		sendmsg($socket, "Nel Server ci sono $urt_utenti giocatori: $output_utenti", $channel);
	}
}

?>