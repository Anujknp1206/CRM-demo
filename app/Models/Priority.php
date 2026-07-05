<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Priority extends Model
{
    protected $fillable = ['name', 'level'];
    public function boms()
    {
        return $this->hasMany(Bom::class);
    }
}
