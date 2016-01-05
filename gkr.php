<?php
	/*
	Google Keyword Rank
	Simple script to get rank of your domain against defined keyword phrase
	Version: 1.0
	Author: Sony AK <sony@sony-ak.com>
	
	Usage:
	php gkr.php [google domain] [your domain] [keyword phrase]
	php gkr.php google.co.id lifull.id apartemen baru
	
	Limitations:
	- Only scan 10 pages
	*/

	// initialize vars
	$iArgCounter 		= 0;
	$keywordPhrase 		= '';
	
	// get keyword phrase
	for($i=0;$i<=count($argv)-1;$i++) {
		if($i == 0 || $i == 1 || $i == 2) { continue; }
		$keywordPhrase = $keywordPhrase . ' ' . $argv[$i];
	}
	$keywordPhrase = trim($keywordPhrase);

	$googleListRank = 1;

	for($googlePage=1;$googlePage<=10;$googlePage++) {
		$startPage = ($googlePage - 1) * 10;

		$googleQueryString = "https://www." . $argv[1] . "/search?q=" . urlencode($keywordPhrase) . "&start=" . $startPage;

		// go to google
	  $curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $googleQueryString);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_ENCODING, "");
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.2) Gecko/20100115 Firefox/3.6 (.NET CLR 3.5.30729)");
		$curlData = curl_exec($curl);

		curl_close($curl);

	  $xmlDoc = new DOMDocument();
	  @$xmlDoc->loadHTML($curlData);

	  $xPathDoc = new DOMXpath($xmlDoc);

	  $xPathQuery = $xPathDoc->query("//li[@class='g']");

	  if($xPathQuery->length == 0) {
	    $isEntryFound = false;
	  } else {
	   	$isEntryFound = true;
	  }

	  if($isEntryFound) {
	  	$iCounter = 0;
	  		
	    foreach($xPathQuery as $eachXpathElement) {
	    	$url 	= trim($eachXpathElement->getElementsByTagName('a')->item(0)->getAttribute('href'));
	    	$title 	= trim($eachXpathElement->getElementsByTagName('a')->item(0)->nodeValue);

	    	if(substr($url, 0, 7) != '/url?q=') {
	    		continue;
	    	}

	    	$iCounter++;

	    	$url = trim(str_replace('/url?q=', '', $url));
	    	$url = substr($url, 0, strpos($url, '&sa=U&ved='));

	    	// DEBUG
	    	//echo $googleListRank . ' ' . $googlePage . ' ' . $startPage . ' ' . $iCounter . ' ' .$url . "\n";
	    		
	    	$pos = strpos($url, $argv[2]);

	    	if(!($pos === false)) {
	    		echo 'Domain: ' . $argv[2] . ' / ' . 'Keyword Phrase: ' . $keywordPhrase . ' / ' . 'Rank: ' . $googleListRank . ' / ' . 'Page: ' . $googlePage;
	    		exit;
	    	}
	    		
	    	$googleListRank++;
	    }
	  }
  }
