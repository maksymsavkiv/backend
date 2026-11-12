<?php
require_once __DIR__ . '/CsvRepository.php';
function e($value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

$types = ['categories', 'properties', 'examples'];
$errors = [];
$selected = $_POST['type'] ?? '';
$action = $_POST['action'] ?? 'add';

$recordId = filter_var($_POST['id'] ?? null,FILTER_VALIDATE_INT);
$editingType = $_GET['edit'] ?? '';
$editingId = filter_var($_GET['id'] ?? null,FILTER_VALIDATE_INT);

$editing = null;
if ($editingId && in_array($editingType, $types, true)) {
    $repository = new CsvRepository($editingType);
    $editing = $repository->find($editingId);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!in_array($selected, $types, true)) {
        $errors[] = 'Невідомий тип запису.';
    }
    if (!in_array($action, ['add', 'update', 'delete'], true)) {
        $errors[] = 'Невідома дія.';
    }
    if (empty($errors) && $action !== 'add') {
        $repository = new CsvRepository($selected);
        if (!$recordId || !$repository->find($recordId)) {
            $errors[] = 'Запис не знайдено.';
        }
    }
    if ($action === 'delete' && empty($errors)) {
        try {
            $repository = new CsvRepository($selected);
            if ($selected === 'categories') {
                $propertyRepository = new CsvRepository('properties');
                $properties = $propertyRepository->all();
                foreach ($properties as $property) {
                    if ((int) $property->getCategory() === $recordId) {
                        throw new RuntimeException(
                            'Спочатку видаліть або перенесіть властивості цієї категорії.'
                        );
                    }
                }
            }
            $oldRecord = $repository->find($recordId);
            $repository->delete($recordId);
            if ($selected === 'examples' && $oldRecord->getImage()) {
                $uploadsDirectory = realpath(__DIR__ . '/uploads');
                $imagePath = realpath(
                    __DIR__ . '/' . $oldRecord->getImage()
                );
                if ($uploadsDirectory && $imagePath && str_starts_with($imagePath, $uploadsDirectory . DIRECTORY_SEPARATOR)
                ) {
                    unlink($imagePath);
                }
            }
            header('Location: index.php?deleted=1', true, 303);
            exit;
        } catch (Throwable $error) {
            $errors[] = $error->getMessage();
        }
    }
    if ($action !== 'delete' && empty($errors)) {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $code = trim($_POST['code'] ?? '');
        $category = filter_var($_POST['category'] ?? null,FILTER_VALIDATE_INT);
        $data = [];
        if ($selected === 'categories') {
            if ($name === '') {
                $errors[] = 'Вкажіть назву категорії.';
            }
            $data = ['name' => $name];
        }
        if ($selected === 'properties') {
            if ($name === '' || $description === '') {
                $errors[] = 'Заповніть назву й опис властивості.';
            }
            $categoryRepository = new CsvRepository('categories');
            $allCategories = $categoryRepository->all();
            $categoryExists = false;
            foreach ($allCategories as $item) {
                if ((int) $item->getId() === (int) $category) {
                    $categoryExists = true;
                    break;
                }
            }
            if (!$category || !$categoryExists) {
                $errors[] = 'Виберіть категорію.';
            }
            $data = [
                'name' => $name,
                'category' => (string) $category,
                'description' => $description
            ];
        }
        $uploadedFile = null;
        $mime = null;
        $allowedTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp'
        ];
        if ($selected === 'examples') {
            if ($description === '' || $code === '') {
                $errors[] = 'Заповніть опис і CSS-код.';
            }
            $data = [
                'description' => $description,
                'code' => $code,
                'image' => ''
            ];
            if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
                if ($action === 'add') {
                    $errors[] = 'Додайте зображення прикладу.';
                }
            } elseif ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                $errors[] = 'Помилка завантаження файлу.';
            } elseif ($_FILES['image']['size'] > 3 * 1024 * 1024) {
                $errors[] = 'Максимальний розмір файлу — 3 МБ.';
            } else {
                $uploadedFile = $_FILES['image']['tmp_name'];
                $fileInfo = new finfo(FILEINFO_MIME_TYPE);
                $mime = $fileInfo->file($uploadedFile);
                if (!isset($allowedTypes[$mime]) || getimagesize($uploadedFile) === false) {
                    $errors[] = 'Дозволено лише JPG, PNG або WebP.';
                }
            }
        }
        if (empty($errors)) {
            $uploadedPath = null;
            $previousImage = '';
            try {
                if ($selected === 'examples' && $action === 'update') {
                    $exampleRepository = new CsvRepository('examples');
                    $oldExample = $exampleRepository->find($recordId);
                    $previousImage = $oldExample->getImage();
                    $data['image'] = $previousImage;
                }
                if ($selected === 'examples' && $uploadedFile !== null) {
                    $directory = __DIR__ . '/uploads';
                    if (!is_dir($directory)) {
                        if (!mkdir($directory, 0755, true)) {
                            throw new RuntimeException(
                                'Не вдалося створити папку uploads.'
                            );
                        }
                    }
                    $fileName = bin2hex(random_bytes(16));
                    $fileName .= '.' . $allowedTypes[$mime];
                    $uploadedPath = $directory . '/' . $fileName;
                    if (!move_uploaded_file($uploadedFile, $uploadedPath)) {
                        throw new RuntimeException(
                            'Не вдалося зберегти зображення.'
                        );
                    }
                    $data['image'] = 'uploads/' . $fileName;
                }
                $repository = new CsvRepository($selected);
                if ($action === 'update') {
                    $repository->update($recordId, $data);
                } else {
                    $repository->add($data);
                }
                if ($uploadedPath && $previousImage) {
                    $uploadsDirectory = realpath(__DIR__ . '/uploads');
                    $oldPath = realpath(
                        __DIR__ . '/' . $previousImage
                    );
                    if ($uploadsDirectory && $oldPath && str_starts_with($oldPath, $uploadsDirectory . DIRECTORY_SEPARATOR)
                    ) {
                        unlink($oldPath);
                    }
                }
                if ($action === 'update') {
                    header('Location: index.php?updated=1', true, 303);
                } else {
                    header('Location: index.php?added=1', true, 303);
                }
                exit;
            } catch (Throwable $error) {
                if ($uploadedPath && is_file($uploadedPath)) {
                    unlink($uploadedPath);
                }
                $errors[] = 'Не вдалося зберегти запис.';
            }
        }
    }
}

$categoryRepository = new CsvRepository('categories');
$propertyRepository = new CsvRepository('properties');
$exampleRepository = new CsvRepository('examples');

$categories = $categoryRepository->all();
$properties = $propertyRepository->all();
$examples = $exampleRepository->all();

$categoryNames = [];
foreach ($categories as $category) {
    $categoryNames[$category->getId()] = $category->getName();
}
require_once __DIR__ . '/views/index.view.php';
function showActions(string $type, int $id): void
{
    ?>
    <div class="actions">
        <a href="index.php?edit=<?= e($type) ?>&amp;id=<?= $id ?>#edit">
            Редагувати
        </a>
        <form
            action="index.php"
            method="post"
            onsubmit="return confirm('Видалити цей запис?');"
        >
            <input
                type="hidden"
                name="type"
                value="<?= e($type) ?>"
            >
            <input
                type="hidden"
                name="action"
                value="delete"
            >
            <input
                type="hidden"
                name="id"
                value="<?= $id ?>"
            >
            <button type="submit">
                Видалити
            </button>
        </form>
    </div>
    <?php
}

