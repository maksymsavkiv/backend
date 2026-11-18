<?php

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/CssCategory.php';
require_once __DIR__ . '/CssProperty.php';
require_once __DIR__ . '/CssExample.php';

class CsvRepository
{
    private PDO $connection;
    private string $table;
    private string $type;
    private array $columns;

    public function __construct(string $type)
    {
        $definitions = [
            'categories' => [
                'table' => 'categories',
                'columns' => ['name' => 'name'],
            ],
            'properties' => [
                'table' => 'properties',
                'columns' => [
                    'name' => 'name',
                    'category' => 'categories_id',
                    'description' => 'description',
                ],
            ],
            'examples' => [
                'table' => 'examples',
                'columns' => [
                    'description' => 'description',
                    'code' => 'code',
                    'image' => 'image',
                ],
            ],
        ];

        if (!isset($definitions[$type])) {
            throw new InvalidArgumentException('Невідомий тип запису.');
        }

        $this->type = $type;
        $this->table = $definitions[$type]['table'];
        $this->columns = $definitions[$type]['columns'];
        $this->connection = database();
    }

    private function makeObject(array $row): object
    {
        if ($this->type === 'categories') {
            return new CssCategory($row);
        }

        if ($this->type === 'properties') {
            $row['category'] = $row['categories_id'];
            return new CssProperty($row);
        }

        $row['image'] = $row['image'] ?? '';
        return new CssExample($row);
    }

    public function all(): array
    {
        $statement = $this->connection->prepare(
            "SELECT * FROM {$this->table} ORDER BY id"
        );
        $statement->execute();

        $items = [];
        foreach ($statement->fetchAll() as $row) {
            $items[] = $this->makeObject($row);
        }

        return $items;
    }

    public function find(int $id): ?object
    {
        $statement = $this->connection->prepare(
            "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1"
        );
        $statement->execute(['id' => $id]);

        $row = $statement->fetch();
        return $row === false ? null : $this->makeObject($row);
    }

    private function values(array $data): array
    {
        $values = [];

        foreach ($this->columns as $field => $column) {
            if (array_key_exists($field, $data)) {
                $values[$column] = $data[$field];
            }
        }

        return $values;
    }

    public function add(array $data): int
    {
        $values = $this->values($data);

        if ($this->type === 'examples' && !isset($values['image'])) {
            $values['image'] = null;
        }

        if ($values === []) {
            throw new InvalidArgumentException('Немає даних для додавання.');
        }

        $fields = array_keys($values);
        $columns = implode(', ', $fields);
        $holders = implode(', ', array_map(fn($field) => ':' . $field, $fields));

        $statement = $this->connection->prepare(
            "INSERT INTO {$this->table} ({$columns}) VALUES ({$holders})"
        );
        $statement->execute($values);

        return (int) $this->connection->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $values = $this->values($data);

        if ($values === []) {
            throw new InvalidArgumentException('Немає даних для оновлення.');
        }

        $assignments = [];
        foreach (array_keys($values) as $column) {
            $assignments[] = "{$column} = :{$column}";
        }

        $statement = $this->connection->prepare(
            "UPDATE {$this->table} SET " . implode(', ', $assignments) . " WHERE id = :record_id"
        );
        $statement->execute($values + ['record_id' => $id]);

        if ($statement->rowCount() === 0 && $this->find($id) === null) {
            throw new RuntimeException('Запис не знайдено.');
        }
    }

    public function delete(int $id): void
    {
        $statement = $this->connection->prepare(
            "DELETE FROM {$this->table} WHERE id = :id"
        );
        $statement->execute(['id' => $id]);

        if ($statement->rowCount() === 0) {
            throw new RuntimeException('Запис не знайдено.');
        }
    }
}
