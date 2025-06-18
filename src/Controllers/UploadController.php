<?php

namespace UploadX\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use UploadX\Core\UploadHandler;

class UploadController extends Controller
{
    public function __invoke(Request $request)
    {
        return (new UploadHandler())->handle($request);
    }
}
