<?php
class Alumno {
    private $id;
    private $nombre;
    private $apellidos;
    private $email;
    private $direccion;
    private $telefono;
    private $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    // Getters
    public function getId() { return $this->id; }
    public function getNombre() { return $this->nombre; }
    public function getApellidos() { return $this->apellidos; }
    public function getEmail() { return $this->email; }
    public function getDireccion() { return $this->direccion; }
    public function getTelefono() { return $this->telefono; }
    
    // Setters
    public function setId($id) { $this->id = $id; }
    public function setNombre($nombre) { $this->nombre = $nombre; }
    public function setApellidos($apellidos) { $this->apellidos = $apellidos; }
    public function setEmail($email) { $this->email = $email; }
    public function setDireccion($direccion) { $this->direccion = $direccion; }
    public function setTelefono($telefono) { $this->telefono = $telefono; }
    
    /**
     * Inserta un nuevo alumno en la base de datos
     * @return bool True si se insertó correctamente, false en caso contrario
     */
    public function insertar() {
        try {
            $sql = "INSERT INTO alumno (nombre, apellidos, email, direccion, telefono) 
                    VALUES (:nombre, :apellidos, :email, :direccion, :telefono)";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':apellidos', $this->apellidos);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':direccion', $this->direccion);
            $stmt->bindParam(':telefono', $this->telefono);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al insertar alumno: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Elimina un alumno de la base de datos por su ID
     * @param int $id ID del alumno a eliminar
     * @return bool True si se eliminó correctamente, false en caso contrario
     */
    public function eliminar($id) {
        try {
            $sql = "DELETE FROM alumno WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al eliminar alumno: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene el listado completo de alumnos
     * @return array Array asociativo con todos los alumnos
     */
    public function listado() {
        try {
            $sql = "SELECT * FROM alumno ORDER BY apellidos, nombre";
            $stmt = $this->pdo->query($sql);
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener listado: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Actualiza los datos de un alumno en la base de datos
     * @return bool True si se actualizó correctamente, false en caso contrario
     */
    public function actualiza() {
        try {
            $sql = "UPDATE alumno 
                    SET nombre = :nombre, 
                        apellidos = :apellidos, 
                        email = :email, 
                        direccion = :direccion, 
                        telefono = :telefono 
                    WHERE id = :id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':apellidos', $this->apellidos);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':direccion', $this->direccion);
            $stmt->bindParam(':telefono', $this->telefono);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar alumno: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene un alumno por su ID
     * @param int $id ID del alumno
     * @return array|null Array asociativo con los datos del alumno o null si no existe
     */
    public function obtenerPorId($id) {
        try {
            $sql = "SELECT * FROM alumno WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetch();
            return $resultado ?: null;
        } catch (PDOException $e) {
            error_log("Error al obtener alumno: " . $e->getMessage());
            return null;
        }
    }
}
?>