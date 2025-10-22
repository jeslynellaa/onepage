<div>
    <form wire:submit.prevent="addStep" id="procedure-steps-form" class="space-y-4">
        <div>
            <label>Responsibility</label>
            <input type="text" wire:model="responsibility" class="border p-2 w-full">
        </div>

        <div>
            <label>Activities</label>
            <textarea wire:model="activities" class="border p-2 w-full"></textarea>
        </div>

        <div>
            <label>Note</label>
            <textarea wire:model="note" class="border p-2 w-full"></textarea>
        </div>

        <!-- Inputs -->
        <div>
            <h4>References (Inputs)</h4>
            <div class="flex gap-2 mb-1">
                <input type="text" placeholder="Category" class="border p-1">
                <input type="text" placeholder="Name" class="border p-1">
            </div>
            @foreach($interfaces_input as $index => $input)
                <div class="flex gap-2 mb-1">
                    <input type="text" wire:model="interfaces_input.{{ $index }}.category" placeholder="Category" class="border p-1">
                    <input type="text" wire:model="interfaces_input.{{ $index }}.name" placeholder="Name" class="border p-1">
                    <button type="button" wire:click="removeInput({{ $index }})" class="text-red-600">x</button>
                </div>
            @endforeach
            <button type="button" wire:click="addInput" class="bg-blue-500 text-white px-2 py-1 rounded">+ Add Input</button>
        </div>

        <!-- Outputs -->
        <div>
            <h4>Outputs</h4>
            @foreach($interfaces_output as $index => $output)
                <div class="flex gap-2 mb-1">
                    <input type="text" wire:model="interfaces_output.{{ $index }}.category" placeholder="Category" class="border p-1">
                    <input type="text" wire:model="interfaces_output.{{ $index }}.name" placeholder="Name" class="border p-1">
                    <button type="button" wire:click="removeOutput({{ $index }})" class="text-red-600">x</button>
                </div>
            @endforeach
            <button type="button" wire:click="addOutput" class="bg-blue-500 text-white px-2 py-1 rounded">+ Add Output</button>
        </div>

        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Add Step</button>
    </form>

    <!-- Steps Table -->
    <table class="w-full mt-6 border">
        <thead>
            <tr class="bg-gray-200">
                <th>Responsibility</th>
                <th>Activities</th>
                <th>Note</th>
                <th>Inputs</th>
                <th>Outputs</th>
            </tr>
        </thead>
        <tbody>
            @forelse($steps as $step)
                <tr>
                    <td class="border p-2">{{ $step['responsibility'] }}</td>
                    <td class="border p-2">{{ $step['activities'] }}</td>
                    <td class="border p-2">{{ $step['note'] }}</td>
                    <td class="border p-2">
                        @foreach($step['interfaces_input'] as $i)
                            {{ $i['name'] ?? '' }}<br>
                        @endforeach
                    </td>
                    <td class="border p-2">
                        @foreach($step['interfaces_output'] as $o)
                            {{ $o['name'] ?? '' }}<br>
                        @endforeach
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-gray-500 py-2">No steps added yet</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Hidden field (optional if you still need JSON) -->
    <input type="hidden" name="procedure_steps_json" value="{{ json_encode($steps) }}">
</div>
