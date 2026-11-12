
<?php
/**
 * @var array $errors
 * @var array $categories
 * @var array $properties
 * @var array $examples
 * @var array $categoryNames
 * @var object|null $editing
 * @var string $editingType
 * @var int|false|null $editingId
 */
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSS-довідник</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <h1>Довідник CSS</h1>
    <p>Категорії, CSS-властивості та приклади.</p>
    <?php if (isset($_GET['added'])): ?>
        <p class="notice">Запис успішно додано!</p>
    <?php endif; ?>
    <?php if (isset($_GET['updated'])): ?>
        <p class="notice">Запис оновлено!</p>
    <?php endif; ?>
    <?php if (isset($_GET['deleted'])): ?>
        <p class="notice">Запис видалено!</p>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
        <div class="error">
            <strong>Помилки:</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <section>
        <h2>Додати категорію</h2>
        <form action="index.php" method="post">
            <input type="hidden" name="type" value="categories">
            <label for="category-name">Назва категорії</label>
            <input type="text" id="category-name" name="name" maxlength="150" required>
            <button type="submit">Додати категорію</button>
        </form>
    </section>
    <section>
        <h2>Додати CSS-властивість</h2>
        <form action="index.php" method="post">
            <input type="hidden" name="type" value="properties">
            <label for="property-name">Назва властивості</label>
            <input type="text" id="property-name" name="name" maxlength="150" required>
            <label for="property-category">Категорія</label>
            <select id="property-category" name="category" required>
                <option value="">Оберіть категорію</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= e($category->getId()) ?>">
                        <?= e($category->getName()) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label for="property-description">Опис</label>
            <input id="property-description" name="description" rows="3" required></input>
            <button type="submit">Додати властивість</button>
        </form>
    </section>
    <section>
        <h2>Додати CSS-приклад</h2>
        <form action="index.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="type" value="examples">
            <label for="example-description">Опис</label>
            <input type="text" id="example-description" name="description" required>
            <label for="example-code">CSS-код</label>
            <input id="example-code" name="code" rows="3" required></input>
            <label for="example-image">Зображення</label>
            <input type="file" id="example-image" name="image" accept="image/jpeg,image/png,image/webp"required>
            <button type="submit">Додати приклад</button>
        </form>
    </section>
    <?php if ($editing !== null): ?>
        <section class="edit" id="edit">
            <h2>Редагування запису</h2>
            <form action="index.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="type" value="<?= e($editingType) ?>">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?= e($editingId) ?>">
                <?php if ($editingType === 'categories' || $editingType === 'properties'): ?>
                    <label for="edit-name">Назва</label>
                    <input type="text" id="edit-name" name="name" value="<?= e($editing->getName()) ?>" maxlength="150" required>
                <?php endif; ?>
                <?php if ($editingType === 'properties'): ?>
                    <label for="edit-category">Категорія</label>
                    <select id="edit-category" name="category" required>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= e($category->getId()) ?>"<?= (int) $editing->getCategory() === (int) $category->getId() ? 'selected' : '' ?>>
                                <?= e($category->getName()) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label for="edit-property-description">Опис</label>
                    <textarea id="edit-property-description" name="description" rows="3" required><?= e($editing->getDescription()) ?></textarea>
                <?php endif; ?>
                <?php if ($editingType === 'examples'): ?>
                    <label for="edit-example-description">Опис</label>
                    <input type="text" id="edit-example-description" name="description" value="<?= e($editing->getDescription()) ?>"required>
                    <label for="edit-code">CSS-код</label>
                    <textarea id="edit-code" name="code" rows="3" required><?= e($editing->getCode()) ?></textarea>
                    <label for="edit-image">Замінити зображення</label>
                    <input type="file" id="edit-image" name="image" accept="image/jpeg,image/png,image/webp">
                    <?php if ($editing->getImage()): ?>
                        <img src="<?= e($editing->getImage()) ?>" alt="Поточне зображення">
                    <?php endif; ?>
                <?php endif; ?>
                <div class="actions">
                    <button type="submit">Зберегти зміни</button>
                    <a href="index.php" class="cancel">Скасувати</a>
                </div>
            </form>
        </section>
    <?php endif; ?>
    <section>
        <h2>Категорії</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Назва</th>
                    <th>Дії</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?= e($category->getId()) ?></td>
                        <td><?= e($category->getName()) ?></td>
                        <td><?php showActions('categories', $category->getId());?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
    <section>
        <h2>Властивості CSS</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Назва</th>
                    <th>Категорія</th>
                    <th>Опис</th>
                    <th>Дії</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($properties as $property): ?>
                    <tr>
                        <td><?= e($property->getId()) ?></td>
                        <td><?= e($property->getName()) ?></td>
                        <td><?= e($categoryNames[$property->getCategory()] ?? 'Невідома') ?></td>
                        <td><?= e($property->getDescription()) ?></td>
                        <td><?php showActions('properties', $property->getId());?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
    <section>
        <h2>Приклади</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Опис</th>
                    <th>Код</th>
                    <th>Зображення</th>
                    <th>Дії</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($examples as $example): ?>
                    <tr>
                        <td><?= e($example->getId()) ?></td>
                        <td><?= e($example->getDescription()) ?></td>
                        <td><code><?= e($example->getCode()) ?></code></td>
                        <td>
                            <?php if ($example->getImage()): ?>
                                <img src="<?= e($example->getImage()) ?>"alt="<?= e($example->getDescription()) ?>">
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            showActions('examples', $example->getId());
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</body>
</html>
