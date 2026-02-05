#!/bin/bash

# Script para ejecutar el backfill del DOF en segundo plano (background)
# Uso: ./run_dof_backfill.sh [anio_inicio] [anio_fin]
# Ejemplo: ./run_dof_backfill.sh 2000 2023

START_YEAR=${1:-2000}
END_YEAR=${2:-2023}
LOG_FILE="dof_backfill_${START_YEAR}_${END_YEAR}.log"

echo "==============================================="
echo "Iniciando descarga masiva del DOF"
echo "Desde: $START_YEAR"
echo "Hasta: $END_YEAR"
echo "Log: $LOG_FILE"
echo "==============================================="
echo "El proceso continuará ejecutándose aunque cierres esta terminal."
echo "Para ver el progreso, ejecuta: tail -f $LOG_FILE"
echo "==============================================="

# Ejecutar con nohup para ignorar señal de cierre (SIGHUP)
# Redirigir stdout y stderr al archivo de log
nohup php artisan dof:backfill "$START_YEAR" --end-year="$END_YEAR" > "$LOG_FILE" 2>&1 &

PID=$!
echo "Proceso iniciado con PID: $PID"
echo "Guardando PID en dof_backfill.pid para referencia futura."
echo $PID > dof_backfill.pid
