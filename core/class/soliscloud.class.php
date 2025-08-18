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

/* * ***************************Includes********************************* */
require_once __DIR__  . '/../../../../core/php/core.inc.php';
include_once "soliscloudApi.class.php";

class soliscloud extends eqLogic {
    
	/*     * *************************Attributs****************************** */
	public $cmdList = array();
    
  /*
   * Permet de définir les possibilités de personnalisation du widget (en cas d'utilisation de la fonction 'toHtml' par exemple)
   * Tableau multidimensionnel - exemple: array('custom' => true, 'custom::layout' => false)
	public static $_widgetPossibility = array();
   */
    
    /*     * ***********************Methode static*************************** */


    // Fonction exécutée automatiquement toutes les minutes par Jeedom
    public static function cron() {
		foreach (self::byType('soliscloud') as $soliscloud) {//parcours tous les équipements du plugin soliscloud
			if ($soliscloud->getIsEnable() == 1) {//vérifie que l'équipement est actif
				$cmd = $soliscloud->getCmd(null, 'refresh');//retourne la commande "refresh si elle existe
				if (!is_object($cmd)) {//Si la commande n'existe pas
					continue; //continue la boucle
				}
				$cmd->execCmd(); // la commande existe on la lance
			}
		}      
      }
 
    // Fonction exécutée automatiquement toutes les 5 minutes par Jeedom
    public static function cron5() {
		foreach (self::byType('soliscloud') as $soliscloud) {//parcours tous les équipements du plugin soliscloud
			if ($soliscloud->getIsEnable() == 1) {//vérifie que l'équipement est actif
				$cmd = $soliscloud->getCmd(null, 'refresh');//retourne la commande "refresh si elle existe
				if (!is_object($cmd)) {//Si la commande n'existe pas
					continue; //continue la boucle
				}
				$cmd->execCmd(); // la commande existe on la lance
			}
		} 
    }
     

    /*
     * Fonction exécutée automatiquement toutes les 10 minutes par Jeedom
      public static function cron10() {
      }
     */
    
    /*
     * Fonction exécutée automatiquement toutes les 15 minutes par Jeedom
      public static function cron15() {
      }
     */
    
    /*
     * Fonction exécutée automatiquement toutes les 30 minutes par Jeedom
      public static function cron30() {
      }
     */
    
    /*
     * Fonction exécutée automatiquement toutes les heures par Jeedom
      public static function cronHourly() {
      }
     */

    /*
     * Fonction exécutée automatiquement tous les jours par Jeedom
      public static function cronDaily() {
      }
     */



    /*     * *********************Méthodes d'instance************************* */
    
 // Fonction exécutée automatiquement avant la création de l'équipement 
    public function preInsert() {
        
    }

 // Fonction exécutée automatiquement après la création de l'équipement 
    public function postInsert() {
        
    }

 // Fonction exécutée automatiquement avant la mise à jour de l'équipement 
    public function preUpdate() {
        
    }

 // Fonction exécutée automatiquement après la mise à jour de l'équipement 
    public function postUpdate() {
 		$cmd = $this->getCmd(null, 'refresh'); // On recherche la commande refresh de l’équipement
		if (is_object($cmd)) { //elle existe et on lance la commande
			 $cmd->execCmd();
		}       
    }

    public function preSave() {
		$this->setDisplay("width","400px");
		$this->setDisplay("height","350px");
    }
	
	public function loadCommandConfFile($fileName = "solisCmdList.json") {
		$jsonCmdList = file_get_contents(dirname(__FILE__) . '/'.$fileName);
		$this->cmdList = json_decode($jsonCmdList, true);
		
		if (!is_array($this->cmdList)) {
			log::add('soliscloud','warning',__('Impossible de décoder le fichier '.$fileName, __FILE__));
			log::add('soliscloud', 'error',$jsonCmdList);
			log::add('soliscloud', 'error',"<pre>".print_r($this->cmdList,true)."</pre>");
		}
	}
	
    private function setCmdConfig($cmdConfig) {
		try {
			$info = $this->getCmd(null, $cmdConfig["logicalId"]);
			if (!is_object($info)) {
				$info = new soliscloudCmd();
				$info->setName(__($cmdConfig["name"], __FILE__));
				$info->setLogicalId($cmdConfig["logicalId"]);
				$info->setEqLogic_id($this->getId());
				$info->setType($cmdConfig["type"]);
				$info->setSubType($cmdConfig["subType"]);
				$info->setIsHistorized($cmdConfig["historized"]);
				$info->setIsVisible($cmdConfig["visible"]);	
				$info->setUnite($cmdConfig["unit"]);
				$info->setOrder($cmdConfig["order"]);
				$info->save();
			}
		} catch (\Exception $e) {
			log::add('soliscloud','error',__('Erreur setCmdConfig '));
			log::add('soliscloud', 'error',"<pre>".print_r($cmdConfig,true)."</pre>");
		}	
	}
	
