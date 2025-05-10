<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Select Jira Projects') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-2xl p-6 space-y-6">
                <p class="text-gray-700">
                    Select which Jira projects you want to include when fetching tickets:
                </p>

                <form method="POST" action="{{ route('jira.projects.save') }}">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Select All Option --}}
                        <div class="col-span-2">
                            <label class="flex items-center space-x-2 font-medium text-gray-800">
                                <input type="checkbox" id="select-all" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span>Select All Projects</span>
                            </label>
                        </div>

                        {{-- Individual Projects --}}
                        @foreach ($projects as $project)
                            <label class="flex items-center space-x-2">
                                <input
                                    type="checkbox"
                                    name="projects[]"
                                    value="{{ $project['key'] }}"
                                    class="project-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    {{ in_array($project['key'], $selectedProjectKeys) ? 'checked' : '' }}
                                >
                                <span>{{ $project['name'] }} ({{ $project['key'] }})</span>
                            </label>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition">
                            💾 Save Selection
                        </button>
                        <a href="{{ route('integrations.index') }}"
                           class="ml-4 inline-flex items-center px-4 py-2 text-sm text-gray-700 hover:underline">
                            ← Back to Integrations
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectAllCheckbox = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('.project-checkbox');

            selectAllCheckbox.addEventListener('change', function () {
                checkboxes.forEach(cb => cb.checked = this.checked);
            });
        });
    </script>
@endpush
</x-app-layout>
