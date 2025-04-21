<?php
namespace Tests\Feature;

use Tests\Support\TestCase;            // your base class
use CodeIgniter\Test\FeatureTestTrait; // the trait you "use"

class BasicTest extends TestCase
{
    use FeatureTestTrait;

    public function testHomepageIsProtected()
    {
        // If you have an auth filter, you can disable it for the test:
        // $this->withoutFilters();

        $result = $this->get('/');
        $result->assertRedirect();
    }
}
