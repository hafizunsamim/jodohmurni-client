<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class TranslationController extends Controller
{
    private function filePath(): string
    {
        // ikut file kau: resources/lang/messages.php
        return resource_path('lang/messages.php');
    }

    public function show()
    {
        $path = $this->filePath();

        if (!File::exists($path)) {
            return response()->json([
                'ok' => false,
                'message' => 'messages.php not found',
            ], 404);
        }

        $data = include $path;

        if (!is_array($data)) {
            return response()->json([
                'ok' => false,
                'message' => 'messages.php did not return an array',
            ], 500);
        }

        return response()->json([
            'ok' => true,
            'data' => $data,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'data' => ['required', 'array'],
        ]);

        $path = $this->filePath();

        // backup
        $backupDir = storage_path('app/lang_backups');
        File::ensureDirectoryExists($backupDir);
        if (File::exists($path)) {
            File::copy($path, $backupDir.'/messages_'.date('Ymd_His').'.php');
        }

        $payload = $request->input('data');

        $php = "<?php\n\nreturn " . var_export($payload, true) . ";\n";
        File::put($path, $php);

        return response()->json([
            'ok' => true,
            'message' => 'messages.php updated',
        ]);
    }
}
