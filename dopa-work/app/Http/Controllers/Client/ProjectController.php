<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Project;
use App\Models\ProjectProposal;
use App\Models\ProjectMilestone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::where('client_id', Auth::id())
            ->withCount('proposals')
            ->with('category')
            ->latest()
            ->paginate(10);

        return view('client.projects.index', compact('projects'));
    }

    public function create()
    {
        $categories = Category::active()->parentOnly()->with('children')->get();
        return view('client.projects.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'              => 'required|string|max:150',
            'description'        => 'required|string|min:50',
            'category_id'        => 'nullable|exists:categories,id',
            'budget_type'        => 'required|in:fixed,hourly',
            'budget_min'         => 'nullable|numeric|min:1',
            'budget_max'         => 'nullable|numeric|min:1|gte:budget_min',
            'deadline'           => 'nullable|date|after:today',
            'required_skills'    => 'nullable|string',
            'preferred_location' => 'nullable|string|max:100',
            'attachments.*'      => 'nullable|file|max:10240',
        ]);

        // Handle attachments
        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $attachmentPaths[] = $file->store('projects/attachments', 'public');
            }
        }

        // Parse skills
        $skills = [];
        if ($request->filled('required_skills')) {
            $skills = array_filter(array_map('trim', explode(',', $request->required_skills)));
        }

        $project = Project::create([
            'client_id'          => Auth::id(),
            'category_id'        => $request->category_id,
            'title'              => $request->title,
            'description'        => $request->description,
            'budget_type'        => $request->budget_type,
            'budget_min'         => $request->budget_min,
            'budget_max'         => $request->budget_max,
            'deadline'           => $request->deadline,
            'required_skills'    => array_values($skills),
            'attachments'        => $attachmentPaths,
            'preferred_location' => $request->preferred_location,
            'status'             => 'open',
        ]);

        return redirect()->route('client.projects.show', $project)
            ->with('success', app()->getLocale() === 'ar' ? 'تم نشر مشروعك بنجاح! ✓' : 'Project posted successfully! ✓');
    }

    public function show(Project $project)
    {
        abort_if($project->client_id !== Auth::id(), 403);

        $project->load(['category', 'proposals.freelancer.freelancerProfile', 'milestones']);

        return view('client.projects.show', compact('project'));
    }

    public function acceptProposal(Project $project, ProjectProposal $proposal)
    {
        abort_if($project->client_id !== Auth::id(), 403);
        abort_if($proposal->project_id !== $project->id, 403);
        abort_if($project->status !== 'open', 403);

        // Reject all other proposals
        $project->proposals()->where('id', '!=', $proposal->id)->update(['status' => 'rejected']);

        $proposal->update(['status' => 'accepted']);
        $project->update(['status' => 'in_progress']);

        // Create default milestone if none exist
        if ($project->milestones()->count() === 0) {
            ProjectMilestone::create([
                'project_id'  => $project->id,
                'proposal_id' => $proposal->id,
                'title'       => app()->getLocale() === 'ar' ? 'التسليم النهائي' : 'Final Delivery',
                'amount'      => $proposal->budget,
                'status'      => 'pending',
                'sort_order'  => 1,
            ]);
        }

        // Open conversation
        $conversation = \App\Models\Conversation::firstOrCreate([
            'client_id'     => Auth::id(),
            'freelancer_id' => $proposal->freelancer_id,
        ], ['last_message_at' => now()]);

        return redirect()->route('client.projects.show', $project)
            ->with('success', app()->getLocale() === 'ar' ? 'تم قبول العرض وبدأ المشروع ✓' : 'Proposal accepted! Project started ✓');
    }

    public function rejectProposal(Project $project, ProjectProposal $proposal)
    {
        abort_if($project->client_id !== Auth::id(), 403);
        abort_if($proposal->project_id !== $project->id, 403);

        $proposal->update(['status' => 'rejected']);

        return back()->with('success', app()->getLocale() === 'ar' ? 'تم رفض العرض' : 'Proposal rejected');
    }

    public function approveMilestone(Project $project, ProjectMilestone $milestone)
    {
        abort_if($project->client_id !== Auth::id(), 403);
        abort_if($milestone->project_id !== $project->id, 403);
        abort_if($milestone->status !== 'submitted', 403);

        $milestone->update([
            'status'      => 'approved',
            'approved_at' => now(),
        ]);

        // Check if all milestones approved → complete project
        $pending = $project->milestones()->whereNotIn('status', ['approved'])->count();
        if ($pending === 0) {
            $project->update(['status' => 'completed']);
        }

        return back()->with('success', app()->getLocale() === 'ar' ? 'تم قبول التسليم ✓' : 'Milestone approved ✓');
    }

    public function requestRevision(Project $project, ProjectMilestone $milestone, Request $request)
    {
        abort_if($project->client_id !== Auth::id(), 403);
        abort_if($milestone->project_id !== $project->id, 403);
        abort_if($milestone->status !== 'submitted', 403);

        $request->validate(['revision_note' => 'required|string|max:1000']);

        $milestone->update([
            'status'        => 'revision_requested',
            'revision_note' => $request->revision_note,
        ]);

        return back()->with('success', app()->getLocale() === 'ar' ? 'تم إرسال طلب التعديل' : 'Revision requested');
    }

    public function cancel(Project $project)
    {
        abort_if($project->client_id !== Auth::id(), 403);
        abort_if(!in_array($project->status, ['open']), 403);

        $project->update(['status' => 'cancelled']);

        return redirect()->route('client.projects.index')
            ->with('success', app()->getLocale() === 'ar' ? 'تم إلغاء المشروع' : 'Project cancelled');
    }
}
