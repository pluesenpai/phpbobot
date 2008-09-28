#!/usr/bin/php -n

<?php
/*
TODO:
  1) Inserimento semafori nella send per evitare invio dello stesso messaggio!!!
*/

system("clear");

declare(ticks = 1);
$debug = 1;
$irc_server = "irc.syrolnet.org";
$irc_port = 6668;
$irc_chan = "#sardylan";
$user_name = "filetor";
$user_psw = "E_CHE_LA_VENGO_A_DIRE_A_VOI????";

$functions = array();

function chiama($folder, $fun, $irc, $irc_chan, $infos)
{
  $pid = pcntl_fork();
  if($pid == -1) {
    die("Could not fork");
  } elseif(!$pid) {
    include_once("functions/$folder/$fun.php");
    call_user_func($fun, $irc, $irc_chan, $infos);
    posix_kill(posix_getppid(), SIGUSR1);
    posix_kill(posix_getpid(), 9);
  }
}

function get_from_file($file)
{
  $words_array = preg_replace("#\r\n?|\n#", "", file($file)); // restituisce un array in cui ogni elemento e' preso da una riga del file

  return $words_array;
}

function removeExtension($fileName)
{
  $ext = strrchr($fileName, '.');

  if($ext !== false) {
    $fileName = substr($fileName, 0, -strlen($ext));
  }

  return $fileName;
}

function getFunctions() // Scan delle dir e recupero delle funzioni per ogni cartella
{
  $folders = getDirs("functions/");

  $i = 0;

  foreach($folders as $folder) {
    $xml = simplexml_load_file("functions/".$folder."/functions.xml");
    foreach($xml->function as $func) {
      $j = 0;
      $functions[$i][$j++] = $folder;
      $functions[$i][$j++] = $func->name;
      $functions[$i][$j++] = $func->privileged;
      $functions[$i][$j++] = $func->regex;
      $functions[$i++][$j++] = $func->descr;
    }
  }

  return $functions;
}

function getDirs($dir) // Elenco cartelle dei plugin
{
  $files = scandir($dir);

  foreach($files as $i => $value) {
    if(substr($value, 0, 1) == '.')           // Removes . and ..
      unset($files[$i]);
    elseif(!is_dir($dir.$value))              // Removes Files
      unset($files[$i]);
  }

  return array_values($files);
}

function getFiles($dir, $type) // Elenco file dentro la cartella
{
  $files = scandir($dir);
  $type_len = strlen($type);

  foreach($files as $i => $value) {
    if(substr($value, 0, 1) == '.')             // Removes . and ..
      unset($files[$i]);
    elseif(is_dir($dir.$value))                 // Removes Directories
      unset($files[$i]);
    elseif(substr($value, -1, 1) == '~')        // Removes Backup Files
      unset($files[$i]);
    elseif(substr($value, -$type_len, $type_len) != $type)  //Removes files where extension is different from $type
      unset($files[$i]);
  }

  return array_values($files); // Ripristino degli indici dell'array
}

function dbg($debug, $text) {
  if($debug) {
    echo " [ deb ] $text\n";
  }
}

///
function send($stream, $data, $ritardo = 0) {
//   $shm = shm_attach(1);
//   shm_put_var($shm, 7, utf8_encode($data));
//   $sem = sem_get(1, 1);
  $pid_write = pcntl_fork();
  if($pid_write == -1) {
    echo "ERROR:  Cannot fork!!!\n";
    die();
  } elseif($pid_write) {
    return true;
  } elseif(!$pid_write) {
//     $shm = shm_attach(1);
//     sem_acquire($sem);
//     $data = shm_get_var($shm, 7);
//     sem_release($sem);
    echo "   --->> $data";
    ///TODO: Aggiungere ritardo
    //usleep($ritardo * )
    fwrite($stream, $data);
    posix_kill(posix_getpid(), 9);
  }
}

