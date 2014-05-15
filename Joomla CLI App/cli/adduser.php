<?php 
/** 
 * @package    Joomla.Cli 
 * 
 * @copyright  Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved. 
 * @license    GNU General Public License version 2 or later; see LICENSE.txt 
 */ 

/** 
 * Overload CLI create dummy articles and categories
 * 
 * Run the framework bootstrap with a couple of mods based on the script's needs 
 */ 

// We are a valid entry point. 
const _JEXEC = 1; 

// Load system defines 
if (file_exists(dirname(__DIR__) . '/defines.php')) 
{ 

    require_once dirname(__DIR__) . '/defines.php'; 
} 

if (!defined('_JDEFINES')) 
{ 
    define('JPATH_BASE', dirname(__DIR__)); 
    require_once JPATH_BASE . '/includes/defines.php'; 
} 

// Get the framework. 
require_once JPATH_LIBRARIES . '/import.legacy.php'; 

// Bootstrap the CMS libraries. 
require_once JPATH_LIBRARIES . '/cms.php'; 
require_once dirname(__DIR__) . '/cli/clipbar.php';
// Import the configuration. 
require_once JPATH_CONFIGURATION . '/configuration.php'; 

// System configuration. 
$config = new JConfig; 

// Configure error reporting to maximum for CLI output. 
//error_reporting(E_ALL ^ E_NOTICE);
error_reporting(E_ALL);
//ini_set('display_errors', 1); 
@set_time_limit(0);
@ini_set('memory_limit', '-1');

/**  
 * Bootstrap file for the Joomla Platform.  Including this file into your application will make Joomla  
 * Platform libraries available for use.  
 *  
 * @package    Joomla.Platform  
 *  
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.  
 * @license    GNU General Public License version 2 or later; see LICENSE  
 */  

// Set the platform root path as a constant if necessary.  
if (!defined('JPATH_PLATFORM'))  
{  
   
    define('JPATH_PLATFORM', __DIR__);  
}  
  
// Detect the native operating system type.  
$os = strtoupper(substr(PHP_OS, 0, 3));  

if (!defined('IS_WIN'))  
{  
    define('IS_WIN', ($os === 'WIN') ? true : false);  
}  
if (!defined('IS_UNIX'))  
{  
    define('IS_UNIX', (IS_WIN === false) ? true : false);  
}  

// Import the platform version library if necessary.  
if (!class_exists('JPlatform'))  
{  

    require_once JPATH_PLATFORM . '/platform.php';  
}  

// Import the library loader if necessary.  
if (!class_exists('JLoader'))  
{  
  
    require_once JPATH_PLATFORM . '/loader.php';  
}  
  
// Make sure that the Joomla Platform has been successfully loaded.  
if (!class_exists('JLoader'))  
{  
    throw new RuntimeException('Joomla Platform not loaded.');  
}  

// Setup the autoloaders.  
JLoader::setup();  

// Import the base Joomla Platform libraries.  
JLoader::import('joomla.factory');  

// Register classes for compatability with PHP 5.3  
if (version_compare(PHP_VERSION, '5.4.0', '<'))  
{  
    JLoader::register('JsonSerializable', JPATH_PLATFORM . '/compat/jsonserializable.php');  
}  

// Register classes that don't follow one file per class naming conventions.  
JLoader::register('JText', JPATH_PLATFORM . '/joomla/language/text.php');  
JLoader::register('JRoute', JPATH_PLATFORM . '/joomla/application/route.php');  

// Work around for not being in the CMS and needing to deal with wrong named files  
// and new cross-library dependencies.  
if (!defined('JPATH_LIBRARIES'))  
{  
    define('JPATH_LIBRARIES', dirname(__FILE__) . '/libraries');  
    define('JPATH_ROOT', dirname(__FILE__));  
    require JPATH_PLATFORM . '/import.legacy.php';  
    require JPATH_PLATFORM . '/cms.php';  
    require JPATH_PLATFORM . '/cms/helper/tags.php';  
    JLoader::registerPrefix('J', JPATH_PLATFORM . '/legacy');  
    JLoader::Register('J', JPATH_PLATFORM . '/cms');  
}   
/**
 * Add user
 *
 * @package  Joomla.Shell
 *
 * @since    1.0
 */
