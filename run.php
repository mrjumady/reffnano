<?php
date_default_timezone_set('Asia/Jakarta');
require_once(dirname(__FILE__) . '/vendor/autoload.php');


$colors = new \Colors();
use Curl\Curl;
echo ''.$colors->getColoredString('-------', 'yellow').' Auto Reff Nanovest with otpinaja.com '.$colors->getColoredString('-------', 'yellow').''.PHP_EOL;
echo ''.$colors->getColoredString('-----', 'yellow').' Thanks : Muhammad Ikhsan, Josski, Jumady '.$colors->getColoredString('-----', 'yellow').''.PHP_EOL.PHP_EOL;

echo '['.$colors->getColoredString(date('H:i:s'), 'green').'] | 1). Nanovest [205] Rp 900'.PHP_EOL;
echo '['.$colors->getColoredString(date('H:i:s'), 'green').'] | 2). Nanovest s2 [206] Rp 1.300'.PHP_EOL;
echo '['.$colors->getColoredString(date('H:i:s'), 'green').'] | Masukan APIKEY otpinaja: ';
$apikey = trim(fgets(STDIN));
echo '['.$colors->getColoredString(date('H:i:s'), 'green').'] | Masukan service ID otpinaja: ';
$ids = trim(fgets(STDIN));
echo '['.$colors->getColoredString(date('H:i:s'), 'green').'] | Masukan Reff Nanovest: ';
$reff = trim(fgets(STDIN));
while (-1) {
	echo '---------------------------------------------------------------------'.PHP_EOL;
	$curl = new Curl();
	$curl->post('https://otpinaja.com/api/order', [
	        'api_key' => $apikey,
	        'service_id' => $ids,
	        ]);
	$getNo = json_decode($curl->response, TRUE);
	$statusOrder = $getNo["status"];
	$idOrder = $getNo["data"]["id"];
	$noHP = substr($getNo["data"]["number"], 2);
	if($statusOrder == "1"){
		echo '['.$colors->getColoredString(date('H:i:s'), 'green').'] | Nomor HP: '.$noHP.''.PHP_EOL;
		$did = substr(str_shuffle("012345678910abcdefghijklmnopqrstuvwxyz"), -16);
		$curl = new Curl();
		$curl->setUserAgent('okhttp/3.12.12');
		$curl->setHeader('accept', 'application/json, text/plain, */*');
		$curl->setHeader('x-device-id', ''.$did.'');
		$curl->setHeader('timezone', 'Asia/Jakarta');
		$curl->setHeader('accept-language', 'ID');
		$curl->setHeader('sentry-trace', '6f6c9c91cf4a44f39be7fd7cf1985f3a-9862b3604ca119ef-1');
		$curl->setHeader('Content-Type', 'application/json');
		$curl->setHeader('Host', 'api.nanovest.io');
		$curl->setHeader('Connection', 'Keep-Alive');
		$curl->post('https://api.nanovest.io/v1/auth/otp', [
	                    'countryCode' => '62',
	                    'phoneNumber' => $noHP,
	                ]);
	    if($curl->response->code == "1") {
	    	echo '['.$colors->getColoredString(date('H:i:s'), 'green').'] | Sukses Meminta OTP'.PHP_EOL;
	    	echo '['.$colors->getColoredString(date('H:i:s'), 'green').'] | Mencoba Mengambil OTP...'.PHP_EOL;
	    	tryOtp:
	    	$curl = new Curl();
			$curl->post('https://otpinaja.com/api/status', [
				        'api_key' => $apikey,
				        'order_id' => $idOrder,
				        ]);
			$getOTP = json_decode($curl->response, TRUE);	
			$statusOTP = $getOTP["data"]["status"];
			$otp = $getOTP["data"]["otp"];
	    	if($statusOTP == "success") {
	    		$codeOTP = preg_replace("/[^0-9]/", "", $otp);
	    		echo '['.$colors->getColoredString(date('H:i:s'), 'green').'] | Kode OTP: '.$codeOTP.''.PHP_EOL;
	    		$curl = new Curl();
	    		$curl->setUserAgent('okhttp/3.12.12');
				$curl->setHeader('accept', 'application/json, text/plain, */*');
				$curl->setHeader('x-device-id', ''.$did.'');
				$curl->setHeader('timezone', 'Asia/Jakarta');
				$curl->setHeader('accept-language', 'ID');
				$curl->setHeader('sentry-trace', '6f6c9c91cf4a44f39be7fd7cf1985f3a-9862b3604ca119ef-1');
				$curl->setHeader('Content-Type', 'application/json');
				$curl->setHeader('Host', 'api.nanovest.io');
				$curl->setHeader('Connection', 'Keep-Alive');
	    		$curl->post('https://api.nanovest.io/v1/auth/token', [
			                    'countryCode' => '62',
			                    'phoneNumber' => $noHP,
			                    'code' => $codeOTP,
			                ]);
			    $token = $curl->response->data->token;
			    echo '['.$colors->getColoredString(date('H:i:s'), 'green').'] | Mencoba Menggunakan Reff....'.PHP_EOL;
			    $curl = new Curl();
	    		$curl->setUserAgent('okhttp/3.12.12');
				$curl->setHeader('accept', 'application/json, text/plain, */*');
				$curl->setHeader('x-device-id', ''.$did.'');
				$curl->setHeader('x-timezone', 'Asia/Jakarta');
				$curl->setHeader('accept-language', 'ID');
				$curl->setHeader('authorization', 'bearer '.$token.'');
				$curl->setHeader('Host', 'api.nanovest.io');
				$curl->setHeader('Connection', 'Keep-Alive');
	    		$curl->post('https://api.nanovest.io/v1/referral/referral-code/submit/'.$reff.'');
	    		if($curl->response->code == "1") {
	    			echo '['.$colors->getColoredString(date('H:i:s'), 'green').'] | Sukses Menggunakan Reff '.$reff.''.PHP_EOL;
	    		} else {
	    			echo '['.$colors->getColoredString(date('H:i:s'), 'green').'] | Gagal Menggunakan Reff....'.PHP_EOL;
	    		}
	    	} else {
	    		goto tryOtp;
	    	}
	    } else {
	    	echo '['.$colors->getColoredString(date('H:i:s'), 'green').'] | Gagal Meminta OTP'.PHP_EOL;
	    }
	} else {
		echo '['.$colors->getColoredString(date('H:i:s'), 'green').'] | '.$getNo["msg"].''.PHP_EOL;
	}
}







