<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;

class LegalProject extends Model
{
    use SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'legal_workflow_id',
        'cliente_id',
        'expediente_id',
        'title',
        'status',
        'current_step',
        'data',
        'progress'
    ];

    protected $casts = [
        'data' => 'array',
        'current_step' => 'integer',
        'progress' => 'integer'
    ];

    public function workflow()
    {
        return $this->belongsTo(LegalWorkflow::class, 'legal_workflow_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function expediente()
    {
        return $this->belongsTo(Expediente::class);
    }
}
