<?php

namespace Modules\Language\app\Traits;

use Exception;
use Illuminate\Support\Facades\Log;

trait LanguageTrait
{
    private function deleteFolder($folderPath)
    {
        if (is_dir($folderPath)) {
            $files = scandir($folderPath);
            foreach ($files as $file) {
                if ($file != '.' && $file != '..') {
                    $this->deleteFolder($folderPath.'/'.$file);
                }
            }
            rmdir($folderPath);
        } else {
            unlink($folderPath);
        }
    }

    private function copyPasteFile($code, $file, $dataArray)
    {
        $originalFileContent = file_get_contents(base_path("lang/{$code}/{$file}.php"));

        try {
            file_put_contents(base_path("lang/{$code}/{$file}.php"), '');
            $dataArray = var_export($dataArray, true);
            file_put_contents(base_path("lang/{$code}/{$file}.php"), "<?php\n return {$dataArray};\n ?>");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            file_put_contents(base_path("lang/{$code}/{$file}.php"), $originalFileContent);
        }

    }
}