class Colors {
    private $foreground_colors = array();
      private $background_colors = array();
  
      public function __construct() {
          // Set up shell colors
          $this->foreground_colors['black'] = '0;30';
          $this->foreground_colors['dark_gray'] = '1;30';
          $this->foreground_colors['blue'] = '0;34';
          $this->foreground_colors['light_blue'] = '1;34';
          $this->foreground_colors['green'] = '0;32';
          $this->foreground_colors['light_green'] = '1;32';
          $this->foreground_colors['cyan'] = '0;36';
          $this->foreground_colors['light_cyan'] = '1;36';
          $this->foreground_colors['red'] = '0;31';
          $this->foreground_colors['light_red'] = '1;31';
          $this->foreground_colors['purple'] = '0;35';
          $this->foreground_colors['light_purple'] = '1;35';
          $this->foreground_colors['brown'] = '0;33';
          $this->foreground_colors['yellow'] = '1;33';
          $this->foreground_colors['light_gray'] = '0;37';
          $this->foreground_colors['white'] = '1;37';
  
          $this->background_colors['black'] = '40';
          $this->background_colors['red'] = '41';
          $this->background_colors['green'] = '42';
          $this->background_colors['yellow'] = '43';
          $this->background_colors['blue'] = '44';
          $this->background_colors['magenta'] = '45';
          $this->background_colors['cyan'] = '46';
          $this->background_colors['light_gray'] = '47';
      }
  
      // Returns colored string
      public function getColoredString($string, $foreground_color = null, $background_color = null) {
          $colored_string = "";
  
          // Check if given foreground color found
          if (isset($this->foreground_colors[$foreground_color])) {
              $colored_string .= "\033[" . $this->foreground_colors[$foreground_color] . "m";
          }
          // Check if given background color found
          if (isset($this->background_colors[$background_color])) {
              $colored_string .= "\033[" . $this->background_colors[$background_color] . "m";
          }
  
          // Add string and end coloring
          $colored_string .=  $string . "\033[0m";
  
          return $colored_string;
      }
  
      // Returns all foreground color names
      public function getForegroundColors() {
          return array_keys($this->foreground_colors);
      }
  
      // Returns all background color names
      public function getBackgroundColors() {
          return array_keys($this->background_colors);
      }
  }
