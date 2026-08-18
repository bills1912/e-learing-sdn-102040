<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class MataPelajaran extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'mata_pelajaran';

    protected $fillable = ['nama_mapel', 'deskripsi', 'icon'];
}
