<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KontrakNodelink extends Model
{
    protected $table = 'tbl_kontrak_nodelink';

    protected $fillable = [
        'id',
        'kontrak_id',
        'nodelink_id',
        'nama_perusahaan',
        'latitude',
        'longitude',
        'total_biaya',
        'created_date',
    ];

    public $timestamps = false;

    public function kontrak_layanan()
    {
        return $this->belongsTo(KontrakLayanan::class, 'kontrak_layanan_id', 'id');
    }

    public function nodelink()
    {
        return $this->belongsTo(Nodelink::class, 'nodelink_id', 'id');
    }

    public function billing_revenue()
    {
        return $this->hasMany(BillingRevenue::class, 'kontrak_nodelink_id', 'id');
    }
}
