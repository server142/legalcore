<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asesorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('cliente_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('abogado_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('factura_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('expediente_id')->nullable()->constrained()->onDelete('set null');
            
            // Datos de la asesoría
            $table->string('folio')->unique();
            $table->enum('tipo', ['telefonica', 'videoconferencia', 'presencial']);
            $table->enum('estado', ['agendada', 'realizada', 'cancelada', 'no_atendida'])->default('agendada');
            
            // Información del prospecto/cliente
            $table->string('nombre_prospecto');
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->text('asunto');
            $table->text('notas')->nullable();
            
            // Fecha y hora
            $table->dateTime('fecha_hora');
            $table->integer('duracion_minutos')->default(30);
            
            // Seguimiento
            $table->text('motivo_cancelacion')->nullable();
            $table->text('motivo_no_atencion')->nullable();
            $table->text('resumen')->nullable();
            $table->boolean('prospecto_acepto')->nullable();
            
            // Datos financieros
            $table->decimal('costo', 10, 2)->default(0);
            $table->boolean('pagado')->default(false);
            $table->dateTime('fecha_pago')->nullable();
            
            // Link de videoconferencia
            $table->string('link_videoconferencia')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['tenant_id', 'estado', 'fecha_hora']);
            $table->index(['abogado_id', 'fecha_hora']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asesorias');
    }
};
