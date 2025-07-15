<?php

namespace App\Filament\Resources\SchoolResource\Pages;

use App\Jobs\SendBulkInviteJob;
use App\Models\School;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Filament\Forms\Form;

class BulkInviteUsers extends Page
{
    protected static ?string $title = 'Bulk Invite Users';
    protected static string $resource = \App\Filament\Resources\SchoolResource::class;
    protected static string $view = 'filament.resources.school-resource.pages.bulk-invite-users';

    // DO NOT ADD TYPE HINTS TO PUBLIC PROPERTIES
    public $record;
    public $csv_file;

    // Remove type hint from mount
    public function mount($record)
    {
        $school = School::findOrFail($record);
        if (!$this->canUserAccess($school)) {
            abort(403, 'You do not have permission to access this page.');
        }
        $this->record = $school;
    }

    protected function canUserAccess($school)
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }
        return $user->schools()
            ->where('school_id', $school->id)
            ->wherePivotIn('role', ['owner', 'admin'])
            ->exists();
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\FileUpload::make('csv_file')
                ->disk('public')
                ->label('CSV File')
                ->acceptedFileTypes(['text/csv', 'text/plain', 'application/csv'])
                ->required(),
        ]);
    }

    public function importCsv()
    {
        $this->validate([
            'csv_file' => 'required',
        ]);

        $path = $this->csv_file;
        $file = Storage::disk('public')->get($path);
        $rows = array_map('str_getcsv', explode("\n", $file));
        $header = array_map('trim', array_map('strtolower', $rows[0] ?? []));
        $emailIdx = array_search('email', $header);
        $roleIdx = array_search('role', $header);
        if ($emailIdx === false || $roleIdx === false) {
            Notification::make()->title('CSV must have email and role columns.')->danger()->send();
            return;
        }
        $user = Auth::user();
        $count = 0;
        foreach (array_slice($rows, 1) as $row) {
            if (count($row) < max($emailIdx, $roleIdx) + 1) continue;
            $email = trim($row[$emailIdx]);
            $role = trim($row[$roleIdx]);
            $validator = Validator::make([
                'email' => $email,
                'role' => $role,
            ], [
                'email' => 'required|email:dns',
                'role' => 'required|in:admin,teacher,student',
            ]);
            if ($validator->fails()) continue;
            SendBulkInviteJob::dispatch($this->record->id, $email, $role, $user->id);
            $count++;
        }
        Notification::make()->title("$count invites queued!")->success()->send();
        $this->csv_file = null;
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('importCsv')
                ->label('Import CSV')
                ->action('importCsv'),
        ];
    }
} 