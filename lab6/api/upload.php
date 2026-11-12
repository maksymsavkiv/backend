<?php
require_once __DIR__ . '/records.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(['error' => 'Потрібен POST-запит.'], 405);
}
$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
$repository = new CsvRepository('examples');
$path = null;
try {
    $example = $id > 0 ? $repository->find($id) : null;
    if (!$example) respond(['error' => 'Приклад не знайдено.'], 404);
    $file = $_FILES['image'] ?? null;
    if (!$file || $file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
        respond(['error' => 'Не вдалося отримати файл.'], 422);
    }
    if ($file['size'] > 3 * 1024 * 1024) {
        respond(['error' => 'Максимальний розмір — 3 МБ.'], 422);
    }
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!isset($allowed[$mime]) || !getimagesize($file['tmp_name'])) {
        respond(['error' => 'Дозволено лише JPG, PNG та WebP.'], 422);
    }
    $directory = __DIR__ . '/../uploads';
    if (!is_dir($directory) && !mkdir($directory, 0755, true)) {
        throw new RuntimeException('Cannot create uploads');
    }
    $name = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
    $path = $directory . '/' . $name;
    if (!move_uploaded_file($file['tmp_name'], $path)) {
        throw new RuntimeException('Cannot save image');
    }
    $image = 'uploads/' . $name;
    $repository->update($id, ['image' => $image]);
    $path = null;
    if ($example->getImage()) removeImage($example->getImage());
    respond(['id' => $id, 'image' => $image]);
} catch (Throwable $error) {
    if ($path && is_file($path)) unlink($path);
    error_log($error->getMessage());
    respond(['error' => 'Не вдалося зберегти зображення.'], 500);
}
