<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Integrations') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-2">🔌 Connect Jira</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Integrate your Jira account to pull in Service Management ticket history and generate intelligent documentation.
                </p>

                <a href="{{ route('jira.connect') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-black uppercase tracking-widest hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                    🔗 Connect to Jira
                </a>
            </div>

            {{-- Future integrations can go below --}}
        </div>
    </div>
</x-app-layout>
