<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Income extends Model
{
    use SoftDeletes;

    protected $table = 'incomes';
    protected $guarded = [];

    public function journal_entry() {
        return $this->morphOne(JournalEntry::class, 'transactable');
    }
}
