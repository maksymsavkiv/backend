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
