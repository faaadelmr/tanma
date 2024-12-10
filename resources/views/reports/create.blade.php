<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-primary-800">
            {{ __('Create Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-secondary-200">
                    <form method="POST" action="{{ route('reports.store') }}" class="space-y-6" x-data="reportForm()">
                        @csrf

                        <div>
                            <x-input-label for="report_date" value="Report Date" class="text-primary-700"/>
                            <x-text-input id="report_date" type="date" name="report_date" class="block mt-1 w-full border-secondary-300" :value="old('report_date')" required />
                            <x-input-error :messages="$errors->get('report_date')" class="mt-2" />
                        </div>

                        <div id="tasks-container" class="space-y-4">
                            <template x-for="(task, index) in tasks" :key="index">
                                <div class="p-4 rounded border border-secondary-300">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-primary-700">Category</label>
                                            <select :name="'tasks['+index+'][category_id]'" x-model="task.category_id" class="block mt-1 w-full rounded-md shadow-sm border-secondary-300 focus:border-accent-500 focus:ring-accent-500" required>
                                                <option value="">Select Category</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-primary-700">Description</label>
                                            <input type="text" :name="'tasks['+index+'][description]'" x-model="task.description" class="block mt-1 w-full rounded-md shadow-sm border-secondary-300 focus:border-accent-500 focus:ring-accent-500" required>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-primary-700">Quantity</label>
                                            <input type="number" :name="'tasks['+index+'][quantity]'" x-model="task.quantity" min="0" step="0.01" class="block mt-1 w-full rounded-md shadow-sm border-secondary-300 focus:border-accent-500 focus:ring-accent-500" required>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-primary-700">Unit</label>
                                            <input type="text" :name="'tasks['+index+'][unit]'" x-model="task.unit" class="block mt-1 w-full rounded-md shadow-sm border-secondary-300 focus:border-accent-500 focus:ring-accent-500" required>
                                        </div>
                                    </div>

                                    <button type="button" @click="removeTask(index)" class="mt-2 text-accent-600 hover:text-accent-800">Remove Task</button>
                                </div>
                            </template>
                        </div>

                        <div class="flex justify-between">
                            <button type="button" @click="addTask()" class="px-4 py-2 font-bold text-white rounded bg-accent-500 hover:bg-accent-700">
                                Add Task
                            </button>

                            <button type="submit" class="px-4 py-2 font-bold text-white rounded bg-primary-500 hover:bg-primary-700">
                                Submit Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('reportForm', () => ({
                tasks: [],
                init() {
                    this.addTask();
                },
                addTask() {
                    this.tasks.push({
                        category_id: '',
                        description: '',
                        quantity: '',
                        unit: ''
                    });
                },
                removeTask(index) {
                    this.tasks.splice(index, 1);
                }
            }));
        });
    </script>
</x-app-layout>
