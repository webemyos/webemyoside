CREATE TABLE IF NOT EXISTS `EeProfilCompetenceEntity` ( 
`Id` int(11) NOT NULL AUTO_INCREMENT, 
`CompetenceId` INT  NULL ,
`UserId` INT  NULL ,
`AppName` VARCHAR(200)  NULL ,
`AppId` INT  NULL ,
`EntityName` VARCHAR(200)  NULL ,
`EntityId` INT  NULL ,
PRIMARY KEY (`Id`)
) ENGINE=InnoDB  DEFAULT CHARACTER SET `utf8`; 