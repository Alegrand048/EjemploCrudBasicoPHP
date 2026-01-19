<?php
class BD {
    private $conexion;
    
    public function __construct($archivoConfig = 'config.ini') {
        if (!file_exists($archivoConfig)) {
            throw new Exception("El archivo de configuración no existe: $archivoConfig");
        }
        
        $config = parse_ini_file($archivoConfig, true);
        
        if (!isset($config['database'])) {
            throw new Exception("Sección 'database' no encontrada en el archivo de configuración");
        }
        
        $db = $config['database'];
        $host = $db['host'] ?? 'localhost';
        $dbname = $db['dbname'] ?? '';
        $username = $db['username'] ?? 'root';
        $password = $db['password'] ?? '';
        $charset = $db['charset'] ?? 'utf8mb4';
        
        try {
            $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
            $this->conexion = new PDO($dsn, $username, $password);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error de conexión: " . $e->getMessage());
        }
    }
    
    public function getConexion() {
        return $this->conexion;
    }
    
    public function cerrarConexion() {
        $this->conexion = null;
    }
}
?>