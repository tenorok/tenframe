SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `tmod_shop` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `tmod_shop` ;

-- -----------------------------------------------------
-- Table `tmod_shop`.`tmod_shop_categories`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `tmod_shop`.`tmod_shop_categories` (
  `tmod_shop_categories_id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  `alias` VARCHAR(255) NULL ,
  `serial` INT NULL DEFAULT 0 ,
  `hide` TINYINT NULL DEFAULT 1 ,
  `tmod_shop_categories_fk` INT NULL ,
  PRIMARY KEY (`tmod_shop_categories_id`) ,
  INDEX `fk_categories_categories1` (`tmod_shop_categories_fk` ASC) ,
  CONSTRAINT `fk_categories_categories1`
    FOREIGN KEY (`tmod_shop_categories_fk` )
    REFERENCES `tmod_shop`.`tmod_shop_categories` (`tmod_shop_categories_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tmod_shop`.`tmod_shop_fields`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `tmod_shop`.`tmod_shop_fields` (
  `tmod_shop_fields_id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  `type` VARCHAR(45) NULL ,
  `count` TINYINT NULL DEFAULT 1 ,
  `list` TINYINT NULL ,
  `tmod_shop_fields_fk` INT NULL ,
  `tmod_shop_categories_fk` INT NOT NULL ,
  PRIMARY KEY (`tmod_shop_fields_id`) ,
  INDEX `fk_fields_product_types` (`tmod_shop_categories_fk` ASC) ,
  INDEX `fk_fields_fields1` (`tmod_shop_fields_fk` ASC) ,
  CONSTRAINT `fk_fields_product_types`
    FOREIGN KEY (`tmod_shop_categories_fk` )
    REFERENCES `tmod_shop`.`tmod_shop_categories` (`tmod_shop_categories_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_fields_fields1`
    FOREIGN KEY (`tmod_shop_fields_fk` )
    REFERENCES `tmod_shop`.`tmod_shop_fields` (`tmod_shop_fields_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tmod_shop`.`tmod_shop_values`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `tmod_shop`.`tmod_shop_values` (
  `tmod_shop_values_id` INT NOT NULL AUTO_INCREMENT ,
  `val_int` INT NULL ,
  `val_float` FLOAT NULL ,
  `val_varchar` VARCHAR(255) NULL ,
  `val_text` TEXT NULL ,
  `val_datetime` DATETIME NULL ,
  `val_file` VARCHAR(255) NULL ,
  `tmod_shop_fields_fk` INT NOT NULL ,
  PRIMARY KEY (`tmod_shop_values_id`) ,
  INDEX `fk_values_fields1` (`tmod_shop_fields_fk` ASC) ,
  CONSTRAINT `fk_values_fields1`
    FOREIGN KEY (`tmod_shop_fields_fk` )
    REFERENCES `tmod_shop`.`tmod_shop_fields` (`tmod_shop_fields_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tmod_shop`.`tmod_shop_products`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `tmod_shop`.`tmod_shop_products` (
  `tmod_shop_products_id` INT NOT NULL AUTO_INCREMENT ,
  `count` INT NULL ,
  `publish` TINYINT NULL ,
  `tmod_shop_categories_fk` INT NOT NULL ,
  PRIMARY KEY (`tmod_shop_products_id`) ,
  INDEX `fk_products_categories1` (`tmod_shop_categories_fk` ASC) ,
  CONSTRAINT `fk_products_categories1`
    FOREIGN KEY (`tmod_shop_categories_fk` )
    REFERENCES `tmod_shop`.`tmod_shop_categories` (`tmod_shop_categories_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tmod_shop`.`tmod_shop_products_values`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `tmod_shop`.`tmod_shop_products_values` (
  `tmod_shop_products_values_id` INT NOT NULL AUTO_INCREMENT ,
  `tmod_shop_products_fk` INT NOT NULL ,
  `tmod_shop_values_fk` INT NOT NULL ,
  PRIMARY KEY (`tmod_shop_products_values_id`) ,
  INDEX `fk_products_values_values1` (`tmod_shop_values_fk` ASC) ,
  INDEX `fk_products_values_products1` (`tmod_shop_products_fk` ASC) ,
  CONSTRAINT `fk_products_values_values1`
    FOREIGN KEY (`tmod_shop_values_fk` )
    REFERENCES `tmod_shop`.`tmod_shop_values` (`tmod_shop_values_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_products_values_products1`
    FOREIGN KEY (`tmod_shop_products_fk` )
    REFERENCES `tmod_shop`.`tmod_shop_products` (`tmod_shop_products_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tmod_shop`.`tmod_shop_prices`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `tmod_shop`.`tmod_shop_prices` (
  `tmod_shop_prices_id` INT NOT NULL AUTO_INCREMENT ,
  `price` INT NULL ,
  `name` VARCHAR(255) NULL ,
  `display` VARCHAR(45) NULL ,
  PRIMARY KEY (`tmod_shop_prices_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tmod_shop`.`tmod_shop_products_prices`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `tmod_shop`.`tmod_shop_products_prices` (
  `tmod_shop_products_prices_id` INT NOT NULL AUTO_INCREMENT ,
  `tmod_shop_products_fk` INT NOT NULL ,
  `tmod_shop_prices_fk` INT NOT NULL ,
  PRIMARY KEY (`tmod_shop_products_prices_id`) ,
  INDEX `fk_products_prices_products1` (`tmod_shop_products_fk` ASC) ,
  INDEX `fk_products_prices_prices1` (`tmod_shop_prices_fk` ASC) ,
  CONSTRAINT `fk_products_prices_products1`
    FOREIGN KEY (`tmod_shop_products_fk` )
    REFERENCES `tmod_shop`.`tmod_shop_products` (`tmod_shop_products_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_products_prices_prices1`
    FOREIGN KEY (`tmod_shop_prices_fk` )
    REFERENCES `tmod_shop`.`tmod_shop_prices` (`tmod_shop_prices_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tmod_shop`.`tmod_shop_orders`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `tmod_shop`.`tmod_shop_orders` (
  `tmod_shop_orders_id` INT NOT NULL AUTO_INCREMENT ,
  `hash` VARCHAR(45) NULL ,
  `date` DATETIME NULL ,
  `status` VARCHAR(45) NULL ,
  `email` VARCHAR(45) NULL ,
  `information` TEXT NULL ,
  PRIMARY KEY (`tmod_shop_orders_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tmod_shop`.`tmod_shop_products_orders`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `tmod_shop`.`tmod_shop_products_orders` (
  `tmod_shop_products_orders_id` INT NOT NULL AUTO_INCREMENT ,
  `tmod_shop_products_fk` INT NOT NULL ,
  `tmod_shop_orders_fk` INT NOT NULL ,
  PRIMARY KEY (`tmod_shop_products_orders_id`) ,
  INDEX `fk_products_orders_products1` (`tmod_shop_products_fk` ASC) ,
  INDEX `fk_products_orders_orders1` (`tmod_shop_orders_fk` ASC) ,
  CONSTRAINT `fk_products_orders_products1`
    FOREIGN KEY (`tmod_shop_products_fk` )
    REFERENCES `tmod_shop`.`tmod_shop_products` (`tmod_shop_products_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_products_orders_orders1`
    FOREIGN KEY (`tmod_shop_orders_fk` )
    REFERENCES `tmod_shop`.`tmod_shop_orders` (`tmod_shop_orders_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
