<?php
/**
 * Test Database Connection
 * File ini untuk mengetes koneksi database dan melihat status tabel
 */

// Include database connection
require_once 'config/database.php';

// Test connection
echo "<h2>Database Connection Test</h2>";

if (testConnection()) {
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
    
    // Get database stats
    $stats = getDatabaseStats();
    echo "<h3>Database Statistics:</h3>";
    echo "<ul>";
    echo "<li>Total Events: " . $stats['total_events'] . "</li>";
    echo "<li>Total Mentors: " . $stats['total_mentors'] . "</li>";
    echo "<li>Total News: " . $stats['total_news'] . "</li>";
    echo "<li>Total Reviews: " . $stats['total_reviews'] . "</li>";
    echo "</ul>";
    
    // Check available tables
    echo "<h3>Available Tables:</h3>";
    $tables = ['event', 'mentor', 'news', 'review', 'documentation', 'expert_mentor'];
    echo "<ul>";
    foreach ($tables as $table) {
        if (tableExists($table)) {
            echo "<li style='color: green;'>✅ $table</li>";
            
            // Show table structure
            $structure = getTableStructure($table);
            if (!empty($structure)) {
                echo "<ul>";
                foreach ($structure as $column) {
                    echo "<li><strong>{$column['Field']}</strong> - {$column['Type']} " . 
                         ($column['Null'] === 'NO' ? '(NOT NULL)' : '(NULL)') . 
                         ($column['Key'] === 'PRI' ? ' - PRIMARY KEY' : '') . "</li>";
                }
                echo "</ul>";
            }
        } else {
            echo "<li style='color: red;'>❌ $table (not found)</li>";
        }
    }
    echo "</ul>";
    
    // Test sample queries
    echo "<h3>Sample Data Test:</h3>";
    try {
        $pdo = getDBConnection();
        
        // Test events
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM event");
        $eventCount = $stmt->fetch()['count'];
        echo "<p>Events in database: $eventCount</p>";
        
        // Test mentors
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM mentor");
        $mentorCount = $stmt->fetch()['count'];
        echo "<p>Mentors in database: $mentorCount</p>";
        
        // Test news
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM news");
        $newsCount = $stmt->fetch()['count'];
        echo "<p>News in database: $newsCount</p>";
        
        // Test reviews
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM review");
        $reviewCount = $stmt->fetch()['count'];
        echo "<p>Reviews in database: $reviewCount</p>";
        
        // Show sample data
        if ($eventCount > 0) {
            echo "<h4>Sample Events:</h4>";
            $stmt = $pdo->query("SELECT title, category, date FROM event ORDER BY date DESC LIMIT 3");
            $events = $stmt->fetchAll();
            echo "<ul>";
            foreach ($events as $event) {
                echo "<li>{$event['title']} ({$event['category']}) - {$event['date']}</li>";
            }
            echo "</ul>";
        }
        
        if ($mentorCount > 0) {
            echo "<h4>Sample Mentors:</h4>";
            $stmt = $pdo->query("SELECT name, description FROM mentor ORDER BY name LIMIT 3");
            $mentors = $stmt->fetchAll();
            echo "<ul>";
            foreach ($mentors as $mentor) {
                echo "<li>{$mentor['name']} - {$mentor['description']}</li>";
            }
            echo "</ul>";
        }
        
    } catch(Exception $e) {
        echo "<p style='color: red;'>❌ Error testing queries: " . $e->getMessage() . "</p>";
    }
    
} else {
    echo "<p style='color: red;'>❌ Database connection failed!</p>";
    echo "<p>Please check your database configuration in <code>config/database.php</code></p>";
}

echo "<hr>";
echo "<p><a href='login.php'>Go to Admin Login</a></p>";
echo "<p><a href='index.php'>Go to Admin Dashboard</a></p>";
?> 