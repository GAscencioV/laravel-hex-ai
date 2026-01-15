<?php

namespace Gascencio\Hexagonal\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'hex:install';
    protected $description = 'Instala estructura Hexagonal y Contexto para IA (Google Antigravity)';

    public function handle()
    {
        $this->info('🚀 Iniciando transformación a Arquitectura Hexagonal...');

        $this->createDirectories();
        $this->generateAiContext();
        $this->moveDefaultModels();
        $this->updateComposerAutoload();

        $this->info('✅ ¡Listo! Ejecuta "composer dump-autoload" para finalizar.');
    }

    protected function createDirectories()
    {
        $directories = [
            // 1. CAPA DE DOMINIO (El núcleo, PHP puro)
            // Aquí van las reglas de negocio, entidades y contratos.
            'src/Domain/Entities',
            'src/Domain/ValueObjects',
            'src/Domain/Repositories', // Interfaces (Contratos)
            'src/Domain/Events',
            'src/Domain/Exceptions',
            'src/Domain/Services',     // Servicios de Dominio (lógica que no cabe en una entidad)

            // 2. CAPA DE APLICACIÓN (Orquestación)
            // Casos de uso y comunicación entre capas.
            'src/Application/UseCases/Commands', // Escritura (Create, Update, Delete)
            'src/Application/UseCases/Queries',  // Lectura (Get, Search)
            'src/Application/DTOs',              // Data Transfer Objects
            'src/Application/Interfaces',        // Interfaces para servicios externos (Email, PDF, etc)
            'src/Application/Services',          // Implementaciones de aplicación

            // 3. CAPA DE INFRAESTRUCTURA (Detalles técnicos y Framework)
            // Persistencia (Base de datos)
            'src/Infrastructure/Persistence/Eloquent/Models',
            'src/Infrastructure/Persistence/Eloquent/Repositories', // Implementación de las interfaces del Dominio
            
            // 4. ESTRUCTURA DE TESTING (Espejo de la Arquitectura)
            'tests/Unit/Domain',
            'tests/Unit/Application',
            'tests/Integration/Infrastructure', // Opcional, si quieres separar Unit de Feature

            // HTTP (Entrada Web/API)
            'src/Infrastructure/Http/Controllers',
            'src/Infrastructure/Http/Requests',  // FormRequests
            'src/Infrastructure/Http/Resources', // API Resources (JSON transformation)
            'src/Infrastructure/Http/Middleware',
            
            // Servicios Externos (Implementaciones de interfaces de Aplicación)
            'src/Infrastructure/Services',       // Ej: MailerService, StripePaymentService
            
            // Configuración / Start
            'src/Infrastructure/Providers',      // Service Providers específicos
        ];

        foreach ($directories as $dir) {
            // USO DE $this->laravel->basePath()
            $path = $this->laravel->basePath($dir);
            
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
                $this->line("📂 Creado: $dir");
            }
        }
    }

    protected function generateAiContext()
    {
        // Mapa: 'Nombre del Stub' => 'Ruta relativa'
        $files = [
            // Reglas Estáticas (Ocultas y Modulares)
            'antigravityrules.stub'     => '.agent/rules/00-core-behavior.md',
            'ARCHITECTURE_CONTEXT.stub' => '.agent/rules/01-architecture.md',
            'CODING_STANDARDS.stub'     => '.agent/rules/02-coding-style.md',
            
            // Memoria Dinámica (Visible en Raíz)
            'PROJECT_MEMORY.stub'       => 'PROJECT_MEMORY.md',
        ];

        foreach ($files as $stubName => $relativePath) {
            // Obtenemos nombre del proyecto de la config inyectada
            $projectName = $this->laravel['config']['app.name'] ?? 'Laravel Project';

            $this->publishStub($stubName, $this->laravel->basePath($relativePath), [
                'PROJECT_NAME' => $projectName,
                'DATE'         => date('Y-m-d'),
            ]);
        }
        
        $this->info('🧠 Contexto de IA generado en .agent/rules/ y PROJECT_MEMORY.md');
    }

    protected function moveDefaultModels()
    {
        $source = $this->laravel->basePath('app/Models/User.php');
        $dest   = $this->laravel->basePath('src/Infrastructure/Persistence/Eloquent/Models/User.php');

        if (file_exists($source)) {
            $content = file_get_contents($source);

            $content = str_replace(
                'namespace App\Models;', 
                'namespace Src\Infrastructure\Persistence\Eloquent\Models;', 
                $content
            );

            // Asegurar que el directorio destino existe antes de mover
            if (!is_dir(dirname($dest))) {
                mkdir(dirname($dest), 0755, true);
            }

            file_put_contents($dest, $content);
            unlink($source);
            
            $modelsDir = $this->laravel->basePath('app/Models');
            if (is_dir($modelsDir) && count(scandir($modelsDir)) == 2) {
                rmdir($modelsDir);
            }

            $this->info('🚚 Modelo User.php movido a Infraestructura.');
            
            $this->updateAuthConfig();
        }
    }

    protected function updateAuthConfig()
    {
        $authConfig = $this->laravel->basePath('config/auth.php');
        
        if (file_exists($authConfig)) {
            $content = file_get_contents($authConfig);
            $content = str_replace(
                'App\\Models\\User::class', 
                'Src\\Infrastructure\\Persistence\\Eloquent\\Models\\User::class', 
                $content
            );
            file_put_contents($authConfig, $content);
            $this->line('🔧 config/auth.php actualizado.');
        }
    }

    protected function updateComposerAutoload()
    {
        $composerPath = $this->laravel->basePath('composer.json');
        
        if (file_exists($composerPath)) {
            $composer = json_decode(file_get_contents($composerPath), true);
            $composer['autoload']['psr-4']['Src\\'] = "src/";
            
            file_put_contents(
                $composerPath, 
                json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            );
            
            $this->info('⚙️ composer.json actualizado.');
        }
    }

    protected function publishStub($stubName, $destinationPath, $replacements = [])
    {
        $stubPath = __DIR__ . '/../Stubs/' . $stubName;

        if (!file_exists($stubPath)) {
            $this->warn("⚠️ No se encontró el stub: $stubName");
            return;
        }

        $content = file_get_contents($stubPath);

        foreach ($replacements as $key => $value) {
            $content = str_replace("{{ $key }}", $value, $content);
        }

        $directory = dirname($destinationPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($destinationPath, $content);
        $this->line("   📄 Generado: " . basename($destinationPath));
    }
}