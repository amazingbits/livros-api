<?php

namespace App\Jobs\V1;

use App\Services\V1\XMLService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ImportIndexXMLJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public int    $bookId,
        public string $fileName
    )
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $file = storage_path('app/' . $this->fileName);

        if (!file_exists($file)) {
            Log::error('file does not exist: ' . $file);
        }

        try {

            DB::beginTransaction();

            $filePath = storage_path('app/' . $file);
            $xmlString = file_get_contents($filePath);
            $xml = simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOCDATA);
            $collection = collect(json_decode(json_encode($xml), true)['item']);

            $bookId = $this->bookId;
            $collection->each(function ($item) use ($bookId) {
                XMLService::import($item, $bookId);
            });

            DB::commit();

        } catch (ValidationException $e) {

            DB::rollBack();
            Log::error('XML format is incorrect: ' . $e->getMessage());

        } catch (\Exception $e) {

            DB::rollBack();
            Log::error('Exception: ' . $e->getMessage());
        }
    }
}
