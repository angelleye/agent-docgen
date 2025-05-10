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
                        @foreach ($projects as $project)
                            <label class="flex items-center space-x-2">
                                <input
                                    type="checkbox"
                                    name="projects[]"
                                    value="{{ $project['key'] }}"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
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
</x-app-layout>
