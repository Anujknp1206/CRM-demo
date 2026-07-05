<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProductionStatus extends Model
{
    protected $fillable = ['company_id', 'name', 'badge_color', 'sort_order', 'default_progress'];

    public function progresses()
    {
        return $this->hasMany(
            BomPartStageProgress::class,
            'status_id'
        );
    }
}