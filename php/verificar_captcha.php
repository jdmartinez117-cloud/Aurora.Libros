<?php
// verificar_captcha.php
// Función reutilizable para validar el reCAPTCHA v2 en cualquier formulario.

function verificar_captcha($token) {
    // Tu clave secreta (NO la compartas públicamente ni la subas a repos públicos)
    $secret_key = '6Lc67MstAAAAAEvRYER5AsaZuC4CdsmJurc-TtWv';

    if (empty($token)) {
        return false;
    }

    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $datos = [
        'secret'   => $secret_key,
        'response' => $token
    ];

    $opciones = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($datos)
        ]
    ];

    $contexto = stream_context_create($opciones);
    $respuesta = file_get_contents($url, false, $contexto);
    $resultado = json_decode($respuesta, true);

    return isset($resultado['success']) && $resultado['success'] === true;
}
?>
