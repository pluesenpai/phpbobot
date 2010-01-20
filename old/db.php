<?php

	function aggiungi_user($db, $user, $chan, $password = "", $mess = "", $modes = "")
	{
		$iduser = $db->verifica_user($user, $password);
		$idchan = $db->verifica_chan($chan);

		if($mess != "") {
			$idsaluto = $db->verifica_saluto($mess);
			$db->verifica_entra($iduser, $idchan, $idsaluto, $modes);
		}
	}

	function elimina_saluto($db, $user, $chan)
	{
		$iduser = $db->verifica_user($user);
		$idchan = $db->verifica_chan($chan);

		$db->del_saluto($iduser, $idchan);
	}

	function saluto($db, $user, $chan)
	{
		$iduser = $db->verifica_user($user);
		$idchan = $db->verifica_chan($chan);

		return $db->get_saluto($iduser, $idchan);
	}

	function mode($db, $user, $chan)
	{
		$iduser = $db->verifica_user($user);
		$idchan = $db->verifica_chan($chan);

		echo "chan: $idchan, user: $iduser => '" . $db->get_mode($iduser, $idchan) . "'";

		return $db->get_mode($iduser, $idchan);
	}

	function list_operatori($db)
	{
		return $db->get_operatori();
	}

	function operatore($db, $user)
	{
		$iduser = $db->verifica_user($user);

		$db->set_operatore($iduser);
	}

	function elimina_operatore($db, $user)
	{
		$iduser = $db->verifica_user($user);

		$db->del_operatore($iduser);
	}
?>