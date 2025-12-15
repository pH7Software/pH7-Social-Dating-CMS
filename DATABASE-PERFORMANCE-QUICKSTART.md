# Database Performance Features - Quick Start Guide

This guide explains how to use the new database performance features in pH7Builder v18.

## 🚀 Features Overview

### 1. Database Load Balancer
Routes database queries to appropriate servers:
- **Write queries** → Master database
- **Read queries** → Slave databases (round-robin)
- **Automatic failover** if slaves are unavailable

### 2. Connection Pooling
Maintains a pool of reusable database connections:
- Reduces connection overhead
- Configurable pool size
- Automatic connection health checks

### 3. Enhanced File Operations
Better error handling for file operations:
- Detailed error messages with constants
- Automatic validation and logging
- Standardized JSON error responses

## 📋 Quick Setup

### Option 1: Using Environment Variables (Recommended)

Add to your `.env` file or server configuration:

```bash
# Enable load balancing
DB_ENABLE_LOAD_BALANCING=true
DB_MASTER_HOST=master.example.com
DB_SLAVE_HOSTS=slave1.example.com,slave2.example.com

# Enable connection pooling
DB_ENABLE_CONNECTION_POOL=true
DB_POOL_SIZE=15
```

Then add to `_protected/app/Bootstrap.php`:

```php
// After Db::getInstance() call
if (getenv('DB_ENABLE_LOAD_BALANCING') === 'true') {
    require PH7_PATH_APP . 'configs/bootstrap-integration.example.php';
}
```

### Option 2: Direct Configuration

In `_protected/app/Bootstrap.php`, after database initialization:

```php
use PH7\Framework\Mvc\Model\Engine\Db;

// Enable load balancing
Db::enableLoadBalancer(
    [
        'dsn' => 'mysql:host=master.db;dbname=ph7cms',
        'username' => 'user',
        'password' => 'pass',
        'options' => []
    ],
    [
        ['dsn' => 'mysql:host=slave1.db;dbname=ph7cms', 'username' => 'user', 'password' => 'pass'],
        ['dsn' => 'mysql:host=slave2.db;dbname=ph7cms', 'username' => 'user', 'password' => 'pass']
    ]
);

// Enable connection pooling
Db::enableConnectionPool(10);
```

## 💡 Usage Examples

### Using PerformanceHelper (Easiest)

```php
use PH7\Framework\Mvc\Model\Engine\Util\PerformanceHelper;

// Read query (automatically uses slave if available)
$oStmt = PerformanceHelper::queryRead(
    'SELECT * FROM users WHERE active = :active',
    [':active' => 1]
);
$aUsers = $oStmt->fetchAll(PDO::FETCH_OBJ);

// Write query (automatically uses master)
PerformanceHelper::queryWrite(
    'UPDATE users SET lastLogin = NOW() WHERE id = :id',
    [':id' => $iUserId]
);

// Batch reads for better performance
$aResults = PerformanceHelper::batchRead([
    ['query' => 'SELECT COUNT(*) FROM users'],
    ['query' => 'SELECT * FROM settings WHERE active = 1']
]);
```

### Direct Database Connection

```php
use PH7\Framework\Mvc\Model\Engine\Db;

// Get connection for reading
$oReadConn = Db::getConnection(false);
$oStmt = $oReadConn->prepare('SELECT * FROM users');
$oStmt->execute();

// Get connection for writing
$oWriteConn = Db::getConnection(true);
$oStmt = $oWriteConn->prepare('INSERT INTO logs VALUES (...)');
$oStmt->execute();
```

### File Operations with Better Errors

```php
use PH7\Framework\File\File;

$oFile = new File();
$mResult = $oFile->putFile('/path/to/file.txt', 'content');

if (is_array($mResult) && isset($mResult['error'])) {
    // Detailed error with automatic logging
    $this->displayError($mResult['error']);
} else {
    // Success
    $this->displaySuccess("File written successfully");
}
```

