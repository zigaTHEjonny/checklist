<?php
namespace Tests\Support;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

abstract class TestCase extends CIUnitTestCase
{
    use DatabaseTestTrait;

    /** @var bool */
    protected $refresh = true;
}
