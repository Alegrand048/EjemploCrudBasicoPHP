<?php
require_once 'clase_bd.php';
require_once 'clase_alumno.php';

$alumnos = [];
$error = '';

try {
    $bd = new BD();
    $pdo = $bd->getConexion();
    
    $alumno = new Alumno($pdo);
    $alumnos = $alumno->listado();
    
    $bd->cerrarConexion();
} catch (Exception $e) {
    $error = "Error al cargar los alumnos: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Alumnos</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }
        .header-buttons {
            margin-bottom: 20px;
            text-align: right;
        }
        .btn {
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
            font-size: 14px;
            cursor: pointer;
            border: none;
            transition: background-color 0.3s;
        }
        .btn-success {
            background-color: #4CAF50;
            color: white;
        }
        .btn-success:hover {
            background-color: #45a049;
        }
        .btn-warning {
            background-color: #ff9800;
            color: white;
            padding: 6px 12px;
            font-size: 12px;
        }
        .btn-warning:hover {
            background-color: #e68900;
        }
        .btn-danger {
            background-color: #f44336;
            color: white;
            padding: 6px 12px;
            font-size: 12px;
        }
        .btn-danger:hover {
            background-color: #da190b;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
        .tabla-container {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        td {
            color: #555;
        }
        .acciones {
            white-space: nowrap;
        }
        .acciones a {
            margin-right: 5px;
        }
        .sin-registros {
            text-align: center;
            padding: 40px;
            color: #999;
            font-size: 18px;
        }
        .confirmacion {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
        }
        .confirmacion-contenido {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            min-width: 300px;
        }
        .confirmacion-contenido h3 {
            margin-bottom: 15px;
            color: #333;
        }
        .confirmacion-contenido p {
            margin-bottom: 20px;
            color: #666;
        }
        .confirmacion-botones {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Listado de Alumnos</h1>
        
        <div class="header-buttons">
            <a href="alta_alumno.php" class="btn btn-success">+ Nuevo Alumno</a>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if (empty($alumnos)): ?>
            <div class="sin-registros">
                No hay alumnos registrados. ¡Añade el primero!
            </div>
        <?php else: ?>
            <div class="tabla-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Apellidos</th>
                            <th>Email</th>
                            <th>Dirección</th>
                            <th>Teléfono</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($alumnos as $alumno): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($alumno['id']); ?></td>
                                <td><?php echo htmlspecialchars($alumno['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($alumno['apellidos']); ?></td>
                                <td><?php echo htmlspecialchars($alumno['email']); ?></td>
                                <td><?php echo htmlspecialchars($alumno['direccion']); ?></td>
                                <td><?php echo htmlspecialchars($alumno['telefono']); ?></td>
                                <td class="acciones">
                                    <a href="actualiza_alumno.php?id=<?php echo $alumno['id']; ?>" 
                                       class="btn btn-warning">Actualizar</a>
                                    <a href="#" 
                                       class="btn btn-danger"
                                       onclick="confirmarEliminacion(<?php echo $alumno['id']; ?>, '<?php echo htmlspecialchars($alumno['nombre'] . ' ' . $alumno['apellidos'], ENT_QUOTES); ?>'); return false;">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Modal de confirmación -->
    <div id="modalConfirmacion" class="confirmacion">
        <div class="confirmacion-contenido">
            <h3>Confirmar eliminación</h3>
            <p id="mensajeConfirmacion"></p>
            <div class="confirmacion-botones">
                <button class="btn btn-danger" onclick="eliminar()">Eliminar</button>
                <button class="btn" style="background-color: #999; color: white;" onclick="cerrarModal()">Cancelar</button>
            </div>
        </div>
    </div>
    
    <script>
        let idEliminar = null;
        
        function confirmarEliminacion(id, nombre) {
            idEliminar = id;
            document.getElementById('mensajeConfirmacion').textContent = 
                '¿Está seguro de que desea eliminar al alumno "' + nombre + '"?';
            document.getElementById('modalConfirmacion').style.display = 'block';
        }
        
        function eliminar() {
            if (idEliminar) {
                window.location.href = 'eliminar_alumno.php?id=' + idEliminar;
            }
        }
        
        function cerrarModal() {
            document.getElementById('modalConfirmacion').style.display = 'none';
            idEliminar = null;
        }
        
        // Cerrar modal al hacer clic fuera
        window.onclick = function(event) {
            const modal = document.getElementById('modalConfirmacion');
            if (event.target === modal) {
                cerrarModal();
            }
        }
    </script>
</body>
</html>