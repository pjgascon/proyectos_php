<?php
class cURL{
		public function __construct($cookies=NULL, $agent=NULL, $proxy=NULL){
				$this->proxy = $proxy;
				$this->cookiesJar = $cookies;
				$this->agent = $agent;
		}

		private function cURL(){
 			$c = curl_init($this->URL);
			curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
			if($this->connectTimeout) curl_setopt($c, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
			if($this->timeout) curl_setopt($c, CURLOPT_TIMEOUT, $this->timeout);
			if($this->agent) curl_setopt($c, CURLOPT_USERAGENT, $this->agent);
			if($this->headers) curl_setopt($c, CURLOPT_HTTPHEADER, $this->headers);
			if($this->proxy){
				$pD = array_flip(explode('@', $this->proxy));
				curl_setopt($c, CURLOPT_PROXY, $pD[0]);
				if($pD[1]) curl_setopt($c, CURLOPT_PROXYUSERPWD, $pD[1]);
				if(!$this->proxyType) $this->proxyType = 'HTTP';
				curl_setopt($ch, CURLOPT_PROXYTYPE, $this->proxyType);
				if(!$this->proxyAuth) $this->proxyAuth = 'CURLOPT_HTTPAUTH';
				curl_setopt($ch, CURLOPT_HTTPAUTH, $this->proxyAuth);
			}
			if($this->cookiesJar){
					curl_setopt($c, CURLOPT_COOKIEFILE, $this->cookiesJar);
					curl_setopt($c, CURLOPT_COOKIEJAR, $this->cookiesJar);
			}
			return $c;
		}

		private function exec($c){
			return curl_exec($c);
		}

		public function browse($URL, $referer=NULL, $post=NULL){
			$this->URL = $URL;
			$this->referer = $referer;
			if($post) $this->post = $post;

			$c = $this->cURL();

			if($this->referer) curl_setopt($c, CURLOPT_REFERER, $this->referer);
			if($this->post){
					curl_setopt($c, CURLOPT_POST, 1);
					curl_setopt($c, CURLOPT_POSTFIELDS, $this->post);
			}

			if(curl_error($c)) die('cURL Error ('.curl_errno($c).')');

			$this->result = $this->exec($c);
			return $this;
		}

		public function getSize(){
			return explode('/', $this->getHeader('Content-Range')[1])[1];
		}

		public function searchHeader($h){
 			foreach($this->headers AS $k => $v) if($e = explode(':', $v) AND stristr($e[0], $h)) return array_map('trim', $e);
			return false;
		}

		public function getHeader($header=NULL, $follow=1){
			$c = $this->cURL();
			curl_setopt($c, CURLOPT_HEADER, 1);
			curl_setopt($c, CURLOPT_FOLLOWLOCATION, $follow);
			curl_setopt($c, CURLOPT_NOBODY, 1);
			$this->headerResult = curl_exec($c);
			$this->headers = array_map('trim', explode(PHP_EOL, $this->headerResult));
			if($header) return $this->searchHeader($header);
			return $this;
		}

		public function forceDownload($n=NULL, $b=1024){
				if(!$b) die('no bytes');
				if(!$this->size AND !$s = $this->getSize()) die('Error con tama&ntilde;o');
				set_time_limit(0);
				header('Content-Type: application/octet-stream');
				($n) ? header("Content-Disposition: attachment; filename=$n") : header(@implode(':', $this->searchHeader('Content-Disposition')));
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Expires: 0');
				header('Pragma: public');
				header('Content-Length: '.$s);
				$i = 0;
				while($i <= $s){
					$c = $this->cURL();
					$r = $i.'-'.(($i+$b) > $s ? $s : $i+$b);
					curl_setopt($c, CURLOPT_RANGE, $r);
					curl_setopt($c, CURLOPT_WRITEFUNCTION, array($this, 'writeBytes'));
					curl_setopt($c, CURLOPT_BINARYTRANSFER, 1);
					$this->exec($c);
					$i = $i+$b+1;
				}
		}

		private function writeBytes($ch, $str){
			print($str);
			$this->flush();
			return strlen($str);
		}


		private function flush(){
			ob_end_flush();
			ob_flush();
			flush();
			ob_start();
			return $this;
		}
}
?>