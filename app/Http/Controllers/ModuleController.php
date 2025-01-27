<?php

namespace App\Http\Controllers;

use App\Http\Requests\ModuleRequest;
use App\Models\Module;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class ModuleController extends Controller
{
    /**
     * Data validated by form request is saved in the database.
     * It returns a message about creating a new module along with the new module id. The response status code is 201.
     * @param ModuleRequest $request
     * @return JsonResponse
     */
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

    /**
     * @param string $id
     * @return BinaryFileResponse|JsonResponse
     */
    public function download(string $id): BinaryFileResponse|JsonResponse
    {
        /**
         * If the given id is incorrect, it returns error.
         */
        try{
            $module = Module::findOrFail($id);
        } catch (ModelNotFoundException $e){
            return response()->json(['message' => 'Module not found'], 404);
        }

        /**
         * Create paths for temporary files and for zip folder. The folder name is module id.
         */
        $tempPath = storage_path('app/public/modules/' . $module->id);
        $zipPath = storage_path('app/public/modules/' . $module->id . '.zip');

        try {
            /**
             * The makeDirectory method will create a folder with the given path $tempPath.
             * It will be created even if:
             * the transitive folder does not exist or if the folder already exists.
             */
            File::makeDirectory($tempPath, 775, true, true);

            /**
             * The put method will create files with the given names and the given content.
             * If the file already exists, its content will be modified.
             */
            File::put($tempPath . '/index.html', $this->getHTML());
            File::put($tempPath . '/style.css', $this->getCSS($module));
            File::put($tempPath . '/script.js', $this->getJS($module->link));

            $zip = new ZipArchive();

            /**
             * If the ZIP archive cannot be created, an exception will be thrown.
             */
            if ($zip->open($zipPath, ZipArchive::CREATE) !== TRUE) {
                $error = $zip->getStatusString();
                throw new \Exception('Failed to create zip archive: ' . $error);
            }

            /**
             * Add files from a temporary folder to a ZIP archive.
             */
            $zip->addFile($tempPath . '/index.html', 'index.html');
            $zip->addFile($tempPath . '/style.css', 'style.css');
            $zip->addFile($tempPath . '/script.js', 'script.js');

            /**
             * Close an open ZIP archive.
             */
            $zip->close();
        } catch (\Exception $exception) {
            /**
             * If an error occurred and a folder exists in $temp Path it will be deleted.
             */
            if (File::exists($tempPath)) {
                File::deleteDirectory($tempPath);
            }
            /**
             * Return a response with the error content.
             */
            return response()->json(['message' => $exception->getMessage()], 500);
        }

        /**
         * Deletes the temporary folder from the given path.
         */
        File::deleteDirectory($tempPath);

        /**
         * Return a ZIP archive and delete the ZIP archive after it has been sent from the server.
         */
        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    /**
     * @return string
     */
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

    /**
     * @param Module $module
     * @return string
     */
    private function getCSS(Module $module): string
    {
        return ".module {
        width: {$module->width};
        height: {$module->height};
        background-color: {$module->color};
        cursor: pointer;
        }";
    }

    /**
     * @param string $link
     * @return string
     */
    private function getJS(string $link): string
    {
        return "document.querySelector('.module').addEventListener('click', function() {
        window.location.href = '{$link}';
        });";
    }

}
