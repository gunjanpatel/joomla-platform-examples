<?php

/**
 * A command line job to download and unzip Joomla Package.
 *
 * @package  Joomla.Cli
 * @since    3.0
 */
class DownloadCli {

    private $joomlaZipUrl;
    private $localFile;
    private $dirPath;
    private $filePath;
    private $overwrite =false;  
    private $_time;
    
    const CLI_NAME = 'Download and Unzip Joomla! from CLI';
    const CLI_VERSION ='DUJ 0.1 RC [FirstStep] - www.alikonweb.it';
    
    public function doExecute() {
         $this->_time = microtime(true);  	

        echo(DownloadCli::CLI_NAME)."\n"; 
        echo(DownloadCli::CLI_VERSION)."\n"; 
        echo('============================'."\n");
        
          $args = (array) $GLOBALS['argv'];
        //var_dump($args);
        if (count($args) < 2) {
            $this->help();
            exit(1);
        }
        
        switch ($args[1])
        {
         case '-z': 
         	  if ($args[2]) {
              $this->justunzip($args[2]);
            } else {
            	echo '[WARNING] Missed [filezip] Joomla Package parameter'."\n" ;
            	$this->help(3);
              exit(2);
            } 
            break;
         case '-u': 
         	  if ($args[2]) {
              $this->JoomlaDownloader($args[2],'joomla.zip');
            } else {
            	echo '[WARNING] Missed [url] Joomla Package parameter'."\n" ;
            	$this->help(7);
              exit(2);
            } 
            break;   
         case '-f': 
         case '-fo': 
            if ($args[1]=='-fo') {    
            	$this->overwrite =true;        
            }	
         	  if ($args[2]) {
              $this->fromFile($args[2]);
            } else {
            	echo '[WARNING] Missed [file] Joomlacode parameter'."\n" ;
            	$this->help(11);
              exit(2);
            } 
            break;      
         default:   
            echo '[WARNING] Unknown parameter'."\n" ;
           	$this->help();
            break;
        }  
        

        echo ("\n".'============================');
        echo "\n" .'Completed in ', round(microtime(true) - $this->_time, 3)."\n";
    }

    public function JoomlaDownloader($joomlaZipUrl, $localFile) {
        $this->joomlaZipUrl = $joomlaZipUrl;
        $this->localFile = $localFile;
        $this->dirPath = getcwd();
        $this->filePath = $this->dirPath . '/' . $this->localFile;

        if ($this->downloadFile()) {
            echo ("\n\n" . 'DOWNLOADED_UPDATE_CLI'."\n\n");
          //  $this->countzipFile();
            $this->unzip2();
            $this->clean();
         /*
            if ($this->unzipFile()) {
                echo ("\n\n" . 'UNZIPPED_UPDATE_CLI');
                //	$this->clean();
                //	$this->redirect();
            }
        */
        }
    }

    public function downloadFile() {
        if (file_exists($this->filePath)) {
            echo '[WARNING] '.$this->filePath . ' already exists.';
            if ($this->overwrite) {
                return true;
            }
            return false;
        }

        if (!$this->fileExistsOnUrl($this->joomlaZipUrl)) {
            echo '[WARNING] There is no file at ' . $this->joomlaZipUrl;
            return false;
        }
        
        try {
        	  echo 'Downloading from ' . $this->joomlaZipUrl . ' web to ' . $this->localFile ;
        	  //progress bar;
            $bartask = 4;
           // var_dump($zip->numFiles);
            $this->a = new CliProgressBar();
            $this->a->initPBar($bartask, 13);
            $this->status = 1;
            $this->a->advancePBar($this->status, 'Download');       
            
            $data = file_get_contents($this->joomlaZipUrl);
            
            $this->status++;
            $this->a->advancePBar($this->status, 'Getting');
           
            $handle = fopen($this->localFile, "w");
            
            $this->status++;
            $this->a->advancePBar($this->status, 'Open');
            
            fwrite($handle, $data);
            
            $this->status++;
            $this->a->advancePBar($this->status, 'write');
            
            fclose($handle);
            
            $this->a->finishPBar();      
            return true;
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }

        echo 'Download errror';
        return false;
    }

    public function unzipFile() {
    	
    	
    	
    	
    	
    	
        $zip = new ZipArchive;
        $res = $zip->open($this->filePath);
        

        if ($res === TRUE) {
        	  $stat =$zip->numFiles; 
        	  var_dump($stat);
            $extracted = $zip->extractTo($this->dirPath);
            $zip->close();

            if ($extracted) {
                echo 'Joomla extracted!';
                return true;
            } else {
                echo 'Zip extraction failed.' . $this->getZipError($res);
                return false;
            }
        } else {
            echo 'Extraction error: ' . $this->getZipError($res);
            return false;
        }
    }

    public function clean() {
      //  unlink(__FILE__);
        unlink($this->localFile);
    }
    
    public function justunzip($localFile) {
        $this->localFile = $localFile;
        $this->dirPath = getcwd();
        $this->filePath = $this->dirPath . '/' . $this->localFile;
        if (file_exists($this->filePath)) {
          $this->unzip2();
        } else {  
            echo '[WARNING] Joomla Package not found!';   
        } 
    }
    
