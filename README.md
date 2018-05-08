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
OR

Connect to a server FTP via SSL (on port 22 or another port) :
```php
// connect to ftp server
$ftp = new FtpClient();
$ftp->connect($host, true, 22, $timeout);
$ftp->login($login, $password);
```
## Usage
Upload all files and all directories is easy :

```php
// upload with the BINARY mode
$ftp->put($local_file, $remote_file);

// Is equal to
$ftp->put($local_file, $remote_file, FTP_BINARY);

// or upload with the ASCII mode
$ftp->put($local_file, $remote_file, FTP_ASCII);
```

### Using Passive Mode :
```php
// This uses passive mode
$ftp->passive();

// If you want to disable using passive mode then
$ftp->passive(false);
```

### Running custom command on remote server
```php
$ftp->exec($command);
```

### Download file from FTP server
```php
// download with the BINARY mode
$ftp->get($remote_file, $local_file);

// Is equal to
$ftp->get($remote_file, $local_file, FTP_BINARY);

// download with the ASCII mode
$ftp->get($remote_file, $local_file, FTP_ASCII);
```

### All FTP PHP functions are supported and some improved :
```php
// Returns a list of files in the given directory
$ftp->nList('path/of/directory/to/create');

// Creates a directory
$ftp->mkdir('path/of/directory/to/create');

// Get last modified time to file
$ftp->getLastMod('file.php');

// Set permissions on a file via FTP
$ftp->chmod(0777, 'file.php');

// Get file size
$ftp->getSize('file.php');

// Get current directory
$ftp->pwd();

// Delete file on FTP server
$ftp->delete('file.php');

//and more...
```

## API doc

See the [source code](https://github.com/ZakharovAndrew/php-ftp-client/tree/master/src/FtpClient) for more details.
It is fully documented :blue_book:

## License

[MIT](https://github.com/ZakharovAndrew/php-ftp-client/blob/master/LICENSE) c) 2018, Zakharov Andrew <https://github.com/ZakharovAndrew>.