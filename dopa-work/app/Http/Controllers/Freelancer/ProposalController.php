<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Mail\ProposalReceivedMail;
use App\Models\Project;
use App\Models\ProjectProposal;
use App\Models\ProjectMilestone;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ProposalController extends Controller
{
    // Browse open projects
    public function browseProjects(Request $request)
    {
        $query = Project::open()
            ->with(['client', 'category'])
            ->withCount('proposals');

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        if ($request->filled('budget_type')) {
            $query->where('budget_type', $request->budget_type);
        }
        if ($request->filled('location')) {
            $query->where('preferred_location', 'like', '%' . $request->location . '%');
        }
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->q . '%')
                  ->orWhere('description', 'like', '%' . $request->q . '%');
            });
        }

        $projects = $query->latest()->paginate(12)->withQueryString();
        $categories = Category::active()->parentOnly()->get();

        return view('freelancer.projects.browse', compact('projects', 'categories'));
    }

    // View single project + submit proposal form
    public function showProject(Project $project)
    {
        abort_if($project->status !== 'open', 404);

        $project->load(['client', 'category']);

        $myProposal = ProjectProposal::where('project_id', $project->id)
            ->where('freelancer_id', Auth::id())
            ->first();

        return view('freelancer.projects.show', compact('project', 'myProposal'));
    }

    // Submit a proposal
    public function submitProposal(Request $request, Project $project)
    {
        abort_if($project->status !== 'open', 403);

        $request->validate([
            'cover_letter'  => 'required|string|min:50|max:2000',
            'budget'        => 'required|numeric|min:1',
            'delivery_days' => 'required|integer|min:1|max:365',
        ]);

        ProjectProposal::updateOrCreate(
            ['project_id' => $project->id, 'freelancer_id' => Auth::id()],
            [
                'cover_letter'  => $request->cover_letter,
                'budget'        => $request->budget,
                'delivery_days' => $request->delivery_days,
                'status'        => 'pending',
            ]
        );

        // Increment proposals count
        $project->increment('proposals_count');

        // Email client about new proposal
        try {
            $proposal = ProjectProposal::where('project_id', $project->id)
                ->where('freelancer_id', Auth::id())
                ->with(['project.client', 'freelancer'])
                ->first();
            if ($proposal && $project->client?->email) {
                Mail::to($project->client->email)->queue(new ProposalReceivedMail($proposal));
            }
        } catch (\Throwable) {}

        return redirect()->route('freelancer.projects.show', $project)
            ->with('success', app()->getLocale() === 'ar' ? 'تم إرسال عرضك بنجاح ✓' : 'Proposal submitted successfully ✓');
    }

    // Withdraw a proposal
    public function withdrawProposal(ProjectProposal $proposal)
    {
        abort_if($proposal->freelancer_id !== Auth::id(), 403);
        abort_if($proposal->status !== 'pending', 403);

        $proposal->update(['status' => 'withdrawn']);
        $proposal->project->decrement('proposals_count');

        return back()->with('success', app()->getLocale() === 'ar' ? 'تم سحب عرضك' : 'Proposal withdrawn');
    }

    // Active contracts (accepted proposals with milestones)
    public function contracts()
    {
        $contracts = ProjectProposal::where('freelancer_id', Auth::id())
            ->where('status', 'accepted')
            ->with(['project.client', 'milestones' => fn($q) => $q->orderBy('sort_order')])
            ->latest()
            ->paginate(10);

        return view('freelancer.contracts.index', compact('contracts'));
    }

    // My proposals list
    public function myProposals()
    {
        $proposals = ProjectProposal::where('freelancer_id', Auth::id())
            ->with(['project.client', 'project.category'])
            ->latest()
            ->paginate(10);

        return view('freelancer.projects.my_proposals', compact('proposals'));
    }

    // Deliver a milestone
    public function deliverMilestone(Request $request, ProjectMilestone $milestone)
    {
        abort_if($milestone->proposal->freelancer_id !== Auth::id(), 403);
        abort_if(!in_array($milestone->status, ['pending', 'in_progress', 'revision_requested']), 403);

        $request->validate(['delivery_note' => 'required|string|max:2000']);

        $milestone->update([
            'status'        => 'submitted',
            'delivery_note' => $request->delivery_note,
            'delivered_at'  => now(),
        ]);

        return back()->with('success', app()->getLocale() === 'ar' ? 'تم رفع التسليم، في انتظار موافقة العميل ✓' : 'Milestone delivered, awaiting client approval ✓');
    }
}
