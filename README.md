# Plateforme web de gestion de retour d'évaluations anonymes
Prérequis :
- Serveur Apache avec PHP 8 fonctionnel
- Serveur MySQL fonctionnel
- Connexion à l'Active Directory de l'ETML possible

Procédure :
1.	Téléchargez le code source de la plateforme sur  GitHub : https://github.com/LucieMoulin/anonymousEvaluations
2.	Dé-zipper l’archive téléchargée dans le dossier WEB de votre serveur Apache
3.	Exécutez le script de création de la base de données createDatabase.sql disponible dans le contenu téléchargé, ainsi que le script insertRolesPermissionsStates.sql, insérant les rôles, permissions et états dans la base de données. Ces informations sont nécessaires au fonctionnement de la plateforme. Un script de démonstration est également disponible : demo.sql.
ATTENTION : le script createDatabase.sql va créer une base de donnée nommée db_anonymousevlauations. Si une base de donnée de ce nom est déjà présente sur votre serveur, elle sera supprimée.
4.	Modifier le fichier constants.php en insérant les informations sur la base de données, ainsi que le nom du dossier dans lequel la plateforme est stockée. Constantes à modifier : DB_HOST, DB_NAME, DB_USER, DB_PASS, ROOT_DIR. Au besoin, modifier également les informations de connexion LDAP
5.	Modifier le fichier .htaccess en spécifiant le chemin d’accès de l’index. 
La plateforme est maintenant prête à être utilisée.

Un fichier de configuration est disponible : config.json. Il permet la configuration des identifiants anonymes disponibles, ainsi que les formats de fichier autorisés. Chaque identifiant anonyme est défini par un id, et éventuellement des symboles. Chaque identifiant peut être maqué comme désactivé, ce qui fera qu’il ne sera pas utilisé dans les futures évaluations.  Par défaut, l’identifiant bêta est désactivé. Il est possible d’ajouter un identifiant, simplement en ajoutant les informations nécessaires dans le fichier json.
Pour les formats de fichier, ils sont séparés en deux, les formats autorisés pour les fichiers de consigne « instructionsAcceptedFormats », et les formats autorisés pour les retours d’élèves « returnAcceptedFormats ». Pour ces deux éléments, n’importe quel format peut être ajouté, et les formats mentionnés peuvent être désactivés. 

Si vous avez des questions ou des problèmes d’installation, contactez Lucie Moulin par e-mail à moulin.l@outlook.com ou via la page GitHub du projet : https://github.com/LucieMoulin/anonymousEvaluations
