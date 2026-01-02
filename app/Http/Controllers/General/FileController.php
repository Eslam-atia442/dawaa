<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @group Admin
 * @subgroup Files
 */
class FileController extends Controller
{

    public function  destroy($id)
    {
         Media::find($id)->delete();
         return response()->json(['message' => 'File deleted successfully']);
    }

}
