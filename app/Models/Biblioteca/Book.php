<?php

namespace App\Models\Biblioteca;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $table = 'books';

    protected $fillable = [
        'idbiblioteca',
        'numadqui',
        'ficha_no',
        'titulo',
        'autor',
        'editorial',
        'isbn',
        'clasificacion',
        'fechaingreso',
        'notas_conservacion',
        'portada',
        'tipo_material',
        'created_by',
        'updated_by',
    ];
    
    protected $primaryKey = 'id';
    public $incrementing = true;      
    protected $keyType = 'int';

    public $timestamps = false;
}