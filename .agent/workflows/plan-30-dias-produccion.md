---
description: Plan de 30 días para lanzar LegalCore a producción (Budget: $50-200/mes)
---

# 🚀 PLAN DE 30 DÍAS: LEGALCORE A PRODUCCIÓN

**Contexto:**
- Solo desarrollador: Tú
- Presupuesto: $50-200 USD/mes
- Clientes actuales: 1-5 (beta)
- Objetivo: Lanzar a producción y conseguir primeros 10 clientes pagados

---

## 📊 SEMANA 1: OPTIMIZACIÓN DE COSTOS (Días 1-7)

### Objetivo: Reducir costos operativos al mínimo

#### DÍA 1: Auditoría de Recursos
```bash
php artisan analyze:resources --export
```

**Tareas:**
- [ ] Ejecutar análisis de recursos
- [ ] Identificar top 3 consumidores de costos
- [ ] Documentar hallazgos en Excel/Notion

**Entregable:** Reporte de costos actuales

---

#### DÍA 2-3: Limpieza de Base de Datos

**Problema:** Logs antiguos consumen espacio y hacen queries lentas.

**Solución:**
```bash
# Crear comando de limpieza
php artisan make:command CleanOldLogs
```

**Implementar:**
1. Archivar audit_logs > 90 días
2. Eliminar ai_usage_logs > 60 días
3. Soft delete documentos de tenants inactivos

**Ahorro estimado:** 30-40% de espacio en BD

---

#### DÍA 4-5: Optimización de IA (Mayor Gasto)

**Estrategia de reducción de costos:**

1. **Cambiar modelo por defecto:**
   - ❌ gpt-4 ($30/1M tokens)
   - ✅ gpt-4o-mini ($0.15/1M tokens)
   - **Ahorro: 99.5%**

2. **Implementar caché de respuestas:**
```php
// En LegalAIService.php
$cacheKey = "ai_response_" . md5($prompt);
return Cache::remember($cacheKey, 3600, function() use ($prompt) {
    return $this->callAI($prompt);
});
```

3. **Límites por tenant:**
   - Trial: 50 requests/mes
   - Básico: 200 requests/mes
   - Profesional: 1000 requests/mes

**Ahorro estimado:** $30-50/mes

---

#### DÍA 6-7: Migración a Hosting Económico

**Opción Recomendada: DigitalOcean Droplet**
- Plan: Basic Droplet $6/mes (1GB RAM, 25GB SSD)
- Base de datos: Managed MySQL $15/mes (OPCIONAL, usar SQLite inicialmente)
- **Total: $6-21/mes**

**Alternativa Ultra-Económica: Railway.app**
- $5/mes por servicio
- Incluye BD PostgreSQL
- Deploy automático desde GitHub
- **Total: $5/mes**

**Tareas:**
- [ ] Crear cuenta en Railway/DigitalOcean
- [ ] Configurar dominio (Namecheap $8/año)
- [ ] SSL gratis con Let's Encrypt

---

## 🔧 SEMANA 2: ESTABILIZACIÓN (Días 8-14)

### Objetivo: Sistema sin bugs críticos

#### DÍA 8-9: Error Tracking

**Implementar Sentry (Gratis hasta 5K eventos/mes):**

```bash
composer require sentry/sentry-laravel
php artisan sentry:publish
```

**Configurar en .env:**
```env
SENTRY_LARAVEL_DSN=https://your-dsn@sentry.io/project
SENTRY_TRACES_SAMPLE_RATE=0.2
```

**Beneficio:** Sabrás EXACTAMENTE qué falla en producción.

---

#### DÍA 10-11: Testing Crítico

**Escribir 10 tests esenciales:**

```bash
php artisan make:test ExpedienteCreationTest
php artisan make:test MultiTenancyIsolationTest
php artisan make:test ContractGenerationTest
```

**Prioridad:**
1. ✅ Multi-tenancy (que no se mezclen datos)
2. ✅ Facturación (no perder dinero)
3. ✅ Generación de contratos (core feature)

**Comando:**
```bash
php artisan test --filter=Critical
```

---

#### DÍA 12-13: Performance Básico

**Quick Wins:**

1. **Eager Loading (eliminar N+1):**
```php
// En ExpedienteController
Expediente::with(['cliente', 'abogado', 'documentos'])->get();
```

2. **Caché de queries frecuentes:**
```php
Cache::remember('materias', 3600, fn() => Materia::all());
```

3. **Optimizar imágenes subidas:**
```bash
composer require intervention/image
```

**Resultado esperado:** 2-3x más rápido

---

#### DÍA 14: Code Review y Refactoring

**Usar Laravel Pint (ya instalado):**
```bash
./vendor/bin/pint
```

**Checklist:**
- [ ] Eliminar código comentado
- [ ] Remover archivos .backup
- [ ] Consolidar servicios duplicados
- [ ] Documentar funciones complejas

---

## 🎨 SEMANA 3: PULIDO Y UX (Días 15-21)

### Objetivo: Impresionar a nuevos clientes

#### DÍA 15-16: Onboarding Mejorado

**Crear tour guiado para nuevos usuarios:**

```bash
composer require stancl/tenancy
npm install driver.js
```

**Implementar:**
1. Video de bienvenida (ya lo tienes ✅)
2. Checklist de primeros pasos
3. Tooltips en secciones clave

---

#### DÍA 17-18: Landing Page Optimizada

