<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $table = 'livros';
    protected $fillable = ['usuario_publicador_id', 'titulo'];

    protected $hidden = ['id', 'usuario_publicador_id', 'created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'usuario_publicador_id', 'id');
    }

    public function indexes()
    {
        return $this->hasMany(Index::class, 'livro_id')->whereNull('indice_pai_id')->with('subIndexes');
    }

    public function subIndexes()
    {
        return $this->hasMany(Index::class, 'livro_id')->whereNull('indice_pai_id')->with('subIndexes');
    }
}
