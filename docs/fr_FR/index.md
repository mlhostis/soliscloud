<br><br><br>

Présentation
===
Le plugin Solis cloud permet de récupérer les informations de votre onduleur monitoré par le site https://www.soliscloud.com/ , comme par exemple l'onduleur SOLIS S6-EHP1P(3-6k)K-L

Liste (non exhaustives) des onduleurs pris en compte à l'heure actuelle et le fichier de configuration associé:  
<br>
Les docs des onduleurs peuvent se trouver dans [ce répertoire](\doc_onduleurs\ )
<br>

| Onduleurs supportés                      | Observations                                                                                                                  |
|------------------------------------------|-------------------------------------------------------------------------------------------------------------------------------|
| SOLIS S5-EHP1P(3-6k)K-L                  |                                                                                                                               |
| SOLIS S6-EHP1P(3-6k)K-L                  |                                                                                                                               |
| ...                                      |                                                                                                                               |
<br><br><br><br><br>


Pré-requis:
===
Pour pouvoir récupérer les infos de votre onduleur, il faut un onduleur compatible (liste non exhaustive ci-dessus) conncté à la plateforme cloud Solis

<br>
<br>

Installation du plugin
===
Besoin d'explications? Ok, alors une fois le plugin installé faites une mise à jour des dépendances

<br>
<br>

Configuration générale du plugin
===
![Config générale](config-plugin.png)

<br>
<br>

Création d'un nouvel équipement
===
![Nouvel équipement](ajout_ondul.png)

Cliquer sur le + "Ajouter"

## Choix
<br>

![Choix équipement](ajout_ondul1.png)

Donnez un nom à votre nouvel équipement puis choisissez le fichier modèle qui servira à le paramétrer

<br>

## Paramétrage de l'équipement:
<br>

![paramétrage équipement](param_equipmnt.png)
<br>
Les premiers champs sont classiques.

Il faut que vous saisissiez l'API registration number et le token que vous trouverez sur votre compte du portail Solis https://www.soliscloud.com/ 

![paramétrage clé API](APImanagementSolis.png)

Si vous ne trouvez pas ces identifiants sur le portail solis, il faut au support solis d'activer la clé d'API pour votre compte solis.

Ensuite saisissez l'identifiant de l'onduleur au format base 10 (et pas au format hexadécimal).

## Paramétrage de la réserve batterie:
<br>
Ces parametres, permettent de faire varier la réserve de la batterie pour par exemple forcer la charge de la batterie durant les heures creuses l'hiver. Le principe est de définir des plages de recharges à partir du réseau sur l'onduleur. Pour éviter la décharge durant les heures cruses, il faut augmenter le taux de réserve sur la batterie. 
<p>
Par exemple, si vos heures creuses débuttent à 22h et se terminent à 6h, il vous suffit de fixer la réserve à 80% à 22h et la rétablir à la valeur minimale souhaité à la fin des heures creuses (ici 20%)

![paramétrage batterie](param_charge_bat.png)
<br>

Remerciements
===

Merci à [@phroc] qui a développé le plugin solax cloud duquel je me suis inspiré

Bug
===

En cas de bug sur le plugin il est possible de demander de l'aide :

[https://community.jeedom.com/tag/plugin-soliscloud](https://community.jeedom.com/tag/plugin-soliscloud)

