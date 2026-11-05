@props(['status', 'type' => 'status'])

@php
$normalized = strtolower(trim((string) $status));

$badgeConfig = match ($normalized) {
    // Video status
    'pending' => [
        'bg' => 'bg-amber-100 text-amber-900 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800/80',
        'dot' => 'bg-amber-500 dark:bg-amber-400',
        'label' => 'Menunggu Review',
    ],
    'approved' => [
        'bg' => 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800/80',
        'dot' => 'bg-emerald-500 dark:bg-emerald-400',
        'label' => 'Disetujui',
    ],
    'revision' => [
        'bg' => 'bg-orange-100 text-orange-900 border-orange-200 dark:bg-orange-950/60 dark:text-orange-300 dark:border-orange-800/80',
        'dot' => 'bg-orange-500 dark:bg-orange-400',
        'label' => 'Perlu Revisi',
    ],

    // Task status
    'todo' => [
        'bg' => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
        'dot' => 'bg-slate-400 dark:bg-slate-500',
        'label' => 'Belum Dimulai',
    ],
    'in_progress' => [
        'bg' => 'bg-sky-100 text-sky-900 border-sky-200 dark:bg-sky-950/60 dark:text-sky-300 dark:border-sky-800/80',
        'dot' => 'bg-sky-500 dark:bg-sky-400',
        'label' => 'Sedang Dikerjakan',
    ],
    'done' => [
        'bg' => 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800/80',
        'dot' => 'bg-emerald-500 dark:bg-emerald-400',
        'label' => 'Selesai',
    ],

    // Project status
    'active' => [
        'bg' => 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800/80',
        'dot' => 'bg-emerald-500 dark:bg-emerald-400',
        'label' => 'Aktif',
    ],
    'completed' => [
        'bg' => 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800/80',
        'dot' => 'bg-emerald-500 dark:bg-emerald-400',
        'label' => 'Selesai',
    ],
    'archived' => [
        'bg' => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
        'dot' => 'bg-slate-400 dark:bg-slate-500',
        'label' => 'Diarsipkan',
    ],

    // User status
    'inactive', '0' => [
        'bg' => 'bg-rose-100 text-rose-800 border-rose-200 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-800/80',
        'dot' => 'bg-rose-500 dark:bg-rose-400',
        'label' => 'Nonaktif',
    ],

    // Roles
    'admin' => [
        'bg' => 'bg-purple-100 text-purple-900 border-purple-200 dark:bg-purple-950/60 dark:text-purple-300 dark:border-purple-800/80',
        'dot' => 'bg-purple-500 dark:bg-purple-400',
        'label' => 'Admin',
    ],
    'coach' => [
        'bg' => 'bg-blue-100 text-blue-900 border-blue-200 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-800/80',
        'dot' => 'bg-blue-500 dark:bg-blue-400',
        'label' => 'Coach',
    ],
    'mahasiswa' => [
        'bg' => 'bg-teal-100 text-teal-900 border-teal-200 dark:bg-teal-950/60 dark:text-teal-300 dark:border-teal-800/80',
        'dot' => 'bg-teal-500 dark:bg-teal-400',
        'label' => 'Mahasiswa',
    ],

    // Handover document types
    'tech_stack' => [
        'bg' => 'bg-indigo-100 text-indigo-900 border-indigo-200 dark:bg-indigo-950/60 dark:text-indigo-300 dark:border-indigo-800/80',
        'dot' => 'bg-indigo-500 dark:bg-indigo-400',
        'label' => 'Tech Stack',
    ],
    'guide' => [
        'bg' => 'bg-sky-100 text-sky-900 border-sky-200 dark:bg-sky-950/60 dark:text-sky-300 dark:border-sky-800/80',
        'dot' => 'bg-sky-500 dark:bg-sky-400',
        'label' => 'Panduan',
    ],
    'migration' => [
        'bg' => 'bg-amber-100 text-amber-900 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800/80',
        'dot' => 'bg-amber-500 dark:bg-amber-400',
        'label' => 'Migrasi',
    ],
    'asset' => [
        'bg' => 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800/80',
        'dot' => 'bg-emerald-500 dark:bg-emerald-400',
        'label' => 'Aset Berkas',
    ],

    default => [
        'bg' => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
        'dot' => 'bg-slate-400 dark:bg-slate-500',
        'label' => ucfirst(str_replace('_', ' ', $normalized)),
    ],
};
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold tracking-wide border whitespace-nowrap ' . $badgeConfig['bg']]) }}>
    <span class="w-1.5 h-1.5 rounded-full {{ $badgeConfig['dot'] }} shrink-0"></span>
    <span>{{ $slot->isEmpty() ? $badgeConfig['label'] : $slot }}</span>
</span>
