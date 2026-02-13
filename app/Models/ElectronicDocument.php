<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class ElectronicDocument extends Model
{
    use BelongsToCompany;
    
    protected $fillable = [
        'company_id', 'sale_id', 'clave_acceso', 'document_type',
        'xml_content', 'pdf_path', 'status', 'authorization_date'
    ];
    
    public function company() { return $this->belongsTo(Company::class); }
    public function sale() { return $this->belongsTo(Sale::class); }
}
