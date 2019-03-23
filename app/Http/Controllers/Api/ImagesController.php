<?php

namespace App\Http\Controllers\Api;

use App\Handlers\ImageUploadHandler;
use App\Http\Requests\Api\ImageRequest;
use App\Models\Image;
use App\Transformers\ImageTransformer;
use Illuminate\Http\Request;
use League\Flysystem\Filesystem;

class ImagesController extends Controller
{
    public function store(ImageRequest $request, ImageUploadHandler $uploadHandler, Image $image)
    {
        $user = $this->user();
        $result = $uploadHandler->save($request->image,$request->type);
        $image->path = $result['path'];
        $image->type = $request->type;
        $image->user_id = $user->id;
        $image->save();

        return $this->response->item($image, new ImageTransformer())->setStatusCode(201);
    }
}
