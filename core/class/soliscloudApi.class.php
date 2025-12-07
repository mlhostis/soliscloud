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



/* Classe soliscloudApi : permet d'interagir avec l'API SolisCloud pour récupérer des informations sur un onduleur.
 * Utilisée dans le plugin Jeedom pour la gestion des équipements SolisCloud.
 */

class soliscloudApi {
    // Identifiants d'authentification API
    private $apiId;
    private $apiSecret;
    // URL de base de l'API SolisCloud
    private $baseUrl = 'https://www.soliscloud.com:13333';
	
    // Détail de l'onduleur récupéré depuis l'API
    public $inverterDetail = array();

    /**
     * Constructeur de la classe
     * @param string $apiId Identifiant API
     * @param string $apiSecret Clé secrète API
     */
    public function __construct($apiId, $apiSecret) {
        $this->apiId = $apiId;
        $this->apiSecret = $apiSecret;
    }
	
    /**
     * Génère les en-têtes de signature pour une requête API
     * @param string $method Méthode HTTP (GET, POST, etc.)
     * @param string $path Chemin de l'endpoint
     * @param string $body Corps de la requête
     * @return array Tableau des en-têtes à ajouter à la requête
     */
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
	
	 /**
     * Requetes vers solis cloud pour un endPoint
     * @param string $endPoint ex: $endPoint = '/v1/api/inverterDetail';
     * @param string $body	request body data 
     * @return array Tableau de la réponse à la requête
     */
    private function cloudRequest($endPoint, $body) {
        // Calcul des en-têtes nécessaires à l'authentification
        $contentMD5 = base64_encode(md5($body, true));
        $contentType = 'application/json';
        $gmdate = gmdate('D, d M Y H:i:s T');
  
        $stringToSign = "POST\n$contentMD5\n$contentType\n$gmdate\n$endPoint";
        $signature = base64_encode(hash_hmac('sha1', $stringToSign, $this->apiSecret, true));
        $authorization = 'API ' . $this->apiId . ':' . $signature;

        // Initialisation de la requête cURL
        $ch = curl_init();
        $headers = array(
            'Content-MD5: ' . $contentMD5,
            'Authorization: ' . $authorization,
            'Content-Type: ' . $contentType,
            'Date: ' . $gmdate
        );
		
        $url = 'https://www.soliscloud.com:13333' . $endPoint;
log::add('soliscloud', 'debug',"cloudRequest() : $url");
log::add('soliscloud', 'debug',"cloudRequest() header : ".print_r($headers,true));
log::add('soliscloud', 'debug',"cloudRequest() body : ".print_r($body,true));
        // Configuration des options cURL
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

        // Exécution de la requête et décodage de la réponse JSON
        $response = curl_exec($ch);
        $json = json_decode($response, true);
log::add('soliscloud', 'debug',"cloudRequest() response : ".print_r($json,true));
		//structure du type array("success" => 0/1, "code" => "ex code", "msg" => "message du code")
		$code = isset($json["code"]) ? $json["code"] : "unknown";
        if($code != '0') {
			$success = isset($json["success"]) ? $json["success"] : -1;
			$msg = isset($json["msg"]) ? $json["msg"] : "unknown";
			log::add('soliscloud', 'error',"cloudRequest() failed code : $code message : $msg <pre>".print_r($json,true)."</pre>");
            $json = false;
        }
		return $json;
    }
	
	
    /**
     * Récupère le détail d'un onduleur via l'API SolisCloud
     * @param string $sn Numéro de série de l'onduleur
     * @param bool $fullDetail Indique si on souhaite le détail complet (non utilisé ici)
     * @return array|null Détail de l'onduleur ou null si erreur
     */
    public function getInverterDetail($sn, $fullDetail = true) {
        // Préparation du corps de la requête
        $body = '{
            "sn": '.$sn.'
        }';
		
		$endPoint = '/v1/api/inverterDetail';
		$json = $this->cloudRequest($endPoint, $body);
		
