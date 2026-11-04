<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Handover: {{ $project->name }}
            </h2>
            <a href="{{ route('coach.handover.create', $project) }}"
               class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                + Tambah Dokumen
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-md bg-green-100 p-4 text-sm text-green-800 dark:bg-green-900/50 dark:text-green-300">{{ session('status') }}</div>
            @endif

            @php
                $typeLabels = [
                    'guide' => 'Panduan (Guide)',
                    'tech_stack' => 'Tech Stack',
                    'migration' => 'Catatan Migrasi',
                    'asset' => 'Aset Pendukung',
                ];
            @endphp

            @forelse ($documents->groupBy('type') as $type => $docs)
                <div class="bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800 dark:text-gray-100">
                    <h3 class="mb-4 text-lg font-semibold">{{ $typeLabels[$type] ?? ucfirst($type) }}</h3>

                    <div class="space-y-4">
                        @foreach ($docs as $doc)
                            <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <h4 class="font-medium">{{ $doc->title }}</h4>
                                        @if ($doc->content)
                                            <div class="mt-2 whitespace-pre-line text-sm text-gray-600 dark:text-gray-300">{{ $doc->content }}</div>
                                        @endif
                                        @if ($doc->file_name)
                                            <div class="mt-3 flex items-center gap-2 text-sm">
                                                <span class="text-gray-500 dark:text-gray-400">📎 {{ $doc->file_name }} ({{ $doc->file_size }} KB)</span>
                                                <a href="{{ route('handover.download', $doc) }}"
                                                   class="text-indigo-600 hover:underline dark:text-indigo-400 font-medium">Unduh</a>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <a href="{{ route('coach.handover.edit', $doc) }}"
                                           class="text-sm text-indigo-600 hover:underline dark:text-indigo-400">Edit</a>
                                        <form method="POST" action="{{ route('coach.handover.destroy', $doc) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm text-red-600 hover:underline dark:text-red-400"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?')">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800 dark:text-gray-100">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada dokumen handover pada proyek ini.</p>
                </div>
            @endforelse

            <a href="{{ route('coach.projects.show', $project) }}" class="text-sm text-indigo-600 hover:underline dark:text-indigo-400">← Kembali ke Detail Proyek</a>
        </div>
    </div>
</x-app-layout>
