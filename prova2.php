<?php
// tick use required as of PHP 4.3.0
declare(ticks = 1);

// signal handler function
function sig_handler($signo)
{
  switch($signo) {
    case SIGTERM:
     // handle shutdown tasks
      exit;
      break;
    case SIGHUP:
      // handle restart tasks
      break;
    case SIGUSR1:
      echo "Caught SIGUSR1...\n";
      break;
    default:
      // handle all other signals
  }
}

echo "Installing signal handler...\n";

// setup signal handlers
pcntl_signal(SIGTERM, "sig_handler");
pcntl_signal(SIGHUP,  "sig_handler");
pcntl_signal(SIGUSR1, "sig_handler");

// or use an object, available as of PHP 4.3.0
// pcntl_signal(SIGUSR1, array($obj, "do_something");

echo "Generating signal SIGTERM to self...\n";

// send SIGUSR1 to current process id
$pid = pcntl_fork();
if($pid == -1) {
  die("Could not fork");
} elseif(!$pid) {
  posix_kill(posix_getppid(), SIGUSR1);
  sleep(3);
}

echo "Done\n";

?>