    public function fromFile($localFile) {
        $this->localFile = $localFile;
        $this->dirPath = getcwd();
        $this->filePath = $this->dirPath . '/' . $this->localFile;
        if (file_exists($this->filePath)) {
           $handle = fopen($this->filePath, "rb");
           $contents = fread($handle, filesize($this->filePath));
           fclose($handle);
           //print $contents;
           $this->JoomlaDownloader($contents,'joomla.zip');
        } else {  
            echo '[WARNING] Joomlacode file not found!';   
        } 
        
    }
    
    
    
    
    
    
    public function unzip2() {
       $zip = new ZipArchive;
      
       
       if ($zip->open($this->filePath) === true) {
           //progress bar;
            $bartask = $zip->numFiles + 2;
           // var_dump($zip->numFiles);
            $this->a = new CliProgressBar();
            $this->a->initPBar($bartask, 13);
            $this->status = 1;
            $this->a->advancePBar($this->status, 'Unzipping');          
          for($i = 0; $i < $zip->numFiles; $i++) {
                         
           $zip->extractTo($this->dirPath, array($zip->getNameIndex($i)));
            $this->status++;
            $this->a->advancePBar($this->status, $zip->getNameIndex($i));
                        
        // here you can run a custom function for the particular extracted file
                        
         }
        $this->a->finishPBar();                
       $zip->close();
                    
      }

    }
    
    
    public function countzipFile() {
    	$zip = zip_open($this->filePath);
      $files=0;
      $zippeddim=0;
      $dim=0;
       if ($zip) {
    while ($zip_entry = zip_read($zip)) {
    	/*
        echo "\rName:               " . zip_entry_name($zip_entry) . "\n";
        echo "\rActual Filesize:    " . zip_entry_filesize($zip_entry) . "\n";
        echo "\rCompressed Size:    " . zip_entry_compressedsize($zip_entry) . "\n";
        echo "\rCompression Method: " . zip_entry_compressionmethod($zip_entry) . "\n";
        */
        $dim+=zip_entry_filesize($zip_entry)      ;
        $zippeddim+= zip_entry_compressedsize($zip_entry);
        $files++;
/*
        if (zip_entry_open($zip, $zip_entry, "r")) {
            echo "File Contents:\n";
            $buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
            echo "$buf\n";

            zip_entry_close($zip_entry);
        }
        */
        echo "\r";
        echo 'Total files:'.$files.' TCompressed Size:'.$zippeddim.' Total Size:'.$dim;
    }

    zip_close($zip);
  }
}

 
    public function getZipError($errorNumber) {
        $errors = array();
        $errors[0] = 'No error';
        $errors[1] = 'Multi-disk zip archives not supported';
        $errors[2] = 'Renaming temporary file failed';
        $errors[3] = 'Closing zip archive failed';
        $errors[4] = 'S Seek error';
        $errors[5] = 'Read error';
        $errors[6] = 'Write error';
        $errors[7] = 'CRC error';
        $errors[8] = 'Containing zip archive was closed';
        $errors[9] = 'No such file';
        $errors[10] = 'File already exists';
        $errors[11] = 'Can\'t open file';
        $errors[12] = 'Failure to create temporary file';
        $errors[13] = 'Zlib error';
        $errors[14] = 'Malloc failure';
        $errors[15] = 'Entry has been changed';
        $errors[16] = 'Compression method not supported';
        $errors[17] = 'Premature EOF';
        $errors[18] = 'Invalid argument';
        $errors[19] = 'Not a zip archive';
        $errors[20] = 'Internal error';
        $errors[21] = 'Zip archive inconsistent';
        $errors[22] = 'Can\'t remove file';
        $errors[23] = 'Entry has been deleted';

        if (isset($errors[$errorNumber])) {
            return $errors[$errorNumber];
        } else {
            return 'Unknown error';
        }
    }

    public function fileExistsOnUrl($url) {
     //   var_dump($url);
        $file_headers = @get_headers($url);
     //   var_dump(substr($headers[0], 9, 3));
     //   var_dump($file_headers);
        if (($file_headers[0] == 'HTTP/1.1 404 Not Found') || 
            (!$file_headers)  //|| 
           // (!strpos('zip',$file_headers[5]))
           ) {
            return false;
        } else {
            return true;
        }
    }
    
     protected function help($option=null) {
        // Initialize variables.
        $help = array();
        // Build the help screen information.
        $help[] = '[HELP] Download and Unzip Joomla! from CLI';
        $help[] = 'Usage: php downloadcli.php [options]';
        $help[] = '';
        $help[] = 'Option: -z [filezip]';
        $help[] = 'Example usage:php downloadcli.php -z joomla.zip';
        $help[] = 'Unzip the joomla package from joomla.zip';
        $help[] = '';
        $help[] = 'Option: -u [url]';
        $help[] = 'Example usage:php downloadcli.php -u http://joomlacode.org/gf/download/frsrelease/19239/158104/Joomla_3.2.3-Stable-Full_Package.zip';
        $help[] = 'Download and unzip from http://joomlacode.org/gf/download/frsrelease/19239/158104/Joomla_3.2.3-Stable-Full_Package.zip';
        $help[] = '';
        $help[] = 'Option: -f [file]';
        $help[] = 'Example usage:php downloadcli.php -f joomlacode.txt';
        $help[] = 'Download and unzip from url listed on file joomlacode.txt';
        $help[] = '';
        // Print out the help information.
        if(!$option) {
          echo(implode("\n", $help));
        }else  {
        	   for($i = $option; $i < $option+4; $i++) {
        	   	echo $help[$i]."\n";
        	   }	
        }
    }
}

// Instantiate the application object, passing the class name to JCli::getInstance
// and use chaining to execute the application.
//JApplicationCli::getInstance('DownloadCli')->execute();
require_once   '/clipbar.php';
$i=new DownloadCli();
$i->doExecute();