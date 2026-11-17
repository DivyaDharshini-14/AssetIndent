<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusSource extends Model
{
    
    protected $fillable = ['lead_source_id', 'lead_status_id'];

    public function leadSource()
    {
        return $this->belongsTo(LeadSource::class);
    }

    public function leadStatus()
    {
        return $this->belongsTo(LeadStatus::class);
    }

}
