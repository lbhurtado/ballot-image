<?php

namespace LBHurtado\BallotImage\Models;

use Illuminate\Support\Arr;
use RobbieP\ZbarQrdecoder\ZbarDecoder;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
//use App\Http\Requests\BallotImageRequest as Request;
use Spatie\SchemalessAttributes\SchemalessAttributes;

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
        tap(new ZbarDecoder(config('image.zbar')), function(ZbarDecoder $zbar) {
            tap($zbar->make($this->path), function ($result) {
                if (!empty($result->getText()))
                    $this->update(['qr_code' => $result->getText()]);
            });
        });

        return $this;
    }
}
