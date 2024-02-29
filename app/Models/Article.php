<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\Conversions\Conversion;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\FileAdder;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @method void prepareToAttachMedia(Media $media, FileAdder $fileAdder)
 */
class Article extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    protected $table='articles';

    protected $fillable=[
        'title',
        'description',
    ];


}