function sig_handler($signo) // Gestione dei segnali
{
  switch($signo) {
    case SIGTERM:
    case SIGHUP:
      global $chiusura; // global perchÃ© Ãš esterna alla funzione
      $chiusura = true;
      break;
    case SIGUSR1:
      $dirs = getDirs("functions/");
      foreach($dirs as $dir) {
        echo "Calling {$dir}_update\n";
        call_user_func("{$dir}_update");
      }
      break;
    default:
      // handle all other signals
  }
}

echo "\n\n";
echo "roBOT per IRC\n\n\n";
echo "Riepilogo dati di connessione:\n\n";
echo "Server:\t\t$irc_server\n";
echo "Porta:\t\t$irc_port\n";
echo "Nome canale:\t$irc_chan\n";
echo "Nome utente:\t$user_name\n";
echo "Password:\t$user_psw\n";
echo "\n\n";

$chiusura = false; // Impostare a true per chiudere il BOT
$functions = getFunctions(); // Richiama la funzione

echo "Creazione socket... ";
$irc = fsockopen($irc_server, $irc_port, $irc_errno, $irc_errstr, 15);
if ($irc) {
  echo "fatto!\n";
} else {
  echo "ERRORE!!!!\n";
  die($irc_errstr . " ($irc_errno)");
}

echo "\n\n";

// $shm = shm_attach(1);
// shm_put_var($shm, 1, $irc_server);
// shm_put_var($shm, 2, $irc_port);
// shm_put_var($shm, 3, $irc_chan);
// shm_put_var($shm, 4, $user_name);
// shm_put_var($shm, 5, $user_psw);
// shm_put_var($shm, 6, $debug);

// $sem = sem_get(1, 1);
$pid = pcntl_fork();

