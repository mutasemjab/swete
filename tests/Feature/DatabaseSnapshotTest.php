<?php

namespace Tests\Feature;

use App\Support\DatabaseSnapshot;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Tests\TestCase;

class DatabaseSnapshotTest extends TestCase
{
    private DatabaseSnapshot $snapshots;

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        $this->snapshots = new DatabaseSnapshot();
        $this->createSchema();
        DB::table('parents')->insert(['id' => 9, 'name' => 'بيانات المصدر']);
        DB::table('children')->insert(['id' => 4, 'parent_id' => 9, 'quantity' => -3]);
        DB::table('users')->insert(['id' => 7, 'email' => 'user@example.test', 'password' => 'existing-hash', 'remember_token' => 'session-token']);
        DB::table('password_resets')->insert(['email' => 'user@example.test', 'token' => 'reset-token']);
    }

    public function test_snapshot_round_trip_preserves_ids_references_and_password_hashes(): void
    {
        $snapshot = $this->snapshots->capture();
        $this->assertArrayNotHasKey('migrations', $snapshot['tables']);
        $this->assertArrayNotHasKey('password_resets', $snapshot['tables']);
        $this->assertNull($snapshot['tables']['users']['rows'][0]['remember_token']);
        $path = tempnam(sys_get_temp_dir(), 'database-seed-');
        try {
            $this->snapshots->write($snapshot, $path);
            $loaded = $this->snapshots->read($path);
            $this->assertSame($snapshot, $loaded);
            $this->emptyDestination();
            $this->assertTrue($this->snapshots->restore($loaded));
            $this->assertTrue($this->snapshots->matches($loaded));
            $this->assertFalse($this->snapshots->restore($loaded));
            $this->assertDatabaseHas('children', ['id' => 4, 'parent_id' => 9, 'quantity' => -3]);
            $this->assertDatabaseHas('users', ['id' => 7, 'password' => 'existing-hash', 'remember_token' => null]);
            $this->assertSame(0, DB::table('password_resets')->count());
            $this->assertSame(1, (int) DB::selectOne('PRAGMA foreign_keys')->foreign_keys);
        } finally {
            unlink($path);
        }
    }

    public function test_existing_destination_records_are_never_overwritten(): void
    {
        $snapshot = $this->snapshots->capture();
        $this->emptyDestination();
        DB::table('parents')->insert(['id' => 9, 'name' => 'Existing destination']);

        try {
            $this->snapshots->restore($snapshot);
            $this->fail('A populated destination must be rejected.');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('already contains data', $e->getMessage());
        }
        $this->assertDatabaseHas('parents', ['id' => 9, 'name' => 'Existing destination']);
        $this->assertSame(0, DB::table('children')->count());
    }

    public function test_failed_restore_rolls_back_every_table_and_restores_foreign_keys(): void
    {
        $snapshot = $this->snapshots->capture();
        $this->emptyDestination();
        // Children are inserted first. A later constraint failure must roll them back too.
        DB::statement("CREATE TRIGGER reject_parent BEFORE INSERT ON parents BEGIN SELECT RAISE(ABORT, 'test failure'); END");
        try {
            $this->snapshots->restore($snapshot);
            $this->fail('The target constraint must abort restoration.');
        } catch (\Illuminate\Database\QueryException $e) {
            $this->assertStringContainsString('test failure', $e->getMessage());
        }
        $this->assertSame(0, DB::table('children')->count());
        $this->assertSame(0, DB::table('parents')->count());
        $this->assertSame(1, (int) DB::selectOne('PRAGMA foreign_keys')->foreign_keys);
    }

    public function test_changed_snapshot_rows_are_rejected_before_inserting_anything(): void
    {
        $snapshot = $this->snapshots->capture();
        $snapshot['tables']['parents']['rows'][0]['name'] = 'Changed after export';
        $this->emptyDestination();
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('checksum mismatch');
        $this->snapshots->restore($snapshot);
    }

    private function emptyDestination(): void
    {
        DB::purge('sqlite');
        $this->createSchema();
    }

    private function createSchema(): void
    {
        Schema::create('migrations', function (Blueprint $table) {
            $table->id();
            $table->string('migration');
        });
        DB::table('migrations')->insert(['migration' => 'target-schema']);
        Schema::create('parents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });
        Schema::create('children', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('parents');
            $table->integer('quantity');
        });
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('remember_token')->nullable();
        });
        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email');
            $table->string('token');
        });
    }
}
