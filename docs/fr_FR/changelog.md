# Changelog plugin Solarman

>**IMPORTANT**
>
>S'il n'y a pas d'information sur la mise à jour, c'est que celle-ci concerne uniquement de la mise à jour de documentation, de traduction ou de texte.

# 1.2.4

   - possibilité de scanner une plages de registres pour vérifier s'ils sont accessibles et la conformité des données reccueillies
   - ajout de fichiers modbus pour quelques onduleurs

# 1.2.3

   - ajout du numéro de version en plus de la date dans la zone "état" de la configuration du plugin (merci à @Bad pour son coup de main)
   - amélioration du processus d'interrogation de l'onduleur. Maintenant le plugin se connecte puis ne se déconnecte qu'une fois tous les registres interrogés (avant il y avait déconnexion/reconnexion à chaque plage de registre, honte à moi)
   - correction de qq fautes de frappes

# 1.2.2

   - prise en compte des clés S2-WL-ST installées sur certains onduleurs SOLIS (peut être d'autres?)

# 1.2.1 (12/09/2024) => béta

   - utilisation des lib de @nebz et de @Mips + @TiTidom-RC (dependance.lib et pyenv.lib)

# 1.1.1b (05/09/2024) => béta + stable

   - correctif un peu plus "propre" que celui apporté rapidement au 1.1.1

# 1.1.1 (02/09/2024) => béta + stable

   - correction bug qui saturait la mémoire

# 1.1.0 (28/08/2024) => béta + stable

   - création d'un environnement virtuel pour python
   - ajout et/ou modification des fichiers de configuration yaml d'onduleurs

# 1.0.9 (13/07/2024) => béta + stable

   - correction d'un bug lors de la création de certains équipements
   - création d'un bouton pour poster directement sous community

# 1.0.8 (30/03/2024) => béta + stable

   - ajout de règles de décodage propres à sofar solar (heure et date), voir rule 10 et 11 dans la doc
   - ajout règle de décodage avec restitution en binaire
   - ajout et ou modification de fichiers de configuration
   - possibilité d'affecter les commandes au widget depuis l'équipement
   - modification du nom du widget en solarman_distri_onduleur désolé @phpvarious de ne pas y avoir pensé

# 1.0.7 (08/12/2023) => béta + stable

   - ajout fichier onduleur Sofar Solar xxx TL Génération 2
   - ajout de la commande refresh
   - correction bugs dans le template
   - ajout fichiers doc des onduleurs lorsque je les ai

# 1.0.6 (25/11/2023) => béta

   - amélioration gestion du process python
   - corection qq bugs
   - ajout compatibilité widget pour le fichier sofar_XXTL-G3.yaml
   - ajout d'un fichier pour pouvoir envoyer des commandes à l'onduleur

# 1.0.5 (11/11/2023) => béta

   - ajout d'un template pour rendre l'affichage de certaines données plus sympas
   - template OK pour SOFAR SOLAR HYD x000 EP
   - corection qq bugs

# 1.0.4 (29/10/2023) => béta + stable
   
   - correction de bugs

# 1.0.3 (29/10/2023) => béta + stable
   
   - ajout de dépendances pour prise en compte de yampl pour php sur certaines configurations (merci @Loïc)
   - correction d'une mauvaise saisie dans les crons + ajout de la possibilité de saisir directement le temps(1 ou 5 ou ...)
   - possibilité de mettre des espaces dans les noms des équipement, par contre les fichiers de logs contiendront des "_" à la place des espaces
   - ajout d'un nouveau fichier pour onduleur sofar solar XX TL G3, testé sur SOFAR SOLAR 3000 TL G3 (merci @Morzini et @Bernard26300)
   - modification de l'affichage des commandes d'un équipement avec l'ajout des registres en décimal et en héxadécimal
   - amélioration + correction de bugs dans l'interrogation de l'onduleur
   
# 1.0.2 (19/10/2023) => béta
   
   - correction bug
   - correction fichiers de configuration yaml
   - ajout mise à jour des commandes dans l'équipement
   - ajout d'une fonction pour scanner le réseau à la recherche d'onduleurs compatibles
   
# 1.0.1 (16/10/2023) => béta
   
   - documentation
  
  
# 1.0.0 (15/10/2023) => béta

- Première version béta fonctionnelle
