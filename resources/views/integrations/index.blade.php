<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Integrations') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-2xl p-6 space-y-4">
                <div class="flex items-center space-x-4">
                    <span class="text-2xl">🔌</span>
                    <h3 class="text-2xl font-semibold text-gray-900">Connect Jira</h3>
                </div>

                <p class="text-gray-700">
                    Integrate your Jira account to pull in Service Management ticket history and generate intelligent documentation.
                </p>

                @if ($jiraConnected)
                    <div class="inline-flex items-center bg-green-100 text-green-800 text-sm font-medium px-4 py-2 rounded-full">
                        ✅ Connected to Jira
                    </div>
                @else
                    <a href="{{ route('jira.connect') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition">
                        🔗 Connect with Jira
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
