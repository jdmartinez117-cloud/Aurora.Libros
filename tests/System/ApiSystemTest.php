<?php
// tests/System/ApiSystemTest.php

class ApiSystemTest {

    private static function url($path = '') {
        $base = rtrim(getenv('TEST_BASE_URL') ?: 'http://localhost/Aurora.Libros', '/');
        return $base . $path;
    }

    public static function run(&$passed, &$failed) {
        echo "\n\033[1;34m[3. PRUEBAS DEL SISTEMA / APIS - Peticiones HTTP, Reportes y Catálogo]\033[0m\n";

        // Caso 1: Carga del Portal Principal (index.php)
        $respIndex = self::httpGet(self::url('/index.php'));
        self::assert(
            $respIndex['code'] === 200 && strpos($respIndex['body'], 'AURORA') !== false,
            "Sistema Web: index.php responde HTTP 200 y carga la estructura de la aplicación",
            $passed, $failed
        );

        self::assert(
            strpos($respIndex['body'], 'pw-checklist') !== false &&
            strpos($respIndex['body'], 'assets/js/idiomas.js') !== false,
            "Integridad del Sistema: index.php incluye componentes de política de contraseña y selector de idiomas",
            $passed, $failed
        );

        // Caso 2: Endpoint del Panel Administrativo (api_dashboard.php)
        $respDash = self::httpGet(self::url('/php/api_dashboard.php'));
        $dashJson = json_decode($respDash['body'], true);

        self::assert(
            $respDash['code'] === 200 && is_array($dashJson) && ($dashJson['status'] ?? '') === 'success',
            "API Dashboard: /php/api_dashboard.php responde JSON con status 'success'",
            $passed, $failed
        );

        self::assert(
            isset($dashJson['kpis']['total_libros']) && isset($dashJson['kpis']['total_usuarios']),
            "API Dashboard: Estructura de KPIs completa (libros, usuarios, pedidos)",
            $passed, $failed
        );

        // Caso 3: API de Registro con solicitud AJAX (Fetch) y validación de seguridad
        $postData = [
            'nombre_completo' => 'Test User',
            'email' => 'test_invalid@aurora.local',
            'usuario' => 'testuser',
            'password' => '123', // Contraseña débil a propósito
            'g-recaptcha-response' => ''
        ];
        $respReg = self::httpPost(
            self::url('/php/registro_usuario_be.php'),
            $postData,
            ['X-Requested-With: fetch']
        );
        $regJson = json_decode($respReg['body'], true);

        self::assert(
            $respReg['code'] === 200 && is_array($regJson) && ($regJson['ok'] ?? null) === false,
            "Seguridad del Sistema: Endpoint de registro rechaza datos inválidos con JSON {ok: false}",
            $passed, $failed
        );

        // Caso 4: Disponibilidad del motor de idiomas
        $respLang = self::httpGet(self::url('/assets/js/idiomas.js'));
        self::assert(
            $respLang['code'] === 200 && strpos($respLang['body'], 'aurora_lang') !== false,
            "Recursos del Sistema: /assets/js/idiomas.js disponible y sirve el diccionario bilingüe",
            $passed, $failed
        );

        // Caso 5: Generación y exportación de reportes en Excel (.xlsx)
        $respExcel = self::httpGet(self::url('/php/reporte_excel_be.php?tipo=libros'));
        $isExcel = ($respExcel['code'] === 200 && strlen($respExcel['body']) > 1000 &&
                    strpos($respExcel['body'], 'PK') === 0);
        self::assert(
            $isExcel,
            "Generador de Reportes Excel: /php/reporte_excel_be.php genera archivo .xlsx válido (" . strlen($respExcel['body']) . " bytes)",
            $passed, $failed
        );

        // Caso 6: Generación y exportación de reportes en PDF (mPDF)
        $respPdf = self::httpGet(self::url('/php/reporte_pdf_be.php?tipo=libros'));
        $isPdf = ($respPdf['code'] === 200 && strpos($respPdf['body'], '%PDF-') === 0);
        self::assert(
            $isPdf,
            "Generador de Reportes PDF: /php/reporte_pdf_be.php genera documento .pdf válido (" . strlen($respPdf['body']) . " bytes)",
            $passed, $failed
        );

        // Caso 7: API del Catálogo de la Tienda (api.php?action=get_data)
        $respCatalog = self::httpGet(self::url('/php/api.php?action=get_data'));
        $catalogJson = json_decode($respCatalog['body'], true);
        self::assert(
            $respCatalog['code'] === 200 && isset($catalogJson['books']) && count($catalogJson['books']) > 0,
            "API de Catálogo de Libros: /php/api.php?action=get_data retorna catálogo con " . count($catalogJson['books'] ?? []) . " libros",
            $passed, $failed
        );

        // Caso 8: Generador de Copias de Seguridad de la Base de Datos
        $respBackup = self::httpGet(self::url('/php/backup_bd.php'));
        $isSqlBackup = ($respBackup['code'] === 200 && strpos($respBackup['body'], 'CREATE TABLE') !== false);
        self::assert(
            $isSqlBackup,
            "Respaldo de Base de Datos: /php/backup_bd.php genera dump SQL íntegro (" . strlen($respBackup['body']) . " bytes)",
            $passed, $failed
        );

        // Caso 9: API de Restauración de Base de Datos (Validación de archivo SQL)
        $respRestore = self::httpPost(self::url('/php/restaurar_bd.php'), []);
        $restoreJson = json_decode($respRestore['body'], true);
        self::assert(
            $respRestore['code'] === 200 && is_array($restoreJson) && ($restoreJson['status'] ?? '') === 'error',
            "Restauración de Base de Datos: /php/restaurar_bd.php valida y rechaza peticiones sin archivo SQL",
            $passed, $failed
        );

        // Caso 10: Endpoint de Verificación de Sesión Única
        $respToken = self::httpGet(self::url('/php/verificar_token_be.php'));
        $tokenJson = json_decode($respToken['body'], true);
        self::assert(
            $respToken['code'] === 200 && is_array($tokenJson) && ($tokenJson['valido'] ?? null) === false,
            "Mecanismo de Sesión Única: /php/verificar_token_be.php reporta estado de token en formato JSON",
            $passed, $failed
        );
    }

    private static function httpGet($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ['code' => $code, 'body' => $body];
    }

    private static function httpPost($url, $data, $headers = []) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ['code' => $code, 'body' => $body];
    }

    private static function assert($condition, $description, &$passed, &$failed) {
        if ($condition) {
            $passed++;
            echo "  \033[32m✔ [PASSED]\033[0m $description\n";
        } else {
            $failed++;
            echo "  \033[31m✘ [FAILED]\033[0m $description\n";
        }
    }
}

// Ejecución directa desde consola si se llama este archivo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    $passed = 0; $failed = 0;
    ApiSystemTest::run($passed, $failed);
    echo "\n---------------------------------------------------------------\n";
    echo "Resultado Sistema/APIs: $passed Aprobadas | $failed Fallidas\n";
    exit($failed > 0 ? 1 : 0);
}
