<?php

namespace App\Http\Controllers\Tenders;

use App\Http\Controllers\ModuleController;
use App\Models\PriceAnalysisItem;
use App\Models\Project;
use App\Models\ProjectVariationOrder;
use App\Models\TenderStatus;
use Illuminate\Http\Request;

/**
 * "Projects Under Review" — its own module/queue for two kinds of pending review: a tender just
 * converted into a project for the first time (a `Project` row, status `under_review`), and a
 * Variation Order (a later price quote added to a tender that already has an ACTIVE project —
 * see Tenders\TenderController@convertToProject). Approving/rejecting a Variation Order never
 * touches its parent project's own status. Viewing/editing a project itself stays on
 * Tenders\ProjectController — this controller only owns the review queue and its decisions.
 */
class ProjectReviewController extends ModuleController
{
    protected string $module = 'projects_under_review';

    public function index(Request $request)
    {
        $query = Project::with(['customer', 'tender'])->where('status', 'under_review')->where('is_current', true);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(fn ($q) => $q->where('title', 'like', "%{$search}%")
                ->orWhere('number', 'like', "%{$search}%"));
        }

        $projects = $query->latest()->paginate(20)->withQueryString();

        $variationOrders = ProjectVariationOrder::with(['project', 'priceQuote.tender'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        // Pending X8 items — approved/rejected ones don't belong in a review queue, and an item
        // with no x8 row at all has never been decided on, so it counts as pending too.
        $pendingX8Items = PriceAnalysisItem::query()
            ->whereHas('priceAnalysis', fn ($q) => $q->where('is_current', true)->where('linked_type', 'tender'))
            ->where(fn ($q) => $q->whereDoesntHave('x8')->orWhereHas('x8', fn ($q2) => $q2->where('approval_status', 'pending')))
            ->with(['x8', 'material', 'priceAnalysis'])
            ->latest('id')
            ->get();

        // PriceAnalysis::linked() is convention-based (branches on linked_type), not eager-loadable
        // via with() — set manually, same fix as X8Controller::index().
        $pendingX8Items->each(function ($item) {
            if ($item->priceAnalysis) {
                $item->priceAnalysis->setRelation('linked', $item->priceAnalysis->linked()->first());
            }
        });

        return $this->moduleView('tenders.project-reviews.index', compact('projects', 'variationOrders', 'pendingX8Items'));
    }

    public function approve(Project $project)
    {
        abort_unless($project->status === 'under_review', 404);

        $project->update(['status' => 'active']);

        if ($project->tender) {
            $convertedStatus = TenderStatus::firstOrCreate(
                ['code' => 'converted'],
                ['name' => 'تحول لمشروع', 'name_en' => 'Converted to Project', 'color' => 'emerald', 'status' => true]
            );

            $project->tender->update(['status_id' => $convertedStatus->id]);
        }

        return redirect()->route('projects.show', $project)
            ->with('success', __('tenders.project_approved'));
    }

    public function reject(Project $project)
    {
        abort_unless($project->status === 'under_review', 404);

        $project->delete();

        return redirect()->route('project-reviews.index')
            ->with('success', __('tenders.project_rejected'));
    }

    /**
     * Approving a Variation Order splits the project: the pre-change state freezes as its own
     * read-only "-P{n}" page, and the live row (same id) moves to "-P{n+1}", now driven solely by
     * the newly-approved quote — see Project::archiveCurrentVersion().
     */
    public function approveVariationOrder(Request $request, ProjectVariationOrder $variationOrder)
    {
        abort_unless($variationOrder->status === 'pending', 404);

        $project = $variationOrder->project;
        $project->archiveCurrentVersion();
        $project->update(['price_quote_id' => $variationOrder->price_quote_id]);

        $variationOrder->update([
            'status'      => 'approved',
            'decided_by'  => $request->user()->id,
            'decided_at'  => now(),
        ]);

        return redirect()->route('project-reviews.index')
            ->with('success', __('tenders.vo_approved'));
    }

    public function rejectVariationOrder(Request $request, ProjectVariationOrder $variationOrder)
    {
        abort_unless($variationOrder->status === 'pending', 404);

        $variationOrder->update([
            'status'      => 'rejected',
            'decided_by'  => $request->user()->id,
            'decided_at'  => now(),
        ]);

        return redirect()->route('project-reviews.index')
            ->with('success', __('tenders.vo_rejected'));
    }
}
