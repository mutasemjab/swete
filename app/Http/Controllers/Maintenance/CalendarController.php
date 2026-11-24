<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\ModuleController;
use App\Models\Customer;
use App\Models\MaintenanceContractScheduledVisit;
use Carbon\Carbon;
use Illuminate\Http\Request;

/** Month/week/day/list views of every contract-scheduled visit (manual + auto-generated alike). */
class CalendarController extends ModuleController
{
    protected string $module = 'maintenance';

    public function index(Request $request)
    {
        $view = in_array($request->input('view'), ['month', 'week', 'day', 'list'], true) ? $request->input('view') : 'month';
        $anchor = $request->filled('date') ? Carbon::parse($request->input('date')) : now();

        [$rangeStart, $rangeEnd] = match ($view) {
            'month' => $this->monthRange($anchor),
            'week'  => $this->weekRange($anchor),
            'day'   => [$anchor->copy()->startOfDay(), $anchor->copy()->endOfDay()],
            'list'  => [$anchor->copy()->startOfDay(), $anchor->copy()->addMonths(3)->endOfDay()],
        };

        // overdue_only means "show me what's late regardless of which window I'm looking at" —
        // never silently hidden just because the current view's window only looks forward.
        $queryStart = $request->boolean('overdue_only') ? $rangeStart->copy()->subYears(2) : $rangeStart;

        $query = MaintenanceContractScheduledVisit::with(['contract.customer', 'unit'])
            ->whereBetween('scheduled_date', [$queryStart->toDateString(), $rangeEnd->toDateString()]);

        if ($request->filled('customer_id')) {
            $query->whereHas('contract', fn ($q) => $q->where('customer_id', $request->input('customer_id')));
        }
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }
        if ($request->boolean('overdue_only')) {
            $query->where('scheduled_date', '<', now()->toDateString())->whereNull('notified_at');
        }

        $visits = $query->orderBy('scheduled_date')->get();
        $byDate = $visits->groupBy(fn ($v) => $v->scheduled_date->toDateString());

        $weeks = $view === 'month' ? $this->weeksGrid($rangeStart, $rangeEnd) : null;
        $days = $view === 'week' ? $this->daysRange($rangeStart, $rangeEnd) : null;

        $customers = Customer::where('status', true)->orderBy('name')->get();

        return $this->moduleView('maintenance.calendar.index', [
            'view' => $view, 'anchor' => $anchor, 'rangeStart' => $rangeStart, 'rangeEnd' => $rangeEnd,
            'byDate' => $byDate, 'visits' => $visits, 'weeks' => $weeks, 'days' => $days, 'customers' => $customers,
        ]);
    }

    /** Full weeks grid (Sunday-start) covering the whole month, including leading/trailing days. */
    private function monthRange(Carbon $anchor): array
    {
        $monthStart = $anchor->copy()->startOfMonth();
        $monthEnd = $anchor->copy()->endOfMonth();
        $gridStart = $monthStart->copy()->startOfWeek(Carbon::SUNDAY);
        // Carbon's endOfWeek($day) means "the week THAT ENDS ON $day", which is a different week
        // definition than startOfWeek($day) ("the week that STARTS ON $day") — passing the same
        // constant to both does NOT give a consistent 7-day week. Compute the end by walking
        // forward in whole-week steps from the start instead, which is unambiguous.
        $gridEnd = $gridStart->copy();
        while ($gridEnd->lessThan($monthEnd)) {
            $gridEnd->addDays(7);
        }
        $gridEnd->subDay();

        return [$gridStart, $gridEnd];
    }

    private function weekRange(Carbon $anchor): array
    {
        $start = $anchor->copy()->startOfWeek(Carbon::SUNDAY);

        return [$start, $start->copy()->addDays(6)];
    }

    private function weeksGrid(Carbon $start, Carbon $end): array
    {
        $weeks = [];
        $cursor = $start->copy();
        while ($cursor->lessThanOrEqualTo($end)) {
            $weeks[] = $this->daysRange($cursor->copy(), $cursor->copy()->addDays(6));
            $cursor->addDays(7);
        }

        return $weeks;
    }

    private function daysRange(Carbon $start, Carbon $end): array
    {
        $days = [];
        $cursor = $start->copy();
        while ($cursor->lessThanOrEqualTo($end)) {
            $days[] = $cursor->copy();
            $cursor->addDay();
        }

        return $days;
    }
}
