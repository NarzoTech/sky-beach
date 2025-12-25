<?php

namespace Modules\Language\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Stichoza\GoogleTranslate\GoogleTranslate;

class TranslationController extends Controller
{
    public function translateAll(Request $request)
    {
        if (checkAdminHasPermission('language.translate')) {
            $filePath = base_path('lang/'.$request->code.'.json');
            if (File::exists($filePath)) {
                $texts = json_decode(html_entity_decode($request->texts), true);

                $existingData = json_decode(File::get($filePath), true);
                $tr = new GoogleTranslate($request->code);
                foreach ($texts as $index => $value) {
                    $existingData[$index] = $tr->translate($value) ?? $value;
                }

                File::put($filePath, json_encode($existingData, JSON_PRETTY_PRINT));

                return response()->json([
                    'success' => true,
                    'message' => __('All texts translated successfully!'),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => __('File Not Found!'),
            ], 404);
        }

        return response()->json([
            'success' => false,
            'message' => __('Permission Denied!'),
        ], 403);
    }

    public function translateSingleText(Request $request)
    {
        if (checkAdminHasPermission('language.single.translate')) {
            $tr = new GoogleTranslate($request->lang);
            $afterTrans = $tr->translate($request->text);

            return response()->json($afterTrans);
        }

        return response()->json(__('Permission Denied!'), 403);
    }
}
