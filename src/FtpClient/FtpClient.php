<?php

/*
 * Simple PHP FTP client.
 *
 * (c) Zakharov Andrew <https://github.com/ZakharovAndrew>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Zakharov Andrew https://github.com/ZakharovAndrew
 */

class FtpClient {
    // The connection with the server.
    public $conn;

    /**
     * Constructor. 
     * 
     * @param string $host
     * @param bool   $ssl
     * @param int    $port
     * @param int    $timeout
     * @throws Exception  If FTP extension is not loaded.
     */
    public function __construct() 
    {
        if (!extension_loaded('ftp')) {
            throw new Exception('FTP extension is not loaded!');
        }
    }
	
    /**
     * Auto close connection
     */
    public function  __destruct() 
    {
	$this->close();
    }
	
    /**
     * Connect to FTP server.
     *
     * @param string $host
     * @param bool   $ssl
     * @param int    $port
     * @param int    $timeout
     * @throws Exception  If failed to connect to FTP server.
     */
    public function connect($host, $ssl = false, $port = 21, $timeout = 90) 
    {
	// check if non-SSL connection
	if(!$ssl) {
            $this->conn = ftp_connect($host, $port, $timeout);
        // SSL connection
	} else {
            $this->conn = ftp_ssl_connect($host, $port, $timeout);
	} 
        
        if (!$this->conn) {
            throw new Exception("Failed to connect to FTP server:{$host}");
	}
        
        return $this;
    }

    /**
     * Log in to an FTP connection.
     *
     * @param string $user
     * @param string $password
     * @param bool   $passive
     * @throws Exception  If the login is incorrect
     */
    public function login($user = 'anonymous', $password = '') 
    {
        //  attempt login
	if (ftp_login($this->conn, $user, $password)) {
            // connection successful
            return true;
	}
		
	// login failed
	throw new Exception("Failed to login to FTP server:{$this->_server}");
    }
    
    /**
     * Set passive mode
     * 
     * @param boolean $directory
     */
    public function passive($passive = true)
    {
        ftp_pasv($this->conn, (bool)$passive);
    }

    /**
     * Check is Dir?
     *
     * @param string $directory
     * @return bool
     */
    public function isDir($directory) {
        if (@ftp_chdir($this->conn, $directory)) {
            ftp_chdir($this->conn, '..');
            return true;
	} else {
            return false;
        }
    }
	
    /**
     * Returns a list of files in the given directory
     *
     * @param string $directory
     * @return array
     */
    public function nList($directory = null) {
        $list = array();

        // attempt to get list
        if($list = ftp_nlist($this->conn, $directory)) {
            // success
            return $list;
        // Failed to get directory list
        } else {
            return array();
	}
    }
	
    /**
     * Returns a list of file or directory
     * 
     * @param bool   $isDir  'file' or 'dir'
     * @param string $directory
     * @param bool   $recursive
     * @return array file list
     * @throws Exception
     */
    public function listItem($isDir = false, $directory = '.', $recursive = false, $ignoreList = array())
    {
        $fileList = $this->nList($directory);
        $listItem = array();
        foreach ($fileList as $file) {
            // remove directory and subdirectory name
            $file = str_replace("$directory/", '', $file);
            // if dir or file not in ignore
            if ($this->isDir("$directory/$file") === $isDir && $this->ignoreItem($isDir, $file, $ignoreList) !== true) {
                $listItem[] = $file;
            }
            // recursive
            if ($recursive  && $isDir && $this->ignoreItem($isDir, $file, $ignoreList) !== true) {
                $listItem = array_merge($listItem, $this->listItem($isDir, "$directory/$file", $recursive, $ignoreList));
            }
        }
        return $listItem;
    }
    
     /**
     * Ignore item to itemList
     * 
     * @param boolean $isDir if false then file
     * @param string $filename
     * @param array $ignoreList array of names or extension item for ignore
     * @return boolean
     */
    private function ignoreItem($isDir, $filename, $ignoreList)
    {
        // check extension for ignore
        if (!$isDir && in_array(pathinfo($filename, PATHINFO_EXTENSION), $ignoreList)) {
            return true;
        // check dir for ignore
        } elseif ($isDir && in_array($filename, $ignoreList)) {
            return true;
        }
        // ignore item
        return false;
    }

    
    /**
     * Create directory on FTP server
     *
     * @param string $directory
     * @return bool
     */
    public function mkdir($directory = null) {
        // attempt to create dir
        if(ftp_mkdir($this->conn, $directory)) {
            return true;
        } 
	// error
        throw new Exception("Failed to create directory \"{$directory}\"");
    }
	
    /**
     * Set file permissions
     *
     * @param int $permissions (ex: 0644)
     * @param string $file
     * @return bool
     */
    public function chmod($permissions = 0, $file = null) {
        // attempt chmod
        if (ftp_chmod($this->conn, $permissions, $file) !== false) {
            return true;
        } 
        // chmod failed
        throw new Exception("Failed to set file permissions for \"{$file}\"");
    }
	
    /**
     * Get current directory
     *
     * @return string
     */
    public function pwd() {
	return ftp_pwd($this->conn);
    }

    /**
     * Requests execution of a command on the FTP server
     *
     * @param string $command
     */
    public function exec($command = null) {
	if (ftp_exec($this->conn, $command) !== false) {
            return true;
        } 
        // exec failed
        throw new Exception("Could not execute \"{$command}\"");
    }
    
    /**
     * Get file size
     *
     * @param string $file
     * @return bool
     */
    public function getSize($file = null)
    {
        return ftp_size($this->session, $file);
    }
	
    /**
     * Delete file on FTP server
     *
     * @param string $file
     * @return bool
     */
    public function delete($file = null) {
        // attempt to delete file
        if(ftp_delete($this->conn, $file)) {
            return true;
        } 
        // delete failed
        throw new Exception("Failed to delete file \"{$file}\"");
    }

    /**
     * Get last modified time to file
     *
     * @param string $file
     * @return array
     */
    public function getLastMod($file) {
        // attempt to get last modified time to file
        $buff = ftp_mdtm($this->conn, $file);

        if ($buff != -1) {
            // success
            return $buff;
        } else {
            throw new Exception("Failed to get last modified time to file");
        }
    }
	
    /**
     * Download file from FTP server
     *
     * @param string $remote_file
     * @param string $local_file
     * @param int $mode
     * @return bool
     */
    public function get($remote_file = null, $local_file = null, $mode = FTP_BINARY) {
        // attempt download
        if(ftp_get($this->conn, $local_file, $remote_file, $mode)) {
            return true;
        } 
        // download failed
        throw new Exception("Failed to download file \"{$remote_file}\"");
    } 	
	
    /**
     * Upload file to server
     *
     * @param string $local_file
     * @param string $remote_file
     * @param int $mode
     * @return bool
     */
    public function put($local_file = null, $remote_file = null, $mode = FTP_BINARY) {
        // attempt to upload file
        if(ftp_put($this->conn, $remote_file, $local_file, $mode)) {
            // success
            return true;
        } 
        // upload failed
	throw new Exception("Failed to upload file \"{$local_file}\"");
    }
	
    /**
     * Closes the current FTP connection.
     *
     */
    public function close()
    {
        if ($this->conn) {
            ftp_close($this->conn);
            $this->conn = null;
        }
    }
	
}