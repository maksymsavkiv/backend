CREATE DATABASE IF NOT EXISTS product_ms
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE product_ms;

CREATE TABLE IF NOT EXISTS categories (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(150) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS properties (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(150) NOT NULL,
    categories_id INT UNSIGNED NOT NULL,
    description TEXT NOT NULL,
    PRIMARY KEY (id),
    INDEX idx_properties_category (categories_id),
    CONSTRAINT fk_properties_category
        FOREIGN KEY (categories_id) REFERENCES categories(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS examples (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    description TEXT NOT NULL,
    code TEXT NOT NULL,
    image VARCHAR(255) NULL DEFAULT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS property_examples (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    property_id INT UNSIGNED NOT NULL,
    example_id INT UNSIGNED NOT NULL,
    value VARCHAR(255) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_property_example (property_id, example_id),
    INDEX idx_example_id (example_id),
    CONSTRAINT fk_property_examples_property
        FOREIGN KEY (property_id) REFERENCES properties(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_property_examples_example
        FOREIGN KEY (example_id) REFERENCES examples(id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;
