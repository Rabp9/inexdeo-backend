<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\InfosTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\InfosTable Test Case
 */
class InfosTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\InfosTable
     */
    public $Infos;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.infos'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Infos') ? [] : ['className' => InfosTable::class];
        $this->Infos = TableRegistry::get('Infos', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Infos);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
