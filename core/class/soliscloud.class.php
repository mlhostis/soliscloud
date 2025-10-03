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
// Inclusion des fichiers nécessaires au fonctionnement du plugin
require_once __DIR__  . '/../../../../core/php/core.inc.php';
include_once "soliscloudApi.class.php";

/**
 * Classe principale du plugin SolisCloud pour Jeedom.
 * Gère la logique des équipements et l'intégration avec l'API SolisCloud.
 */
class soliscloud extends eqLogic {
    
	/*     * *************************Attributs****************************** */
    
    /*
     * Permet de définir les possibilités de personnalisation du widget (en cas d'utilisation de la fonction 'toHtml' par exemple)
     * Tableau multidimensionnel - exemple: array('custom' => true, 'custom::layout' => false)
     * public static $_widgetPossibility = array();
     */
    
    /*     * ***********************Methode static*************************** */

    /**
     * Fonction exécutée automatiquement toutes les minutes par Jeedom.
     * Parcourt tous les équipements SolisCloud actifs et exécute la commande "refresh" si elle existe.
     */
    public static function cron() {
		foreach (self::byType('soliscloud') as $soliscloud) {
			if ($soliscloud->getIsEnable() == 1) {
				$cmd = $soliscloud->getCmd(null, 'refresh');
				if (!is_object($cmd)) {
					continue;
				}
				$cmd->execCmd();
			}
		}      
    }
 
    /**
     * Fonction exécutée automatiquement toutes les 5 minutes par Jeedom.
     * Même logique que cron(), mais appelée à une autre fréquence.
     */
    public static function cron5() {
		foreach (self::byType('soliscloud') as $soliscloud) {
			if ($soliscloud->getIsEnable() == 1) {
				$cmd = $soliscloud->getCmd(null, 'refresh');
				if (!is_object($cmd)) {
					continue;
				}
				$cmd->execCmd();
			}
		} 
    }
     
    /*
     * Fonctions de cron supplémentaires (10, 15, 30 minutes, horaire, journalier) à implémenter si besoin.
     */

    /*     * *********************Méthodes d'instance************************* */
    
    /**
     * Fonction exécutée automatiquement avant la création de l'équipement.
     * Peut être utilisée pour initialiser des valeurs ou vérifier des prérequis.
     */
    public function preInsert() {
        
    }

    /**
     * Fonction exécutée automatiquement après la création de l'équipement.
     * Peut être utilisée pour effectuer des actions post-création.
     */
    public function postInsert() {
        
    }

    /**
     * Fonction exécutée automatiquement avant la mise à jour de l'équipement.
     * Peut servir à valider ou préparer des données.
     */
    public function preUpdate() {
        
    }

    /**
     * Fonction exécutée automatiquement après la mise à jour de l'équipement.
     * Relance la commande "refresh" si elle existe pour mettre à jour les données.
     */
    public function postUpdate() {
 		$cmd = $this->getCmd(null, 'refresh');
		if (is_object($cmd)) {
			 $cmd->execCmd();
		}       
    }

    /**
     * Fonction exécutée automatiquement avant la sauvegarde de l'équipement.
     * Définit la taille d'affichage par défaut du widget.
     */
    public function preSave() {
		$this->setDisplay("width","400px");
		$this->setDisplay("height","350px");
    }
	
    /**
     * Charge la liste des commandes depuis un fichier de configuration JSON.
     * @param string $fileName Nom du fichier de configuration (par défaut "solisCmdList.json")
     */
	public function loadCommandConfFile($fileName = "solisCmdList.json") {
		$jsonCmdList = file_get_contents(dirname(__FILE__) . '/'.$fileName);
		$cmdList = json_decode($jsonCmdList, true);
		
		if (!is_array($cmdList)) {
			log::add('soliscloud','warning',__('Impossible de décoder le fichier '.$fileName, __FILE__));
			log::add('soliscloud', 'error',$jsonCmdList);
			log::add('soliscloud', 'error',"<pre>".print_r($cmdList,true)."</pre>");
		}
		return $cmdList;
	}
	
    /**
     * Crée ou met à jour une commande Jeedom à partir d'une configuration.
     * @param array $cmdConfig Configuration de la commande (issue du JSON)
     */
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
	
    /**
     * Fonction exécutée automatiquement après la sauvegarde (création ou mise à jour) de l'équipement.
     * Charge la configuration des commandes et les crée si besoin.
     */
    public function postSave() {
		$cmdList = $this->loadCommandConfFile();
		try {
			foreach($cmdList['commands'] as $cmdConf) {
				$this->setCmdConfig($cmdConf);
			}
			log::add('soliscloud', 'info', "postSave ok nb=".count($cmdList['commands']));
		} catch (\Exception $e) {
			log::add('soliscloud','error',__('Erreur décodage fichier '.$inverterCmdFile, __FILE__));
			log::add('soliscloud', 'error',"<pre>".print_r($cmdList,true)."</pre>");
		}
	}

    /**
     * Fonction exécutée automatiquement avant la suppression de l'équipement.
     * Peut servir à nettoyer des ressources.
     */
    public function preRemove() {
        
    }

    /**
     * Fonction exécutée automatiquement après la suppression de l'équipement.
     * Peut servir à nettoyer des ressources.
     */
    public function postRemove() {
        
    }

    /**
     * Récupère les données de l'onduleur via l'API SolisCloud et met à jour les commandes Jeedom.
     * Gère les erreurs de configuration et journalise les actions.
     */
	public function getsoliscloudData() {
		$cmdList = $this->loadCommandConfFile();
		$soliscloud_regisno = $this->getConfiguration("regisno");
		$soliscloud_token = $this->getConfiguration("token");
		$inverterSerialNumber = $this->getConfiguration("invertersn");
		
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
					foreach($cmdList['commands'] as $cmdConfig) {
						if ($cmdConfig["type"] == "info") {
							$value = $api->getInverterValue($cmdConfig["inverterValueId"], "", $cmdConfig["unit"]);
							$this->checkAndUpdateCmd($cmdConfig["logicalId"], $value);
							log::add('soliscloud','info',$cmdConfig["logicalId"]." (".$cmdConfig["inverterValueId"].") = ".$data[$cmdConfig["inverterValueId"]] ." => ".$value." ".$cmdConfig["unit"]);
						}
					}
					log::add('soliscloud', 'debug', "getsoliscloudData ok nb=".count($cmdList['commands']));
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
     * public function toHtml($_version = 'dashboard') {
     * }
     */

    /*
     * Non obligatoire : permet de déclencher une action après modification de variable de configuration
     * public static function postConfig_<Variable>() {
     * }
     */

    /*
     * Non obligatoire : permet de déclencher une action avant modification de variable de configuration
     * public static function preConfig_<Variable>() {
     * }
     */

    /*     * **********************Getteur Setteur*************************** */
}

/**
 * Classe représentant une commande du plugin SolisCloud.
 * Permet d'exécuter des actions ou de récupérer des informations depuis l'équipement.
 */
class soliscloudCmd extends cmd {
    /*     * *************************Attributs****************************** */
    
    /*
     * public static $_widgetPossibility = array();
     */
    
    /*     * ***********************Methode static*************************** */

    /*     * *********************Methode d'instance************************* */

    /*
     * Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
     * public function dontRemoveCmd() {
     *   return true;
     * }
     */

    /**
     * Exécution d'une commande.
     * Pour la commande "refresh", déclenche la récupération des données de l'onduleur.
     * @param array $_options Options d'exécution
     */
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
