<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class ShopController extends Controller
{
    public function share(string $id)
    {
        return ResponseHelper::jsonWithData(200, 'User data retrieved successfully',  [
            'share_link' => URL::to('/shop/' . $id),
        ]);
    }
}
