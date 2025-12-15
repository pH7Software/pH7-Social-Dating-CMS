# pH7Builder v18 - Performance & Feature Improvements

## Performance Optimizations

### Database Performance
- ✅ **Optimized Session Deletion**: Implemented transaction-based batch cleanup for session tables
  - Sessions now deleted using `TRUNCATE` in atomic transactions
  - Reduced lock time and improved cleanup speed
  - Added rollback support for failed operations

- ✅ **MySQL Load Balancer**: New `DbLoadBalancer` class for multi-node database support
  - Master-slave replication support
  - Round-robin load balancing for read operations
  - Automatic fallback to master if slaves fail
  - Connection health monitoring

- ✅ **Database Connection Pooling**: New `DbConnectionPool` class
  - Configurable connection pool size (default: 10 connections)
  - Reuses connections for better performance
  - Persistent connection support
  - Connection health checks

### Caching Improvements
- Enhanced service calls cache with configurable timeouts
- Optimized cache groups for different data types
- Reduced database queries through better caching

## Developer Tools

### JavaScript Dependency Management
- ✅ **package.json Added**: Full npm/yarn support
  - Security vulnerability scanning via GitHub
  - Easy dependency updates
  - ESLint and Prettier for code quality
  - Scripts for linting, formatting, and security audits

### Error Handling
- ✅ **Improved Error Logging**: Standardized error messages with constants
  - Consistent error formatting across codebase
  - Better debugging with sprintf-based messages
  - Centralized error message management

- ✅ **File Operation Errors**: New `FileOperationError` class
  - Simplified, consistent error responses
  - Standardized JSON error format
  - Automatic error logging
  - Success response helpers

### Database Error Handling
- Transaction support with automatic rollback
- Better exception handling in batch operations
- Detailed error logging for connection failures

## Storage Improvements

### Amazon S3 Enhancements
- ✅ **Enhanced S3 URL Handling**:
  - `getSignedUrl()`: Secure temporary URLs with configurable expiration
  - `getSignedPdfUrl()`: Specialized PDF preview support with inline display
  - `getPublicUrl()`: Public URLs for embedded images
  - Proper content-type handling for different file types

### PDF Preview Support
- Authorized S3 URLs with time-limited access
- Inline PDF display support
- Configurable URL expiration (default: 20 minutes)

## Code Quality

### Cleanup
- ✅ **Removed Badoo References**: Cleaned up marketing comparisons
- Improved code documentation
- Better constant usage for maintainability

## Files Added/Modified

### New Files
- `/package.json` - JavaScript dependency management
- `/_protected/framework/Mvc/Model/Engine/DbLoadBalancer.class.php` - Database load balancing
- `/_protected/framework/Mvc/Model/Engine/DbConnectionPool.class.php` - Connection pooling
- `/_protected/framework/File/FileOperationError.class.php` - Standardized file error handling
- `/_protected/framework/Mvc/Model/Engine/Util/PerformanceHelper.class.php` - Helper for using advanced DB features
- `/_protected/app/configs/database-advanced.example.php` - Configuration examples

### Modified Files
- `/_protected/framework/Image/AmazonCloudStorage.class.php` - Enhanced S3 URL handling
- `/_protected/app/system/core/assets/cron/96h/DatabaseCoreCron.php` - Optimized session deletion
- `/_protected/framework/Layout/Gzip/Gzip.class.php` - Improved error logging
- `/_protected/framework/File/File.class.php` - **Integrated FileOperationError for better error handling**
- `/_protected/framework/Mvc/Model/Engine/Db.class.php` - **Integrated DbLoadBalancer and DbConnectionPool support**
- `/README.md` - Cleaned up marketing references

## Integration Details

### FileOperationError Integration
The `File` class now automatically uses `FileOperationError` for better error handling:
- `getFile()` returns error arrays instead of just false on failure
- `putFile()` validates permissions and returns detailed error information
- All file operations include automatic error logging

**Backward Compatibility**: Methods still return false on simple failures, but also return error arrays for detailed diagnostics.

### Database Load Balancer Integration  
The `Db` class now supports load balancing through new methods:
- `Db::enableLoadBalancer()` - Configure master-slave replication
- `Db::getConnection($bForWrite)` - Get connection (automatically routes to master/slave)
- `Db::getLoadBalancerStats()` - Monitor load balancing performance

### Connection Pool Integration
The `Db` class supports connection pooling:
- `Db::enableConnectionPool($iMaxSize)` - Enable pooling with configurable size
- `Db::getConnectionPoolStats()` - Monitor pool usage

