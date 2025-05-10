# OnDutyDoctor

Laravel Kafka Consumer Service — Consome a fila de demandas do Kafka (`demands.created`) e salva localmente usando SQLite.

## 🧩 Visão geral

Este serviço faz parte do ecossistema distribuído do projeto **OnDutyNow**, composto por múltiplos containers. O `OnDutyDoctor` é responsável por:

- Consumir mensagens do Kafka (`demands.created`)
- Persistir os dados localmente em SQLite
- Rodar como worker autônomo (sem necessidade de banco ou serviços externos)

---

## 📦 Estrutura do Container

Baseado em `php:8.2-cli`, com:

- Extensões: `pdo`, `pdo_mysql`, `zip`, `rdkafka`
- Kafka client: [php-rdkafka (PECL)](https://github.com/arnaud-lb/php-rdkafka)
- Banco de dados local: `SQLite`
- Comando principal: `php artisan kafka:consume-demands`

---

## 🚀 Como subir o container

1. Build do container:

```bash
docker-compose build ondutydoctor
```

2. Subir:

```bash
docker-compose up -d ondutydoctor
```

O container executa automaticamente:

- `php artisan serve --host=0.0.0.0 --port=8001` (background)
- `php artisan kafka:consume-demands` (consumer principal)

---

## 📂 Estrutura relevante

```bash
OnDutyDoctor/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       └── ConsumeKafkaDemands.php  # Comando Artisan que consome Kafka
│   └── Models/
│       └── Demand.php                   # Model para persistência dos dados
├── database/
│   ├── migrations/
│   │   └── xxxx_create_demands_table.php
│   └── database.sqlite                  # Arquivo SQLite
├── entrypoint.sh                        # Script que inicia o consumer
├── Dockerfile                           # Dockerfile PHP + Kafka
└── ...
```

---

## 🛠️ Configuração `.env`

```env
DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/database.sqlite
APP_ENV=local
APP_DEBUG=true
KAFKA_BROKER=kafka:9092
```

---

## 💡 Observações

- O `php-rdkafka` é uma extensão PECL, e por isso o VS Code pode acusar `Undefined type 'RdKafka\Conf'`. Isso é normal.
- As mensagens são persistidas no SQLite em JSON (`payload`) junto com `type` e `external_id`.

---

## 📈 Exemplo de payload salvo

```json
{
  "id": "req-123",
  "type": "emergency",
  "location": {
    "lat": -22.9,
    "lng": -43.2
  },
  "metadata": {
    "priority": "high"
  }
}
```

---

## 🔍 Debug

Verifique os logs do container:

```bash
docker logs -f ondutydoctor
```

Para inspecionar os dados salvos:

```bash
docker exec -it ondutydoctor sqlite3 database/database.sqlite
```

---

## 📋 Futuras melhorias

- Validação de schema antes de salvar
- Retry de mensagens com falha
- Suporte a múltiplos tópicos Kafka
- Metrics e healthcheck
