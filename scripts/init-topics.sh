#!/bin/bash

sleep 10  # Aguarda Kafka iniciar

while IFS= read -r topic || [[ -n "$topic" ]]; do
  if [ ! -z "$topic" ]; then
    echo "🔧 Criando tópico: $topic"
    kafka-topics --create \
      --if-not-exists \
      --topic "$topic" \
      --partitions 1 \
      --replication-factor 1 \
      --bootstrap-server kafka:9092
  fi
done < /topics/kafka-topics.txt
