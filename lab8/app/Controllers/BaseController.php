<?php
class BaseController {
protected function json(mixed $data, int $status = 200): never {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}
protected function input(): array {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!is_array($data)) $this->json(['error'=>'Некоректний JSON'], 400);
    return $data;
}
protected function requireAuth(): void {
    if (empty($_SESSION['user_id'])) $this->json(['error'=>'Спочатку увійдіть в обліковий запис'],401);
}
protected function requireAdmin(): void{
    if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
        $this->json(['error' => 'Ця дія доступна лише адміністратору'], 403);
        }
    }
}
