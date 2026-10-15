<?php
session_start();
header('Content-Type: application/json');

try {
    $pdo = new PDO("mysql:host=localhost;dbname=login_register_db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit;
}

// Función auxiliar para registrar logs de auditoría completa
function registrarLogAuditoria($pdo, $usuario_id, $tipo, $descripcion) {
    if (!$usuario_id) return;
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    if ($ip === '::1') $ip = '127.0.0.1';

    try {
        $stmt = $pdo->prepare("INSERT INTO actividad_usuarios (usuario_id, tipo_actividad, descripcion, ip_origen) VALUES (?, ?, ?, ?)");
        $stmt->execute([$usuario_id, $tipo, $descripcion, $ip]);
    } catch (Exception $e) {}
}

$action = $_GET['action'] ?? '';

$user_id = $_SESSION['id_usuario'] ?? null;

if ($action == 'get_data') {
    $libros = $pdo->query("SELECT * FROM libros")->fetchAll(PDO::FETCH_ASSOC);
    $carrito = [];
    $favoritos = [];

    if ($user_id) {
        // Carrito
        $stmt = $pdo->prepare("SELECT l.* FROM libros l JOIN carrito c ON l.id = c.libro_id WHERE c.usuario_id = ?");
        $stmt->execute([$user_id]);
        $carrito = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Favoritos
        $stmt = $pdo->prepare("SELECT libro_id FROM favoritos WHERE usuario_id = ?");
        $stmt->execute([$user_id]);
        $favoritos = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    echo json_encode(['books' => $libros, 'cart' => $carrito, 'wishlist_ids' => $favoritos]);
}

// --- ACCIÓN: ELIMINAR DEL CARRITO ---
if ($action == 'remove_from_cart') {
    $libro_id = $_POST['libro_id'] ?? null;
    if ($user_id && $libro_id) {
        $stmt = $pdo->prepare("DELETE FROM carrito WHERE usuario_id = ? AND libro_id = ? LIMIT 1");
        $stmt->execute([$user_id, $libro_id]);
        
        // Registrar log de auditoría
        registrarLogAuditoria($pdo, $user_id, 'CARRITO', "Eliminó el libro ID #$libro_id del carrito");
        
        echo json_encode(['status' => 'success']);
    } 
} 

// --- ACCIÓN: FINALIZAR COMPRA (CORREGIDA) ---
if ($action == 'checkout') {
    if (!$user_id) {
        echo json_encode(['status' => 'error', 'message' => 'Sesión no válida']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // 1. Obtener los items actuales del carrito para calcular el total
        $stmt = $pdo->prepare("SELECT l.id, l.price FROM libros l JOIN carrito c ON l.id = c.libro_id WHERE c.usuario_id = ?");
        $stmt->execute([$user_id]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($items) === 0) {
            throw new Exception("El carrito está vacío");
        }

        $total = 0;
        foreach ($items as $item) { 
            $total += $item['price']; 
        }

        // 2. Crear la cabecera del pedido
        $stmt = $pdo->prepare("INSERT INTO pedidos (usuario_id, total) VALUES (?, ?)");
        $stmt->execute([$user_id, $total]);
        $pedido_id = $pdo->lastInsertId();

        // 3. Crear el detalle del pedido
        $stmt = $pdo->prepare("INSERT INTO detalle_pedidos (pedido_id, libro_id, precio_unitario) VALUES (?, ?, ?)");
        foreach ($items as $item) {
            $stmt->execute([$pedido_id, $item['id'], $item['price']]);
        }

        // 4. Registrar en actividad_usuarios con el $user_id real y el $total calculado
        $desc_compra = "Realizó la compra del Pedido #" . $pedido_id . " por $" . number_format($total, 2);

        $stmt_act = $pdo->prepare("INSERT INTO actividad_usuarios (usuario_id, tipo_actividad, descripcion) VALUES (?, 'COMPRA', ?)");
        $stmt_act->execute([$user_id, $desc_compra]); // <-- Corregido: $user_id en lugar de $id_usuario

        // 5. Limpiar el carrito del usuario
        $stmt = $pdo->prepare("DELETE FROM carrito WHERE usuario_id = ?");
        $stmt->execute([$user_id]);

        $pdo->commit();
        echo json_encode(['status' => 'success', 'pedido_id' => $pedido_id]);

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

// --- ACCIÓN: TOGGLE FAVORITOS ---
if ($action == 'toggle_wishlist') {
    $libro_id = $_POST['libro_id'] ?? null;
    if ($user_id && $libro_id) {
        $check = $pdo->prepare("SELECT id FROM favoritos WHERE usuario_id = ? AND libro_id = ?");
        $check->execute([$user_id, $libro_id]);
        
        if ($check->fetch()) {
            $stmt = $pdo->prepare("DELETE FROM favoritos WHERE usuario_id = ? AND libro_id = ?");
            $stmt->execute([$user_id, $libro_id]);
            
            // Registrar log de auditoría
            registrarLogAuditoria($pdo, $user_id, 'FAVORITO', "Quitó el libro ID #$libro_id de sus favoritos");
            
            echo json_encode(['status' => 'removed']);
        } else {
            $stmt = $pdo->prepare("INSERT INTO favoritos (usuario_id, libro_id) VALUES (?, ?)");
            $stmt->execute([$user_id, $libro_id]);
            
            // Registrar log de auditoría
            registrarLogAuditoria($pdo, $user_id, 'FAVORITO', "Guardó el libro ID #$libro_id en sus favoritos");
            
            echo json_encode(['status' => 'added']);
        }
    }
}

// --- ACCIÓN: AÑADIR AL CARRITO ---
if ($action == 'add_to_cart') {
    $libro_id = $_POST['libro_id'] ?? null;
    if ($user_id && $libro_id) {
        $stmt = $pdo->prepare("INSERT INTO carrito (usuario_id, libro_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $libro_id]);
        
        // Registrar log de auditoría
        registrarLogAuditoria($pdo, $user_id, 'CARRITO', "Añadió el libro ID #$libro_id al carrito de compras");
        
        echo json_encode(['status' => 'success']);
    }
}
?>