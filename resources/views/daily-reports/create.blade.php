<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="text-2xl font-bold mb-6">Create Daily Report</h2>

                    <form action="{{ route('daily-reports.store') }}" method="POST" id="reportForm">
                        @csrf

                        <div class="mb-6">
                            <label class="block mb-2">Report Date</label>
                            <input type="date" name="report_date" value="{{ old('report_date', date('Y-m-d')) }}" required
                                   class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>

                        <div id="tasks-container" class="space-y-6">
                            <!-- Tasks will be added here -->
                        </div>

                        <div class="mt-6">
                            <button type="button" id="add-task" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Add Task
                            </button>
                        </div>

                        <div class="mt-6">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Save Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <template id="task-template">
        <div class="task-entry border p-4 rounded-lg">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2">Task Category</label>
                    <select name="tasks[INDEX][category_id]" required class="task-category rounded-md shadow-sm border-gray-300 w-full">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                    data-has-batch="{{ $category->has_batch }}"
                                    data-has-claim="{{ $category->has_claim }}"
                                    data-has-time="{{ $category->has_time_range }}"
                                    data-has-sheets="{{ $category->has_sheets }}">
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="task-date-field hidden">
                    <label class="block mb-2">DOR Date</label>
                    <input type="date" name="tasks[INDEX][date]" class="rounded-md shadow-sm border-gray-300 w-full">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mt-4">
                <div class="batch-field hidden">
                    <label class="block mb-2">Batch Count</label>
                    <input type="number" name="tasks[INDEX][batch_count]" class="rounded-md shadow-sm border-gray-300 w-full">
                </div>

                <div class="claim-field hidden">
                    <label class="block mb-2">Claim Count</label>
                    <input type="number" name="tasks[INDEX][claim_count]" class="rounded-md shadow-sm border-gray-300 w-full">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mt-4 time-range-fields hidden">
                <div>
                    <label class="block mb-2">Start Time</label>
                    <input type="time" name="tasks[INDEX][start_time]" class="rounded-md shadow-sm border-gray-300 w-full">
                </div>
                <div>
                    <label class="block mb-2">End Time</label>
                    <input type="time" name="tasks[INDEX][end_time]" class="rounded-md shadow-sm border-gray-300 w-full">
                </div>
            </div>

            <div class="sheets-field hidden mt-4">
                <label class="block mb-2">Sheet Count</label>
                <input type="number" name="tasks[INDEX][sheet_count]" class="rounded-md shadow-sm border-gray-300 w-full">
            </div>

            <button type="button" class="remove-task mt-4 text-red-500 hover:text-red-700">
                Remove Task
            </button>
        </div>
    </template>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let taskIndex = 0;
            const tasksContainer = document.getElementById('tasks-container');
            const template = document.getElementById('task-template');
            const addTaskButton = document.getElementById('add-task');

            function addTask() {
                const taskHtml = template.innerHTML.replace(/INDEX/g, taskIndex);
                tasksContainer.insertAdjacentHTML('beforeend', taskHtml);

                const newTask = tasksContainer.lastElementChild;
                setupTaskListeners(newTask);
                taskIndex++;
            }

            function setupTaskListeners(taskElement) {
                const categorySelect = taskElement.querySelector('.task-category');
                const removeButton = taskElement.querySelector('.remove-task');

                categorySelect.addEventListener('change', function() {
                    const selected = this.options[this.selectedIndex];
                    const hasBatch = selected.dataset.hasBatch === "1";
                    const hasClaim = selected.dataset.hasClaim === "1";
                    const hasTime = selected.dataset.hasTime === "1";
                    const hasSheets = selected.dataset.hasSheets === "1";

                    taskElement.querySelector('.batch-field').classList.toggle('hidden', !hasBatch);
                    taskElement.querySelector('.claim-field').classList.toggle('hidden', !hasClaim);
                    taskElement.querySelector('.time-range-fields').classList.toggle('hidden', !hasTime);
                    taskElement.querySelector('.sheets-field').classList.toggle('hidden', !hasSheets);
                    taskElement.querySelector('.task-date-field').classList.remove('hidden');
                });

                removeButton.addEventListener('click', function() {
                    taskElement.remove();
                });
            }

            addTaskButton.addEventListener('click', addTask);

            // Add initial task
            addTask();

            // Debug form submission
            document.getElementById('reportForm').addEventListener('submit', function(e) {
                const formData = new FormData(this);
                console.log('Form Data:', Object.fromEntries(formData));
            });
        });
    </script>
</x-app-layout>
