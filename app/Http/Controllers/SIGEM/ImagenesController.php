<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;

class ImagenesController extends Controller
{
    public function index()
    {
        $archivos = File::files(public_path());

        $imagenes = [];

        foreach ($archivos as $file) {
            $ext = strtolower($file->getExtension());
            if (in_array($ext, ['png', 'jpg', 'jpeg', 'gif'])) {
                $imagenes[] = $file->getFilename(); // ejemplo: sige1.png
            }
        }

        return view('imagenes.index', ['imagenes' => $imagenes]);
    }
}
