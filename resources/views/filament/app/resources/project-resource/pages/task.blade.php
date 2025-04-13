<x-filament::page>
    @if ($trelloCards && count($trelloCards))
        @foreach ($trelloCards as $card)
            <x-filament::section class="mb-8">
                <header class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $card['name'] }}</h2>
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $card['due'] ? \Carbon\Carbon::parse($card['due'])->format('F d, Y') : 'No Due Date' }}
                    </span>
                </header>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($card['checklists'] as $checklist)
                        <x-filament::card>
                            <header class="mb-2 flex justify-between items-center">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $checklist['name'] }}</h3>
                            </header>
                            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($checklist['items'] as $item)
                                    <li class="py-3 flex items-center justify-between"
                                        x-data="{
                                            showModal: false, 
                                            dueDate: '{{ $item['due'] ?? '' }}', 
                                            status: '{{ strtolower($item['state']) }}',
                                            formattedDueDate: '{{ $item['due'] ? \Carbon\Carbon::parse($item['due'])->format('F d, Y') : 'No Due Date' }}',
                                            errorMessage: '',
                                            updateStatus(newStatus) {
                                                this.status = newStatus;
                                            }
                                        }">

                                        <div>
                                            <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $item['name'] }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                Due: <span x-text="formattedDueDate"></span>
                                            </p>

                                            <div class="mt-2 flex gap-2">
                                                <x-filament::badge color="success" class="px-3 py-1 text-sm"
                                                    icon="heroicon-o-check-circle"	
                                                    x-show="status === 'complete'"
                                                    style="display: none;">
                                                    Complete
                                                </x-filament::badge>
                                                <x-filament::badge color="warning" class="px-3 py-1 text-sm"
                                                    icon="heroicon-o-x-circle"
                                                    x-show="status !== 'complete'"
                                                    style="display: none;">
                                                    Incomplete
                                                </x-filament::badge>
                                            </div>
                                        </div>

                                        <div class="flex items-center space-x-2 pl-4">
                                            <x-filament::button color="warning" @click="showModal = true">
                                                Edit
                                            </x-filament::button>

                                            <x-filament::button color="success" @click="updateStatus('complete')">
                                                Done
                                            </x-filament::button>
                                        </div>



                                        <!-- ito na yoooooon -->
                                        <!-- Modal -->
                                        <x-filament::modal>
                                            <x-slot name="trigger">
                                                <x-filament::button>
                                                    Open modal
                                                </x-filament::button>
                                            </x-slot>

                                            {{-- Modal content --}}
                                            <form>
                                                <div class="space-y-4">
                                                    <!-- Select Due Date -->
                                                    <div>
                                                        <label for="due_date" class="block text-sm font-medium text-gray-700">{{ __('Due Date') }}</label>
                                                        <x-filament::input id="due_date" type="date" name="due_date" required />
                                                    </div>

                                                    <!-- Select Status -->
                                                    <div>
                                                        <label for="status" class="block text-sm font-medium text-gray-700">{{ __('Status') }}</label>
                                                        <x-filament::select id="status" name="status" :options="['complete' => 'Complete', 'incomplete' => 'Incomplete']" required />
                                                    </div>

                                                    <!-- Select Users -->
                                                    <div>
                                                        <label for="user_id" class="block text-sm font-medium text-gray-700">{{ __('Assign Users') }}</label>
                                                        <x-filament::select id="user_id" name="user_id[]" :options="App\Models\User::all()->pluck('name', 'id')->toArray()" multiple required />
                                                    </div>
                                                </div>
                                            </form>
                                        </x-filament::modal>

                                        <div x-show="showModal"
                                             x-cloak
                                             class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 p-4"
                                             role="dialog"
                                             aria-modal="true">
                                            <div class="bg-white dark:bg-gray-900 p-6 rounded-xl shadow-xl w-full max-w-xl">
                                                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                                                    Edit Task
                                                </h2>

                                                <!-- Due Date -->
                                                <div class="mb-4">
                                                    <label for="dueDate" class="block font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                        Due Date
                                                    </label>
                                                    <input type="date" x-model="dueDate" id="dueDate"
                                                           class="w-full border-gray-300 dark:border-gray-700 
                                                                  bg-gray-100 dark:bg-gray-800 
                                                                  rounded-md p-2 focus:ring focus:ring-blue-200"
                                                           @change="
                                                                let selectedDate = new Date(dueDate);
                                                                let today = new Date();
                                                                today.setHours(0, 0, 0, 0); 

                                                                if (selectedDate < today) {
                                                                    errorMessage = 'Due date cannot be in the past.';
                                                                    dueDate = '';
                                                                } else {
                                                                    errorMessage = '';
                                                                }
                                                           ">
                                                    <p class="text-sm text-red-500 dark:text-red-400" x-text="errorMessage"></p>
                                                </div>

                                                <!-- Status -->
                                                <div class="mb-6">
                                                    <label for="status" class="block font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                        Status
                                                    </label>
                                                    <select x-model="status" id="status"
                                                            class="w-full border-gray-300 dark:border-gray-700 
                                                                   bg-gray-100 dark:bg-gray-800 
                                                                   rounded-md p-2 focus:ring focus:ring-blue-200">
                                                        <option value="incomplete">Incomplete</option>
                                                        <option value="complete">Complete</option>
                                                    </select>
                                                </div>

                                                <!-- Modal Actions -->
                                                <div class="flex justify-end space-x-3">
                                                    <x-filament::button color="gray" @click="showModal = false">
                                                        Cancel
                                                    </x-filament::button>

                                                    <x-filament::button color="primary"
                                                        @click="
                                                            if (dueDate) {
                                                                let selectedDate = new Date(dueDate);
                                                                formattedDueDate = new Intl.DateTimeFormat('en-US', { year: 'numeric', month: 'long', day: 'numeric' }).format(selectedDate);
                                                            } else {
                                                                formattedDueDate = 'No Due Date';
                                                            }
                                                            showModal = false;
                                                        ">
                                                        Save
                                                    </x-filament::button>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </x-filament::card>
                    @endforeach
                </div>
            </x-filament::section>
        @endforeach
    @else
        <div class="p-6 text-gray-600 dark:text-gray-400">No tasks found.</div>
    @endif
</x-filament::page>
