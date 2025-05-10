<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ConsumeKafkaDemands extends Command
{
    protected $signature = 'kafka:consume-demands';
    protected $description = 'Consome demandas do Kafka e salva no SQLite';

    public function handle()
    {
        $conf = new \RdKafka\Conf();
        $conf->set('metadata.broker.list', env('KAFKA_BROKER'));
        $conf->set('auto.offset.reset', 'earliest');
        $conf->set('group.id', 'ondutydoctor-consumer');

        $consumer = new \RdKafka\KafkaConsumer($conf);
        $consumer->subscribe(['demands.created']);

        $this->info('📥 Aguardando mensagens...');

        while (true) {
            $message = $consumer->consume(120 * 1000);

            if ($message->err) {
                $this->error($message->errstr());
                continue;
            }

            $data = json_decode($message->payload, true);

            \App\Models\Demand::create([
                'external_id' => $data['id'] ?? null,
                'type' => $data['specialty'] ?? 'unknown',
                'status' => 'pending',
                'payload' => $data,
            ]);

            $this->info('✔️ Demanda salva com sucesso');
        }
    }

}
