<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración de Optimización del Sistema
    |--------------------------------------------------------------------------
    |
    | Este archivo contiene configuraciones para optimizar el rendimiento
    | y escalabilidad de la aplicación PeopleStats.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Configuración de Caché
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'estadisticas' => [
            'duracion' => env('CACHE_ESTADISTICAS_DURACION', 300), // 5 minutos
            'clave_prefijo' => 'stats_',
        ],
        'usuarios' => [
            'duracion' => env('CACHE_USUARIOS_DURACION', 600), // 10 minutos
            'clave_prefijo' => 'user_',
        ],
        'votantes' => [
            'duracion' => env('CACHE_VOTANTES_DURACION', 180), // 3 minutos
            'clave_prefijo' => 'votante_',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Paginación
    |--------------------------------------------------------------------------
    */
    'paginacion' => [
        'votantes' => [
            'por_defecto' => env('PAGINACION_VOTANTES_DEFECTO', 25),
            'maximo' => env('PAGINACION_VOTANTES_MAXIMO', 100),
            'minimo' => env('PAGINACION_VOTANTES_MINIMO', 10),
        ],
        'usuarios' => [
            'por_defecto' => env('PAGINACION_USUARIOS_DEFECTO', 20),
            'maximo' => env('PAGINACION_USUARIOS_MAXIMO', 50),
            'minimo' => env('PAGINACION_USUARIOS_MINIMO', 5),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Rate Limiting
    |--------------------------------------------------------------------------
    */
    'rate_limiting' => [
        'general' => [
            'max_intentos' => env('RATE_LIMIT_GENERAL_MAX', 60),
            'decay_minutos' => env('RATE_LIMIT_GENERAL_DECAY', 1),
        ],
        'busquedas' => [
            'max_intentos' => env('RATE_LIMIT_BUSQUEDAS_MAX', 30),
            'decay_minutos' => env('RATE_LIMIT_BUSQUEDAS_DECAY', 1),
        ],
        'importaciones' => [
            'max_intentos' => env('RATE_LIMIT_IMPORTACIONES_MAX', 5),
            'decay_minutos' => env('RATE_LIMIT_IMPORTACIONES_DECAY', 60),
        ],
        'estadisticas' => [
            'max_intentos' => env('RATE_LIMIT_ESTADISTICAS_MAX', 120),
            'decay_minutos' => env('RATE_LIMIT_ESTADISTICAS_DECAY', 1),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Jobs y Colas
    |--------------------------------------------------------------------------
    */
    'jobs' => [
        'importacion' => [
            'timeout' => env('JOB_IMPORTACION_TIMEOUT', 1800), // 30 minutos
            'tries' => env('JOB_IMPORTACION_TRIES', 3),
            'retry_after' => env('JOB_IMPORTACION_RETRY_AFTER', 300), // 5 minutos
        ],
        'limpieza' => [
            'timeout' => env('JOB_LIMPIEZA_TIMEOUT', 600), // 10 minutos
            'tries' => env('JOB_LIMPIEZA_TRIES', 2),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Archivos
    |--------------------------------------------------------------------------
    */
    'archivos' => [
        'importacion' => [
            'tamaño_maximo' => env('IMPORTACION_TAMANO_MAX', 10240), // 10MB
            'extensiones_permitidas' => ['xlsx', 'xls'],
            'directorio_temporal' => 'temp/imports',
            'tiempo_vida_temporal' => 86400, // 24 horas
        ],
        'limpieza' => [
            'archivos_antiguos_dias' => env('LIMPIEZA_ARCHIVOS_DIAS', 7),
            'mantener_ultimos' => env('LIMPIEZA_MANTENER_ULTIMOS', 100),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Base de Datos
    |--------------------------------------------------------------------------
    */
    'database' => [
        'indices' => [
            'habilitar_automaticos' => env('DB_INDICES_AUTOMATICOS', true),
            'analizar_tablas_automaticamente' => env('DB_ANALIZAR_AUTOMATICO', true),
        ],
        'consultas' => [
            'timeout' => env('DB_QUERY_TIMEOUT', 30),
            'max_resultados' => env('DB_MAX_RESULTADOS', 10000),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Monitoreo
    |--------------------------------------------------------------------------
    */
    'monitoreo' => [
        'habilitar' => env('MONITOREO_HABILITADO', true),
        'log_slow_queries' => env('MONITOREO_LOG_SLOW_QUERIES', true),
        'slow_query_threshold' => env('MONITOREO_SLOW_QUERY_THRESHOLD', 1.0), // segundos
        'log_memory_usage' => env('MONITOREO_LOG_MEMORY', true),
        'memory_threshold' => env('MONITOREO_MEMORY_THRESHOLD', 128), // MB
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Mantenimiento
    |--------------------------------------------------------------------------
    */
    'mantenimiento' => [
        'limpieza_automatica' => [
            'habilitar' => env('MANTENIMIENTO_LIMPIEZA_AUTO', true),
            'frecuencia_horas' => env('MANTENIMIENTO_FRECUENCIA', 24),
        ],
        'optimizacion_automatica' => [
            'habilitar' => env('MANTENIMIENTO_OPTIMIZACION_AUTO', true),
            'frecuencia_horas' => env('MANTENIMIENTO_OPTIMIZACION_FRECUENCIA', 168), // 1 semana
        ],
    ],
];
