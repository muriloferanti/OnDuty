## 🧩 Visão geral

- Laravel Kafka Consumer Service — Consome a fila de demandas do Kafka (`demands.created`) e salva localmente usando SQLite, após isso é possível assumir a demanda ou ignorar, assumindo irá dismparar uma nova mensagem para o Kafka (`demands.assumed`), ignorando só irá atualizar o status da demanda.

- Laravel Kafka Producer Service — Produz a fila de demandas do Kafka (`demands.created`) e apresenta os demais serviços.

- Node Kafka Consumer Service — Consome todos tópicos existentes e mostra em formato de gráfico.

## 🏗️ Arquitetura
A arquitetura do sistema é composta por múltiplos containers Docker orquestrados via docker-compose, com os seguintes componentes:

# Zookeeper
Responsável pela coordenação do cluster Kafka.

# Kafka
Broker principal, exposto internamente via PLAINTEXT://kafka:9092, com auto criação de tópicos habilitada.

# REST Proxy
Permite que aplicações como o OnDutyNow produzam mensagens via HTTP usando o Kafka REST Proxy da Confluent.

# Kowl
Interface web acessível em :8080 para inspeção e gerenciamento dos tópicos Kafka em tempo real.

# OnDutyNow
Serviço Laravel responsável por produzir mensagens no tópico demands.created via REST Proxy.

# OnDutyDoctor
Serviço Laravel que consome mensagens diretamente do broker Kafka usando a extensão nativa php-rdkafka e persiste localmente em SQLite.

# OnDutyMonitor
Aplicação Node.js que consome múltiplos tópicos Kafka diretamente do broker e exibe os dados em tempo real via dashboard (porta 3000).

# init-topics
Container auxiliar que executa um script de criação dos tópicos com base no arquivo kafka-topics.txt.

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
docker-compose build
```

2. Subir:

```bash
docker-compose up -d
```

---

## 📂 Estrutura OnDutyDoctor

Diferente do OnDutyNow que usa Http para comunicação com Kafka, aqui optamos pelo uso da lib RdKafka para estudos.

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

Configuração do .env para ambos ambientes em Laravel é igual ao `.env.exemple`


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

## 📋 Futuras melhorias

- Validação de schema antes de salvar
- Retry de mensagens com falha
- Suporte a múltiplos tópicos Kafka
- Metrics e healthcheck
