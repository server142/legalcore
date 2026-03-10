<?php

namespace App\Http\Controllers;

use App\Models\Asesoria;
use Illuminate\Http\Request;

class PublicAsesoriaController extends Controller
{
    public function show(string $token)
    {
        $asesoria = Asesoria::with(['abogado', 'cliente', 'tenant'])
            ->where('public_token', $token)
            ->firstOrFail();

        // Prevent showing deleted/cancelled? We still show but indicate status.
        $tenant = $asesoria->tenant;
        return view('public.asesoria-card', compact('asesoria', 'tenant'));
    }
}
