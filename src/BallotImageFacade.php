<?php

namespace LBHurtado\BallotImage;

use Illuminate\Support\Facades\Facade;

/**
 * @see \LBHurtado\BallotImage\Skeleton\SkeletonClass
 */
class BallotImageFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'ballotimage';
    }
}