		//structure du type array("success" => 0/1, "code" => "ex code", "msg" => "message du code")
        if(isset($json["data"])) {
            $this->inverterDetail = $json["data"];
        } else {
            $this->inverterDetail= null;
        }
        return $this->inverterDetail;
    }
	
	
	/**
     * Récupère le détail d'un onduleur via l'API SolisCloud à partir du enpoint inverterDetailList
     * @param string $sn Numéro de série de l'onduleur
     * @param bool $fullDetail Indique si on souhaite le détail complet (non utilisé ici)
     * @return array|null Détail de l'onduleur ou null si erreur
     */
    public function getInverterDetailFromList($sn, $fullDetail = true) {
        // Préparation du corps de la requête
        $body = '{
            "pageNo": "1",
			"pageSize" : "5"
        }';
		
		$endPoint = '/v1/api/inverterDetailList';
		$json = $this->cloudRequest($endPoint, $body);
		$this->inverterDetail = false;
		
        if ($json) {
			if(isset($json["data"]["records"])) {
				if( count($json["data"]["records"]) > 0) {
					foreach($json["data"]["records"] as $record) {
						if( $record["sn"] == $sn) {
							$this->inverterDetail = $record;
							break;
						}
					}
					if (!$this->inverterDetail) {
						log::add('soliscloud', 'error',"inverter serial number not found nb inverter = ".count($json["data"]["records"])." <pre>".print_r($json["data"]["records"],true)."</pre>");
					}
				} else {
					log::add('soliscloud', 'debug',"API request ok but no inverter found. Maybe not connected");
				}					
			} else {
				log::add('soliscloud', 'error',"Inverter record found but format unknown <pre>".print_r($json,true)."</pre>");
			}
		}
        return $this->inverterDetail;
    }
	
    /**
     * Récupère la valeur d'un champ de l'onduleur, avec gestion des unités et états spéciaux
     * @param string $fieldName Nom du champ à récupérer
     * @param mixed $defaultValue Valeur par défaut si le champ n'existe pas
     * @param string $unit Unité attendue (ex : "W")
     * @return mixed Valeur du champ ou valeur par défaut
     */
    public function getInverterValue($fieldName, $defaultValue = "", $unit = ""){
        if (isset($this->inverterDetail[$fieldName])) {
            $value = $this->inverterDetail[$fieldName];
            switch ($fieldName) {
                case "state" : {
                                    // Conversion de l'état numérique en texte
                                    switch($value) {
                                        case 1 : return "online";
                                        case 2 : return "offline";
                                        case 3 : return "alarm";
                                        default : return "unknown";
                                    }
                            } break;
                default :    {
                                // Gestion des conversions d'unités si nécessaire
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
	
	 /**
     * Récupère un élément de configuration d'un onduleur via l'API SolisCloud
     * @param string $sn Numéro de série de l'onduleur
     * @param int    $cid control id => id de l'élément de configuration 
     * @return array|null Détail de l'onduleur ou null si erreur
     */
    public function getControlValue($sn, $cid) {
        // Préparation du corps de la requête
        $body = '{
            "inverterSn": "'.$sn.'",
			"cid": "'.$cid.'"
        }';
		$result = false;
		
		$endPoint = '/v2/api/atRead';
		$json = $this->cloudRequest($endPoint, $body);
		
		//structure du type array("success" => 0/1, "code" => "ex code", "msg" => "message du code")
        if(isset($json["data"])) {
			log::add('soliscloud', 'debug',"getControlValue($sn, $cid) = <pre>".print_r($json,true)."</pre>");
			return $json["data"]["msg"];
        } else {
			log::add('soliscloud', 'error',"getControlValue($sn, $cid) error return = <pre>".print_r($body,true)."</pre>");
			return false;
        }
    }
	
	/**
     * Modifie un élément de configuration d'un onduleur via l'API SolisCloud
     * @param string $sn Numéro de série de l'onduleur
     * @param int    $cid control id => id de l'élément de configuration      
	 * @param int    $value => valeur de l'élément de configuration 
     * @return true/false
     */
    public function setControlValue($sn, $cid, $value) {
        // Préparation du corps de la requête
        $body = '{
            "inverterSn": "'.$sn.'",
			"cid": '.$cid.',
			"value": '.$value.'
        }';
		$result = false;
		
		$endPoint = '/v2/api/control';
		$json = $this->cloudRequest($endPoint, $body);
		
		//structure du type array("success" => 0/1, "code" => "ex code", "msg" => "message du code")
        if(isset($json["data"])) {
			log::add('soliscloud', 'debug',"setControlValue($sn,$cid) = <pre>".print_r($json,true)."</pre>");
			return $json["data"]["msg"];
        } else {
			log::add('soliscloud', 'error',"setControlValue($sn,$cid) error return = <pre>".print_r($body,true)."</pre>");
			return false;
        }
    }

}




