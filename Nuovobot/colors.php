<?php
	if($colors) {
		$BLACK = "\x1b[0;30m";
		$RED = "\x1b[0;31m";
		$GREEN = "\x1b[0;32m";
		$BROWN = "\x1b[0;33m";
		$BLUE = "\x1b[0;34m";
		$PURPLE = "\x1b[0;35m";
		$CYAN = "\x1b[0;36m";
		$LGRAY = "\x1b[0;37m";
		$GRAY = "\x1b[1;30m";
		$LRED = "\x1b[1;31m";
		$LGREEN = "\x1b[1;32m";
		$YELLOW = "\x1b[1;33m";
		$LBLUE = "\x1b[1;34m";
		$PINK = "\x1b[1;35m";
		$LCYAN = "\x1b[1;36m";
		$WHITE = "\x1b[1;37m";
		$BOLD = "\x1b[1m";
		$UNDERLINE = "\x1b[4m";
		$Z = "\x1b[0m";
	} else {
		$BLACK = "";
		$RED = "";
		$GREEN = "";
		$BROWN = "";
		$BLUE = "";
		$PURPLE = "";
		$CYAN = "";
		$LGRAY = "";
		$GRAY = "";
		$LRED = "";
		$LGREEN = "";
		$YELLOW = "";
		$LBLUE = "";
		$PINK = "";
		$LCYAN = "";
		$WHITE = "";
		$BOLD = "";
		$UNDERLINE = "";
		$Z = "";
	}

	//echo "{$RED} ***{$Z} {$BOLD}Attenzione{$Z}: la dir {$GREEN}documenti {$Z}sara' {$UNDERLINE}eliminata{$Z}\n";
?>