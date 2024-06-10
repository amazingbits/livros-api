<?php

namespace App\Services\V1;

use App\Models\Index;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class XMLService
{
    public static function import($item, $bookId, $previousItem = null): void
    {
        $validator = Validator::make((array)$item['@attributes'], [
            'titulo' => 'required|string',
            'pagina' => 'required|integer',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $index = new Index();
        $index->livro_id = $bookId;
        $index->indice_pai_id = $previousItem;
        $index->titulo = $item['@attributes']['titulo'];
        $index->pagina = $item['@attributes']['pagina'];
        $index->save();

        if (isset($item['item']) && count($item['item']) > 0) {
            collect($item['item'])->each(function ($item) use ($bookId, $previousItem, $index) {
                self::import($item, $bookId, $index->id);
            });
        }
    }
}
