<?php

namespace App\Http\Controllers;

use App\Models\Article;
use FileUploader;
use Illuminate\Http\Request;

class ExampleController extends Controller
{

    /**
     * show the form
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index() {
        return view('home');
    }

    /**
     * submit the form
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function submit(Request $request) {
        // initialize FileUploader
        $FileUploader = new FileUploader('files', array(
            // options
            'limit' => 4,
            'uploadDir' => storage_path('app/public/'),
            'title' => 'auto'
        ));

        // upload
        $upload = $FileUploader->upload();
        $yourModel = Article::query()->first();


        foreach ($upload['files'] as $file){
            $yourModel->addMedia($file['file'])
                ->toMediaCollection();

        }


    }

    /**
     * delete a file
     *
     * @return void
     */
    public function removeFile(Request $request) {
        unlink($_POST['file']);
        exit;
    }
    //
}
