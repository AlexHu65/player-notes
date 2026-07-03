# Player Notes Module

Prueba técnica para el puesto Líder Técnico / Technical Lead Laravel/PHP Plataforma de Pa en promarketing de módulo de historial de notas de jugador para soporte interno.

La aplicación permite que un agente autenticado:
- Seleccione un jugador (otro usuario).
- Registre una nota interna para ese jugador.
- Visualice el historial de notas que él mismo ha dejado.

## Stack técnico actual

- PHP 8.3+
- Laravel 13
- Livewire 4
- Laravel Breeze 2
- Spatie Laravel-Permission 8
- MySQL 8 con Laravel Sail (desarrollo)
- SQLite en memoria (tests)
- Pest 4

## Arquitectura

### 1) Capa de UI (Livewire)

- Componente principal: `App\Livewire\PlayerNotes`
- Vista: `resources/views/livewire/player-notes.blade.php`
- Integración en dashboard: `resources/views/dashboard.blade.php`

Responsabilidades del componente:
- Cargar jugadores disponibles excluyendo al usuario autenticado.
- Validar selección de jugador y contenido.
- Guardar nota con `author_id` = usuario logueado y `player_id` = usuario seleccionado.
- Listar con paginación las notas creadas por el autor autenticado.

### 2) Capa de dominio/modelos

- `App\Models\PlayerNote`
	- `player()` relación `belongsTo(User::class, 'player_id')`
	- `author()` relación `belongsTo(User::class, 'author_id')`
- `App\Models\User`
	- Usa trait `HasRoles` de Spatie.

### 3) Capa de acceso a datos (Repositorio)

- Contrato: `App\Repositories\Contracts\PlayerNoteRepositoryInterface`
- Implementación: `App\Repositories\Eloquent\EloquentPlayerNoteRepository`
- Binding en contenedor: `App\Providers\AppServiceProvider`

Métodos clave:
- `create(int $playerId, int $authorId, string $content)`
- `getByAuthorPaginated(int $authorId, int $perPage = 10)`
- `getByPlayerPaginated(int $playerId, int $perPage = 10)`

## Flujo funcional

1. Usuario inicia sesión.
2. En `/dashboard` se muestra el componente de notas.
3. Selecciona un jugador en el `<select>`.
4. Escribe contenido y guarda.
5. La nota se persiste con:
	 - `author_id`: usuario autenticado
	 - `player_id`: jugador seleccionado
6. La tabla muestra las notas creadas por el autor logueado, con paginación.

## Instalación

### 1) Clonar e ingresar al proyecto

```bash
git clone <url-del-repo>
cd player-notes
```

### 2) Variables de entorno

```bash
cp .env.example .env
```

### 3) Instalar dependencias

```bash
composer install
npm install
```

### 4) Levantar contenedores

```bash
./vendor/bin/sail up -d
```

Tip (alias opcional):

```bash
alias sail='sh $([ -f sail ] && echo sail || echo vendor/bin/sail)'
```

### 5) Generar APP_KEY

```bash
sail artisan key:generate
```

### 6) Migrar y seedear

```bash
sail artisan migrate --seed
```

### 7) Frontend (opcional, para desarrollo)

```bash
npm run dev
```

## Seeders y datos iniciales

`DatabaseSeeder` ejecuta `UserSeeder`, que crea:
- Permiso `create-player-note`
- Rol `support-agent` con ese permiso
- Usuario agente:
	- Email: `ejemplo@example.com`
	- Password: `password`
- 5 usuarios adicionales (potenciales jugadores)

## Uso

Ruta principal de uso:
- `GET /` (requiere `auth` y `verified`)

El dashboard ya renderiza:

```blade
<livewire:player-notes />
```

## Validaciones actuales

- `playerId`
	- requerido
	- entero
	- debe existir en `users`
	- no puede ser el usuario autenticado
- `content`
	- requerido
	- string
	- máximo 1000 caracteres

Mensajes de validación personalizados en español.

## Esquema de base de datos

### Tabla `player_notes`

| Columna | Tipo | Descripción |
|---|---|---|
| `id` | bigint | PK |
| `player_id` | bigint FK -> `users.id` | Usuario jugador al que pertenece la nota |
| `author_id` | bigint FK -> `users.id` | Agente que creó la nota |
| `content` | text | Contenido de la nota |
| `created_at` / `updated_at` | timestamp | Auditoría |

Índice:
- `(player_id, created_at)`

### Tablas de permisos (Spatie)

Incluye migración de:
- `permissions`
- `roles`
- `model_has_permissions`
- `model_has_roles`
- `role_has_permissions`

## Comandos útiles

| Acción | Comando |
|---|---|
| Levantar contenedores | `sail up -d` |
| Detener contenedores | `sail down` |
| Ver logs | `sail logs` |
| Shell del contenedor | `sail shell` |
| Migrar y seedear | `sail artisan migrate --seed` |
| Limpiar cachés | `sail artisan optimize:clear` |
| Tests | `sail artisan test --compact` |
| Tinker | `sail artisan tinker` |

## Tests

El proyecto usa Pest y actualmente incluye principalmente pruebas del starter kit de autenticación/perfil.

Ejecutar:

```bash
sail artisan test --compact
```

Configuración de tests en memoria:

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

## Errores comunes

### 1) Puerto 3306 ocupado al levantar Docker

Error típico:

```text
Error response from daemon: ports are not available: exposing port TCP 0.0.0.0:3306 -> 127.0.0.1:0: listen tcp 0.0.0.0:3306: bind: address already in use
```

Solución en `.env`:

```env
FORWARD_DB_PORT=3307
```

### 2) SQLSTATE[HY000] [2002] Connection refused

Si Laravel intenta conectar a `127.0.0.1:3306` por defecto, define explícitamente:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password
```

Luego:

```bash
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan migrate --seed
```

Nota:
- Fuera de Sail (`php artisan ...` en host), usa `DB_HOST=127.0.0.1` y `DB_PORT=3307` (o tu `FORWARD_DB_PORT`).

### 3) No application encryption key has been specified

Si falla app o tests por APP_KEY:

```bash
sail artisan key:generate
```

# Usuario de PRUEBA

Siempre se genera el mismo usuario para iniciar sesion:
user: ejemplo@example.com
password: password
