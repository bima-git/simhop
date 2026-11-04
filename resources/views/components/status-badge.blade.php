@props(['status', 'type' => 'status'])

@php
$normalized = strtolower(trim((string) $status));

$badgeConfig = match ($normalized) {
    // Video status
    'pending' => [
        'bg' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800',
        'dot' => 'bg-amber-500',
        'label' => 'Menunggu Review',
    ],
    'approved' => [
        'bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800',
        'dot' => 'bg-emerald-500',
        'label' => 'Disetujui',
    ],
    'revision' => [
        'bg' => 'bg-orange-50 text-orange-700 border-orange-200 dark:bg-orange-950/60 dark:text-orange-300 dark:border-orange-800',
        'dot' => 'bg-orange-500',
        'label' => 'Perlu Revisi',
    ],

    // Task status
    'todo' => [
        'bg' => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
        'dot' => 'bg-slate-400',
        'label' => 'Belum Dimulai',
    ],
    'in_progress' => [
        'bg' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-800',
        'dot' => 'bg-blue-500',
        'label' => 'Sedang Dikerjakan',
    ],
    'done' => [
        'bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800',
        'dot' => 'bg-emerald-500',
        'label' => 'Selesai',
    ],

    // Project status
    'active' => [
        'bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800',
        'dot' => 'bg-emerald-500',
        'label' => 'Aktif',
    ],
    'completed' => [
        'bg' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-800',
        'dot' => 'bg-blue-500',
        'label' => 'Selesai',
    ],
    'archived' => [
        'bg' => 'bg-slate-100 text-slate-600 border-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700',
        'dot' => 'bg-slate-400',
        'label' => 'Diarsipkan',
    ],

    // User status
    'inactive', '0' => [
        'bg' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-800',
        'dot' => 'bg-rose-500',
        'label' => 'Nonaktif',
    ],

    // Roles
    'admin' => [
        'bg' => 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/60 dark:text-purple-300 dark:border-purple-800',
        'dot' => 'bg-purple-500',
        'label' => 'Admin',
    ],
    'coach' => [
        'bg' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-800',
        'dot' => 'bg-blue-500',
        'label' => 'Coach',
    ],
    'mahasiswa' => [
        'bg' => 'bg-teal-50 text-teal-700 border-teal-200 dark:bg-teal-950/60 dark:text-teal-300 dark:border-teal-800',
        'dot' => 'bg-teal-500',
        'label' => 'Mahasiswa',
    ],

    // Handover document types
    'tech_stack' => [
        'bg' => 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-950/60 dark:text-indigo-300 dark:border-indigo-800',
        'dot' => 'bg-indigo-500',
        'label' => 'Tech Stack',
    ],
    'guide' => [
        'bg' => 'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-950/60 dark:text-sky-300 dark:border-sky-800',
        'dot' => 'bg-sky-500',
        'label' => 'Panduan',
    ],
    'migration' => [
        'bg' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800',
        'dot' => 'bg-amber-500',
        'label' => 'Migrasi',
    ],
    'asset' => [
        'bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800',
        'dot' => 'bg-emerald-500',
        'label' => 'Aset Berkas',
    ],

    default => [
        'bg' => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
        'dot' => 'bg-slate-400',
        'label' => ucfirst(str_replace('_', ' ', $normalized)),
    ],
};
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border ' . $badgeConfig['bg']]) }}>
    <span class="w-1.5 h-1.5 rounded-full {{ $badgeConfig['dot'] }} shrink-0"></span>
    <span>{{ $slot->isEmpty() ? $badgeConfig['label'] : $slot }}</span>
</span>