**Mejoras SEO:**
```html
<!-- En welcome.blade.php -->
<meta name="description" content="Software de gestión jurídica para despachos en México">
<meta property="og:image" content="/images/og-image.jpg">
```

**Google Analytics:**
```html
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
```

**A/B Testing:**
- Probar 2 versiones de CTA
- Medir conversión trial → pago

---

#### DÍA 19-20: Email Marketing

**Usar Resend (ya instalado ✅):**

**Crear secuencia de emails:**
1. Día 0: Bienvenida + Quick Start
2. Día 3: "¿Cómo va tu primer expediente?"
3. Día 7: Case study de cliente exitoso
4. Día 14: "50% de tu trial completado"
5. Día 25: "Últimos 5 días - Oferta especial"

**Implementar:**
```bash
php artisan make:mail TrialWelcomeEmail
```

---

#### DÍA 21: Documentación de Usuario

**Mejorar manual existente:**
- [ ] Videos cortos (Loom gratis)
- [ ] GIFs animados de funciones clave
- [ ] FAQ con preguntas reales de beta testers

---

## 💰 SEMANA 4: MONETIZACIÓN (Días 22-30)

### Objetivo: Primeros 10 clientes pagados

#### DÍA 22-23: Configurar Stripe

**Stripe Connect (comisión 2.9% + $0.30):**

```bash
composer require laravel/cashier
php artisan cashier:install
```

**Crear productos en Stripe:**
1. Plan Básico: $29/mes
2. Plan Profesional: $79/mes
3. Plan Despacho: $199/mes

**Implementar webhook:**
```php
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);
```

---

#### DÍA 24-25: Sistema de Referidos

**Incentivo:** 1 mes gratis por cada referido que pague.

**Implementar:**
```bash
php artisan make:migration create_referrals_table
```

**Tracking:**
- Código único por usuario
- Dashboard de referidos
- Pago automático de comisiones

---

#### DÍA 26-27: Outreach a Beta Testers

**Email personalizado:**
```
Asunto: [Nombre], tu feedback transformó LegalCore 🚀

Hola [Nombre],

Gracias a tus sugerencias, implementamos:
- [Feature específico que pidieron]
- [Mejora basada en su uso]

Como agradecimiento, te ofrezco:
✨ 50% OFF permanente ($39 → $19/mes)
✨ Migración gratuita de datos
✨ Soporte prioritario de por vida

¿Listo para hacer oficial nuestra colaboración?

[CTA: Activar Descuento]
```

**Meta:** Convertir 3/5 beta testers

---

#### DÍA 28-29: Lanzamiento Soft

**Estrategia:**
1. Post en LinkedIn (tu red personal)
2. Grupos de Facebook de abogados
3. Foros especializados (Reddit r/LawFirm)

**Contenido:**
- Video demo de 2 min
- Caso de éxito de beta tester
- Oferta de lanzamiento (30% OFF primeros 50)

---

#### DÍA 30: Retrospectiva y Ajustes

**Métricas a revisar:**
- Costo real de servidor
- Tasa de conversión trial → pago
- Churn rate
- NPS (Net Promoter Score)

**Ajustar precios si es necesario.**

---

## 📈 PROYECCIÓN FINANCIERA

### Mes 1 (Lanzamiento)
**Ingresos:**
- 3 clientes beta convertidos: $39 × 3 = $117
- 2 clientes nuevos: $29 × 2 = $58
- **Total: $175/mes**

**Costos:**
- Servidor: $6/mes (Railway)
- Dominio: $0.67/mes ($8/año)
- IA (optimizada): $10/mes
- Stripe fees: $5/mes
- **Total: $21.67/mes**

**Ganancia neta: $153.33/mes** ✅

---

### Mes 3 (Objetivo)
**Ingresos:**
- 10 clientes pagados (promedio $50): $500/mes

**Costos:**
- Servidor escalado: $15/mes
- IA: $25/mes
- Marketing: $50/mes
- **Total: $90/mes**

**Ganancia neta: $410/mes** 🚀

---

## 🎯 SIGUIENTES PASOS INMEDIATOS

### HOY (Día 1):
```bash
# 1. Ejecutar análisis
php artisan analyze:resources --export

# 2. Revisar logs de errores
tail -f storage/logs/laravel.log

# 3. Identificar query más lenta
composer require barryvdh/laravel-debugbar --dev
```

### ESTA SEMANA:
1. Cambiar modelo IA a gpt-4o-mini
2. Implementar caché básico
3. Crear cuenta en Railway.app

---

## ❓ PREGUNTAS FRECUENTES

**Q: ¿Y si no tengo tiempo para todo esto?**
**A:** Prioriza en este orden:
1. Optimización de IA (mayor ahorro)
2. Error tracking (evita perder clientes)
3. Tests críticos (confianza para escalar)

**Q: ¿Qué pasa si un cliente pide feature nueva?**
**A:** Usa la regla 80/20:
- Si 2+ clientes lo piden → priorizar
- Si es solo 1 → agregar a roadmap para v2

**Q: ¿Cuándo contratar ayuda?**
**A:** Cuando llegues a $1,000/mes de ingresos recurrentes.

---

## 📞 SOPORTE

Si te atoras en algún paso, pregúntame específicamente:
- "¿Cómo implemento caché en [componente]?"
- "¿Qué query está causando lentitud?"
- "¿Cómo configuro Stripe para México?"

¡Vamos a hacer que LegalCore sea un éxito! 🚀