	// Fonction exécutée automatiquement après la sauvegarde (création ou mise à jour) de l'équipement 
    public function postSave() {
		$this->loadCommandConfFile();
		try {
			foreach($this->cmdList['commands'] as $cmdConf) {
				$this->setCmdConfig($cmdConf);
			}
			log::add('soliscloud', 'info', "postSave ok nb=".count($this->cmdList['commands']));
		} catch (\Exception $e) {
			log::add('soliscloud','error',__('Erreur décodage fichier '.$inverterCmdFile, __FILE__));
			log::add('soliscloud', 'error',"<pre>".print_r($this->cmdList,true)."</pre>");
		}
	}

	
 // Fonction exécutée automatiquement avant la suppression de l'équipement 
    public function preRemove() {
        
    }

 // Fonction exécutée automatiquement après la suppression de l'équipement 
    public function postRemove() {
        
    }

	
	public function getsoliscloudData() {
		$this->loadCommandConfFile();
		$soliscloud_regisno = $this->getConfiguration("regisno"); //'<your API Key provided by SolisAPI>'; // i.e. '13000000000000000'
		$soliscloud_token = $this->getConfiguration("token"); //'<your API SECRET provided by SolisAPI>'; // i.e. 'aabbccddeff001122334455'
		$inverterSerialNumber = $this->getConfiguration("invertersn"); //id of your solis inverter i.e. ''1000111122223333';
		
		if (strlen($soliscloud_regisno) == 0) {
			log::add('soliscloud', 'debug','Registration Number not provided ...');
			$this->checkAndUpdateCmd('status', 'Registration Number not provided ...');
			return;
		}
		
		if (strlen($soliscloud_token) == 0) {
			log::add('soliscloud', 'debug','Token not provided ...');
			$this->checkAndUpdateCmd('status', 'Token not provided ...');
			return;
		}
		
		$api = new soliscloudApi($soliscloud_regisno, $soliscloud_token);
		if ($data = $api->getInverterDetail($inverterSerialNumber, true)) {
			log::add('soliscloud', 'debug',"<pre>".print_r($data,true)."</pre>");
			if (is_array($data)) {
				try {
					foreach($this->cmdList['commands'] as $cmdConfig) {
						if ($cmdConfig["type"] == "info") {
							$value = $api->getInverterValue($cmdConfig["inverterValueId"], "", $cmdConfig["unit"]);
							$this->checkAndUpdateCmd($cmdConfig["logicalId"], $value);
							log::add('soliscloud','info',$cmdConfig["logicalId"]." (".$cmdConfig["inverterValueId"].") = ".$data[$cmdConfig["inverterValueId"]] ." => ".$value." ".$cmdConfig["unit"]);
						}
					}
					log::add('soliscloud', 'debug', "getsoliscloudData ok nb=".count($this->cmdList['commands']));
				} catch (\Exception $e) {
					log::add('soliscloud','error',__('Erreur décodage fichier cmdList.json', __FILE__));
				}
			}
		} else {
			log::add('soliscloud', 'error',"getInverterData() failed");
		}

	}
	

    /*
     * Non obligatoire : permet de modifier l'affichage du widget (également utilisable par les commandes)
      public function toHtml($_version = 'dashboard') {

      }
     */

    /*
     * Non obligatoire : permet de déclencher une action après modification de variable de configuration
    public static function postConfig_<Variable>() {
    }
     */

    /*
     * Non obligatoire : permet de déclencher une action avant modification de variable de configuration
    public static function preConfig_<Variable>() {
    }
     */

    /*     * **********************Getteur Setteur*************************** */
}

class soliscloudCmd extends cmd {
    /*     * *************************Attributs****************************** */
    
    /*
      public static $_widgetPossibility = array();
    */
    
    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    /*
     * Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
      public function dontRemoveCmd() {
      return true;
      }
     */

  // Exécution d'une commande  
     public function execute($_options = array()) {
				$eqlogic = $this->getEqLogic();
				switch ($this->getLogicalId()) {		
					case 'refresh':
						$info = $eqlogic->getsoliscloudData();
						break;					
		}        
     }

    /*     * **********************Getteur Setteur*************************** */
}
