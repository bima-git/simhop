<?php

namespace Database\Seeders;

use App\Models\HandoverDocument;
use App\Models\Project;
use App\Models\Revision;
use App\Models\Task;
use App\Models\User;
use App\Models\VideoSubmission;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $coach = User::where('email', 'coach@example.com')->first();
        $mahasiswa1 = User::where('email', 'mahasiswa@example.com')->first();
        $mahasiswa2 = User::where('role', 'mahasiswa')->where('email', '!=', 'mahasiswa@example.com')->first();

        if (! $coach || ! $mahasiswa1) {
            return;
        }

        // Proyek 1: Sistem Manajemen Logistik dan Monitoring Server
        $project1 = Project::firstOrCreate(
            ['name' => 'Sistem Manajemen Logistik dan Monitoring Server'],
            [
                'created_by' => $coach->id,
                'description' => 'Pengembangan platform monitoring infrastruktur server dan manajemen distribusi logistik TI di PT Teknologi Server Indonesia.',
                'status' => 'active',
                'start_date' => '2026-08-01',
                'end_date' => '2026-11-30',
            ]
        );

        // Tambahkan anggota Mahasiswa Uji
        if (! $project1->members()->where('user_id', $mahasiswa1->id)->exists()) {
            $project1->members()->attach($mahasiswa1->id);
        }

        // Dokumen Handover Proyek 1
        HandoverDocument::firstOrCreate(
            [
                'project_id' => $project1->id,
                'title' => 'Spesifikasi Arsitektur Sistem dan Deployment',
            ],
            [
                'uploaded_by' => $coach->id,
                'type' => 'tech_stack',
                'content' => "## Spesifikasi Teknis Handover\n\n- **Tech Stack**: PHP 8.4, Laravel 12, MySQL 8.0, Tailwind CSS\n- **Web Server**: Nginx Reverse Proxy\n- **Monitoring Port**: 8080 (Prometheus agent)\n\n### Langkah Deployment Dev:\n1. Clone repositori ke `/var/www/simhop`\n2. Salin berkas `.env.example` ke `.env`\n3. Jalankan `composer install` dan `npm install && npm run build`\n4. Jalankan migrasi: `php artisan migrate --seed`",
            ]
        );

        HandoverDocument::firstOrCreate(
            [
                'project_id' => $project1->id,
                'title' => 'Panduan Konfigurasi Monitoring & Systemd Service',
            ],
            [
                'uploaded_by' => $coach->id,
                'type' => 'guide',
                'content' => "## Konfigurasi Layanan Systemd Daemon\n\nUntuk menjalankan pengumpul metrik secara berkala di background:\n\n```ini\n[Unit]\nDescription=SIMHOP Metrics Collector\nAfter=network.target\n\n[Service]\nType=simple\nUser=www-data\nExecStart=/usr/bin/php /var/www/simhop/artisan queue:work\nRestart=always\n\n[Install]\nWantedBy=multi-user.target\n```",
            ]
        );

        // Tugas 1: Konfigurasi Awal Lingkungan dan Server Web (Done, Video Approved)
        $task1 = Task::firstOrCreate(
            [
                'project_id' => $project1->id,
                'title' => 'Konfigurasi Awal Lingkungan dan Server Web',
            ],
            [
                'created_by' => $coach->id,
                'assigned_to' => $mahasiswa1->id,
                'description' => 'Persiapkan server lokal Ubuntu, pasang dependensi Nginx, PHP 8.4, dan MySQL database.',
                'due_date' => '2026-09-01',
                'status' => Task::STATUS_DONE,
                'progress' => 100,
                'progress_note' => 'Instalasi server web dan database telah selesai dan berjalan normal.',
            ]
        );

        VideoSubmission::firstOrCreate(
            ['task_id' => $task1->id],
            [
                'user_id' => $mahasiswa1->id,
                'title' => 'Dokumentasi Konfigurasi Awal Server dan Nginx',
                'description' => 'Video demonstrasi instalasi web server Nginx dan pengujian respon HTTP.',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'status' => 'approved',
                'reviewed_by' => $coach->id,
                'reviewed_at' => now()->subDays(5),
            ]
        );

        // Tugas 2: Implementasi Modul Monitoring Resource dan Metrik (In Progress, Video Revision)
        $task2 = Task::firstOrCreate(
            [
                'project_id' => $project1->id,
                'title' => 'Implementasi Modul Monitoring Resource dan Metrik',
            ],
            [
                'created_by' => $coach->id,
                'assigned_to' => $mahasiswa1->id,
                'description' => 'Buat script dan controller untuk membaca penggunaan CPU, RAM, serta penyimpanan disk.',
                'due_date' => '2026-11-15',
                'status' => Task::STATUS_IN_PROGRESS,
                'progress' => 65,
                'progress_note' => 'Pengambilan metrik CPU dan RAM telah selesai, sedang memperbaiki catatan review coach.',
            ]
        );

        $video2 = VideoSubmission::firstOrCreate(
            ['task_id' => $task2->id],
            [
                'user_id' => $mahasiswa1->id,
                'title' => 'Demonstrasi Fitur Metrik CPU dan RAM Server',
                'description' => 'Video rekaman demonstrasi pengujian beban CPU dan visualisasi grafik metrik.',
                'video_url' => 'https://drive.google.com/file/d/1a2b3c4d5e6f7g8h9i0jklmnopqrstuv/view',
                'status' => 'revision',
            ]
        );

        if (! Revision::where('video_submission_id', $video2->id)->exists()) {
            Revision::create([
                'video_submission_id' => $video2->id,
                'coach_id' => $coach->id,
                'note' => 'Tampilan grafik metrik CPU sudah bagus, namun bagian toleransi ambang batas (threshold alert) belum ditunjukkan dalam pengujian. Mohon diperbaiki dan didemonstrasikan kembali.',
            ]);
        }

        // Tugas 3: Penyusunan Dokumentasi Handover Arsitektur Jaringan (Todo)
        Task::firstOrCreate(
            [
                'project_id' => $project1->id,
                'title' => 'Penyusunan Dokumentasi Handover Arsitektur Jaringan',
            ],
            [
                'created_by' => $coach->id,
                'assigned_to' => $mahasiswa1->id,
                'description' => 'Tuliskan dokumentasi topologi jaringan dan port forwarding untuk pemagang berikutnya.',
                'due_date' => '2026-11-28',
                'status' => Task::STATUS_TODO,
                'progress' => 0,
            ]
        );

        // Proyek 2: Audit Keamanan Jaringan dan Vulnerability Assessment
        $assignedStudent = $mahasiswa2 ?: $mahasiswa1;
        $project2 = Project::firstOrCreate(
            ['name' => 'Audit Keamanan Jaringan dan Vulnerability Assessment'],
            [
                'created_by' => $coach->id,
                'description' => 'Pengujian kerentanan server internal dan pemetaan port jaringan.',
                'status' => 'active',
                'start_date' => '2026-09-15',
                'end_date' => '2026-12-15',
            ]
        );

        if (! $project2->members()->where('user_id', $assignedStudent->id)->exists()) {
            $project2->members()->attach($assignedStudent->id);
        }

        HandoverDocument::firstOrCreate(
            [
                'project_id' => $project2->id,
                'title' => 'Pedoman Standar Hardening OS Ubuntu Server',
            ],
            [
                'uploaded_by' => $coach->id,
                'type' => 'guide',
                'content' => "## Pedoman Hardening Sistem\n\n1. Matikan login SSH root (`PermitRootLogin no`)\n2. Pasang Fail2ban untuk proteksi brute force\n3. Konfigurasi Firewall UFW hanya membuka port 22, 80, dan 443",
            ]
        );

        Task::firstOrCreate(
            [
                'project_id' => $project2->id,
                'title' => 'Scanning Port dan Analisis Kerentanan Terbuka',
            ],
            [
                'created_by' => $coach->id,
                'assigned_to' => $assignedStudent->id,
                'description' => 'Lakukan pemindaian port menggunakan Nmap dan catat celah keamanan pada port terbuka.',
                'due_date' => '2026-11-20',
                'status' => Task::STATUS_TODO,
                'progress' => 0,
            ]
        );
    }
}
