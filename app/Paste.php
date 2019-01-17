<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \Carbon\Carbon;

class Paste extends Model
{
    protected $hidden = [
      'id', 'updated_at', 'is_encrypted', 'ip',
      'user_id', 'expires_at', 'created_at'
    ];

    public function getMetadataAttribute($value)
    {
      return json_decode($value);
    }

    public function scopeValid()
    {
      return $this->where('expires_at', '>', Carbon::now());
    }
}
