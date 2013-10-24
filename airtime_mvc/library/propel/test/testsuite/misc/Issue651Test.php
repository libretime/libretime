<?php

class Issue651Test extends PHPUnit_Framework_TestCase
{

    public function testIndex()
    {
        $updatedSchema = '
<database>
  <table name="notification">
    <column autoIncrement="true" name="id" primaryKey="true" required="true" type="INTEGER" />
    <column name="target_user_id" required="true" type="INTEGER" />
    <column name="notification_type_unique_name" required="true" size="255" type="VARCHAR" />
    <column name="group_id" type="INTEGER" />
    <column name="date" required="true" type="TIMESTAMP" />
    <column name="objects" type="LONGVARCHAR" />
    <column name="is_new" defaultValue="1" required="true" type="BOOLEAN" />
    <foreign-key foreignTable="notification_type" name="FK_NOTIFICATION_TYPENOTIFICATION0">
      <reference foreign="unique_name" local="notification_type_unique_name" />
    </foreign-key>
    <index name="FK_NOTIFICATION_TARGET_USER">
      <index-column name="target_user_id" />
    </index>
    <index name="FK_NOTIFICATION_TYPENOTIFICATION">
      <index-column name="notification_type_unique_name" />
    </index>
  </table>
  <table name="notification_type">
    <column name="module_unique_name" primaryKey="true" required="true" size="255" type="VARCHAR" />
    <column name="unique_name" primaryKey="true" required="true" size="255" type="VARCHAR" />
    <column name="is_correction" defaultValue="0" required="true" type="BOOLEAN" />
    <column name="disabled_engine" size="255" type="VARCHAR" />
    <foreign-key foreignTable="module" name="FK_TYPENOTIFICATION_MODULE0" onDelete="CASCADE" onUpdate="CASCADE">
      <reference foreign="unique_name" local="module_unique_name" />
    </foreign-key>
    <index name="FK_TYPENOTIFICATION_MODULE">
      <index-column name="module_unique_name" />
    </index>
  </table>
  <table name="module">
    <column autoIncrement="true" name="id" primaryKey="true" required="true" type="INTEGER" />
    <column name="unique_name" required="true" size="255" type="VARCHAR" />
    <column name="label" primaryString="true" required="true" size="255" type="VARCHAR" />
    <column name="description" required="true" size="255" type="VARCHAR" />
  </table>
</database>
';

$actual = "
# This is a fix for InnoDB in MySQL >= 4.1.x
# It \"suspends judgement\" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- notification
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `notification`;

CREATE TABLE `notification`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `target_user_id` INTEGER NOT NULL,
    `notification_type_unique_name` VARCHAR(255) NOT NULL,
    `group_id` INTEGER,
    `date` DATETIME NOT NULL,
    `objects` TEXT,
    `is_new` TINYINT(1) DEFAULT 1 NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `FK_NOTIFICATION_TARGET_USER` (`target_user_id`),
    INDEX `FK_NOTIFICATION_TYPENOTIFICATION` (`notification_type_unique_name`),
    CONSTRAINT `FK_NOTIFICATION_TYPENOTIFICATION0`
        FOREIGN KEY (`notification_type_unique_name`)
        REFERENCES `notification_type` (`unique_name`)
) ENGINE=InnoDb;

-- ---------------------------------------------------------------------
-- notification_type
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `notification_type`;

CREATE TABLE `notification_type`
(
    `module_unique_name` VARCHAR(255) NOT NULL,
    `unique_name` VARCHAR(255) NOT NULL,
    `is_correction` TINYINT(1) DEFAULT 0 NOT NULL,
    `disabled_engine` VARCHAR(255),
    PRIMARY KEY (`module_unique_name`,`unique_name`),
    INDEX `FK_TYPENOTIFICATION_MODULE` (`module_unique_name`),
    INDEX `I_referenced_FK_NOTIFICATION_TYPENOTIFICATION0_1` (`unique_name`),
    CONSTRAINT `FK_TYPENOTIFICATION_MODULE0`
        FOREIGN KEY (`module_unique_name`)
        REFERENCES `module` (`unique_name`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDb;

-- ---------------------------------------------------------------------
-- module
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `module`;

CREATE TABLE `module`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `unique_name` VARCHAR(255) NOT NULL,
    `label` VARCHAR(255) NOT NULL,
    `description` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDb;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
";

        $platform = new MysqlPlatform();
        $platform->setDefaultTableEngine('InnoDb');
        $updatedBuilder = new PropelQuickBuilder();
        $updatedBuilder->setPlatform($platform);
        $updatedBuilder->setSchema($updatedSchema);

        $sql = $updatedBuilder->getSQL();
        $this->assertEquals($actual, $sql);
    }

}
