#------------------------------------------------------------
#	Autrice : Lucie Moulin
#	Date : 05.05.2021
#	Description : Script de génération de la base de données
#	Généré avec JMerise et modifié manuellement
#------------------------------------------------------------

#------------------------------------------------------------
# Database: db_anonymousEvaluations
#------------------------------------------------------------

DROP DATABASE IF EXISTS db_anonymousEvaluations;
CREATE DATABASE db_anonymousEvaluations CHARACTER SET utf8 COLLATE utf8_general_ci;
USE db_anonymousEvaluations;

#------------------------------------------------------------
# Table: t_role
#------------------------------------------------------------

CREATE TABLE t_role(
        idRole         Int  Auto_increment  NOT NULL ,
        rolName        Varchar (255) NOT NULL ,
        rolDescription Text NOT NULL
	,CONSTRAINT t_role_PK PRIMARY KEY (idRole)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: t_user
#------------------------------------------------------------

CREATE TABLE t_user(
        idUser       Int  Auto_increment  NOT NULL ,
        useLogin     Varchar (255) NOT NULL ,
        useLastName  Varchar (255) NOT NULL ,
        useFirstName Varchar (255) NOT NULL ,
        fkRole       Int NOT NULL
	,CONSTRAINT t_user_PK PRIMARY KEY (idUser)

	,CONSTRAINT t_user_t_role_FK FOREIGN KEY (fkRole) REFERENCES t_role(idRole)
	,CONSTRAINT t_user_useLogin UNIQUE KEY `useLogin` (`useLogin`)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: t_permission
#------------------------------------------------------------

CREATE TABLE t_permission(
        idPermission   Int  Auto_increment  NOT NULL ,
        perCode        Varchar (255) NOT NULL ,
        perDescription Text NOT NULL
	,CONSTRAINT t_permission_PK PRIMARY KEY (idPermission)
	
	,CONSTRAINT t_user_perCode UNIQUE KEY `perCode` (`perCode`)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: t_group
#------------------------------------------------------------

CREATE TABLE t_group(
        idGroup Int  Auto_increment  NOT NULL ,
        groName Varchar (255) NOT NULL ,
        fkUser  Int NOT NULL
	,CONSTRAINT t_group_PK PRIMARY KEY (idGroup)

	,CONSTRAINT t_group_t_user_FK FOREIGN KEY (fkUser) REFERENCES t_user(idUser)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: t_state
#------------------------------------------------------------

CREATE TABLE t_state(
        idState Int  Auto_increment  NOT NULL ,
        staName Varchar (255) NOT NULL
	,CONSTRAINT t_state_PK PRIMARY KEY (idState)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: t_evaluation
#------------------------------------------------------------

CREATE TABLE t_evaluation(
        idEvaluation    Int  Auto_increment  NOT NULL ,
        evaModuleNumber Varchar (255) NOT NULL ,
        evaDate         Date NOT NULL ,
        evaLength       Varchar (255) NOT NULL ,
        evaInstructions Varchar (255) DEFAULT NULL,
        fkUser          Int NOT NULL ,
        fkGroup         Int NOT NULL ,
        fkState         Int NOT NULL
	,CONSTRAINT t_evaluation_PK PRIMARY KEY (idEvaluation)

	,CONSTRAINT t_evaluation_t_user_FK FOREIGN KEY (fkUser) REFERENCES t_user(idUser)
	,CONSTRAINT t_evaluation_t_group0_FK FOREIGN KEY (fkGroup) REFERENCES t_group(idGroup)
	,CONSTRAINT t_evaluation_t_state1_FK FOREIGN KEY (fkState) REFERENCES t_state(idState)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: t_r_userEvaluation
#------------------------------------------------------------

CREATE TABLE t_r_userEvaluation(
        fkEvaluation   Int NOT NULL ,
        fkUser         Int NOT NULL ,
        useAnonymousId Varchar (255) NOT NULL ,
        useReturn      Varchar (255) DEFAULT NULL ,
        useGrade       Varchar (255) DEFAULT NULL ,
        useComment     Varchar (255) DEFAULT NULL
	,CONSTRAINT t_r_userEvaluation_PK PRIMARY KEY (fkEvaluation,fkUser)

	,CONSTRAINT t_r_userEvaluation_t_evaluation_FK FOREIGN KEY (fkEvaluation) REFERENCES t_evaluation(idEvaluation)
	,CONSTRAINT t_r_userEvaluation_t_user0_FK FOREIGN KEY (fkUser) REFERENCES t_user(idUser)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: t_r_rolePermission
#------------------------------------------------------------

CREATE TABLE t_r_rolePermission(
        fkRole       Int NOT NULL ,
        fkPermission Int NOT NULL
	,CONSTRAINT t_r_rolePermission_PK PRIMARY KEY (fkRole,fkPermission)

	,CONSTRAINT t_r_rolePermission_t_role_FK FOREIGN KEY (fkRole) REFERENCES t_role(idRole)
	,CONSTRAINT t_r_rolePermission_t_permission0_FK FOREIGN KEY (fkPermission) REFERENCES t_permission(idPermission)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: t_r_groupUser
#------------------------------------------------------------

CREATE TABLE t_r_groupUser(
        fkGroup Int NOT NULL ,
        fkUser  Int NOT NULL
	,CONSTRAINT t_r_groupUser_PK PRIMARY KEY (fkGroup,fkUser)

	,CONSTRAINT t_r_groupUser_t_group_FK FOREIGN KEY (fkGroup) REFERENCES t_group(idGroup)
	,CONSTRAINT t_r_groupUser_t_user0_FK FOREIGN KEY (fkUser) REFERENCES t_user(idUser)
)ENGINE=InnoDB;

