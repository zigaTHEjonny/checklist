<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\ChecklistModel;
use App\Models\ListItemModel;
use App\Models\CustomModel;
use CodeIgniter\Database\BaseConnection;

class ChecklistCommand extends BaseCommand
{
    protected $group       = 'Checklist';
    protected $name        = 'checklist:cli';
    protected $description = 'Import or export checklist data via CLI';

    public function run(array $params)
    {
        $action = $params[0] ?? null;
        $filePath = $params[1] ?? null;
        $listId = isset($params[2]) ? (int) $params[2] : 1;

        if (!$action || !$filePath) {
            CLI::error("Usage: php spark checklist:cli [import|export] path/to/file.json [list_id]");
            return;
        }

        if ($action === 'export') {
            $this->handleExport($filePath, $listId);
        } elseif ($action === 'import') {
            $this->handleImport($filePath);
        } else {
            CLI::error("Invalid action: $action");
        }
    }

    protected function handleExport(string $filePath) //, int $listId
    {
        $db = db_connect();
        $checklistModel = new ChecklistModel();

        $listId = 1;
        $data = $checklistModel->find($listId);

        $list_itemsModel = new CustomModel($db);

        if ($data) {
            $export = [
                'list_id' => $listId,
                'checked_items' => [],
                'unchecked_items' => [],
            ];

            $unchecked_items = $list_itemsModel->getUncompletedTasks();
            foreach ($unchecked_items as $item) {
                $export['unchecked_items'][] = ['task_name' => $item['task_name']];
            }

            $checked_items = $list_itemsModel->getCompletedTasks();
            foreach ($checked_items as $item) {
                $export['checked_items'][] = ['task_name' => $item['task_name']];
            }

            file_put_contents($filePath, json_encode($export, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            CLI::write("✅ Exported checklist to $filePath", 'green');
        } else {
            CLI::error("Checklist with ID $listId not found.");
        }
    }

    protected function handleImport(string $filePath)
    {
        if (!file_exists($filePath)) {
            CLI::error("File does not exist: $filePath");
            return;
        }

        $json = file_get_contents($filePath);
        $data = json_decode($json, true);

        if (!$data || !isset($data['list_id'])) {
            CLI::error("Invalid JSON structure.");
            return;
        }

        $listId = $data['list_id'];
        $list_items = [];

        foreach ($data['checked_items'] as $item) {
            $list_items[] = [
                'task_name' => $item['task_name'],
                'checked' => 1
            ];
        }

        foreach ($data['unchecked_items'] as $item) {
            $list_items[] = [
                'task_name' => $item['task_name'],
                'checked' => 0
            ];
        }

        $model = new ListItemModel();

        foreach ($list_items as $item) {
            $saveData = [
                'list_id' => $listId,
                'task_name' => $item['task_name'],
                'checked' => $item['checked'],
                'user_created' => 0,
                'user_edited' => 0,
            ];

            $existing = $model->withDeleted()->where('task_name', $item['task_name'])->where('list_id', $listId)->first();

            if ($existing) {
                $saveData['li_id'] = $existing['li_id'];
                $saveData['deleted_at'] = null;
                $model->update($saveData['li_id'], $saveData);
            } else {
                $model->save($saveData);
            }
        }

        CLI::write("✅ Imported checklist items from $filePath into list ID $listId", 'green');
    }
}
