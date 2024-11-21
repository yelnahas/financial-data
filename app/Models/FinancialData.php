<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialData extends Model
{
    protected $table = 'financial_data';

    protected $fillable = ['branch_id', 'date', 'bilance'];
}
