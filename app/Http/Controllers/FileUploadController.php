<?php

// app/Http/Controllers/FileUploadController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FileUploadService;

class FileUploadController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    public function uploadChunk(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
            'fileName' => 'required|string',
            'chunkIndex' => 'required|integer',
            'totalChunks' => 'required|integer'
        ]);

        $file = $request->file('file');
        $fileName = $request->fileName;
        $chunkIndex = $request->chunkIndex;
        $totalChunks = $request->totalChunks;

        $this->fileUploadService->saveChunk($file, $fileName, $chunkIndex);

        if ($chunkIndex + 1 === $totalChunks) {
            $this->fileUploadService->combineChunks($fileName, $totalChunks);
        }

        return response()->json(['status' => 'Chunk uploaded successfully']);
    }
}
