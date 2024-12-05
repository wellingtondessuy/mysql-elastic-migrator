#!/bin/sh
set -e

# Inicia a migração
php artisan schedule:run || echo "Erro ao iniciar a execução da migração"


