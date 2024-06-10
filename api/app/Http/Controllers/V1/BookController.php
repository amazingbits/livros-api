<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreBookRequest;
use App\Jobs\V1\ImportIndexXMLJob;
use App\Models\Book;
use App\Services\V1\IndexService;
use App\Services\V1\XMLService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

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

    public function importIndexByXML(Request $request, string $livroId)
    {
        $bookId = (int)$livroId;

        $book = Book::find($bookId);
        if (!$book) {
            return response()->json([
                'error' => 'book not found'
            ], 404);
        }

        if (!$request->hasFile('xml')) {
            return response()->json([
                'error' => 'this requisition needs a valid XML file'
            ], 400);
        }

        $xmlFile = $request->file('xml');

        if (!$xmlFile->isValid()) {
            return response()->json([
                'error' => 'xml file is invalid'
            ], 400);
        }

        $xmlFilePath = $xmlFile->storeAs(
            'uploads/books/' . $bookId . '/xml/',
            uniqid() . '.xml'
        );

        // Vai falhar por questões de diretório do Docker
        ImportIndexXMLJob::dispatch($bookId, $xmlFilePath);

        // Devido a este problema, fiz a solução que o Job faria se estivesse rodando localmente
        try {

            DB::beginTransaction();

            $filePath = storage_path('app/' . $xmlFilePath);
            $xmlString = file_get_contents($filePath);
            $xml = simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOCDATA);
            $collection = collect(json_decode(json_encode($xml), true)['item']);

            $collection->each(function ($item) use ($bookId) {
                XMLService::import($item, $bookId);
            });

            DB::commit();

            return response()->json([
                'message' => 'XML file was successfully imported'
            ]);

        } catch (ValidationException $e) {

            DB::rollBack();
            return response()->json([
                'error' => 'invalid XML structure'
            ], 422);

        } catch (\Exception $e) {

            DB::rollBack();
            return response()->json([
                'error' => 'a problem occurred while importing XML file'
            ], 500);
        }
    }
}
