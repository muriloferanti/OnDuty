<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\KafkaService;

class DemandController extends Controller
{
    public function __construct(protected KafkaService $kafkaService) {}

    public function store(Request $request)
    {
        $demand = [
            'hospitalId' => $request->input('hospitalId'),
            'specialty' => $request->input('specialty'),
            'urgency' => $request->input('urgency'),
            'timestamp' => now()->toIso8601String(),
        ];

        $sent = $this->kafkaService->publish('demands.created', $demand);

        if (! $sent) {
            return response()->json(['error' => 'Falha ao enviar demanda para o Kafka'], 500);
        }

        return response()->json(['message' => 'Demanda enviada com sucesso', 'data' => $demand]);
    }
}
