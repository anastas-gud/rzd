<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $table = 'documents';
    protected $fillable = ['date_of_birth','serial','number','type_of_document','created_at','updated_at'];
    protected $casts = ['date_of_birth' => 'date'];
}
