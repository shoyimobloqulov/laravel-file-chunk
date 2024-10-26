<?php
// app/Services/FileUploadService.php
namespace App\Services;

use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    protected $tempDir = 'temp/uploads/';

    public function saveChunk($file, $fileName, $chunkIndex)
    {
        $chunkPath = $this->tempDir . $fileName . '_' . $chunkIndex;
        Storage::put($chunkPath, file_get_contents($file->getRealPath()));
    }

    public function combineChunks($fileName, $totalChunks)
    {
        $finalPath = 'uploads/' . $fileName;
        $stream = Storage::disk('local')->writeStream($finalPath);

        for ($i = 0; $i < $totalChunks; $i++) {
            $chunkPath = $this->tempDir . $fileName . '_' . $i;
            $chunkStream = Storage::disk('local')->readStream($chunkPath);
            stream_copy_to_stream($chunkStream, $stream);
            fclose($chunkStream);

            // Remove the chunk after copying
            Storage::delete($chunkPath);
        }

        fclose($stream);
    }
}
