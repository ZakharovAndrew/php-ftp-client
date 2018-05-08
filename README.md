# php-ftp-client
Simple PHP FTP client
# Getting Started
Connect and Log in to Server :
```php
// connect to ftp server
$ftp = new FtpClient();
$ftp->connect($host, $ssl, $port, $timeout);
$ftp->login($login, $password);
```
# Using Passive Mode :
```php
// This uses passive mode
$ftp->passive();

// If you want to disable using passive mode then
$ftp->passive(false);
```