## 📊 Monitoring Performance

### Get Statistics

```php
use PH7\Framework\Mvc\Model\Engine\Util\PerformanceHelper;

$aStats = PerformanceHelper::getPerformanceStats();

echo "Query Count: " . $aStats['query_count'] . "\n";
echo "Query Time: " . $aStats['query_time'] . "s\n";

// Load balancer stats
if ($aStats['load_balancer']) {
    print_r($aStats['load_balancer']);
}

// Connection pool stats
if ($aStats['connection_pool']) {
    print_r($aStats['connection_pool']);
}
```

### Create Admin Dashboard Widget

Add to your admin panel:

```php
$aStats = \PH7\Framework\Mvc\Model\Engine\Db::getLoadBalancerStats();

echo "<div class='stats-widget'>";
echo "<h3>Database Performance</h3>";
echo "<p>Master Nodes: {$aStats['master_nodes']}</p>";
echo "<p>Slave Nodes: {$aStats['slave_nodes']}</p>";
echo "<p>Active Connections: {$aStats['active_slave_connections']}</p>";
echo "</div>";
```

## 🔧 Configuration Tips

### For Small Sites (< 1000 daily visitors)
- **Connection Pool**: Yes (size: 5-10)
- **Load Balancer**: Not necessary

### For Medium Sites (1000-10000 daily visitors)
- **Connection Pool**: Yes (size: 10-20)
- **Load Balancer**: Optional (if you have replication)

### For Large Sites (> 10000 daily visitors)
- **Connection Pool**: Yes (size: 20-50)
- **Load Balancer**: Highly recommended (2-3 slaves minimum)

### Pool Size Guidelines
- Calculate: `(concurrent_users / 10) + 5`
- Monitor in-use connections
- Increase if you see pool exhaustion errors

## ⚠️ Important Notes

1. **Load balancing requires MySQL replication** to be properly configured
2. **Test thoroughly** in development before production
3. **Monitor statistics** to optimize configuration
4. **Backup before enabling** new features
5. **Read operations** include: SELECT, SHOW, DESCRIBE
6. **Write operations** include: INSERT, UPDATE, DELETE, CREATE, ALTER, DROP

## 🐛 Troubleshooting

### Load Balancer Issues
```php
// Check if load balancer is working
$aStats = Db::getLoadBalancerStats();
if ($aStats === null) {
    echo "Load balancer not enabled";
}
```

### Connection Pool Issues
```php
// Check pool statistics
$aStats = Db::getConnectionPoolStats();
if ($aStats['in_use_connections'] >= $aStats['max_pool_size']) {
    echo "Pool size too small, increase it!";
}
```

### File Operation Errors
All file errors are automatically logged. Check:
- `_protected/data/log/pH7log/logfile.log`

## 📚 Additional Resources

- Full documentation: `/V18-PERFORMANCE-IMPROVEMENTS.md`
- Configuration examples: `/_protected/app/configs/database-advanced.example.php`
- Bootstrap integration: `/_protected/app/configs/bootstrap-integration.example.php`

## 🎯 Best Practices

1. ✅ Use `PerformanceHelper` for cleaner code
2. ✅ Enable connection pooling first, test, then add load balancing
3. ✅ Monitor statistics regularly
4. ✅ Use read connections for all SELECT queries
5. ✅ Use write connections for all INSERT/UPDATE/DELETE
6. ✅ Check file operation results for error arrays
7. ✅ Set appropriate pool sizes based on traffic
8. ✅ Configure health checks for database nodes

## 🔄 Migration from Old Code

Old code continues to work without changes:

```php
// Old way (still works)
$oDb = Db::getInstance();
$oStmt = $oDb->prepare('SELECT * FROM users');

// New way (optimized)
$oStmt = PerformanceHelper::queryRead('SELECT * FROM users');
```

Both methods work, but the new way automatically benefits from load balancing if enabled.
