<?php
require_once 'clase_bd.php';
require_once 'clase_alumno.php';

$mensaje = '';
$error = '';
$exito = false;

if (!isset($_GET['id'])) {
    header('Location: listado_alumnos.php');
    exit;
}

$id = $_GET['id'];

try {
    $bd = new BD();
    $pdo = $bd->getConexion();
    
    $alumno = new Alumno($pdo);
    
    // Obtener datos del alumno antes de eliminar
    $alumnoData = $alumno->obtenerPorId($id);
    
    if (!$alumnoData) {
        $error = "El alumno no existe o ya fue eliminado";
    } else {
        // Eliminar el alumno
        if ($alumno->eliminar($id)) {
            $exito = true;
            $mensaje = "El alumno " . $alumnoData['nombre'] . " " . $alumnoData['apellidos'] . " ha sido eliminado correctamente";
        } else {
            $error = "Error al eliminar el alumno";
        }
    }
    
    $bd->cerrarConexion();
} catch (Exception $e) {
    $error = "Error de conexión: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Alumno</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            max-width: 600px;
            width: 100%;
            background-color: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .icono {
            font-size: 80px;
            margin-bottom: 20px;
        }
        .icono-exito {
            color: #4CAF50;
        }
        .icono-error {
            color: #f44336;
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        .mensaje-exito {
            background-color: #d4edda;
            color: #155724;
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 30px;
            border: 1px solid #c3e6cb;
            font-size: 16px;
            line-height: 1.6;
        }
        .mensaje-error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 30px;
            border: 1px solid #f5c6cb;
            font-size: 16px;
            line-height: 1.6;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
            border: none;
        }
        .btn-primary {
            background-color: #2196F3;
            color: white;
        }
        .btn-primary:hover {
            background-color: #0b7dda;
        }
        .btn-success {
            background-color: #4CAF50;
            color: white;
            margin-left: 10px;
        }
        .btn-success:hover {
            background-color: #45a049;
        }
        .botones {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($exito): ?>
            <div class="icono icono-exito">✓</div>
            <h1>Registro Eliminado Correctamente</h1>
            <div class="mensaje-exito">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php else: ?>
            <div class="icono icono-error">✕</div>
            <h1>Error al Eliminar</h1>
            <div class="mensaje-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <div class="botones">
            <a href="listado_alumnos.php" class="btn btn-primary">Volver al Listado</a>
            <?php if ($exito): ?>
                <a href="alta_alumno.php" class="btn btn-success">Añadir Nuevo Alumno</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>