<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreBookRequest;
use App\Models\Book;
use App\Services\V1\IndexService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    public function store(StoreBookRequest $request)
    {
        $params = $request->validated();

        try {

            DB::beginTransaction();

            $book = Book::create([
                'titulo' => filter_var($params['titulo'], FILTER_SANITIZE_STRING),
                'usuario_publicador_id' => auth()->id(),
            ]);

            if (isset($params['indices'])) {
                IndexService::store($params['indices'], $book->id);
            }

            DB::commit();

            return response()->json($book, 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'store book error'
            ], 500);
        }
    }

    public function update()
    {
        // TODO: implement
    }

    public function delete(string $bookId)
    {
        // TODO: implement
    }

    public function get(Request $request)
    {
        $title = $request->query('descricao');
        $indexTitle = $request->query('titulo_do_indice');

        $books = Book::with(['user']);


        if ($title) {
            $books->where('titulo', 'LIKE', '%' . $title . '%');
        }

        if ($indexTitle) {
            $books->with(['indexes' => function ($query) use ($indexTitle) {
                $query->where('titulo', 'LIKE', '%' . $indexTitle . '%')
                    ->orWhereHas('subIndexes', function ($query) use ($indexTitle) {
                        $query->where('titulo', 'LIKE', '%' . $indexTitle . '%');
                    })->with('subIndexes');
            },
                'indexes.subIndexes' => function ($query) use ($indexTitle) {
                    $query->where('titulo', 'LIKE', '%' . $indexTitle . '%')->with('subIndexes');
                }]);
        } else {
            $books->with(['indexes.subIndexes']);
        }

        return response()->json($books->get());
    }
}
