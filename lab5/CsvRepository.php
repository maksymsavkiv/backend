<?php
require_once __DIR__ . '/CssCategoryList.php';
require_once __DIR__ . '/CssPropertyList.php';
require_once __DIR__ . '/CssExampleList.php';

class CsvRepository {
    private string $file;
    private array $columns;
    private string $type;

    public function __construct(string $type) {
        $definitions = [
            'categories' => ['Base/CssCategories.csv', ['name']],
            'properties' => ['Base/CssProperty.csv', ['name', 'category', 'description']],
            'examples' => ['Base/CssExemple.csv', ['description', 'code', 'image']],
        ];
        if (!isset($definitions[$type])) throw new InvalidArgumentException('Unknown type');
        $this->type = $type;
        [$path, $this->columns] = $definitions[$type];
        $this->file = __DIR__ . '/' . $path;
    }

    private function read($handle): array {
        rewind($handle);
        $header = fgetcsv($handle, 0, ',', '"', '\\');
        if (!$header) return [];
        $header = array_map('trim', $header);
        $rows = [];
        $next = 0;
        while (($fields = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
            if ($fields === [null]) continue;
            $row = [];
            foreach ($header as $i => $column) $row[$column] = $fields[$i] ?? '';
            // Legacy CSV files did not have IDs. Preserve their original row order.
            $id = isset($row['id']) && ctype_digit((string)$row['id']) && (int)$row['id'] > 0
                ? (int)$row['id'] : $next + 1;
            $next = max($next, $id);
            $row['id'] = $id;
            foreach ($this->columns as $column) $row[$column] = $row[$column] ?? '';
            $rows[] = $row;
        }
        return $rows;
    }

    public function all(): array {
        $handle = fopen($this->file, 'c+');
        if (!$handle) throw new RuntimeException('Cannot open CSV');
        if (!flock($handle, LOCK_SH)) { fclose($handle); throw new RuntimeException('Cannot lock CSV'); }
        try { $rows = $this->read($handle); }
        finally { flock($handle, LOCK_UN); fclose($handle); }
        $list = match ($this->type) {
            'categories' => new CssCategoryList(),
            'properties' => new CssPropertyList(),
            'examples' => new CssExampleList(),
        };
        foreach ($rows as $row) $list->add($row);
        return $list->findAll();
    }

    public function find(int $id): ?object {
        foreach ($this->all() as $item) if ($item->getId() === $id) return $item;
        return null;
    }

    private function change(string $action, ?int $id, array $data = []): void {
        $handle = fopen($this->file, 'c+');
        if (!$handle) throw new RuntimeException('CSV unavailable');
        if (!flock($handle, LOCK_EX)) { fclose($handle); throw new RuntimeException('CSV locked'); }
        try {
            $rows = $this->read($handle);
            $found = false;
            if ($action === 'add') {
                $sequenceFile = $this->file . '.seq';
                $last = is_file($sequenceFile) ? (int)file_get_contents($sequenceFile) : 0;
                $newId = max($last, max(array_column($rows, 'id') ?: [0])) + 1;
                file_put_contents($sequenceFile, (string)$newId, LOCK_EX);
                $rows[] = array_merge(['id' => $newId], $data);
            } else {
                foreach ($rows as $key => $row) {
                    if ((int)$row['id'] !== $id) continue;
                    $found = true;
                    if ($action === 'delete') unset($rows[$key]);
                    else $rows[$key] = array_merge($row, $data);
                    break;
                }
                if (!$found) throw new RuntimeException('Запис не знайдено.');
            }
            rewind($handle);
            if (!ftruncate($handle, 0)) throw new RuntimeException('Cannot truncate CSV');
            $header = array_merge(['id'], $this->columns);
            fputcsv($handle, $header, ',', '"', '\\');
            foreach ($rows as $row) {
                $values = [];
                foreach ($header as $key) $values[] = $row[$key] ?? '';
                fputcsv($handle, $values, ',', '"', '\\');
            }
            fflush($handle);
        } finally { flock($handle, LOCK_UN); fclose($handle); }
    }

    public function add(array $data): void { $this->change('add', null, $data); }
    public function update(int $id, array $data): void { $this->change('update', $id, $data); }
    public function delete(int $id): void { $this->change('delete', $id); }
}
