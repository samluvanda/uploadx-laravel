<?php

namespace UploadX\Core;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UploadHandler
{
    public function handle(Request $request)
    {
        $profileName = $request->header('UploadX-Profile', 'default');
        $fieldName = $request->header('UploadX-File-Field', 'file');

        $config = config("uploadx.profiles.$profileName");

        if (!$config) {
            $config = include __DIR__ . '/../../config/uploadx.php';
            $config = $config['profiles'][$profileName] ?? $config['profiles']['default'];
        }

        $rules = $config['validate'] ?? [];

        if ($fieldName !== 'file' && isset($rules['file'])) {
            $rules[$fieldName] = $rules['file'];
            unset($rules['file']);
        }

        Validator::make($request->all(), $rules)->validate();

        $file = $request->file($fieldName);
        $chunk = (int) $request->input('chunk', 0);
        $chunks = (int) $request->input('chunks', 1);
        $name = $request->input('name', $file->getClientOriginalName());

        $disk = $config['disk'] ?? 'local';
        $path = trim($config['path'] ?? 'uploads', '/');
        $combine = $config['combine_chunks'] ?? true;
        $extension = $file->getClientOriginalExtension();
        $baseName = pathinfo($name, PATHINFO_FILENAME);
        $chunkPath = "$path/chunks";

        if ($chunk === 0) {
            $prefix = "$baseName.$extension.";
            $existingChunks = Storage::disk($disk)->files($chunkPath);

            foreach ($existingChunks as $existing) {
                if (str_starts_with(basename($existing), $prefix)) {
                    Storage::disk($disk)->delete($existing);
                }
            }
        }

        $chunkFileName = "$baseName.$extension.$chunk";

        $file->storeAs($chunkPath, $chunkFileName, $disk);

        if ($combine && $chunk === $chunks - 1) {
            $stream = fopen('php://temp', 'w+b');

            for ($i = 0; $i < $chunks; $i++) {
                $chunkName = "$baseName.$extension.$i";
                $chunkContent = Storage::disk($disk)->get("$chunkPath/$chunkName");
                fwrite($stream, $chunkContent);
            }

            rewind($stream);
            Storage::disk($disk)->put("$path/$name", $stream);
            fclose($stream);

            for ($i = 0; $i < $chunks; $i++) {
                Storage::disk($disk)->delete("$chunkPath/$baseName.$extension.$i");
            }

            return response()->json([
                'OK' => 1,
                'info' => 'Upload complete.',
            ]);
        }

        return response()->json([
            'OK' => 1,
            'info' => 'Chunk uploaded.',
        ]);
    }
}
