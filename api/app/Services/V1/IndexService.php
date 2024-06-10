<?php

namespace App\Services\V1;

use App\Http\Requests\V1\StoreIndexRequest;
use App\Models\Book;
use App\Models\Index;
use Illuminate\Support\Facades\Validator;

class IndexService
{
    public static function store(array $indexes, int $bookId, ?int $parentIndexId = null)
    {
        foreach ($indexes as $index) {
            $indexEx = [
                'titulo' => filter_var($index['titulo'], FILTER_SANITIZE_STRING),
                'pagina' => $index['pagina'],
                'livro_id' => $bookId,
                'indice_pai_id' => $parentIndexId
            ];

            try {
                $validate = Validator::make($indexEx, StoreIndexRequest::internalRules())->validated();
                $bookIndex = Index::create($validate);
                if (isset($index['subindices'])) {
                    self::store($index['subindices'], $bookId, $bookIndex->id);
                }
            } catch (\Exception $e) {
                throw new \Exception('error while creating index');
            }
        }
    }
}
