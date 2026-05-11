<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class FileContentValidator
{
    protected array $errors = [];

    public function validate(UploadedFile $file, string $type): bool
    {
        $this->errors = [];

        return match($type) {
            'png' => $this->validatePNG($file),
            'jpg', 'jpeg' => $this->validateJPG($file),
            'gif' => $this->validateGIF($file),
            'pdf' => $this->validatePDF($file),
            'xlsx', 'xls' => $this->validateExcel($file),
            default => false,
        };
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    protected function addError(string $error): void
    {
        $this->errors[] = $error;
    }

    protected function getFileContent(int $length = 32): string
    {
        $file = func_get_arg(1) ?? null;
        if ($file instanceof UploadedFile) {
            $handle = fopen($file->getRealPath(), 'rb');
            if ($handle) {
                $content = fread($handle, $length);
                fclose($handle);
                return $content;
            }
        }
        return '';
    }

    public function validatePNG(UploadedFile $file): bool
    {
        $handle = fopen($file->getRealPath(), 'rb');
        if (!$handle) {
            $this->addError('No se pudo leer el archivo');
            return false;
        }

        $header = fread($handle, 8);
        fclose($handle);

        if (strlen($header) < 8) {
            $this->addError('Archivo demasiado pequeño para ser PNG válido');
            return false;
        }

        $pngSignature = "\x89PNG\r\n\x1A\n";
        if (substr($header, 0, 8) !== $pngSignature) {
            $this->addError('La firma del archivo no corresponde a PNG');
            return false;
        }

        if (!$this->isSafeFileSize($file, 5120)) {
            $this->addError('El archivo excede el tamaño máximo permitido o es sospechosamente pequeño');
            return false;
        }

        return true;
    }

    public function validateJPG(UploadedFile $file): bool
    {
        $handle = fopen($file->getRealPath(), 'rb');
        if (!$handle) {
            $this->addError('No se pudo leer el archivo');
            return false;
        }

        $header = fread($handle, 3);
        fclose($handle);

        if (strlen($header) < 3) {
            $this->addError('Archivo demasiado pequeño para ser JPEG válido');
            return false;
        }

        $jpegSignature = "\xFF\xD8\xFF";
        if (substr($header, 0, 3) !== $jpegSignature) {
            $this->addError('La firma del archivo no corresponde a JPEG');
            return false;
        }

        if (!$this->isSafeFileSize($file, 5120)) {
            $this->addError('El archivo excede el tamaño máximo permitido o es sospechosamente pequeño');
            return false;
        }

        return true;
    }

    public function validateGIF(UploadedFile $file): bool
    {
        $handle = fopen($file->getRealPath(), 'rb');
        if (!$handle) {
            $this->addError('No se pudo leer el archivo');
            return false;
        }

        $header = fread($handle, 6);
        fclose($handle);

        if (strlen($header) < 6) {
            $this->addError('Archivo demasiado pequeño para ser GIF válido');
            return false;
        }

        $gifSignatures = ['GIF87a', 'GIF89a'];
        $valid = false;
        foreach ($gifSignatures as $sig) {
            if (substr($header, 0, 6) === $sig) {
                $valid = true;
                break;
            }
        }

        if (!$valid) {
            $this->addError('La firma del archivo no corresponde a GIF');
            return false;
        }

        if (!$this->isSafeFileSize($file, 5120)) {
            $this->addError('El archivo excede el tamaño máximo permitido o es sospechosamente pequeño');
            return false;
        }

        return true;
    }

    public function validatePDF(UploadedFile $file): bool
    {
        $handle = fopen($file->getRealPath(), 'rb');
        if (!$handle) {
            $this->addError('No se pudo leer el archivo');
            return false;
        }

        $header = fread($handle, 5);
        fclose($handle);

        if (strlen($header) < 5) {
            $this->addError('Archivo demasiado pequeño para ser PDF válido');
            return false;
        }

        if (substr($header, 0, 5) !== '%PDF-') {
            $this->addError('La firma del archivo no corresponde a PDF');
            return false;
        }

        $footer = $this->getPDFTail($file);
        if ($footer === false || strpos($footer, '%%EOF') === false) {
            $this->addError('El archivo PDF está incompleto o está corrupto');
            return false;
        }

        if (!$this->isSafeFileSize($file, 10240)) {
            $this->addError('El archivo excede el tamaño máximo permitido');
            return false;
        }

        if ($this->containsExecutableContent($file)) {
            $this->addError('El archivo PDF contiene contenido potencialmente peligroso');
            return false;
        }

        return true;
    }

    public function validateExcel(UploadedFile $file): bool
    {
        $realPath = $file->getRealPath();

        $zip = new \ZipArchive();
        $result = $zip->open($realPath, \ZipArchive::RDONLY);

        if ($result !== true) {
            $this->addError('El archivo no es un archivo ZIP válido (XLSX debe ser ZIP)');
            return false;
        }

        $requiredFiles = [
            '[Content_Types].xml',
            'xl/workbook.xml'
        ];

        $hasRequiredFiles = true;
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (in_array($name, $requiredFiles)) {
                $hasRequiredFiles = true;
                break;
            }
        }

        $zip->close();

        if (!$hasRequiredFiles) {
            $this->addError('El archivo ZIP no contiene la estructura válida de Excel');
            return false;
        }

        if (!$this->isSafeFileSize($file, 10240)) {
            $this->addError('El archivo excede el tamaño máximo permitido');
            return false;
        }

        if ($this->containsExecutableContent($file)) {
            $this->addError('El archivo contiene contenido potencialmente peligroso');
            return false;
        }

        return true;
    }

    protected function getPDFTail(UploadedFile $file, int $seek = 1024): string|false
    {
        $size = filesize($file->getRealPath());
        if ($size <= 0) {
            return false;
        }

        $readSize = min($seek, $size);
        $handle = fopen($file->getRealPath(), 'rb');

        if (!$handle) {
            return false;
        }

        fseek($handle, -$readSize, SEEK_END);
        $content = fread($handle, $readSize);
        fclose($handle);

        return $content;
    }

    protected function containsExecutableContent(UploadedFile $file): bool
    {
        $handle = fopen($file->getRealPath(), 'rb');
        if (!$handle) {
            return false;
        }

        $content = fread($handle, 65536);
        fclose($handle);

        $dangerousPatterns = [
            '/<\?php/i',
            '/<\?=/i',
            '/<script/i',
            '/javascript:/i',
            '/on\w+\s*=/i',
            '/<%@/i',
            '/<jsp:/i',
            '/<!DOCTYPE\s+html/i',
            '/<!\[CDATA\[/i',
            '/eval\s*\(/i',
            '/base64_decode\s*\(/i',
            '/system\s*\(/i',
            '/exec\s*\(/i',
            '/shell_exec\s*\(/i',
            '/passthru\s*\(/i',
            '/proc_open\s*\(/i',
            '/popen\s*\(/i',
            '/curl_exec\s*\(/i',
            '/file_get_contents\s*\(/i',
            '/file_put_contents\s*\(/i',
            '/fopen\s*\(/i',
            '/fsockopen\s*\(/i',
            '/GuzzleHttp/i',
            '/\\\\x[0-9a-f]{2}/i',
        ];

        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                Log::warning('Contenido potencialmente peligroso detectado en archivo subido', [
                    'filename' => $file->getClientOriginalName(),
                    'pattern' => $pattern,
                ]);
                return true;
            }
        }

        return false;
    }

    protected function isSafeFileSize(UploadedFile $file, int $maxKb): bool
    {
        $sizeKb = $file->getSize() / 1024;
        return $sizeKb > 0 && $sizeKb <= $maxKb;
    }

    public function getValidExtensions(): array
    {
        return ['png', 'jpg', 'jpeg', 'gif', 'pdf', 'xlsx', 'xls'];
    }

    public function getMimeType(UploadedFile $file): string
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        return $finfo->file($file->getRealPath()) ?? 'unknown';
    }
}