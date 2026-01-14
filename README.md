# Laravel Hexagonal Architecture & AI Starter Kit 🚀

Un paquete de scaffolding automatizado para transformar una instalación limpia de Laravel en una **Arquitectura Hexagonal (Ports & Adapters)** robusta, preparada específicamente para trabajar con **Agentes de IA** (como Google Antigravity, Cursor, etc.).

Este paquete no solo crea carpetas; inyecta el **contexto arquitectónico** necesario para que tu IA entienda el proyecto desde el primer prompt.

## ✨ Características

- **Estructura DDD Automática:** Genera `src/Domain`, `src/Application`, `src/Infrastructure` y carpetas de Testing separadas.
- **Migración Inteligente:** Mueve el modelo `User` por defecto a la capa de Infraestructura y actualiza `config/auth.php` y namespaces automáticamente.
- **Contexto para IA:** Genera archivos `.md` en la raíz (`PROJECT_MEMORY`, `ARCHITECTURE`, `CODING_STANDARDS`) que sirven como "memoria a largo plazo" para tu Agente de IA.
- **Rules Injection:** Configura automáticamente las reglas de comportamiento para Google Antigravity.

## ✅ Requisitos

- PHP ^8.2
- Laravel 10.x, 11.x o 12.x
- Composer

## 📦 Instalación

Como este paquete está alojado en GitHub, debes indicar a Composer dónde buscarlo.

Ejecuta estos dos comandos en tu terminal:

### 1. Configurar el origen

Dile a Composer dónde descargar el paquete (GitHub):

```bash
composer config repositories.hex-ai vcs https://github.com/GAscencioV/laravel-hex-ai
```

### 2. Requerir el paquete

Ejecuta en tu terminal:

```bash
composer require gascencio/laravel-hex-ai:dev-main
```

## 🛠 Uso

Una vez instalado, simplemente ejecuta el comando de instalación:

```bash
php artisan hex:install
```

### ¿Qué hace este comando?

1.  **Crea directorios:** Establece la estructura de carpetas para Dominio, Aplicación e Infraestructura.
2.  **Mueve archivos:** Localiza `app/Models/User.php`, lo mueve a `src/Infrastructure/...` y corrige su namespace.
3.  **Genera Memoria:** Crea archivos `PROJECT_MEMORY.md` y `ARCHITECTURE.md` con la fecha actual y el nombre del proyecto.
4.  **Configura Autoload:** Actualiza tu `composer.json` para cargar las clases desde `src/`.

Al finalizar, recuerda ejecutar:

```bash
composer dump-autoload
```

### 📂 Estructura Resultante

El comando transformará tu proyecto Laravel estándar en esto:

```text
src/
├── Domain/                 # Núcleo: Entidades, Value Objects, Puertos
│   ├── Shared/
│   └── (BoundedContexts)/
├── Application/            # Orquestación: Use Cases, DTOs
│   └── UseCases/
├── Infrastructure/         # Adaptadores: Eloquent, Controllers, API
│   ├── Persistence/
│   │   └── Eloquent/
│   │       └── Models/     # Aquí vivirán tus modelos (User.php, etc)
│   └── Http/
│       └── Controllers/
└── tests/
    ├── Unit/
    │   ├── Domain/         # Tests Unitarios Puros
    │   └── Application/    # Tests de Casos de Uso
    └── Integration/        # Tests de Infraestructura
```

## 🧠 Archivos Generados para la IA

El comando genera archivos clave para que asistentes como **Cursor** o **Antigravity** trabajen mejor:

- **`PROJECT_MEMORY.md`**: Un archivo vivo para llevar el roadmap y estado del proyecto.
- **`ARCHITECTURE.md`**: Explica a la IA que el código vive en `src/` y no en `app/`.
- **`CODING_STANDARDS.md`**: Reglas estrictas de TDD, DDD y Tipado Estricto.

## 📄 Licencia

MIT. Creado por Gabriel Ascencio.
