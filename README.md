# Player Notes Module

Prueba técnica para MaeMódulo de "Historial de Notas de Jugador" que permite a los agentes de soporte visualizar y registrar observaciones internas sobre un usuario/jugador específico. Construido con **Laravel 10+**, **Livewire 3**, patrón **Repositorio** y **Spatie Laravel-Permission**, sobre entorno **Docker (Sail)**.

## Stack técnico

- PHP 8.2+
- Laravel 10+
- Livewire 3
- Spatie Laravel-Permission
- MySQL 8 (vía Sail) / SQLite en memoria (para tests)
- Laravel Sail (Docker)

## Arquitectura

pendiente

**Decisiones de diseño:**

pendiente

## Instalación

### 1. Clonar el repositorio

```bash
git clone <url-del-repo>
cd player-notes-module
```

### 2. Copiar variables de entorno

```bash
cp .env.example .env
```

### 3. Instalar dependencias

```bash
composer install
```

### 4. Levantar contenedores con Sail

```bash
./vendor/bin/sail up -d
```

> Tip: si quieres evitar escribir `./vendor/bin/sail` cada vez, crea un alias en tu shell:
> ```bash
> alias sail='sh $([ -f sail ] && echo sail || echo vendor/bin/sail)'
> ```

### 5. Generar la key de la aplicación

```bash
sail artisan key:generate
```

### 6. Ejecutar migraciones y seeders

```bash
sail artisan migrate --seed
```

Esto crea:
- La tabla `player_notes`
- El permiso `create-player-note`
- El rol `support-agent` con dicho permiso asignado

### 7. (Opcional) Asignar el rol a un usuario de prueba

```bash
sail artisan tinker
```

```php
$user = \App\Models\User::first();
$user->assignRole('support-agent');
```

## Uso del componente

Dentro de cualquier vista Blade (por ejemplo, el perfil del jugador):

```blade
<livewire:player-notes :player="$player" />
```

El componente recibe el modelo `User` del jugador y muestra:
- Tabla con **Fecha**, **Autor** y **Contenido** de cada nota, ordenadas de más reciente a más antigua.
- Formulario para agregar una nota nueva (solo visible si el usuario autenticado tiene el permiso `create-player-note`).

## Validaciones

- El campo `content` es **requerido**.
- Máximo **500 caracteres**.
- Mensajes de error personalizados en español, mostrados inline bajo el textarea.

## Tests

El proyecto incluye un Feature Test que verifica:
1. Que un agente con permiso puede guardar una nota correctamente en base de datos.
2. Que el campo `content` es obligatorio (validación).

Correr los tests:

```bash
sail artisan test --filter=PlayerNoteTest
```

Los tests usan **SQLite en memoria** (configurado en `phpunit.xml`) para ejecutarse de forma rápida y aislada, sin depender del contenedor de MySQL:

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

## Comandos útiles de Sail

| Acción | Comando |
|---|---|
| Levantar contenedores | `sail up -d` |
| Detener contenedores | `sail down` |
| Ver logs | `sail logs` |
| Entrar al contenedor | `sail shell` |
| Correr migraciones | `sail artisan migrate` |
| Correr tests | `sail artisan test` |
| Tinker | `sail artisan tinker` |

## Estructura de la tabla `player_notes`

| Columna | Tipo | Descripción |
|---|---|---|
| `id` | bigint | PK |
| `player_id` | bigint (FK -> users.id) | Jugador sobre el que trata la nota |
| `author_id` | bigint (FK -> users.id) | Agente que escribió la nota |
| `content` | text | Contenido de la nota |
| `created_at` / `updated_at` | timestamp | Auditoría |

Incluye índice compuesto `(player_id, created_at)` para optimizar el listado por jugador ordenado por fecha.

## Permisos

Gestionados con [Spatie Laravel-Permission](https://spatie.be/docs/laravel-permission).

- Permiso: `create-player-note`
- Rol por defecto con ese permiso: `support-agent`

Para agregar el permiso a otro rol:

```php
$role = \Spatie\Permission\Models\Role::findByName('nombre-del-rol');
$role->givePermissionTo('create-player-note');
```


# Errores comúnes
Al levantar el ambiente docker:
Error response from daemon: ports are not available: exposing port TCP 0.0.0.0:3306 -> 127.0.0.1:0: listen tcp 0.0.0.0:3306: bind: address already in use

en tu .env agrega un puerto forward con: 
FORWARD_DB_PORT=3307
