<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\RealNameAuthRequest;
use App\Models\Image;
use App\Models\RealNameAuth;
use App\Transformers\RealNameAuthTransformer;
use Illuminate\Http\Request;

class RealNameAuthsController extends Controller
{
    public function store(RealNameAuthRequest $request,RealNameAuth $realNameAuth)
    {
        if ($this->user()->realNameAuth){
            $realNameAuth = $this->user()->realNameAuth;
            $realNameAuth->update($request->all());
            $realNameAuth->realNameAuthImages()->delete();
        }else {
            $realNameAuth->fill($request->all());
            $realNameAuth->user_id = $this->user()->id;
            $realNameAuth->save();
        }
        foreach ($request->image_ids as $image_id) {
            $image = Image::find($image_id);
            $realNameAuth->realNameAuthImages()->create([
                'url' => $image->path,
            ]);
        }
        return $this->response->item($realNameAuth, new RealNameAuthTransformer())->setStatusCode(201);
    }

    public function show()
    {
        $realNameAuth = $this->user()->realNameAuth;
        return $this->response->item($realNameAuth, new RealNameAuthTransformer())->addMeta('statusMap',RealNameAuth::STATUSES);
    }
}
