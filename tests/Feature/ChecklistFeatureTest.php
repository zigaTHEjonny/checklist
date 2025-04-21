<?php
namespace Tests\Feature;

use Tests\Support\TestCase;
use CodeIgniter\Test\FeatureTestTrait;
use App\Models\ListItemModel;
use App\Models\UserModel;

class ChecklistFeatureTest extends TestCase
{
    use FeatureTestTrait;

    protected function setUp(): void
    {
        parent::setUp();


        $userModel = new UserModel();
        
        $userModel->where('email', 'testuser@example.com')->delete();
        $userModel->save([
            'name'     => 'Test User',
            'email'    => 'testuser@example.com',
            'password' => 'password123'
        ]);
        $user = $userModel->where('email', 'testuser@example.com')->first();

        $usr = [
            'id'         => $user['user_id'],
            'name'       => $user['name'],
            'email'      => $user['email'],
            'isLoggedIn' => true,
        ];

      
        $this->withSession($usr);
    }

    public function testAddItemCreatesNewTask()
    {
        $model = new ListItemModel();
        $model->withDeleted()
              ->where('task_name', 'Test Task')
              ->delete(null, true);

        $data = ['val' => 'Test Task'];

        $result = $this->withHeaders(['Content-Type' => 'application/json'])
                       ->call('POST', '/add_item', $data);


                                    
        $responseBody = json_decode(strip_tags($result->getBody()), true);

        $this->assertEquals(true, $responseBody['success']);
    
        $item = $model->where('task_name', 'Test Task')->first();
        $this->assertNotNull($item, 'Failed to add new task');
    }

     public function testDeleteItemSoftDeletesRecord()
    {
        $model = new ListItemModel();
        $task_name = 'Task to delete';
        $model->withDeleted()
              ->where('task_name', $task_name)
              ->delete(null, true);

        

        $model->save([
            'list_id'      => 1,
            'task_name'    => $task_name,
            'checked'      => 0,
            'user_created' => 1,
            'user_edited'  => 1,
        ]);
        $id = $model->insertID();

        $data = ['id' => $id];
       
        $result = $this->withHeaders(['Content-Type' => 'application/json'])
        ->call('POST', '/remove_item', $data);

                                     
        $responseBody = json_decode(strip_tags($result->getBody()), true);
        $this->assertEquals(true, $responseBody['success']);

      
        $deleted = $model->withDeleted()->find($id);
        
        $this->assertNotNull($deleted['deleted_at'], 'deleted_at not set');
    }

    public function testExportReturnsChecklistJson()
    {
        $model = new ListItemModel();
        
        $model->withDeleted()
              ->where('task_name', 'Exported Task')
              ->delete(null, true);
        
        $model->where('task_name', 'Test Task')
              ->delete(null);

              
        $model->where('task_name', 'Imported Task')
        ->delete(null);

        $model->save([
            'list_id'      => 1,
            'task_name'    => 'Exported Task',
            'checked'      => 0,
            'user_created' => 1,
            'user_edited'  => 1,
        ]);

        $result = $this->get('/export');
        
        $json_body = strip_tags($result->getBody());
        $responseBody = json_decode($json_body, true);
        $this->assertJson( $json_body, "Data is not JSON");
        

        $data = $responseBody;
        $this->assertNotEmpty($data['unchecked_items'], 'No unchecked items in export');
        $this->assertEquals('Exported Task', $data['unchecked_items'][0]['task_name']);
    }

    public function testImportLoadsTasksFromJson()
    {
        $model = new ListItemModel();
        // Clean up imported
        $model->withDeleted()
              ->where('task_name', 'Imported Task')
              ->delete(null, true);
      

        $data = [
            'data' => [
                'list_id'         => 1,
                'checked_items'   => [],
                'unchecked_items' => [ ['task_name' => 'Imported Task'] ],
            ]
           
        ];
         
        $result = $this->withHeaders(['Content-Type' => 'application/json'])
        ->call('POST', '/import', $data);

                               
        $responseBody = json_decode(strip_tags($result->getBody()), true);
        $this->assertEquals(true, $responseBody['success']);

        $task = $model->where('task_name', 'Imported Task')->first();
        
        $this->assertNotNull($task, 'Imported task not found');
        $this->assertEquals(0, $task['checked']);
    } 



    



/**
     * Extreme: Empty value should be rejected
     */
    public function testAddItemWithEmptyValue()
    {
        $data = ['val' => ''];

        $result = $this->withHeaders(['Content-Type' => 'application/json'])
                       ->call('POST', '/add_item', $data);
               
                                                    
        $responseBody = json_decode(strip_tags($result->getBody()), true);
        $this->assertEquals(false, $responseBody['success']);
    }

