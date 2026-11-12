<?php
require_once __DIR__ . '/../CsvRepository.php';

function respond($data, int $status = 200): void{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}
function removeImage(string $image): void{
    $directory = realpath(__DIR__ . '/../uploads');
    $path = realpath(__DIR__ . '/../' . $image);
    if ($directory && $path && str_starts_with($path, $directory) && is_file($path)) {
        unlink($path);
    }
}
function textField(array $data, string $key): string{
    return is_string($data[$key] ?? null) ? trim($data[$key]) : '';
}
function records(string $type): void{
    try {
        $repository = new CsvRepository($type);
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method === 'GET') {
            $items = [];
            foreach ($repository->all() as $item) {
                $items[] = array_merge($item->getAsAssocArray(), ['id' => $item->getId()]);
            }
            respond($items);
        }
        if ($method !== 'POST') {
            respond(['error' => 'Метод не підтримується.'], 405);
        }
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data) || json_last_error() !== JSON_ERROR_NONE) {
            respond(['error' => 'Некоректний JSON.'], 400);
        }
        $action = $data['action'] ?? 'add';
        if (!in_array($action, ['add', 'update', 'delete'], true)) {
            respond(['error' => 'Невідома дія.'], 400);
        }
        $id = filter_var($data['id'] ?? null, FILTER_VALIDATE_INT);
        $old = null;
        if ($action !== 'add') {
            $old = $id > 0 ? $repository->find($id) : null;
            if (!$old) respond(['error' => 'Запис не знайдено.'], 404);
        }
        if ($action === 'delete') {
            if ($type === 'categories') {
                foreach ((new CsvRepository('properties'))->all() as $property) {
                    if ((int) $property->getCategory() === $id) {
                        respond(['error' => 'Спочатку видаліть або перенесіть властивості категорії.'], 409);
                    }
                }
            }
            $repository->delete($id);
            if ($type === 'examples' && $old->getImage()) removeImage($old->getImage());
            respond(['id' => $id]);
        }
        $name = textField($data, 'name');
        $description = textField($data, 'description');
        $code = textField($data, 'code');
        if ($type === 'categories') {
            if ($name === '') respond(['error' => 'Вкажіть назву.'], 422);
            $record = ['name' => $name];
        } elseif ($type === 'properties') {
            $category = filter_var($data['category'] ?? null, FILTER_VALIDATE_INT);
            if ($name === '' || $description === '' || !$category || !(new CsvRepository('categories'))->find($category)) {
                respond(['error' => 'Заповніть назву, опис та оберіть категорію.'], 422);
            }
            $record = ['name' => $name, 'category' => $category, 'description' => $description];
        } else {
            if ($description === '' || $code === '') respond(['error' => 'Заповніть опис і код.'], 422);
            $record = ['description' => $description, 'code' => $code];
        }
        if ($action === 'add') {
            $id = $repository->add($record);
            respond(['id' => $id], 201);
        }
        $repository->update($id, $record);
        respond(['id' => $id]);
    } catch (Throwable $error) {
        error_log($error->getMessage());
        respond(['error' => 'Помилка роботи з даними.'], 500);
    }
}
