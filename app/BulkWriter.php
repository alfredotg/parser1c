<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BulkWriter
{
    public $on_save;
    protected int $chunk_size;
    protected Model $model;
    protected array $rows = [];

    public function __construct(int $chunk_size, Model $model)
    {
        $this->model = $model;
        $this->chunk_size = $chunk_size;
    }
    
    public function size(): int
    {
        return count($this->rows);
    }

    public function add(array $row): void
    {
        $this->rows[] = $row;
        if (count($this->rows) >= $this->chunk_size) {
            $this->save();
        }
    }

    public function save(): void
    {
        if (count($this->rows) == 0) {
            return;
        }
        list($sql, $bindigs) = $this->prepare();
        $this->execute($sql, $bindigs);
        if ($this->on_save !== null) {
            $on_save = $this->on_save;
            $on_save(count($this->rows));
        }
        $this->rows = [];
    }

    protected function execute(string $sql, array $bindigs): void
    {
        DB::statement($sql, $bindigs);
    }

    public function prepare(): array
    {
        $bindigs = [];
        $fields = false;
        foreach ($this->rows as $row) {
            if (!$fields) {
                $fields = array_keys($row);
            }
            $bindigs = array_merge($bindigs, array_values($row));
        }
        $sql = $this->prepareSql($fields);
        return [$sql, $bindigs];
    }

    protected function prepareSql(array $fields): string
    {
        $sql = 'INSERT INTO `'.$this->model->getTable().'` (`'.implode('`, `', $fields).'`) values ';
                                        
        $questions = array_pad([], count($fields), '?');
        $placements = '('.implode(', ', $questions).')';

        $sql .= implode(',', array_pad([], $this->size(), $placements));

        $updates = [];
        foreach ($fields as $field) {
            if ($field != 'id') {
                $updates[] = '`'.$field.'`=VALUES(`'.$field.'`)';
            }
        }
        if ($updates) {
            $sql .= ' ON DUPLICATE KEY UPDATE ';
            $sql .= implode(', ', $updates);
        }
        return $sql;
    }
}
