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

// --- NUEVA ACCIÓN: ELIMINAR DEL CARRITO ---
if ($action == 'remove_from_cart') {
    $libro_id = $_POST['libro_id'] ?? null;
    if ($user_id && $libro_id) {
        // Borramos solo uno (LIMIT 1) por si tiene duplicados
        $stmt = $pdo->prepare("DELETE FROM carrito WHERE usuario_id = ? AND libro_id = ? LIMIT 1");
        $stmt->execute([$user_id, $libro_id]);
        echo json_encode(['status' => 'success']);
    }
}

// --- NUEVA ACCIÓN: FINALIZAR COMPRA ---
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
        foreach ($items as $item) { $total += $item['price']; }

        // 2. Crear la cabecera del pedido
        $stmt = $pdo->prepare("INSERT INTO pedidos (usuario_id, total) VALUES (?, ?)");
        $stmt->execute([$user_id, $total]);
        $pedido_id = $pdo->lastInsertId();

        // 3. Crear el detalle del pedido
        $stmt = $pdo->prepare("INSERT INTO detalle_pedidos (pedido_id, libro_id, precio_unitario) VALUES (?, ?, ?)");
        foreach ($items as $item) {
            $stmt->execute([$pedido_id, $item['id'], $item['price']]);
        }

        // 4. Limpiar el carrito del usuario
        $stmt = $pdo->prepare("DELETE FROM carrito WHERE usuario_id = ?");
        $stmt->execute([$user_id]);

        $pdo->commit();
        echo json_encode(['status' => 'success', 'pedido_id' => $pedido_id]);

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

// --- NUEVA ACCIÓN: TOGGLE FAVORITOS ---
if ($action == 'toggle_wishlist') {
    $libro_id = $_POST['libro_id'] ?? null;
    if ($user_id && $libro_id) {
        // Verificar si ya existe
        $check = $pdo->prepare("SELECT id FROM favoritos WHERE usuario_id = ? AND libro_id = ?");
        $check->execute([$user_id, $libro_id]);
        
        if ($check->fetch()) {
            // Si existe, lo quitamos
            $stmt = $pdo->prepare("DELETE FROM favoritos WHERE usuario_id = ? AND libro_id = ?");
            $stmt->execute([$user_id, $libro_id]);
            echo json_encode(['status' => 'removed']);
        } else {
            // Si no existe, lo ponemos
            $stmt = $pdo->prepare("INSERT INTO favoritos (usuario_id, libro_id) VALUES (?, ?)");
            $stmt->execute([$user_id, $libro_id]);
            echo json_encode(['status' => 'added']);
        }
    }
}

// Acción de añadir (ya la tenías, se mantiene igual)
if ($action == 'add_to_cart') {
    $libro_id = $_POST['libro_id'] ?? null;
    if ($user_id && $libro_id) {
        $stmt = $pdo->prepare("INSERT INTO carrito (usuario_id, libro_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $libro_id]);
        echo json_encode(['status' => 'success']);
    }
}