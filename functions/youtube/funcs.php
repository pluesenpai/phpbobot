<?php
	function getpage($url)
	{
		$page = curl_init();
		$user_agent = "Mozilla/5.0 (X11; U; Linux i686; it; rv:1.9.0.5) Gecko/2008120121 Firefox/3.0.5";
		$header = array(
			"Host: www.youtube.com",
			"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
			"Accept-Language: it-it,it;q=0.8,en-us;q=0.5,en;q=0.3",
			"Accept-Charset: UTF-8,*",
			"Keep-Alive: 300",
			"Connection: keep-alive"
		);

		curl_setopt($page, CURLOPT_URL, $url);
		curl_setopt($page, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($page, CURLOPT_BINARYTRANSFER, true);
		curl_setopt($page, CURLOPT_HEADER, false);
		curl_setopt($page, CURLOPT_USERAGENT, $user_agent);
		curl_setopt($page, CURLOPT_HTTPHEADER, $header);

		$content = curl_exec($page);
		curl_close($page);

		return $content;
	}
?>