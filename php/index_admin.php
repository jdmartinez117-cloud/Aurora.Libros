<?php
session_start();
// Protección de ruta (Descomenta cuando esté listo)
/*if(!isset($_SESSION['admin'])){
    header("location: index.php");
    exit;
}*/
$admin_email = isset($_SESSION['admin']) ? $_SESSION['admin'] : "Admin";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aurora.Libros | Panel de Control</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilos_admin.css">
</head>
<body>

<!-- SIDEBAR SIMPLIFICADA -->
<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo">AURORA<span>.ADMIN</span></div>
    </div>
    
    <nav class="sidebar-nav">
        <ul>
            <!-- Solo dejamos Productos como opción principal -->
            <li class="active" data-target="productos">
                <i class="fa-solid fa-book"></i> <span>Productos</span>
            </li>

            <li data-target="pedidos">
                <i class="fa-solid fa-receipt"></i> <span>Pedidos / Compras</span>
            </li>
        </ul>
    </nav>

    <div class="sidebar-footer">
        <a href="cerrar_sesion.php" class="logout-btn" title="Cerrar Sesión">
            <i class="fa-solid fa-right-from-bracket"></i> Salir
        </a>
    </div>
</aside>

<main class="main-content">
    <header class="top-bar">
        <div class="page-title">
            <h2 id="current-view-title">Gestión de Catálogo</h2>
        </div>
                <div class="admin-profile" id="admin-settings-container">
            <!-- BOTÓN ENGRANAJE DE CONFIGURACIÓN -->
            <button type="button" class="btn-admin-config" id="btn-toggle-admin-settings" title="Configuración de Administrador">
                <i class="fa-solid fa-gear"></i>
            </button>

            <div class="admin-info-chip">
                <span>Bienvenido, <strong><?php echo $admin_email; ?></strong></span>
                <div class="avatar"><i class="fa-solid fa-user-tie"></i></div>
            </div>

            <!-- MENÚ DESPLEGABLE ADMINISTRADOR -->
            <div id="admin-dropdown-menu" class="admin-dropdown">
                <div class="admin-dropdown-header">
                    <span class="badge-admin">Administrador</span>
                    <p class="admin-email-text"><?php echo $admin_email; ?></p>
                </div>

                <ul class="admin-dropdown-list">
                    <li>
                        <a href="javascript:void(0)" class="admin-drop-item">
                            <i class="fa-solid fa-id-badge"></i>
                            <span>Mi Perfil y Cargo</span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0)" class="admin-drop-item">
                            <i class="fa-solid fa-shield-halved"></i>
                            <span>Seguridad y Contraseña</span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0)" class="admin-drop-item">
                            <i class="fa-solid fa-sliders"></i>
                            <span>Ajustes del Catálogo</span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0)" class="admin-drop-item">
                            <i class="fa-solid fa-clipboard-list"></i>
                            <span>Registro de Actividad</span>
                        </a>
                    </li>

                    <li class="admin-drop-divider"></li>

                    <!-- INTERRUPTOR MODO OSCURO -->
                    <li class="admin-drop-item dark-switch-item" id="admin-dark-mode-trigger">
                        <div class="switch-left">
                            <i class="fa-solid fa-moon text-amber-500" id="admin-theme-icon"></i>
                            <span>Modo Oscuro</span>
                        </div>
                        <div class="admin-switch" id="admin-theme-switch">
                            <div class="admin-switch-ball"></div>
                        </div>
                    </li>

                    <li class="admin-drop-divider"></li>

                    <li>
                        <a href="cerrar_sesion.php" class="admin-drop-item logout">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            <span>Cerrar Sesión</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <div class="content-wrapper">
        <!-- SECCIÓN DE PRODUCTOS (Ahora es la principal) -->
        <section id="productos" class="view-section active">
            <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3>Libros en Inventario</h3>
                <button class="btn-primary" onclick="prepararNuevoLibro()">
                    <i class="fa-solid fa-plus"></i> Añadir Nuevo Libro
                </button>
            </div>

            <div class="filter-bar">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="busqueda-libro" placeholder="Buscar por título, autor o categoría..." onkeyup="filtrarLibros()">
            </div>

            <div class="table-container">
                <table class="admin-table" id="tabla-libros">
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>Título</th>
                            <th>Autor</th>
                            <th>Categoría</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include 'conexion_be.php'; 
                        $query = "SELECT * FROM libros ORDER BY id DESC";
                        $resultado = mysqli_query($conexion, $query);
                        while($row = mysqli_fetch_assoc($resultado)){
                        ?>
                        <tr>
                            <td><img src="img_libros/<?php echo $row['image']; ?>" class="img-mini" alt="Portada"></td>
                            <td class="book-title"><strong><?php echo $row['titulo']; ?></strong></td>
                            <td><?php echo $row['author']; ?></td>
                            <td><span class="badge info"><?php echo $row['category']; ?></span></td>
                            <td>$<?php echo number_format($row['price'], 2); ?></td>
                            <td><?php echo $row['stock']; ?></td>
                            <td>
                                <button class="btn-icon edit" onclick='abrirEditarLibro(<?php echo json_encode($row); ?>)'>
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                 <button class="btn-icon delete" onclick="eliminarLibro(<?php echo $row['id']; ?>)">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </section>
                <!-- NUEVA SECCIÓN DE PEDIDOS -->
        <!-- NUEVA SECCIÓN DE PEDIDOS AGRUPADOS -->
<section id="pedidos" class="view-section">
    <div class="section-header" style="margin-bottom: 20px;">
        <h3>Historial de Ventas Agrupado</h3>
    </div>

    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID Pedido</th>
                    <th>Fecha y Hora</th>
                    <th>Cliente</th>
                    <th>Detalle de Productos</th> <!-- Aquí agruparemos los libros -->
                    <th>Total Pagado</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include 'conexion_be.php';
                
                // CONSULTA CON GROUP_CONCAT PARA AGRUPAR POR PEDIDO
                $query_pedidos = "SELECT p.id AS pedido_id, p.fecha, p.total AS total_pago, 
                                         u.nombre_completo, 
                                         GROUP_CONCAT(l.titulo SEPARATOR '||') AS lista_libros,
                                         GROUP_CONCAT(dp.precio_unitario SEPARATOR '||') AS lista_precios
                                  FROM pedidos p
                                  JOIN usuarios u ON p.usuario_id = u.id
                                  JOIN detalle_pedidos dp ON p.id = dp.pedido_id
                                  JOIN libros l ON dp.libro_id = l.id
                                  GROUP BY p.id 
                                  ORDER BY p.fecha DESC";
                
                $res_pedidos = mysqli_query($conexion, $query_pedidos);
                
                while($p = mysqli_fetch_assoc($res_pedidos)){
                    // Convertimos las cadenas separadas por || en arrays de PHP
                    $libros = explode('||', $p['lista_libros']);
                    $precios = explode('||', $p['lista_precios']);
                ?>
                <tr>
                    <td><span class="badge info">#<?php echo $p['pedido_id']; ?></span></td>
                    <td style="font-size: 0.85rem;">
                        <i class="fa-regular fa-calendar-days"></i> <?php echo date('d/m/Y', strtotime($p['fecha'])); ?><br>
                        <i class="fa-regular fa-clock"></i> <?php echo date('H:i', strtotime($p['fecha'])); ?>
                    </td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div class="avatar-mini"><i class="fa-solid fa-circle-user"></i></div>
                            <strong><?php echo $p['nombre_completo']; ?></strong>
                        </div>
                    </td>
                    <td>
                        <!-- LISTA INTERNA DE PRODUCTOS -->
                        <ul style="list-style: none; padding: 0; margin: 0; font-size: 0.9rem;">
                            <?php foreach($libros as $index => $nombre_libro): ?>
                            <li style="border-bottom: 1px solid #f0f0f0; padding: 4px 0; display: flex; justify-content: space-between; gap: 20px;">
                                <span class="text-stone-600">• <?php echo $nombre_libro; ?></span>
                                <span style="color: #999;">$<?php echo number_format($precios[$index], 2); ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </td>
                    <td>
                        <strong style="color: var(--amber-700); font-size: 1.1rem;">
                            $<?php echo number_format($p['total_pago'], 2); ?>
                        </strong>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</section>
    </div>
</main>

<!-- MODAL ÚNICO PARA REGISTRO Y EDICIÓN -->
<div id="modal-libro" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h4 id="modal-titulo-texto">Registrar Nuevo Ejemplar</h4>
            <span class="close" onclick="toggleModal('modal-libro', false)">&times;</span>
        </div>
        
        <form action="guardar_libro_be.php" method="POST" enctype="multipart/form-data" class="admin-form" id="form-libro">
            <!-- Campo oculto para el ID (vacío al crear, lleno al editar) -->
            <input type="hidden" name="id" id="edit-id">
            
            <div class="form-grid">
                <div class="form-group">
                    <label>Título del Libro</label>
                    <input type="text" name="titulo" id="edit-titulo" required>
                </div>
                <div class="form-group">
                    <label>Autor</label>
                    <input type="text" name="author" id="edit-author" required>
                </div>
                <div class="form-group">
                    <label>Categoría</label>
                    <select name="category" id="edit-category" required>
                        <option value="Literatura">Literatura</option>
                        <option value="Académicos">Académicos</option>
                        <option value="Misterio">Misterio</option>
                        <option value="Ciencia Ficción">Ciencia Ficción</option>
                        <option value="Infantiles">Infantiles</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Precio ($)</label>
                    <input type="number" step="0.01" name="price" id="edit-price" required>
                </div>
                <div class="form-group">
                    <label>Stock (Cantidad)</label>
                    <input type="number" name="stock" id="edit-stock" required>
                </div>
                <div class="form-group">
                    <label>Portada (Imagen)</label>
                    <input type="file" name="image" id="edit-image" accept="image/*">
                    <small id="aviso-imagen" style="display:none; color: #b45309;">
                        * Dejar vacío para conservar la imagen actual
                    </small>
                </div>
            </div>
            <div class="form-group full-width" style="margin-top: 15px;">
                <label>Descripción</label>
                <textarea name="description" id="edit-description" rows="3" required style="width: 100%; resize: none;"></textarea>
            </div>
            <button type="submit" class="btn-save" id="btn-submit-modal">Guardar Libro</button>
        </form>
    </div>
</div>

<script src="script_admin.js"></script>
</body>
</html>