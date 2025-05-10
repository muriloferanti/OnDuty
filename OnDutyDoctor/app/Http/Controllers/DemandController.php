<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Demand;

class DemandController extends Controller
{
    public function index()
    {
        return view('demands.index');
    }

    public function assume(Request $request, $id)
    {
        $demand = Demand::findOrFail($id);
        $demand->status = 'assumed';
        $demand->save();
    
        $message = [
            'id' => $demand->id,
            'assumedAt' => now()->toIso8601String(),
            'ip' => $request->ip(),
        ];
    
        app(\App\Services\KafkaService::class)->publish('demands.assumed', $message);
    
        return response()->json(['status' => 'ok']);
    }
    

    public function ignore($id)
    {
        $demand = Demand::findOrFail($id);
        $demand->status = 'ignored';
        $demand->save();

        return response()->json(['message' => 'Demanda ignorada']);
    }

}
