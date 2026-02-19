<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ContactMessage;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Salary;
use App\Models\Service;
use App\Models\Task;
use App\Models\Team;
use App\Models\TeamContent;
use App\Models\Testimonial;
use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class RecycleBinController extends Controller
{
    private function resources(): array
    {
        return [
            'users' => ['class' => User::class, 'label' => 'User', 'permission' => 'users.manage', 'title' => 'name'],
            'clients' => ['class' => Client::class, 'label' => 'Client', 'permission' => 'clients.manage', 'title' => 'company_name'],
            'projects' => ['class' => Project::class, 'label' => 'Project', 'permission' => 'projects.manage', 'title' => 'name'],
            'tasks' => ['class' => Task::class, 'label' => 'Task', 'permission' => 'tasks.manage', 'title' => 'title'],
            'teams' => ['class' => Team::class, 'label' => 'Team', 'permission' => 'teams.manage', 'title' => 'name'],
            'invoices' => ['class' => Invoice::class, 'label' => 'Invoice', 'permission' => 'invoices.manage', 'title' => 'invoice_number'],
            'payments' => ['class' => Payment::class, 'label' => 'Payment', 'permission' => 'payments.manage', 'title' => 'transaction_id'],
            'salaries' => ['class' => Salary::class, 'label' => 'Salary', 'permission' => 'salaries.manage', 'title' => 'id'],
            'services' => ['class' => Service::class, 'label' => 'Service', 'permission' => 'services.manage', 'title' => 'title'],
            'testimonials' => ['class' => Testimonial::class, 'label' => 'Testimonial', 'permission' => 'testimonials.manage', 'title' => 'client_name'],
            'team-contents' => ['class' => TeamContent::class, 'label' => 'Team Content', 'permission' => 'team-content.manage', 'title' => 'name'],
            'contact-messages' => ['class' => ContactMessage::class, 'label' => 'Contact Message', 'permission' => 'contact-messages.manage', 'title' => 'subject'],
            'user-activities' => ['class' => UserActivity::class, 'label' => 'User Activity', 'permission' => 'user-activities.restore', 'title' => 'description'],
        ];
    }

    public function index(Request $request)
    {
        $resources = $this->resources();
        $selectedType = (string) $request->query('type', '');

        $rows = collect();

        foreach ($resources as $type => $config) {
            if ($selectedType !== '' && $selectedType !== $type) {
                continue;
            }

            $modelClass = $config['class'];
            $records = $modelClass::onlyTrashed()->latest('deleted_at')->get();

            foreach ($records as $record) {
                $titleField = $config['title'];
                $titleValue = (string) ($record->{$titleField} ?? ('#' . $record->id));
                if ($titleValue === '') {
                    $titleValue = '#' . $record->id;
                }

                $rows->push([
                    'type' => $type,
                    'label' => $config['label'],
                    'id' => $record->id,
                    'title' => $titleValue,
                    'deleted_at' => $record->deleted_at,
                    'permission' => $config['permission'],
                ]);
            }
        }

        $sorted = $rows->sortByDesc('deleted_at')->values();

        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 30;
        $items = $sorted->slice(($page - 1) * $perPage, $perPage)->values();

        $deletedItems = new LengthAwarePaginator(
            $items,
            $sorted->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return view('admin.recycle-bin.index', [
            'deletedItems' => $deletedItems,
            'resources' => $resources,
            'selectedType' => $selectedType,
        ]);
    }

    public function restore(Request $request, string $type, int $id)
    {
        Log::info('RecycleBin restore requested', [
            'type' => $type,
            'id' => $id,
            'user_id' => $request->user()?->id,
        ]);

        $resources = $this->resources();

        if (!array_key_exists($type, $resources)) {
            abort(404);
        }

        $config = $resources[$type];

        try {
            $modelClass = $config['class'];
            $record = $modelClass::onlyTrashed()->findOrFail($id);
            $record->restore();
            $restoredDependencies = $this->restoreDependencies($type, $record);

            UserActivity::log(
                'update',
                'Restored deleted ' . $config['label'] . ' #' . $record->id,
                class_basename($modelClass),
                $record->id,
                ['deleted_at' => ['old' => 'deleted', 'new' => null]]
            );

            return redirect()->route('admin.recycle-bin.index')
                ->with('success', $config['label'] . ' restored successfully.' . ($restoredDependencies > 0 ? ' Related records restored: ' . $restoredDependencies . '.' : ''));
        } catch (\Throwable $exception) {
            report($exception);
            Log::error('RecycleBin restore failed', [
                'type' => $type,
                'id' => $id,
                'user_id' => $request->user()?->id,
                'error' => $exception->getMessage(),
            ]);

            return redirect()->route('admin.recycle-bin.index')
                ->with('error', 'Restore failed for ' . $config['label'] . '. ' . $exception->getMessage());
        }
    }

    private function restoreDependencies(string $type, mixed $record): int
    {
        $restored = 0;

        if ($type === 'users') {
            $restored += Project::onlyTrashed()->where('project_manager_id', $record->id)->restore();
            $restored += Task::onlyTrashed()->where('created_by', $record->id)->restore();
            $restored += Salary::onlyTrashed()->where('user_id', $record->id)->restore();
            $restored += UserActivity::onlyTrashed()->where('user_id', $record->id)->restore();

            $clients = Client::onlyTrashed()->where('user_id', $record->id)->get();
            foreach ($clients as $client) {
                $client->restore();
                $restored += 1;
                $restored += $this->restoreClientDependencies($client);
            }
        }

        if ($type === 'clients') {
            $restored += $this->restoreClientDependencies($record);
        }

        if ($type === 'projects') {
            $restored += $this->restoreProjectDependencies($record);
        }

        if ($type === 'invoices') {
            $restored += Payment::onlyTrashed()->where('invoice_id', $record->id)->restore();
        }

        return $restored;
    }

    private function restoreClientDependencies(Client $client): int
    {
        $restored = 0;

        $clientUser = User::onlyTrashed()->find($client->user_id);
        if ($clientUser) {
            $clientUser->restore();
            $restored += 1;
        }

        $projects = Project::onlyTrashed()->where('client_id', $client->id)->get();
        foreach ($projects as $project) {
            $project->restore();
            $restored += 1;
            $restored += $this->restoreProjectDependencies($project);
        }

        $invoices = Invoice::onlyTrashed()->where('client_id', $client->id)->get();
        foreach ($invoices as $invoice) {
            $invoice->restore();
            $restored += 1;
            $restored += Payment::onlyTrashed()->where('invoice_id', $invoice->id)->restore();
        }

        return $restored;
    }

    private function restoreProjectDependencies(Project $project): int
    {
        $restored = 0;
        $restored += Task::onlyTrashed()->where('project_id', $project->id)->restore();
        $restored += Salary::onlyTrashed()->where('project_id', $project->id)->restore();

        $invoices = Invoice::onlyTrashed()->where('project_id', $project->id)->get();
        foreach ($invoices as $invoice) {
            $invoice->restore();
            $restored += 1;
            $restored += Payment::onlyTrashed()->where('invoice_id', $invoice->id)->restore();
        }

        return $restored;
    }
}
