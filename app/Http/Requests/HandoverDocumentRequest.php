<?php

namespace App\Http\Requests;

use App\Models\HandoverDocument;
use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HandoverDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $project = $this->resolveProject();

        return $project !== null && $project->created_by === $this->user()?->id;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(HandoverDocument::TYPES)],
            'content' => ['nullable', 'string'],
            'file' => [
                'nullable',
                'file',
                'max:' . HandoverDocument::MAX_FILE_KB,
                'mimes:pdf,zip,txt,md,doc,docx,png,jpg,jpeg',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul dokumen wajib diisi.',
            'title.max' => 'Judul dokumen tidak boleh lebih dari 255 karakter.',
            'type.required' => 'Tipe dokumen wajib dipilih.',
            'type.in' => 'Tipe dokumen tidak valid.',
            'file.max' => 'Ukuran berkas maksimal 10 MB.',
            'file.mimes' => 'Tipe berkas tidak diizinkan. Gunakan: pdf, zip, txt, md, doc, docx, png, jpg.',
        ];
    }

    private function resolveProject(): ?Project
    {
        $project = $this->route('project');
        if ($project instanceof Project) {
            return $project;
        }

        $document = $this->route('document');
        if ($document instanceof HandoverDocument) {
            return $document->project;
        }

        if (is_numeric($document)) {
            return HandoverDocument::with('project')->find($document)?->project;
        }

        return null;
    }
}
