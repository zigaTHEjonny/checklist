<?php

namespace App\Controllers;

use App\Models\ChecklistModel;
use App\Models\CustomModel;
use App\Models\ListItemModel;
class Checklist extends BaseController
{
    public function index($id)
    {
        $db = db_connect();
        $checklistModel = new ChecklistModel();
        $data = $checklistModel->find($id);
        
        $list_itemsModel = new CustomModel($db);

        //print_r($list_itemsModel->getCompletedTasks());
        //die();
        //print_r($list_itemsModel->getUncompletedTasks());

        if ($data) {
            $data['title'] = "Checklist";

            $unchecked_items = $list_itemsModel->getUncompletedTasks();
    
            
            $checked_items = $list_itemsModel->getCompletedTasks();
    
            $data['unchecked_items'] = $unchecked_items;
            $data['checked_items'] = $checked_items;
            
        } else {
            // error todo error handliing
        }
        
        return view('templates\checklist', $data);
        
    }

    public function export($id)
    {
        $db = db_connect();
        $checklistModel = new ChecklistModel();
        $data = $checklistModel->find($id);
        
        $list_itemsModel = new CustomModel($db);

   
        if ($data) {
            $export = [
                'list_id' => 1,
                'checked_items' => [],
                'unchecked_items' => [],
            ];
            
            $unchecked_items = $list_itemsModel->getUncompletedTasks();
            foreach ($unchecked_items as $item) {
                $tmp = [];
                $tmp['task_name'] = $item['task_name'];
                $export['unchecked_items'][] = $tmp;
            }

            $checked_items = $list_itemsModel->getCompletedTasks();
            foreach ($checked_items as $item) {
                $tmp = [];
                $tmp['task_name'] = $item['task_name'];
                $export['checked_items'][] = $tmp;
            }

            $jsonData = json_encode($export, JSON_UNESCAPED_UNICODE);
        
        } else {
            // error todo error handliing
        }
        
        return $this->response
        ->setHeader('Content-Type', 'application/json')
        ->setHeader('Content-Disposition', 'attachment; filename="list_'.$id.'-'.date('Y-m-d_H:i').'.json"')
        ->setBody($jsonData);        
    }

    public function import($id)
    {
        
        if ($this->request->getPost('data')!=null) {
            $data = $this->request->getPost('data');
        } else {
            $json = $this->request->getBody(); 
        
            $data = json_decode($json, true);
        }
        
        if (!empty($data) && $data['list_id'] > 0) {

            $return = [
                'success' => true,
                'message' => 'Imported succesfully.',
            ];            
    
    
            $list_id = $data['list_id'];
    
            $list_items = [];
    
            foreach ($data['checked_items'] as $item) {
                $tmp = [];
                $tmp['task_name'] = $item['task_name'];
                $tmp['checked'] = 1;
                $list_items[] = $tmp;
            }
    
            
            foreach ($data['unchecked_items'] as $item) {
                $tmp = [];
                $tmp['task_name'] = $item['task_name'];
                $tmp['checked'] = 0;
                $list_items[] = $tmp;
            }
    
    
            $model = new ListItemModel();
            foreach ($list_items as $item) {
                
                $data = [
                    'list_id' => '1',
                    'task_name' => $item['task_name'],
                    'checked' => $item['checked'],
                    'user_created' => session()->get('id'),
                    'user_edited' => session()->get('id'),
                ];
    
                $list_item = $model->withDeleted()->where('task_name', $item['task_name'])->where('list_id', 1)->first();
                if ($list_item) {
                    
                    
                    $data['li_id'] = $list_item['li_id'];
                    $data['deleted_at'] = NULL;
    
                    $model->update($data['li_id'], $data);
                } else {
                    $model->save($data);
                }
            }
        } else {
            $return = [
                'success' => false,
                'message' => 'Imported failed, no list id provided.',
            ];            
    
        }



        return $this->response->setJSON($return);

    }


    public function add()
    {
        $return = [
            'success' => false,
            'message' => 'Error adding the item.',
            'edited' => 0,
        ];


        $val = $this->request->getPost('val');
        $err = 0;
        if (!empty($val) && gettype($val) == 'string') {
        
            $val = strip_tags($val);
            if (strlen($val) > 255) {
                $val = substr($val, 0, 255);
            }

            if (preg_match('/[\'"=<>]/', $val)) {
                $err++;
            }
        } else {
            $err++;
        }

        if ($err>0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid input.'
            ]);
        }
         
       
 

         
        if (!empty($val)) {
            $return = [
                'success' => true,
                'message' => 'Item added succesfully.',
            ];          

            $model = new ListItemModel();
            $data = [
                'list_id' => '1',
                'task_name' => $val,
                'checked' => 0,
                'user_created' => session()->get('id'),
                'user_edited' => session()->get('id'),
            ];

            $list_item = $model->withDeleted()->where('task_name', $val)->where('list_id', 1)->first();
            if ($list_item) {
                
                $data['li_id'] = $list_item['li_id'];
                $data['deleted_at'] = NULL;

                $model->update($data['li_id'], $data);

                
            if ($list_item && empty($list_item['deleted_at'])) {
                $return['edited'] = $data['li_id'];
            }
            } else {
                $model->save($data);
                $newId = $model->insertID();
                $return['id'] = $newId;
            }
            
            
        }

        return $this->response->setJSON($return);
    }

    public function updateState()
    {
        $return = [
            'success' => false,
            'message' => 'Error pdating checkbox.',
        ];

        $data = $this->request->getJSON();
        $id = $data->id;
        if (!empty($id)) {
            
            $checked = $data->checked;
            
            $model = new ListItemModel();
            $list_item = $model->find($id);
            
            $task_name = $list_item['task_name'];
           

            if (!empty($data->name)) {
                $task_name = $data->name;
            }
        

            $data = [
                'li_id' => $id,
                'task_name' => $task_name,
                'checked' => $checked,
                'user_edited' => session()->get('id'),
            ];
     
            $model->save($data);
            $return = [
                'success' => true,
                'message' => 'Item updated successfully.',
                'received' => ['id' => $id, 'checked' => $checked]
            ];               
        }

        return $this->response->setJSON($return);
    }


    public function delete() {
        
        $id = $this->request->getPost('id');
        if (!empty($id)) {
            $model = new ListItemModel();
            $list_item = $model->find($id);
            
            if($list_item) {
                
                $model->delete($id);
                //return reditect()->to('/');
            }
        }
        $return = [
            'success' => true,
            'message' => 'Item deleted successfully.',
            'received' => ['id' => $id]
        ];     
    
        return $this->response->setJSON($return);
    }

    public function delete_all($id) {
        
        
        
        $model = new \App\Models\ListItemModel();
        $model->where('list_id', 1)->delete();

        
        $return = [
            'success' => true,
            'message' => 'Checkbox deleted successfully.',
            'received' => ['id' => $id]
        ];     
    
    }

    

}