    /**
     * Extreme: Very large input should be handled or rejected
     */
    public function testAddItemWithVeryLongValue()
    {
        $longString = str_repeat('x', 5000); // extreme length
        $data = ['val' => $longString];

        $result = $this->withHeaders(['Content-Type' => 'application/json'])
        ->call('POST', '/add_item', $data);

                                     
        $responseBody = json_decode(strip_tags($result->getBody()), true);
        
        $this->assertEquals(true, $responseBody['success']);
    }

    /**
     * Extreme: Non-string input (array) should be rejected
     */
    public function testAddItemWithNonStringValue()
    {
        $data = ['val' => ['array']];

        $result = $this->withHeaders(['Content-Type' => 'application/json'])
        ->call('POST', '/add_item', $data);

                                     
        $responseBody = json_decode(strip_tags($result->getBody()), true);
        $this->assertEquals(false, $responseBody['success']);
    }

    /**
     * Export extreme: No items in list should return empty arrays
     */
    public function testExportWhenNoItems()
    {
        // Ensure no items exist
        $model = new ListItemModel();
        $model->withDeleted()->where('list_id', 1)->delete(null, true);

        $result = $this->get('/export');
                                     
        $responseBody = json_decode(strip_tags($result->getBody()), true);
        
        $this->assertIsArray($responseBody['checked_items']);
        $this->assertIsArray($responseBody['unchecked_items']);
        $this->assertEmpty($responseBody['checked_items']);
        $this->assertEmpty($responseBody['unchecked_items']);
    }

    /**
     * Import extreme: Empty payload should be handled gracefully
     */
    public function testImportWithEmptyPayload()
    {
        $data = [];


        $result = $this->withHeaders(['Content-Type' => 'application/json'])
        ->call('POST', '/import', $data);

        

        // Depending on controller logic, may return 400 or success with no changes
        $responseBody = json_decode(strip_tags($result->getBody()), true);
        $this->assertEquals(false, $responseBody['success']);
    }



    public function testSqlInjectionPrevention()
    {
        $maliciousInput = "' OR 1=1 --";
    
        $result = $this->withHeaders(['Content-Type' => 'application/json'])
                       ->call('POST', '/add_item', ['val' => $maliciousInput]);
    

        $responseBody = json_decode(strip_tags($result->getBody()), true);
        $this->assertEquals(false, $responseBody['success']);
    
        $model = new ListItemModel();
        $item = $model->where('task_name', $maliciousInput)->first();
        $this->assertNull($item, 'SQL Injection input should not be stored.');
    }
   
    public function testXSSPrevention()
    {
        $xssPayload = "<script>alert('XSS')</script>";

        $result = $this->withHeaders(['Content-Type' => 'application/json'])
                    ->call('POST', '/add_item', ['val' => $xssPayload]);

        $responseBody = json_decode(strip_tags($result->getBody()), true);
        $this->assertEquals(false, $responseBody['success']);

        $model = new ListItemModel();
        $item = $model->where('task_name', $xssPayload)->first();
        $this->assertNull($item, 'XSS payload should not be saved.');
    }
 

    public function testDeleteAllItems()
    {
        $model = new ListItemModel();

        
        $model->insert(['task_name' => 'Task A', 'list_id' => 1]);
        $model->insert(['task_name' => 'Task B', 'list_id' => 1]);

        
        $this->assertGreaterThan(0, $model->where('list_id', 1)->countAllResults());

        
        $result = $this->withHeaders(['Content-Type' => 'application/json'])
                        ->call('POST', '/remove_all_items');


        
        $remaining = $model->where('list_id', 1)->findAll();
        $this->assertEmpty($remaining);
    }






}
