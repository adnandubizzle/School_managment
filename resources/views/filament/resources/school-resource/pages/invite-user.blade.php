<x-filament::page>
    <div class="space-y-6">
        <div>
            <h2 class="text-lg font-medium text-gray-900">
                Invite User to {{ $this->record->name }}
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                Send an invitation to join this school.
            </p>
        </div>

        <form wire:submit.prevent="sendInvite" class="space-y-6">
            {{ $this->form }}
            
            <div class="flex justify-end">
                <x-filament::button type="submit">
                    Send Invite
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament::page>