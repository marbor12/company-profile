<?php
/**
 * Database Configuration Example
 * Copy this file to database.php and update with your actual database credentials
 */

// Database configuration
$host = 'localhost';           // Your database host
$dbname = 'company_profil_idspora';  // Your database name
$username = 'your_username';   // Your database username
$password = 'your_password';   // Your database password

// PDO Options
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
];

/**
 * Get database connection
 * @return PDO
 */
function getDBConnection() {
    global $host, $dbname, $username, $password, $options;
    
    try {
        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password, $options);
        return $pdo;
    } catch(PDOException $e) {
        // Log error (in production, you might want to log to file)
        error_log("Database connection failed: " . $e->getMessage());
        
        // Show user-friendly error message
        die("Database connection failed. Please check your configuration.");
    }
}

/**
 * Test database connection
 * @return bool
 */
function testConnection() {
    try {
        $pdo = getDBConnection();
        return true;
    } catch(Exception $e) {
        return false;
    }
}

/**
 * Get database statistics
 * @return array
 */
function getDatabaseStats() {
    try {
        $pdo = getDBConnection();
        
        $stats = [];
        
        // Get total events
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM event");
        $stats['total_events'] = $stmt->fetch()['total'];
        
        // Get total mentors
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM mentor");
        $stats['total_mentors'] = $stmt->fetch()['total'];
        
        // Get total news
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM news");
        $stats['total_news'] = $stmt->fetch()['total'];
        
        // Get total reviews
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM review");
        $stats['total_reviews'] = $stmt->fetch()['total'];
        
        return $stats;
    } catch(Exception $e) {
        error_log("Error getting database stats: " . $e->getMessage());
        return [
            'total_events' => 0,
            'total_mentors' => 0,
            'total_news' => 0,
            'total_reviews' => 0
        ];
    }
}

/**
 * Check if table exists
 * @param string $tableName
 * @return bool
 */
function tableExists($tableName) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->query("SHOW TABLES LIKE '$tableName'");
        return $stmt->rowCount() > 0;
    } catch(Exception $e) {
        return false;
    }
}

/**
 * Get table structure
 * @param string $tableName
 * @return array
 */
function getTableStructure($tableName) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->query("DESCRIBE $tableName");
        return $stmt->fetchAll();
    } catch(Exception $e) {
        return [];
    }
}
?> 