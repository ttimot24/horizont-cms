<?php

namespace App\Controllers\Trait;

use TypeError;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

trait UploadsImage {

    protected $form_field_name = 'up_file';

    private function getMaxImageSize(): int {
        return config('horizontcms.max_upload_file_size', 2560); // 2.5 MB
    }

    private function getStrippedDirectoryPath($model): string {
        return str_replace('storage/', '', $model->getImageDirectory());
    }

    public function uploadImage($model): bool {

        if(!in_array('App\Model\Trait\HasImage', class_uses($model, true))){
           throw new TypeError('Class['.get_class($model).'] does not use HasImage trait');
        }

        if (request()->hasFile($this->form_field_name)) {

            request()->validate([
                $this->form_field_name => 'nullable|image|max:' . $this->getMaxImageSize(),
            ]);

            File::ensureDirectoryExists($model->getImageDirectory());
            $img = request()->{$this->form_field_name}->store($this->getStrippedDirectoryPath($model));
            $model->attachImage($img);
            if (extension_loaded('gd') && starts_with(request()->{$this->form_field_name}->getMimeType(), 'image/')) {
		        File::ensureDirectoryExists($model->getImageDirectory() . '/thumbs');
                (new ImageManager(new Driver()))->read(storage_path($img))->resize(300, 200)->save($model->getThumbnailDirectory(). DIRECTORY_SEPARATOR . $model->image);
            }

            return true;
        }
        
        return false;
    }

}
