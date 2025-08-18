<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */



class soliscloudApi {
    private $apiId;
    private $apiSecret;
    private $baseUrl = 'https://www.soliscloud.com:13333';
	
	public $inverterDetail = array();

    public function __construct($apiId, $apiSecret) {
        $this->apiId = $apiId;
        $this->apiSecret = $apiSecret;
    }
	

    private function signRequest($method, $path, $body) {
        $contentMd5 = base64_encode(md5($body, true));
        $contentType = 'application/json;charset=UTF-8';
        $date = gmdate('D, d M Y H:i:s') . ' GMT';
        $stringToSign = $method . "\n" . $contentMd5 . "\n" . $contentType . "\n" . $date . "\n" . $path;
        $signature = base64_encode(hash_hmac('sha1', $stringToSign, $this->apiSecret, true));
        return [
            'Content-MD5' => $contentMd5,
            'Content-Type' => $contentType,
            'Date' => $date,
            'Authorization' => 'API ' . $this->apiId . ':' . $signature
        ];
    }
	
	public function getInverterDetail($sn, $fullDetail = true) {
		$body = '{
			"sn": '.$sn.'
		}';

		$contentMD5 = base64_encode(md5($body, true));
		$contentType = 'application/json';
		$gmdate = gmdate('D, d M Y H:i:s T');
		$endPoint = '/v1/api/inverterDetail';

		$stringToSign = "POST\n$contentMD5\n$contentType\n$gmdate\n$endPoint";
		$signature = base64_encode(hash_hmac('sha1', $stringToSign, $this->apiSecret, true));
		$authorization = 'API ' . $this->apiId . ':' . $signature;

		$ch = curl_init();
		$headers = array(
			'Content-MD5: ' . $contentMD5,
			'Authorization: ' . $authorization,
			'Content-Type: ' . $contentType,
			'Date: ' . $gmdate
		);
		
		$url = 'https://www.soliscloud.com:13333' . $endPoint;

		curl_setopt_array($ch, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => $body,
			CURLOPT_HTTPHEADER => $headers,
		));

		$response = curl_exec($ch);
		$json = json_decode($response, true);
		if(isset($json["data"])) {
			$this->inverterDetail = $json["data"];
		} else {
			$this->inverterDetail= null;
		}
		return $this->inverterDetail;
	}
	
	public function getInverterValue($fieldName, $defaultValue = "", $unit = ""){
		if (isset($this->inverterDetail[$fieldName])) {
			$value = $this->inverterDetail[$fieldName];
			switch ($fieldName) {
				case "state" : {
									switch($value) {
										case 1 : return "online";
										case 2 : return "offline";
										case 3 : return "alarm";
										default : return "unknown";
									}
							} break;
				default : 	{
								if (strlen($unit) > 0 ) {
									$inverterUnitFieldName = $fieldName."Str";
									switch ($unit) {
										case "W" : {
														if (isset($this->inverterDetail[$inverterUnitFieldName])) {
															if ($this->inverterDetail[$inverterUnitFieldName] == "kW") $value = floatval($value)*1000;
															if ($this->inverterDetail[$inverterUnitFieldName] == "MW") $value = floatval($value)*1000000;
														}
													} break;
									}
								}
							}
			}
			return $value;
		} else return $defaultValue;
	}

}