class Adduser extends JApplicationCli
{
	     protected $dbo = null; 
       const CLI_NAME = 'Add dummy users from CLI';
       const CLI_VERSION ='adduserCli 0.1 RC [FirstStep] - www.alikonweb.it';
       private $_time; 
	     public function __construct() { 
       
        // Call the parent __construct method so it bootstraps the application class. 
        //   parent::__construct(); 
        $this->app = JFactory::getApplication('site'); 
        // 
        // Prepare the logger. 
        // 
     

        // Include the JLog class. 
        jimport('joomla.log.log'); 

        // Get the date so that we can roll the logs over a time interval. 
        $date = JFactory::getDate()->format('Y-m-d'); 

        JLog::addLogger( 
                // Pass an array of configuration options. 
                // Note that the default logger is 'formatted_text' - logging to a file. 
                array( 
            // Set the name of the log file. 
            'text_file' => 'addusercli.' . $date . '.php', 
             
                // Set the path for log files. 
                //    'text_file_path' => __DIR__ . '/logs' 
                ), JLog::INFO 
        ); 

        // 
        // Prepare the database connection. 
        // 

        jimport('joomla.database.database'); 
        $config = JFactory::getConfig(); 
 
        $this->dbo = JFactory::getDBO(); 
        // Get the quey builder class from the database. 
         
    } 
     
