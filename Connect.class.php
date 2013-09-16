<?php

class Connect {
	protected $url = "";
	protected $cookit;
	protected $curl;
	protected $data = null;
	protected $method = FALSE;
	protected $cookie_file;
	protected $charset;
	public $html = "";
	protected $userAgent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)';

	public function __construct($url,$charset=null) {
		$this -> cookie_file = tempnam(SAE_TMP_PATH, "cookie");
		//print_r($this->cookie_file);
		$this -> url = $url;
		$this->charset = $charset;
	}

	public function setUrl($url) {
		$this -> url = $url;
		return $this;
	}

	public function setData($key, $value) {
		if (is_null($this -> data)) {
			$this -> data = sprintf("%s=%s", $key, $value);
		}
		else {
			$this -> data .= sprintf("&%s=%s", $key, $value);
		}
		return $this;
	}

	public function get($a = false) {
		$this -> method = FALSE;
		return $this -> run($a);
	}

	public function post($a = false) {
		$this -> method = TRUE;
		return $this -> run($a);
	}

	protected function run($a) {
		$this -> curl = curl_init();
		curl_setopt($this -> curl, CURLOPT_URL, $this -> url);
		curl_setopt($this -> curl, CURLOPT_HEADER, 0);
		curl_setopt($this -> curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this -> curl, CURLOPT_USERAGENT, $this -> userAgent);

		curl_setopt($this -> curl, CURLOPT_COOKIEJAR, $this -> cookie_file);
		curl_setopt($this -> curl, CURLOPT_COOKIEFILE, $this -> cookie_file);

		if ($this -> method) {
			curl_setopt($this -> curl, CURLOPT_POST, 1);
			curl_setopt($this -> curl, CURLOPT_POSTFIELDS, $this -> data);
		}
		$this -> html = curl_exec($this -> curl);
		curl_close($this -> curl);
		
		if(!is_null($this->charset)||$this->charset!="UTF-8"){
			$this->html=mb_convert_encoding($this->html,"UTF-8",$this->charset);
		}
		
		$this -> data = null;
		if ($a) {
			import("@.ORG.simple_html_dom");
			return str_get_html($this -> html);
		}
		return $this -> html;
	}

	public function getCookie() {
		return file_get_contents($this -> cookie_file);
	}

	public function setCookie($cookie) {
		file_put_contents($this -> cookie_file, $cookie);
		return $this;
	}

}
?>