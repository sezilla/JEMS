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
                                            errorMessage: ''
                                        }">

                                        <div>
                                            <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $item['name'] }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                Due: <span x-text="formattedDueDate"></span>
                                            </p>
                                            <p class="text-sm font-semibold"
                                                :class="status === 'complete' ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400'">
                                                <span x-text="status === 'complete' ? 'Complete' : 'Incomplete'"></span>
                                            </p>
                                        </div>

                                        <div class="flex items-center space-x-2">
                                            <button class="px-4 py-2 rounded-lg shadow-md transition 
                                                    bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 
                                                    text-white dark:text-gray-200"
                                                @click="showModal = true">
                                                Edit
                                            </button>
                                            <button class="px-4 py-2 rounded-lg shadow-md transition 
                                                    bg-green-500 hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700 
                                                    text-white dark:text-gray-200"
                                                @click="status = 'complete'">
                                                Done
                                            </button>
                                        </div>

                                        <!-- Modal -->
                                        <div x-show="showModal"
                                             class="fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center p-4"
                                             x-cloak>
                                            <div class="bg-white dark:bg-gray-900 p-6 rounded-xl shadow-lg w-[500px]">
                                                <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-gray-100">
                                                    Edit Task
                                                </h2>

                                                <label class="block font-medium text-gray-700 dark:text-gray-300 mb-2">Due Date</label>
                                                <input type="date" x-model="dueDate"
                                                       class="w-full border-gray-300 dark:border-gray-700 
                                                              bg-gray-100 dark:bg-gray-800 
                                                              rounded-md p-2 mb-2 focus:ring focus:ring-blue-200"
                                                       @change="
                                                            let selectedDate = new Date(dueDate);
                                                            let today = new Date();
                                                            today.setHours(0, 0, 0, 0); 

                                                            if (selectedDate < today) {
                                                                errorMessage = 'Due date cannot be in the past.';
                                                                dueDate = '';
                                                            } else {
                                                                formattedDueDate = new Intl.DateTimeFormat('en-US', { year: 'numeric', month: 'long', day: 'numeric' }).format(selectedDate);
                                                                errorMessage = '';
                                                            }
                                                       ">

                                                <p class="text-red-500 dark:text-red-400 text-sm" x-text="errorMessage"></p>

                                                <label class="block font-medium text-gray-700 dark:text-gray-300 mt-4 mb-2">Status</label>
                                                <select x-model="status"
                                                        class="w-full border-gray-300 dark:border-gray-700 
                                                               bg-gray-100 dark:bg-gray-800 
                                                               rounded-md p-2 focus:ring focus:ring-blue-200">
                                                    <option value="incomplete">Incomplete</option>
                                                    <option value="complete">Complete</option>
                                                </select>

                                                <div class="flex justify-end space-x-3 mt-6">
                                                    <button class="px-4 py-2 rounded-lg transition 
                                                        bg-gray-400 hover:bg-gray-500 
                                                        dark:bg-gray-700 dark:hover:bg-gray-600 
                                                        text-white font-semibold"
                                                        @click="showModal = false">
                                                        Cancel
                                                    </button>
                                                    <button class="px-4 py-2 rounded-lg shadow-md transition 
                                                        bg-blue-500 hover:bg-blue-600 
                                                        dark:bg-blue-600 dark:hover:bg-blue-700 
                                                        text-white font-semibold"
                                                        @click="showModal = false">
                                                        Save
                                                    </button>
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
