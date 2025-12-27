<?php

namespace App\Model\Trait;

trait HasImage
{
    
    public function attachImage(string $image): void
    {
       if ($this->isUrl($image)) {
            $this->image = $image;
        } else {
            $this->image = basename($image);
        }
    }

    public function getImageDirectory(): string
    {

        return empty($this->imageDir) ? 'storage/images/' . $this->getTable() : rtrim($this->imageDir, DIRECTORY_SEPARATOR);
    }

    public function getThumbnailDirectory(): string
    {
        return $this->getImageDirectory() . DIRECTORY_SEPARATOR . 'thumbs';
    }

    public function hasImage(): bool
    {
        return (isset($this->image) && !empty($this->image));
    }

    public function imageFileExists(): bool
    {
        return $this->hasImage() && file_exists($this->getImageDirectory() . DIRECTORY_SEPARATOR . $this->image);
    }

    public function thumbnailFileExists(): bool
    {
        return $this->hasImage() && file_exists($this->getThumbnailDirectory() . DIRECTORY_SEPARATOR . $this->image);
    }

    public function getImageFilePath(): string
    {
        return $this->getImageDirectory() . DIRECTORY_SEPARATOR . $this->image;
    }

    public function getThumbnailFilePath(): string
    {
        return $this->getThumbnailDirectory() . DIRECTORY_SEPARATOR . $this->image;
    }

    public function getThumb(): string
    {
        if($this->isUrl($this->image)){
            return $this->image;
        }

        if ($this->thumbnailFileExists()) {
            return url($this->getThumbnailFilePath());
        } else {
            return $this->getImage();
        }
    }

    public function getImage(): string
    {
        if($this->isUrl($this->image)){
            return $this->image;
        }

        if ($this->imageFileExists()) {
            return url($this->getImageFilePath());
        } else {
            return url($this->getDefaultImage());
        }
    }

    public function getFeaturedMediaType(): string {
        return rescue(fn() => explode('/', mime_content_type($this->getImageFilePath()))[0], 'image');
    }

    public function getDefaultImage(): string
    {
        return $this->defaultImage;
    }

    public function setDefaultImage($image): void
    {
        $this->defaultImage = $image;
    }

    public function isUrl($string): bool {
        return filter_var($string, FILTER_VALIDATE_URL);
    }

}
