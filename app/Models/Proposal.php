<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'customer_title',
        'customer_email',
        'pdf_link',
        'total'
    ];

    public function items()
    {
        return $this->hasMany(ProposalItem::class);
    }
}