	/**
	 * Entry point for the script
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	
 
	public function doExecute()
	{
		echo(JPlatform::getLongVersion())."\n";
    echo(JPlatform::COPYRIGHT)."\n"; 
    echo(Adduser::CLI_NAME)."\n"; 
    echo(Adduser::CLI_VERSION)."\n"; 
    $this->_time = microtime(true);  
    // Fool the system into thinking we are running as JSite with Finder as the active component 
    //JFactory::getApplication('admin'); 
    //$_SERVER['HTTP_HOST'] = 'domain.com'; 
    
     $args = (array) $GLOBALS['argv'];
    
     if (defined('JSHELL'))
     {
     array_shift($args);
     }
     
     if (count($args) < 2) {
         $this->out($this->help());
         exit(1);
     }
     // var_dump($args);
       switch ($args[1])
        {
        	 case  '-m':         	   
        	   $result=$this->addusers($args[2]);        	  
        	   break;
        	 case  '-i':         	   
        	   $result=$this->importusers($args[2]);        	  
        	   break;  
        	 case  '-u':    
        	 
        	   $username = $args[2];		
		         $name = $args[4];
		         $email = $args[6];		
		         $groups = $args[8];   	   
        	   $result=$this->addusers(1,$username,$name,$email,$groups); 
        	  
        	   break;  
        	 case  '-d':         	   
        	   $result=$this->delusers();        	  
        	   break;  
        	 default:   
             echo '[WARNING] Unknown parameter'."\n" ;
             $this->help();
             break;   
        }	
		// username, name, email, groups are required values.
			
	  //var_dump($result);

		$this->out();
	}
	protected function help($option=null) {
        // Initialize variables.
        $help = array();
        // Build the help screen information.
        $help[] = 'Add users from CLI';
        $help[] = 'Usage: php addusers.php [options]';
        $help[] = '';
        $help[] = 'Option: -m [the number of users]';
        $help[] = 'Example usage:php adduser.php -m 100';
        $help[] = 'Add 100 dummy users';
        $help[] = '';
        $help[] = 'Option: -u [userdata]';
        $help[] = 'Example usage:php adduser.php -u username -n name -e user@dummy.com -g 2';
        $help[] = 'Add one user ';        
        $help[] = '';
        $help[] = 'Option: -d';
        $help[] = 'Example usage:php adduser.php -d';
        $help[] = 'Delete all dummy users';
        $help[] = '';
        $help[] = 'Option: -i';
        $help[] = 'Example usage:php adduser.php -i www.domain.it';
        $help[] = 'Import users from website';
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
    
    public function addusers($num,$username='user',$name='user',$email='@dummy.com',$groups=2) {
    	require_once JPATH_ADMINISTRATOR.'/components/com_users/models/user.php'; 
       $user = new JUser();
		   $array = array();
		   $data = array();
		   $array['username'] = $username;
		   $array['name'] = $name;
		   $array['email'] = $email;
		   $array['password'] = '12345678';
		   $array['groups']=array($groups);
		   $array['activation']='';
		   $array['block']=0;         
		   $array['result']=array();        
       $app = JFactory::getApplication('site');
       $app->initialise();
          // Make sure we're not in FTP mode
       $app->input->set('method', 'direct');
          // com_users model
       
       for ($u = 1; $u <= $num; $u++) {  
       	$model = JModelLegacy::getInstance('UsersModelUser');
       	$data['username']=$array['username'].$u;       
       	$data['name']=$array['name'].$u;       
       	$data['email']=$data['name'].$array['email'];       
       	$data['groups']=$array['groups'];
       	$data['password'] = '12345678';
       	$data['password2']=$data['password'];
       	$data['block'] = 0;
       	//var_dump($data);
       	$result=$model->save($data);
       	if(!$result) {
          echo('User not created:'.$u)."\n";        	
        }else  {        	
     	    echo('Usercreated:'.$u)."\n";        	
        }
       	$array['result']=array($u,$result);
       	//unset($data);
       }	
       return $array['result'];
    }
    public function delusers() {
    	require_once JPATH_ADMINISTRATOR.'/components/com_users/models/user.php'; 
       $db = JFactory::getDBO(); 
      $mail='%@dummy.com%';
      $query = $db->getQuery(true); 
      $query->select('id') 
        ->from( $db->qn('#__users') ) 
        ->where($db->qn('email').' like '.$db->q($mail)); 
      $db->setQuery($query); 
      $ids = $db->loadObjectList(); 
      $del=count($ids);
      $vettore=array();
      for($i = 0; $i < $del; $i++) {
      	$vettore[]=$ids[$i]->id;
      	echo ($ids[$i]->id);  
      }
      //jexit(var_dump($vettore));	 
      //echo ($ids[99]->id);  
      $query = $db->getQuery(true);    
      // delete all users keys for userid in list.
      $conditions = array(
          $db->quoteName('id') . ' IN ('.implode(",",$vettore).')'         
       ); 
       $query->delete($db->quoteName('#__users'));
       $query->where($conditions); 
       $db->setQuery($query); 
       $result = $db->query();       
       $query = $db->getQuery(true);    
      // delete all user_profiles keys for userid in list.
      $conditions = array(
          $db->quoteName('user_id') . ' IN ('.implode(",",$vettore).')'        
       ); 
       $query->delete($db->quoteName('#__user_profiles'));
       $query->where($conditions); 
       $db->setQuery($query); 
       $result = $db->query();
       $query = $db->getQuery(true);    
       // delete all user_profiles keys for userid in list.
       $conditions = array(
          $db->quoteName('user_id') . ' IN ('.implode(",",$vettore).')'         
       ); 
       $query->delete($db->quoteName('#__user_notes'));
       $query->where($conditions); 
       $db->setQuery($query); 
       $result = $db->query();
       $query = $db->getQuery(true);    
       // delete all user_profiles keys for userid in list.
       $conditions = array(
          $db->quoteName('user_id') . ' IN ('.implode(",",$vettore).')'          
       ); 
       $query->delete($db->quoteName('#__user_keys'));
       $query->where($conditions); 
       $db->setQuery($query); 
       $result = $db->query();
       $query = $db->getQuery(true);    
       // delete all user_profiles keys for userid in list.
       $conditions = array(
          $db->quoteName('user_id') . ' IN ('.implode(",",$vettore).')'         
       ); 
       $query->delete($db->quoteName('#__user_usergroup_map'));
       $query->where($conditions); 
       $db->setQuery($query); 
       $result = $db->query();
      
      /*
      
      $del=count($ids);
      for($i = 0; $i < $del; $i++) {
      	echo $ids[$i]->id;
      	$model = JModelLegacy::getInstance('UsersModelUser');
        $result=$model->delete($ids[$i]->id);
        
        if(!$result) {
          echo('User not deleted:'.$ids[$i]->id)."\n";        	
        }else  {        	
     	    echo('User deleted:'.$ids[$i]->id)."\n";        	
        }
        
       	$array['result']=array($ids[$i]->id,$result);
      }	
      */           
    } 
    
    public function importusers($domain) {
	    //$json = file_get_contents('http://localhost/newalikon/?option=com_ajax&plugin=latestusers&format=json'); // this WILL do an http request for you
	    $json = file_get_contents('http://'.$domain.'?option=com_ajax&plugin=latestusers&format=json');
	    $jdata = (json_decode($json, true)); 
	 
     	 require_once JPATH_ADMINISTRATOR.'/components/com_users/models/user.php'; 
       $user = new JUser();
		  
		   $data = array();
		   
       $app = JFactory::getApplication('site');
       $app->initialise();
          // Make sure we're not in FTP mode
       $app->input->set('method', 'direct');
          // com_users model
       
      foreach($jdata[0] AS $item) {
       	$model = JModelLegacy::getInstance('UsersModelUser');
       	   print_r($item);
       	$data['username']=$item['username'];       
       	$data['name']=$item['name'];       
       	$data['email']=$item['email'];       
       	$data['groups']=array(2);
       	$data['password'] = $item['email'];
       	$data['password2']=$data['password'];
       	$data['block'] = 0;
       	//var_dump($data);
       	$result=$model->save($data);
       	if(!$result) {
          echo('User not created:')."\n";        	
        }else  {        	
     	    echo('Usercreated:')."\n";        	
        }
       	$array['result']=array($result);
       	//unset($data);
       }	
       return $array['result'];
    }
}

if (!defined('JSHELL'))
{
	JApplicationCli::getInstance('Adduser')->execute();
}


