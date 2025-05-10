#!/bin/bash

# Inicia o servidor Laravel em background (se quiser acessar via browser para debug)
php artisan serve --host=0.0.0.0 --port=8011 &

# Roda o consumer Kafka (fica rodando como processo principal)
php artisan kafka:consume-demands
