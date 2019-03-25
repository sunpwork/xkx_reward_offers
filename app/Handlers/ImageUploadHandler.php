<?php

namespace App\Handlers;

use Illuminate\Support\Facades\Storage;
use Image;

class ImageUploadHandler
{
    protected $allowed_ext = ['png', 'jpg', 'gif', 'jpeg'];

    public function save($file, $folder, $max_width = false)
    {
        //获取文件后缀(粘贴图片无后缀，默认为png)
        $extension = strtolower($file->getClientOriginalExtension()) ?: 'png';

        if (!in_array($extension, $this->allowed_ext)) {
            return false;
        }

        $folder_name = "uploads/images/$folder/" . date("Ym/d", time());

//        $file->move($upload_path, $filename);
//
//        if ($max_width && $extension != 'gif') {
//            $this->reduceSize("$upload_path/$filename", $max_width);
//        }

        $disk = Storage::disk('qiniu');
        $filepath = $disk->put($folder_name, $file);

        if ($filepath) {
            return [
                'path' => $disk->getUrl($filepath),
            ];
        } else {
            return false;
        }
    }

    public function reduceSize($file_path, $max_width)
    {
        $image = Image::make($file_path);

        $image->resize($max_width, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $image->save();
    }
}