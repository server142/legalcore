<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ManualPage;
use Illuminate\Support\Facades\DB;

class AiManualSeeder extends Seeder
{
    public function run()
    {
        // 1. Limpiar versiones anteriores
        ManualPage::where('slug', 'modos-de-diogenes-intelligence')->delete();

        // 2. Crear la versión Markdown Maestra
        ManualPage::create([
            'title' => 'Manual Maestro: Diogenes Intelligence',
            'slug'  => 'modos-de-diogenes-intelligence', // Slug consistente
            'order' => 99,
            'content' => '# Bienvenido a la Abogacía Aumentada

**Diogenes Intelligence** no es un simple buscador ni un chat convencional. Es un **Modelo de Lenguaje Jurídico (LLM)** integrado profundamente en los datos de su despacho. Piense en él como un *"Pasante Senior"* con memoria eidética y capacidad de redacción instantánea, pero que requiere su dirección experta.

---

## 🧠 Arquitectura Cognitiva: ¿Cómo funciona?

### El Mito del "Aprendizaje"
Es común pensar que la IA "aprende" sobre sus casos con el tiempo. **Esto es falso.** Diogenes funciona mediante lo que llamamos *"Ventana de Contexto Efímera"*.

Cada vez que usted abre un chat en el Expediente 605/2024, el sistema realiza una operación quirúrgica: extrae las partes, el estado procesal, los últimos acuerdos y las fechas clave, y se los "inyecta" a la IA en una instrucción oculta. Por eso Diogenes sabe de qué habla **hoy**, pero si mañana abre otro expediente, no mezclará la información. Su memoria es segura, aislada y temporal.

### El Ciclo de Respuesta
1. **Lectura:** Usted envía una pregunta.
2. **Contextualización:** El sistema adjunta legalmente los datos del expediente actual.
3. **Procesamiento:** La IA analiza la solicitud bajo el rol seleccionado (Analista, Redactor, etc.).
4. **Generación:** Se redacta una respuesta palabra por palabra basada en lógica jurídica.

---

## ⚡ Los 4 Pilares de Operación

No todas las tareas jurídicas son iguales. Diogenes ajusta sus parámetros internos según el modo que usted seleccione.

### 🔍 Modo Analista
*Diseñado para la auditoría procesal. Lógica pura, creatividad mínima.*

> **Uso Estratégico:**
> "Revisa las notificaciones de este mes y genérame una tabla cronológica. ¿Existe algún término precluido?"

---

### ✍️ Modo Redactor
*Su asistente de escritura forense. Retórica formal y estilo jurídico.*

> **Uso Estratégico:**
> "Redacta el capítulo de Hechos para una demanda de divorcio incausado, narrando que el cónyuge abandonó el hogar el día 15 de mayo. Usa tono firme pero respetuoso."

---

### 🧠 Modo Estratega
*Su socio consultor. Pensamiento lateral y simulación de escenarios.*

> **Uso Estratégico:**
> "Si presentamos esta apelación, ¿qué argumentos podría usar la contraparte para desestimarla? Dame 3 contra-argumentos sólidos."

---

### 📚 Modo Investigador
*El bibliotecario jurídico. Doctrina y Ley.*

> **Uso Estratégico:**
> "Explícame la diferencia jurisprudencial reciente entre el interés superior del menor y la patria potestad."

---

## 🗣️ El Arte de Preguntar (Prompting)

La calidad de la respuesta de Diogenes depende de la instrucción.

*   ❌ **Petición Débil:** "Hazme una demanda." (El sistema tendrá que adivinar todo).
*   ✅ **Petición Maestra:** "Actúa como abogado patronal. Redacta una contestación negando el despido injustificado argumentando renuncia voluntaria. Cita la LFT."

---

## 💰 Economía de la IA
El consumo se mide en **Tokens** (aprox. 0.75 palabras = 1 token).
*   **Input:** Lo que usted escribe + el contexto oculto.
*   **Output:** Lo que Diogenes responde.
*   **Context Window:** Si el chat es muy largo, Diogenes "olvida" el inicio para ahorrar espacio.

> **Nota Final:** Diogenes es una herramienta de apoyo. La responsabilidad legal final siempre recae en el abogado titular.'
        ]);
    }
}
