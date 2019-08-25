<?php
namespace x51\functions;

/* функции дл€ внешних запросов

*/

class funcRequest {
	public static $nTimeout=5;
	public static $nMaxRetries=5;
	public static $userAgent='Mozilla/5.0 (Windows; U; Windows NT 5.1; de; rv:1.8.1.11) Gecko/20071127 Firefox/2.0.0.11';
	
	/** ѕростой get запрос с использованием curl
	 * 
	 * @param unknown $sUrl
	 * @param array $param login и password - когда требуетс€ авторизаци€
	 * @return string|mixed
	 */
	static public function simpleRequest($sUrl, array $param=array()) {
		$curl=curl_init();
			curl_setopt ( $curl, CURLOPT_URL, $sUrl);
			curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt ( $curl, CURLOPT_CONNECTTIMEOUT, static::$nTimeout );
			curl_setopt ( $curl, CURLOPT_USERAGENT, static::$userAgent );
			if (isset($param['login']) && isset($param['password'])) curl_setopt($ch, CURLOPT_USERPWD, $param['login'].':'.$param['password']);
		$retry=0;
		$data='';
		while($data=='' AND $retry < static::$nMaxRetries) {
			$data=curl_exec($curl);
			$retry++;
		}
		curl_close($curl);
		return $data;
	} // end simpleRequest
	
	/**
	 * 
	 * @param unknown $sUrl
	 * @param array $arPostParam
	 * @param array $param login и password - когда требуетс€ авторизаци€
	 * @return string|mixed
	 */
	static public function simpleRequestPost($sUrl, array $arPostParam, array $param=array()) {
		$strPostParam='';
		foreach ($arPostParam as $pName => $pVal) {
			if ($strPostParam!='') {
				$strPostParam.='&';
			}
			$strPostParam.=$pName.'='.rawurlencode($pVal);
		}
		$curl=curl_init();
			curl_setopt ($curl, CURLOPT_URL, $sUrl);
			curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($curl, CURLOPT_CONNECTTIMEOUT, static::$nTimeout);
			curl_setopt ($curl, CURLOPT_USERAGENT, static::$userAgent);
			curl_setopt ($curl, CURLOPT_POST, true);
			if ($strPostParam) {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $strPostParam);
			}
			if (isset($param['login']) && isset($param['password'])) {
				curl_setopt($ch, CURLOPT_USERPWD, $param['login'].':'.$param['password']);
			}
			
		$retry=0;
		$data='';
		while($data=='' AND $retry < static::$nMaxRetries) {
			$data=curl_exec($curl);
			$retry++;
		}
		curl_close($curl);
		return $data;
	} // end func
} // end class funcRequest