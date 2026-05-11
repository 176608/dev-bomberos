<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class SecureFileUpload
{
    protected FileContentValidator $validator;

    public function __construct()
    {
        $this->validator = new FileContentValidator();
    }

    public function getValidator(): FileContentValidator
    {
        return $this->validator;
    }

    public function uploadIcon(UploadedFile $file, ?string $existingFile = null): ?string
    {
        $this->validateOrFail($file, 'png', 'PNG');

        return $this->storeFile($file, 'img/SIGEM_mapas', $existingFile);
    }

    public function uploadImage(UploadedFile $file, ?string $existingFile = null): ?string
    {
        $this->validateOrFail($file, 'png', 'PNG', 'jpg', 'JPEG', 'gif', 'GIF');

        return $this->storeFile($file, 'imagenes/subtemas_u', $existingFile);
    }

    public function uploadPDF(UploadedFile $file, ?string $existingFile = null): ?string
    {
        $this->validateOrFail($file, 'pdf', 'PDF');

        return $this->storeFile($file, 'u_pdf', $existingFile);
    }

    public function uploadExcel(UploadedFile $file, ?string $existingFile = null): ?string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $this->validateOrFail($file, $extension, strtoupper($extension));

        return $this->storeFile($file, 'u_excel', $existingFile);
    }

    public function uploadExcelFormated(UploadedFile $file, ?string $existingFile = null): ?string
    {
        $this->validateOrFail($file, 'xlsx', 'XLSX', 'xls', 'XLS');

        return $this->storeFile($file, 'u_xlsx_formated', $existingFile);
    }

    protected function validateOrFail(UploadedFile $file, ...$types): void
    {
        $extension = strtolower($file->getClientOriginalExtension());

        $valid = false;
        foreach ($types as $type) {
            if ($this->validator->validate($file, $type)) {
                $valid = true;
                break;
            }
        }

        if (!$valid) {
            $errors = $this->validator->getErrors();
            $errorMsg = !empty($errors) ? implode(', ', $errors) : 'Tipo de archivo no permitido';
            throw new \InvalidArgumentException($errorMsg);
        }

        if ($this->validator->hasErrors()) {
            Log::warning('Validación de contenido tuvo advertencias', [
                'file' => $file->getClientOriginalName(),
                'errors' => $this->validator->getErrors(),
            ]);
        }
    }

    protected function storeFile(UploadedFile $file, string $directory, ?string $existingFile = null): string
    {
        $directory = public_path($directory);

        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        if ($existingFile) {
            $existingPath = $directory . '/' . $existingFile;
            if (file_exists($existingPath)) {
                unlink($existingPath);
            }
        }

        $extension = $file->getClientOriginalExtension();
        $safeFilename = $this->generateSafeFilename($extension);

        $file->move($directory, $safeFilename);

        return $safeFilename;
    }

    protected function generateSafeFilename(string $extension): string
    {
        $timestamp = time();
        $random = bin2hex(random_bytes(8));
        $cleanExtension = preg_replace('/[^a-zA-Z0-9]/', '', $extension);

        return "{$timestamp}_{$random}.{$cleanExtension}";
    }

    public function deleteFile(string $path, string $directory): bool
    {
        $fullPath = public_path($directory . '/' . $path);

        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }

        return false;
    }

    public function validateFile(UploadedFile $file, string $type): bool
    {
        return $this->validator->validate($file, $type);
    }
}