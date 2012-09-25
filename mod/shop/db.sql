SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';


-- -----------------------------------------------------
-- Table `tmod_shop`.`tmod_shop_categories`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `tmod_shop`.`tmod_shop_categories` (
  `categories_id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  `categories_fk` INT NOT NULL ,
  PRIMARY KEY (`categories_id`) ,
  INDEX `fk_categories_categories1` (`categories_fk` ASC) ,
  CONSTRAINT `fk_categories_categories1`
    FOREIGN KEY (`categories_fk` )
    REFERENCES `tmod_shop`.`tmod_shop_categories` (`categories_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tmod_shop`.`tmod_shop_fields`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `tmod_shop`.`tmod_shop_fields` (
  `fields_id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  `type` VARCHAR(45) NULL ,
  `count` TINYINT NULL DEFAULT 1 ,
  `categories_fk` INT NOT NULL ,
  PRIMARY KEY (`fields_id`) ,
  INDEX `fk_fields_product_types` (`categories_fk` ASC) ,
  CONSTRAINT `fk_fields_product_types`
    FOREIGN KEY (`categories_fk` )
    REFERENCES `tmod_shop`.`tmod_shop_categories` (`categories_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tmod_shop`.`tmod_shop_values`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `tmod_shop`.`tmod_shop_values` (
  `values_id` INT NOT NULL AUTO_INCREMENT ,
  `val_datetime` DATETIME NULL ,
  `val_int` INT NULL ,
  `val_double` DOUBLE NULL ,
  `val_varchar` VARCHAR(255) NULL ,
  `val_text` TEXT NULL ,
  `val_file` VARCHAR(255) NULL ,
  `fields_fk` INT NOT NULL ,
  PRIMARY KEY (`values_id`) ,
  INDEX `fk_values_fields1` (`fields_fk` ASC) ,
  CONSTRAINT `fk_values_fields1`
    FOREIGN KEY (`fields_fk` )
    REFERENCES `tmod_shop`.`tmod_shop_fields` (`fields_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tmod_shop`.`tmod_shop_products`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `tmod_shop`.`tmod_shop_products` (
  `products_id` INT NOT NULL AUTO_INCREMENT ,
  `count` INT NULL ,
  `publish` TINYINT NULL ,
  `categories_fk` INT NOT NULL ,
  PRIMARY KEY (`products_id`) ,
  INDEX `fk_products_categories1` (`categories_fk` ASC) ,
  CONSTRAINT `fk_products_categories1`
    FOREIGN KEY (`categories_fk` )
    REFERENCES `tmod_shop`.`tmod_shop_categories` (`categories_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tmod_shop`.`tmod_shop_products_values`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `tmod_shop`.`tmod_shop_products_values` (
  `products_values_id` INT NOT NULL AUTO_INCREMENT ,
  `products_fk` INT NOT NULL ,
  `values_fk` INT NOT NULL ,
  PRIMARY KEY (`products_values_id`) ,
  INDEX `fk_products_values_values1` (`values_fk` ASC) ,
  INDEX `fk_products_values_products1` (`products_fk` ASC) ,
  CONSTRAINT `fk_products_values_values1`
    FOREIGN KEY (`values_fk` )
    REFERENCES `tmod_shop`.`tmod_shop_values` (`values_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_products_values_products1`
    FOREIGN KEY (`products_fk` )
    REFERENCES `tmod_shop`.`tmod_shop_products` (`products_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tmod_shop`.`tmod_shop_prices`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `tmod_shop`.`tmod_shop_prices` (
  `prices_id` INT NOT NULL AUTO_INCREMENT ,
  `price` INT NULL ,
  `name` VARCHAR(255) NULL ,
  `display` VARCHAR(45) NULL ,
  PRIMARY KEY (`prices_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tmod_shop`.`tmod_shop_products_prices`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `tmod_shop`.`tmod_shop_products_prices` (
  `products_prices_id` INT NOT NULL AUTO_INCREMENT ,
  `products_fk` INT NOT NULL ,
  `prices_fk` INT NOT NULL ,
  PRIMARY KEY (`products_prices_id`) ,
  INDEX `fk_products_prices_products1` (`products_fk` ASC) ,
  INDEX `fk_products_prices_prices1` (`prices_fk` ASC) ,
  CONSTRAINT `fk_products_prices_products1`
    FOREIGN KEY (`products_fk` )
    REFERENCES `tmod_shop`.`tmod_shop_products` (`products_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_products_prices_prices1`
    FOREIGN KEY (`prices_fk` )
    REFERENCES `tmod_shop`.`tmod_shop_prices` (`prices_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tmod_shop`.`tmod_shop_orders`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `tmod_shop`.`tmod_shop_orders` (
  `orders_id` INT NOT NULL AUTO_INCREMENT ,
  `hash` VARCHAR(45) NULL ,
  `date` DATETIME NULL ,
  `status` VARCHAR(45) NULL ,
  `email` VARCHAR(45) NULL ,
  `information` TEXT NULL ,
  PRIMARY KEY (`orders_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tmod_shop`.`tmod_shop_products_orders`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `tmod_shop`.`tmod_shop_products_orders` (
  `products_orders_id` INT NOT NULL AUTO_INCREMENT ,
  `products_fk` INT NOT NULL ,
  `orders_fk` INT NOT NULL ,
  PRIMARY KEY (`products_orders_id`) ,
  INDEX `fk_products_orders_products1` (`products_fk` ASC) ,
  INDEX `fk_products_orders_orders1` (`orders_fk` ASC) ,
  CONSTRAINT `fk_products_orders_products1`
    FOREIGN KEY (`products_fk` )
    REFERENCES `tmod_shop`.`tmod_shop_products` (`products_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_products_orders_orders1`
    FOREIGN KEY (`orders_fk` )
    REFERENCES `tmod_shop`.`tmod_shop_orders` (`orders_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
