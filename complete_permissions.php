<?php
require_once 'vendor/autoload.php';

// Configurar conexión a la base de datos
$pdo = new PDO('mysql:host=127.0.0.1;dbname=nabiodbsolutions', 'serterobio', 'y/MkMAJpeaSn0A.7');

// Definir qué permisos debe tener cada rol
$rolePermissions = [
    'editor' => [
        'create recommendations',
        'view recommendations',
        'update recommendations'
    ],
    'doctor' => [
        'create recommendations',
        'view recommendations',
        'update recommendations'
    ],
    'therapist' => [
        'create recommendations',
        'view recommendations',
        'update recommendations'
    ],
    'nurse' => [
        'view recommendations'
    ],
    'receptionist' => [
        'view recommendations'
    ]
];

// Asignar permisos
foreach ($rolePermissions as $roleName => $permissions) {
    // Obtener ID del rol
    $roleStmt = $pdo->prepare("SELECT id FROM roles WHERE name = ? LIMIT 1");
    $roleStmt->execute([$roleName]);
    $roleId = $roleStmt->fetchColumn();
    
    if ($roleId) {
        echo "\nAsignando permisos al rol '{$roleName}'...\n";
        
        foreach ($permissions as $permissionName) {
            // Obtener ID del permiso
            $permStmt = $pdo->prepare("SELECT id FROM permissions WHERE name = ? LIMIT 1");
            $permStmt->execute([$permissionName]);
            $permissionId = $permStmt->fetchColumn();
            
            if ($permissionId) {
                try {
                    $stmt = $pdo->prepare("INSERT IGNORE INTO role_has_permissions (permission_id, role_id) VALUES (?, ?)");
                    $stmt->execute([$permissionId, $roleId]);
                    echo "  ✓ Asignado: {$permissionName}\n";
                } catch (Exception $e) {
                    echo "  ✗ Error asignando {$permissionName}: " . $e->getMessage() . "\n";
                }
            }
        }
    } else {
        echo "Rol '{$roleName}' no encontrado\n";
    }
}

echo "\nProceso completado!\n";

// Verificar la asignación final
echo "\nVerificación final de permisos:\n";
$stmt = $pdo->query("
    SELECT r.name as role_name, p.name as permission_name 
    FROM roles r
    JOIN role_has_permissions rhp ON r.id = rhp.role_id
    JOIN permissions p ON rhp.permission_id = p.id
    WHERE p.name LIKE '%recommendations%'
    ORDER BY r.name, p.name
");

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "{$row['role_name']}: {$row['permission_name']}\n";
}
?>