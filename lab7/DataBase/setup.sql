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
USE product_ms;

INSERT IGNORE INTO categories (id, name) VALUES
    (1, 'Стилі тексту'),
    (2, 'Блочна деталь'),
    (3, 'Фон та кольори'),
    (8, 'font-size');

INSERT IGNORE INTO properties (id, name, categories_id, description) VALUES
    (1, 'Color', 1, 'Змінює колір тексту елемента.'),
    (2, 'margin', 2, 'Задає зовнішні відступи елемента.'),
    (3, 'font-size', 1, 'Встановлює розмір шрифту.');

INSERT IGNORE INTO examples (id, description, code, image) VALUES
    (1, 'Колір за назвою', 'color: red;', NULL),
    (2, 'HEX-код', 'color: #ff0000;', NULL),
    (3, 'Відступи з усіх боків', 'margin: 15px;', NULL),
    (4, 'Розмір у пікселях', 'font-size: 16px;', NULL),
    (6, 'Відступи нв 10px зверху і знизу, 15px по бокам', 'padding: 10px 15;', 'uploads/41c2330cb0253e3e81c85de014d4afa8.png');

INSERT IGNORE INTO property_examples (property_id, example_id, value) VALUES
    (1, 1, 'red'),
    (1, 2, '#ff0000'),
    (2, 3, '15px'),
    (3, 4, '16px');
