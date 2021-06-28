<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function checkToken(){
        if(! Auth::guard("sanctum")->check()){
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode([
                "error" => [
                    "code" => 401,
                    "message" => "Unauthorized!"
                ]
            ]);
            exit();
        }
    }
}
