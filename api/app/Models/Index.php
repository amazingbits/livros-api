<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Index extends Model
{
    use HasFactory;

    protected $table = 'indices';
    protected $fillable = ['livro_id', 'indice_pai_id', 'titulo', 'pagina'];

    protected $hidden = ['livro_id', 'indice_pai_id', 'created_at', 'updated_at'];

    public function book()
    {
        return $this->belongsTo(Book::class, 'livro_id', 'id');
    }

    public function indexes()
    {
        return $this->hasMany(Index::class, 'livro_id', 'id');
    }

    public function subIndexes()
    {
        return $this->hasMany(Index::class, 'indice_pai_id')->with('subIndexes');
    }
}
