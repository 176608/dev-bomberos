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
        $extension = $this->validateOrFail($file, 'png');

        return $this->storeFile($file, 'img/SIGEM_mapas', $existingFile, $extension);
    }

    public function uploadImage(UploadedFile $file, ?string $existingFile = null): ?string
    {
        $extension = $this->validateOrFail($file, 'png', 'jpg', 'gif');

        return $this->storeFile($file, 'imagenes/subtemas_u', $existingFile, $extension);
    }

    public function uploadPDF(UploadedFile $file, ?string $existingFile = null): ?string
    {
        $extension = $this->validateOrFail($file, 'pdf');

        return $this->storeFile($file, 'u_pdf', $existingFile, $extension);
    }

    public function uploadPDFToMapas(UploadedFile $file, ?string $existingFile = null): string
    {
        $extension = $this->validateOrFail($file, 'pdf');

        $safeFilename = $this->generateSafeFilename($extension);

        if ($existingFile) {
            $disk = \Illuminate\Support\Facades\Storage::disk('mapas');
            if ($disk->exists($existingFile)) {
                $disk->delete($existingFile);
            }
        }

        $file->storeAs('', $safeFilename, 'mapas');

        return $safeFilename;
    }

    public function uploadExcel(UploadedFile $file, ?string $existingFile = null): ?string
    {
        $extension = $this->validateOrFail($file, 'xlsx', 'xls');

        return $this->storeFile($file, 'u_excel', $existingFile, $extension);
    }

    public function uploadExcelFormated(UploadedFile $file, ?string $existingFile = null): ?string
    {
        $extension = $this->validateOrFail($file, 'xlsx', 'xls');

        return $this->storeFile($file, 'u_xlsx_formated', $existingFile, $extension);
    }

    protected function validateOrFail(UploadedFile $file, ...$types): string
    {
        $validatedType = null;
        foreach ($types as $type) {
            if ($this->validator->validate($file, $type)) {
                $validatedType = $type;
                break;
            }
        }

        if (!$validatedType) {
            $errors = $this->validator->getErrors();
            $errorMsg = !empty($errors) ? implode(', ', $errors) : 'Tipo de archivo no permitido';
            throw new \InvalidArgumentException($errorMsg);
        }

        if ($this->validator->containsExecutableContent($file)) {
            Log::warning('Contenido potencialmente peligroso rechazado en subida', [
                'file' => $file->getClientOriginalName(),
            ]);
            throw new \InvalidArgumentException('El archivo contiene contenido no permitido.');
        }

        return strtolower($validatedType);
    }

    protected function storeFile(UploadedFile $file, string $directory, ?string $existingFile, string $extension): string
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