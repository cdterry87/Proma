<?php

namespace App\Livewire\Issues;

use App\Models\Issue;
use Livewire\Component;
use App\Traits\WithModal;
use App\Models\IssueTask;
use Livewire\Attributes\On;

class IssuesTasksForm extends Component
{
    use WithModal;

    public $issue_id, $issue_name;
    public $task_id, $title, $description, $completed_date;

    #[On('getIssue')]
    public function getIssue($id)
    {
        $issue = Issue::find($id);
        if ($issue) {
            $this->issue_id = $issue->id;
            $this->issue_name = $issue->name;
        }
    }

    public function render()
    {
        return view('livewire.issues.issues-tasks-form');
    }

    public function saveTask()
    {
        $this->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'completed_date' => 'nullable|date',
        ]);

        IssueTask::updateOrCreate([
            'id' => $this->task_id,
            'issue_id' => $this->issue_id,
        ], [
            'title' => $this->title,
            'description' => $this->description,
            'completed_date' => $this->completed_date,
        ]);

        $this->dispatch('refreshData');

        session()->flash('success', 'Task saved successfully.');
    }

    #[On('editTask')]
    public function editTask($id)
    {
        $issueTask = IssueTask::query()
            ->with('issue')
            ->find($id);
        if ($issueTask) {
            $this->task_id = $issueTask->id;
            $this->title = $issueTask->title;
            $this->description = $issueTask->description;
            $this->completed_date = $issueTask->completed_date;

            if ($issueTask->issue) {
                $this->issue_id = $issueTask->issue->id;
                $this->issue_name = $issueTask->issue->name;
            }
        }

        $this->dispatch('refreshData');
    }

    #[On('deleteTask')]
    public function deleteTask($taskId, $issueId)
    {
        $issueTask = IssueTask::query()
            ->where('id', $taskId)
            ->where('issue_id', $issueId)
            ->first();

        if ($issueTask) {
            $issueTask->delete();
        }

        $this->dispatch('refreshData');
    }

    public function toggleCompleteTask()
    {
        $issueTask = IssueTask::findOrFail($this->task_id);

        if ($issueTask->completed_date) {
            $issueTask->completed_date = null;
            session()->flash('success', 'Task is now incomplete.');
        } else {
            $issueTask->completed_date = now();
            session()->flash('success', 'Task completed successfully.');
        }
        $issueTask->save();

        $this->completed_date = $issueTask->completed_date;

        $this->dispatch('refreshData');
    }
}
