<?php

namespace LBHurtado\BallotImage\Models;

use Illuminate\Support\Arr;
use RobbieP\ZbarQrdecoder\ZbarDecoder;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
//use App\Http\Requests\BallotImageRequest as Request;
use Spatie\SchemalessAttributes\SchemalessAttributes;
use LBHurtado\BallotImage\Exceptions\ZBarPathException;

class Image extends Model implements HasMedia
{
    use HasMediaTrait;

    const MEDIA_COLLECTION = 'ballots';

    protected $fillable = [
        'sender_mac_address',
        'qr_code',
    ];

    public $casts = [
        'extra_attributes' => 'array',
        'output' => 'array',
    ];

    /** @var \Imagick */
    protected $imagick;

    public function registerMediaCollections()
    {
        $this->addMediaCollection(self::MEDIA_COLLECTION)->singleFile();
    }

    public function transfuseQRCode()
    {
        if (!file_exists($path = config('ballot-image.zbar.path')))
            throw new ZBarPathException;

        tap(new ZbarDecoder(config('ballot-image.zbar.path')), function(ZbarDecoder $zbar) {
            tap($zbar->make($this->path), function ($result) {
                if (!empty($result->getText()))
                    $this->update(['qr_code' => $result->getText()]);
            });
        });

        return $this;
    }

    public function getMediaUrlAttribute()
    {
        return $this->getFirstMedia(self::MEDIA_COLLECTION)->getUrl();
    }

    public function getMediaPathAttribute()
    {
        return $this->getFirstMedia(self::MEDIA_COLLECTION)->getPath();
    }

    public function getMediaStorageFileNameAttribute()
    {
        $id = $this->getFirstMedia(self::MEDIA_COLLECTION)->id;
        $fn = $this->getFirstMedia(self::MEDIA_COLLECTION)->file_name;

        return "{$id}/{$fn}";
    }

    public function getMarkingsAttribute()
    {
        return Arr::get($this->extra_attributes, 'markings');
    }

    public function setMarkingsAttribute($value)
    {
        Arr::set($this->extra_attributes, 'markings', $value);
    }
}
