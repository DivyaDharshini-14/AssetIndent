<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\IconTrait;

class AssetDocument extends Model
{
    use IconTrait;

    protected $table = 'asset_documents';

    const FILE_PATH = 'asset-docs';   

    protected $fillable = [
        'asset_id',
        'document_name',
        'filename',
        'document_notes',
        'created_by',
        'updated_by',
    ];  

    protected $casts = [
        'created_at'=>'datetime'
    ];

     protected $appends = ['doc_url', 'icon'];

    public function getDocUrlAttribute()
    {
        return !empty($this->filename) ? asset_url_local_s3(AssetDocument::FILE_PATH . '/' . $this->filename) : '';
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
