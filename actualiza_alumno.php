<?php
require_once 'clase_bd.php';
require_once 'clase_alumno.php';

$errores = [];
$mensaje = '';
$alumnoData = null;

if (!isset($_GET['id']) && !isset($_POST['id'])) {
    header('Location: listado_alumnos.php');
    exit;
}

$id = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

try {
    $bd = new BD();
    $pdo = $bd->getConexion();
    $alumno = new Alumno($pdo);
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = trim($_POST['nombre'] ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        
        if (empty($nombre)) {
            $errores[] = "El nombre es obligatorio";
        } elseif (strlen($nombre) > 100) {
            $errores[] = "El nombre no puede exceder 100 caracteres";
        }
        
        if (empty($apellidos)) {
            $errores[] = "Los apellidos son obligatorios";
        } elseif (strlen($apellidos) > 100) {
            $errores[] = "Los apellidos no pueden exceder 100 caracteres";
        }
        
        if (empty($email)) {
            $errores[] = "El email es obligatorio";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El formato del email no es válido";
        } elseif (strlen($email) > 100) {
            $errores[] = "El email no puede exceder 100 caracteres";
        }
        
        if (empty($direccion)) {
            $errores[] = "La dirección es obligatoria";
        } elseif (strlen($direccion) > 200) {
            $errores[] = "La dirección no puede exceder 200 caracteres";
        }
        
        if (empty($telefono)) {
            $errores[] = "El teléfono es obligatorio";
        } elseif (!preg_match('/^[0-9]{9}$/', $telefono)) {
            $errores[] = "El teléfono debe tener exactamente 9 dígitos numéricos";
        }
        
        if (empty($errores)) {
            $alumno->setId($id);
            $alumno->setNombre($nombre);
            $alumno->setApellidos($apellidos);
            $alumno->setEmail($email);
            $alumno->setDireccion($direccion);
            $alumno->setTelefono($telefono);
            
            if ($alumno->actualiza()) {
                $mensaje = "Alumno actualizado correctamente";
                $alumnoData = $alumno->obtenerPorId($id);
            } else {
                $errores[] = "Error al actualizar el alumno";
            }
        } else {
            $alumnoData = [
                'id' => $id,
                'nombre' => $nombre,
                'apellidos' => $apellidos,
                'email' => $email,
                'direccion' => $direccion,
                'telefono' => $telefono
            ];
        }
    } else {
        $alumnoData = $alumno->obtenerPorId($id);
        if (!$alumnoData) {
            header('Location: listado_alumnos.php');
            exit;
        }
    }
    
    $bd->cerrarConexion();
} catch (Exception $e) {
    $errores[] = "Error de conexión: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Alumno</title>
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
            max-width: 600px;
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
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }
        input[type="text"],
        input[type="email"],
        input[type="tel"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="tel"]:focus {
            outline: none;
            border-color: #ff9800;
        }
        .btn-container {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }
        button, .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        .btn-warning {
            background-color: #ff9800;
            color: white;
        }
        .btn-warning:hover {
            background-color: #e68900;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        .errores {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
        .errores ul {
            list-style-position: inside;
            margin-left: 10px;
        }
        .mensaje-exito {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
        .info-id {
            background-color: #e7f3ff;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            color: #004085;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Actualizar Alumno</h1>
        
        <?php if ($alumnoData): ?>
            <div class="info-id">
                ID del Alumno: <?php echo htmlspecialchars($alumnoData['id']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($errores)): ?>
            <div class="errores">
                <strong>Se encontraron los siguientes errores:</strong>
                <ul>
                    <?php foreach ($errores as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($mensaje)): ?>
            <div class="mensaje-exito">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($alumnoData): ?>
            <form method="POST" action="">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($alumnoData['id']); ?>">
                
                <div class="form-group">
                    <label for="nombre">Nombre *</label>
                    <input type="text" id="nombre" name="nombre" 
                           value="<?php echo htmlspecialchars($alumnoData['nombre']); ?>" 
                           maxlength="100" required>
                </div>
                
                <div class="form-group">
                    <label for="apellidos">Apellidos *</label>
                    <input type="text" id="apellidos" name="apellidos" 
                           value="<?php echo htmlspecialchars($alumnoData['apellidos']); ?>" 
                           maxlength="100" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo htmlspecialchars($alumnoData['email']); ?>" 
                           maxlength="100" required>
                </div>
                
                <div class="form-group">
                    <label for="direccion">Dirección *</label>
                    <input type="text" id="direccion" name="direccion" 
                           value="<?php echo htmlspecialchars($alumnoData['direccion']); ?>" 
                           maxlength="200" required>
                </div>
                
                <div class="form-group">
                    <label for="telefono">Teléfono (9 dígitos) *</label>
                    <input type="tel" id="telefono" name="telefono" 
                           value="<?php echo htmlspecialchars($alumnoData['telefono']); ?>" 
                           pattern="[0-9]{9}" 
                           placeholder="ej: 612345678" required>
                </div>
                
                <div class="btn-container">
                    <button type="submit" class="btn-warning">Actualizar</button>
                    <a href="listado_alumnos.php" class="btn btn-secondary">Volver al Listado</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>