if($pid == -1) {
  die("Could not fork");
} elseif($pid) { // Processo padre
//   $shm = shm_attach(1);
//   sem_acquire($sem);
//   $irc_server = shm_get_var($shm, 1);
//   $irc_port = shm_get_var($shm, 2);
//   $irc_chan = shm_get_var($shm, 3);
//   $user_name = shm_get_var($shm, 4);
//   $user_psw = shm_get_var($shm, 5);
//   $debug = shm_get_var($shm, 6);
//   sem_release($sem);
  dbg($debug, "Invio l'user-name ed il nick");
  send($irc, "USER $user_name \"1\" \"1\" :Bot for Filetor CHAN.\n");
  send($irc, "NICK $user_name\n");
  pcntl_wait($status); // Da qui in poi funzioni per la chiusura del bot
  ///TODO: Chiudere semafori e shm
  fclose($irc);
} elseif(!$pid) { // Processo figlio
  pcntl_signal(SIGTERM, "sig_handler"); // Gestione dei segnali
  pcntl_signal(SIGHUP,  "sig_handler"); // e richiamo della funzione
  pcntl_signal(SIGUSR1, "sig_handler"); // per la loro gestione
  foreach($functions as $func)
    include_once("functions/$func[0]/init.php");
//   $shm = shm_attach(1);
//   sem_acquire($sem);
//   $irc_server = shm_get_var($shm, 1);
//   $irc_port = shm_get_var($shm, 2);
//   $irc_chan = shm_get_var($shm, 3);
//   $user_name = shm_get_var($shm, 4);
//   $user_psw = shm_get_var($shm, 5);
//   $debug = shm_get_var($shm, 6);
//   sem_release($sem);
  while (!feof($irc) && $chiusura == false) {
    $rawdata = str_replace(array("\n","\r"), "", fgets($irc, 512));
    $data = str_replace("  ", " ", $rawdata);
    echo " <<---   $data\n";
    $d = explode(" ", trim($data));
    if (strtolower($d[0]) == "ping") {
      dbg($debug, "Richiesta di ping");
      send($irc, "PONG $d[1]\n");
    } elseif(strtolower($d[1]) == "join") {
      dbg($debug, "Nuovo join");
      $first_string = explode("!", $d[0]);
      $joiner = str_replace(array(" ", ":"), "", $first_string[0]);
      dbg($debug, "\$joiner = $joiner");
      $xml = simplexml_load_file("welcome.xml");
      foreach($xml->joiner as $joiner_info) {
        $joiner_name = $joiner_info->name;
        $joiner_mess = $joiner_info->mess;
        dbg($debug, "\$joiner_name = $joiner_name");
        dbg($debug, "\$joiner_mess = $joiner_mess");
        if(strcmp($joiner_name, $joiner) == 0)
          break;
      }
      if(strcmp($joiner_name, $joiner) == 0) {
        send($irc, "PRIVMSG $irc_chan :Ciao $joiner_name\n");
        send($irc, "PRIVMSG $irc_chan :[$joiner_name]: $joiner_mess\n");
        send($irc, "PRIVMSG $irc_chan :Per informazioni dai il comando \"$user_name help\"!!!\n");
      }
    } elseif ((count($d) > 1) and (($d[1] == "376") or ($d[1] == "422"))) {
      dbg($debug, "Codice 376 o codice 422 ricevuto... Procedo con join e login");
//       send($irc, "PRIVMSG NickServ :IDENTIFY $user_psw\n");
      send($irc, "JOIN $irc_chan\n");
      send($irc, "PRIVMSG $irc_chan :Ciao a tutti... $user_name e' tornato!\n");
      send($irc, "PRIVMSG $irc_chan :Ora controllo il canale!!!\n");
      send($irc, "PRIVMSG $irc_chan :Per informazioni dai il comando \"$user_name help\"!!!\n");
    } elseif ((count($d) > 1) and ($d[1] == "433")) {
      dbg($debug, "Codice 433 ricevuto, necessario GHOST");
      send($irc, "PRIVMSG NickServ :GHOST $user_name $user_psw\n");
      send($irc, "PRIVMSG NickServ :IDENTIFY $user_psw\n");
      send($irc, "JOIN $irc_chan\n");
      send($irc, "PRIVMSG $irc_chan :Ciao a tutti... $user_name e' tornato!\n");
      send($irc, "PRIVMSG $irc_chan :Ora controllo il canale!!!\n");
      send($irc, "PRIVMSG $irc_chan :Per informazioni dai il comando \"$user_name help\"!!!\n");
    } else {
      if(count($d) > 3) {
        $check = substr($d[3], 1, (strlen($d[3]) - 1));
      } else {
        $check = "";
      }
      $head = explode("!", $d[0]);
      $sender = substr($head[0], 1, (strlen($head[0]) - 1));
      dbg($debug, "\$check = $check");
      dbg($debug, "\$head = $head");
      dbg($debug, "\$sender = $sender");
      for($j = 3; $j < count($d); $j++) {
        if($j == 3) {
          $parola = substr(strtolower($d[$j]), 1);
        } else {
          $parola = strtolower($d[$j]);
        }
        dbg($debug, "Parola in posizione $j: $parola");
        if($moderated) {
          for($k = 0; $k < count($bad_words); $k++) {
            if(($parola == $bad_words[$k]) && !in_array($sender, $operators)) {
              send($irc, "PRIVMSG $irc_chan :$sender::: La parola $parola non e' ammessa!!!\n");
              send($irc, "PRIVMSG $irc_chan :$sender::: Ti prendo a calci!!!\n");
              send($irc, "KICK $irc_chan $sender\n");
              $kickkati[$sender]++;
              dbg($debug, "Kick: $sender: $kickkati[$sender]");
              send($irc, "PRIVMSG $irc_chan :$sender::: E siamo a $kickkati[$sender]...\n");
              if($kickkati[$sender] == 2) {
                send($irc, "PRIVMSG $irc_chan :$sender::: Alla terza ti butto FUORI!!!\n");
              } elseif($kickkati[$sender] == 3) {
                send($irc, "PRIVMSG $irc_chan :$sender::: Ti avevo avertito!!!\n");
                send($irc, "PRIVMSG $irc_chan :$sender::: FFUUOORRIIIIIII!!!\n");
                send($irc, "MODE $irc_chan +b $sender!*@*\n");
                send($irc, "PRIVMSG $irc_chan :Peggio per lui... Si arrangia!!! AHAHAHAH ;)\n");
                $kickkati[$sender] = 0;
              }
            }
          }
        }
      }
      ///NOTE: "/^filetor[ \,;.:\-!\?]*[ ]+(.*)$/" Questa è la regex completa... andrebbe rimossa la divisione di $data in $d[]
      if(preg_match("/^{$user_name}[ \,;.:\-!\?]*$/", $check)) { // Accetta tutti i $user_name + una combinazione (lunga quanto vuoi) dei char nelle [ ]...
        if(count($d) > 4) {
          $cmd = $d[4];
          /// TODO: eregiare anche il $d[4]...
          dbg($debug, "\$cmd = $cmd");
          if($cmd == "salutami") {
            send($irc, "PRIVMSG $irc_chan :Ciao $sender :)\n");
          }
          if($cmd == "saluta") {
            if(count($d) > 5) {
              send($irc, "PRIVMSG $irc_chan :Ciao $d[5]... Come stai??\n");
              /// TODO: $d[5] va controllato se e' in chan... Altrimenti stai salutando il nulla ;)
            } else {
              send($irc, "PRIVMSG $irc_chan :Ciao a tutti!! :)\n");
            }
          }
          if($cmd == "ciao") {
            send($irc, "PRIVMSG $irc_chan :Ciao a tutti!! :)\n");
          }
          if($cmd == "help") {
            /// TODO: Creare una funzione HELP per gestire separatamente l'invio dell'help!!!
            send($irc, "PRIVMSG $sender :Ecco la lista delle funzioni:\n");
            sleep(1);
            send($irc, "PRIVMSG $sender :( ) help: Shows this listing.\n");
            sleep(1);
            send($irc, "PRIVMSG $sender :( ) ciao: I'll greet all people in this chan.\n");
            sleep(1);
            send($irc, "PRIVMSG $sender :( ) saluta: I'll greet all people in this chan.\n");
            sleep(1);
            send($irc, "PRIVMSG $sender :( ) salutami: I'll greet you.\n");
            sleep(1);
            send($irc, "PRIVMSG $sender :(*) sparati: I'll close the connection with this chan.\n");
            sleep(1);
            send($irc, "PRIVMSG $sender :(*) debanna: I'll deban the user indicated.\n");
            sleep(1);
            foreach($functions as $func) {
              if($func[2] == 1)
                $priv = "*";
              else
                $priv = " ";
              send($irc, "PRIVMSG $sender :($priv) $func[1]: $func[4]\n");
              sleep(1);
            }
            send($irc, "PRIVMSG $sender :     NOTE: (*) means that you need to be bot operator to exec it.\n");
          }
          if($cmd == "sparati" && in_array($sender, $operators)) {
            send($irc, "PRIVMSG $irc_chan :Ok... :( Addio!!!\n");
            sleep(1);
            send($irc, "PRIVMSG $irc_chan :BANG!\n");
            $chiusura = true;
          }
          $trovato = false;
          for($i = 0; ($i < count($functions)) && ($trovato == false); $i++) {
            $folder = $functions[$i][0];
            $fun = $functions[$i][1];
            $priv = $functions[$i][2];
            $regex = $functions[$i][3];
            if($priv == 1) {
              if(preg_match($regex, $cmd, $infos) && in_array($sender, $operators)) {
                chiama($folder, $fun, $irc, $irc_chan, $infos);
                $trovato = true;
              }
            } else {
              if(preg_match($regex, $cmd, $infos)) {
                chiama($folder, $fun, $irc, $irc_chan, $infos);
                $trovato = true;
              }
            }
          }

          if($cmd == "debanna" && in_array($sender, $operators)) {
            if(count($d) > 5) {
              send($irc, "PRIVMSG $irc_chan :OK... Debanno $d[5]...\n");
              send($irc, "MODE $irc_chan -b $d[5]!*@*\n");
            }
          }
        } else {
          send($irc, "PRIVMSG $irc_chan :Cosa c'ï¿œ $sender??\n");
        }
      }
    }
  }
  posix_kill(posix_getpid(), 9);
}
?>
