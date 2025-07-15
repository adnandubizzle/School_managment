<x-filament::page>
    <div class="space-y-4">
        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded">
            <strong>CSV Format:</strong> The file must have columns: <code>email</code>, <code>role</code>.<br>
            Example:<br>
            <pre>email,role
user1@example.com,admin
user2@example.com,teacher
</pre>
        </div>
        {{ $this->form }}
    </div>
</x-filament::page> 