### Performance Helper
New `PerformanceHelper` class provides convenient methods:
- `queryRead()` - Execute read queries on slaves
- `queryWrite()` - Execute write queries on master
- `batchRead()` - Execute multiple read queries efficiently
- `getPerformanceStats()` - Get comprehensive performance metrics

## Usage Examples

### Using File Operation Errors
```php
use PH7\Framework\File\File;

$oFile = new File();

// Writing a file - now returns detailed error info
$mResult = $oFile->putFile('/path/to/file.txt', 'content');

if (is_array($mResult) && isset($mResult['error'])) {
    // Detailed error occurred
    error_log($mResult['error']);
    echo "Error: " . $mResult['error'];
} elseif ($mResult === false) {
    // Simple failure
    echo "Failed to write file";
} else {
    // Success - $mResult contains bytes written
    echo "Written {$mResult} bytes";
}

// Reading a file
$mContent = $oFile->getFile('/path/to/file.txt');

if (is_array($mContent) && isset($mContent['error'])) {
    error_log($mContent['error']);
} else {
    echo $mContent; // File content
}

// Direct usage of FileOperationError
use PH7\Framework\File\FileOperationError;

return FileOperationError::successResponse('File uploaded successfully', [
    'file_path' => $sFilePath,
    'file_size' => filesize($sFilePath)
]);
```

### Using Performance Helper
```php
use PH7\Framework\Mvc\Model\Engine\Util\PerformanceHelper;

// Read query (uses slave if available)
$oStmt = PerformanceHelper::queryRead(
    'SELECT * FROM users WHERE status = :status',
    [':status' => 'active']
);
$aUsers = $oStmt->fetchAll(\PDO::FETCH_OBJ);

// Write query (always uses master)
$oStmt = PerformanceHelper::queryWrite(
    'UPDATE users SET lastActivity = NOW() WHERE id = :id',
    [':id' => $iUserId]
);

// Batch read operations
$aResults = PerformanceHelper::batchRead([
    ['query' => 'SELECT COUNT(*) as total FROM users', 'params' => []],
    ['query' => 'SELECT * FROM settings WHERE active = :active', 'params' => [':active' => 1]]
]);

// Get performance statistics
$aStats = PerformanceHelper::getPerformanceStats();
print_r($aStats);
```

### Using the Database Load Balancer
```php
$oBalancer = new DbLoadBalancer();

// Add master node
$oBalancer->addMasterNode('mysql:host=master;dbname=db', 'user', 'pass');

// Add slave nodes
$oBalancer->addSlaveNode('mysql:host=slave1;dbname=db', 'user', 'pass');
$oBalancer->addSlaveNode('mysql:host=slave2;dbname=db', 'user', 'pass');

// Get connections
$oWriteConn = $oBalancer->getWriteConnection(); // Uses master
$oReadConn = $oBalancer->getReadConnection();   // Uses slave (round-robin)
```

### Using S3 Signed URLs
```php
$oStorage = new AmazonCloudStorage($sTempFile, $sBucket);

// For embedded images (public)
$sImageUrl = $oStorage->getPublicUrl('images/photo.jpg');

// For secure file access
$sSecureUrl = $oStorage->getSignedUrl('documents/report.pdf', '+1 hour');

// For PDF preview
$sPdfUrl = $oStorage->getSignedPdfUrl('documents/report.pdf');
```

### Using File Operation Errors
```php
use PH7\Framework\File\FileOperationError;

if (!file_exists($sFilePath)) {
    return FileOperationError::fileNotFound($sFilePath);
}

if (!is_readable($sFilePath)) {
    return FileOperationError::fileNotReadable($sFilePath);
}

// Success response
return FileOperationError::successResponse('File uploaded successfully', [
    'file_path' => $sFilePath,
    'file_size' => filesize($sFilePath)
]);
```

## Installation Notes

### For JavaScript Dependencies
```bash
npm install
npm run security-audit
npm run lint
```

### Database Configuration for Load Balancing
Update your config file to include slave nodes:
```php
'db' => [
    'master' => [...],
    'slaves' => [
        ['host' => 'slave1.example.com', ...],
        ['host' => 'slave2.example.com', ...]
    ]
]
```

## Testing Recommendations
- Test session cleanup performance on large databases
- Verify load balancer failover scenarios
- Check S3 signed URL expiration
- Validate file operation error responses

---
**Version**: 18.0.0  
**Date**: December 2025  
**Author**: Pierre-Henry Soria
