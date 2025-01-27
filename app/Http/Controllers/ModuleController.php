<?php

namespace App\Http\Controllers;

use App\Http\Requests\ModuleRequest;
use App\Models\Module;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class ModuleController extends Controller
{
    public function store(ModuleRequest $request): JsonResponse
    {
        $newModule = Module::create($request->validated());

        return response()->json(
            [
                'message' => 'Module created successfully!',
                'data' => $newModule->id
            ],
            201);
    }

    public function download(string $id): BinaryFileResponse|JsonResponse
    {
        $module = Module::findOrFail($id);

        $tempPath = storage_path('app/public/modules/' . $module->id);
        $zipPath = storage_path('app/public/modules/' . $module->id . '.zip');

        try {

            File::makeDirectory($tempPath, 775, true, true);

            File::put($tempPath . '/index.html', $this->getHTML());
            File::put($tempPath . '/style.css', $this->getCSS($module));
            File::put($tempPath . '/script.js', $this->getJS($module->link));

            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE) !== TRUE) {
                $error = $zip->getStatusString();
                throw new \Exception('Failed to create zip archive: ' . $error);
            }

            $zip->addFile($tempPath . '/index.html', 'index.html');
            $zip->addFile($tempPath . '/style.css', 'style.css');
            $zip->addFile($tempPath . '/script.js', 'script.js');
            $zip->close();
        } catch (\Exception $exception) {
            if (File::exists($tempPath)) {
                File::deleteDirectory($tempPath);
            }
            return response()->json(['message' => $exception->getMessage(), 'a'=>$exception->getTrace()], 500);
        }

        File::deleteDirectory($tempPath);

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    private function getHTML(): string
    {
        return "
<!DOCTYPE html>
<html lang='en'>
    <head>
        <link rel='stylesheet' href='style.css'>
        <script src='script.js' defer></script>
        <title>Models</title>
    </head>
    <body>
        <div class='module'>Download module</div>
    </body>
</html>";
    }

    private function getCSS(Module $module): string
    {
        return ".module {
        width: {$module->width};
        height: {$module->height};
        background-color: {$module->color};
        cursor: pointer;
        }";
    }

    private function getJS(string $link): string
    {
        return "document.querySelector('.module').addEventListener('click', function() {
        window.location.href = '{$link}';
        });";
    }

}
