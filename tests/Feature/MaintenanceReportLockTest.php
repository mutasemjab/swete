<?php

namespace Tests\Feature;

use App\Http\Controllers\Maintenance\MobileVisitController;
use App\Http\Controllers\Maintenance\ReportController;
use App\Models\MaintenanceReport;
use App\Models\MaintenanceVisit;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class MaintenanceReportLockTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.url' => null,
            'database.connections.sqlite.database' => ':memory:',
            'activitylog.enabled' => false,
        ]);
        DB::purge('sqlite');

        Schema::create('maintenance_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('visit_id');
            $table->string('content_status');
            $table->string('materials_approval_status')->default('none');
            $table->text('problem')->nullable();
            $table->text('solution')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
        Schema::create('maintenance_report_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('report_id');
            $table->string('type');
            $table->json('options')->nullable();
            $table->integer('order')->default(0);
            $table->text('answer')->nullable();
            $table->timestamps();
        });
        Schema::create('maintenance_report_materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('report_id');
            $table->unsignedBigInteger('material_id');
            $table->decimal('quantity', 12, 3);
            $table->timestamps();
        });

        DB::table('maintenance_reports')->insert([
            'id' => 1, 'visit_id' => 7, 'content_status' => 'approved', 'solution' => 'Approved solution',
        ]);
        DB::table('maintenance_report_fields')->insert([
            'id' => 1, 'report_id' => 1, 'type' => 'number', 'answer' => '12',
        ]);
        DB::table('maintenance_report_materials')->insert([
            'id' => 1, 'report_id' => 1, 'material_id' => 3, 'quantity' => 2,
        ]);
    }

    /** @dataProvider approvedMutationProvider */
    public function test_approved_report_rejects_mutations_even_when_visit_is_open(string $operation, array $data): void
    {
        $before = $this->storedReport();
        $report = MaintenanceReport::findOrFail(1);

        try {
            if ($operation === 'destroy') {
                (new ReportController())->destroy($report);
            } else {
                (new MobileVisitController())->{$operation}($this->request($data), $this->openVisit(), $report);
            }
            $this->fail('An approved report must reject this mutation.');
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }

        $this->assertSame($before, $this->storedReport());
    }

    public static function approvedMutationProvider(): array
    {
        return [
            'report text' => ['updateReport', ['solution' => 'Changed solution']],
            'reading' => ['updateAnswer', ['field_id' => 1, 'answer' => '42']],
            'materials' => ['syncMaterialsForReport', ['materials' => []]],
            'deletion' => ['destroy', []],
        ];
    }

    /** @dataProvider editableStatusProvider */
    public function test_existing_mobile_edits_still_work_before_approval(string $status): void
    {
        DB::table('maintenance_reports')->where('id', 1)->update(['content_status' => $status]);
        $report = MaintenanceReport::findOrFail(1);
        $controller = new MobileVisitController();

        $response = $controller->updateReport($this->request(['solution' => 'Corrected solution']), $this->openVisit(), $report);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertDatabaseHas('maintenance_reports', ['id' => 1, 'solution' => 'Corrected solution', 'content_status' => $status]);

        $response = $controller->updateAnswer($this->request(['field_id' => 1, 'answer' => '24']), $this->openVisit(), $report);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertDatabaseHas('maintenance_report_fields', ['id' => 1, 'answer' => '24']);
    }

    public static function editableStatusProvider(): array
    {
        return [['received'], ['engineer']];
    }

    private function request(array $data): Request
    {
        $request = Request::create('/', 'POST', $data);
        $user = new User();
        $user->id = 5;
        $request->setUserResolver(fn () => $user);

        return $request;
    }

    private function openVisit(): MaintenanceVisit
    {
        $visit = new MaintenanceVisit(['created_by' => 5, 'status' => 'open']);
        $visit->id = 7;

        return $visit;
    }

    private function storedReport(): array
    {
        return collect(['maintenance_reports', 'maintenance_report_fields', 'maintenance_report_materials'])
            ->mapWithKeys(fn ($table) => [$table => DB::table($table)->orderBy('id')->get()->map(fn ($row) => (array) $row)->all()])
            ->all();
    }
}
