<?php

namespace RapideSoftware\SyncStack\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $path
 * @property int $batch
 */

class Synchronisation extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];
}
