<?php

namespace App\Http\Controllers;

use FileUploader;
use Illuminate\Http\Request;

class VideoController extends Controller
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
     * @return void
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
