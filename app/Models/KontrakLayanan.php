<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KontrakLayanan extends Model
{
    protected $table = 'tbl_kontrak_layanan';

    protected $fillable = [
        'id',
        'kontrak_id',
        'layanan_id',
        'produk_id',
        'jumlah_node'
    ];

    public $timestamps = false;

    public function kontrak()
    {
        return $this->belongsTo(Kontrak::class, 'kontrak_id', 'id');
    }

    public function kontrak_nodelink()
    {
        return $this->hasMany(KontrakNodeLink::class, 'kontrak_layanan_id', 'id');
    }
}
