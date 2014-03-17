<?php 
/** 
 * @package    Joomla.Cli 
 * 
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved. 
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
error_reporting(E_ALL ^ E_NOTICE);
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
 * A command line cron job to create dummy articles and categories base on com_overload. 
 * 
 * @package  Joomla.Cli 
 * @since    3.0 
 */ 
class OverloadCli extends JApplicationCli 
{ 
    var $categories =10; 
    var $depth = 1;  
    var $totalcats=0; 
    var $donecats= 0; 
    var $level=0; 
    var $levelmap=0;
    var $articles=10;  
    protected $dbo = null; 
    const CLI_NAME = 'Add dummy articles and categories from CLI';
    const CLI_VERSION ='OverloadCli 0.1 RC [FirstStep] - www.alikonweb.it';
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
            'text_file' => 'overloadcli.' . $date . '.php', 
             
                // Set the path for log files. 
                //    'text_file_path' => __DIR__ . '/logs' 
                ), JLog::INFO 
        ); 

        // 
        // Prepare the database connection. 
        // 

        jimport('joomla.database.database'); 
        $config = JFactory::getConfig(); 
        // Note, this will throw an exception if there is an error 
        // creating the database connection. 
        /* 
          $this->dbo = JDatabase::getInstance( 
          array( 
          'driver' => $this->get('dbDriver'), 
          'host' => $this->get('dbHost'), 
          'user' => $this->get('dbUser'), 
          'password' => $this->get('dbPass'), 
          'database' => $this->get('dbName'), 
          'prefix' => $this->get('dbPrefix'), 
          ) 
          ); 
         */ 
        $this->dbo = JFactory::getDBO(); 
        // Get the quey builder class from the database. 
         
    } 
     
     
     
     
     
    /** 
     * Entry point for CLI script 
     * 
     * @return  void 
     * 
     * @since   3.0 
     */ 
    public function doExecute() 
    {  
    	  echo(JPlatform::getLongVersion())."\n";
        echo(JPlatform::COPYRIGHT)."\n"; 
        echo(OverloadCli::CLI_NAME)."\n"; 
        echo(OverloadCli::CLI_VERSION)."\n"; 
        $this->_time = microtime(true);  
        // Fool the system into thinking we are running as JSite with Finder as the active component 
        //JFactory::getApplication('admin'); 
        //$_SERVER['HTTP_HOST'] = 'domain.com'; 
         // Include the JLog class. 
               
         $options = getopt("a:c:d:"); 
    if (!is_array($options) ) { 
        print "There was a problem reading in the options.\n\n"; 
        exit(1); 
    } 
    $errors = array(); 
    print_r($options);   
    
    
  

    // Handle command line arguments
    foreach (array_keys($options) as $opt) {
    	//var_dump($opt);
    	
       switch ($opt) {
       	
         case 'a':
           // Do something with s parameter
           $this->articles = $options['a'];
           break;
        case 'c':
           // Do something with s parameter
           $this->categories = $options['c'];
           break;
         case 'd':
           $this->depth = $options['d'];
           break;
       }
      // var_dump($something);
    }    
    
    
    
    
    
    
        
        //echo 'start'."\n";  
         $this->start(); 
         $this->a->finishPBar();
         $this->out("\n" .'Completed in '. round(microtime(true) - $this->_time, 3)."\n");
    } 
   /**
	 * Generates a category level mapping, i.e. an array containing a category
	 * hierarchy based on the category and depth preferences.
	 * 
	 * @param type $categories
	 * @param type $depth
	 * @param type $prefix
	 * @return array
	 */
	private function makeLevelmap($categories, $depth, $prefix = '')
	{
		$ret = array();
		$prefix = empty($prefix) ? '' : $prefix.'.';
		for($i = 1; $i <= $categories; $i++) {
			$partial = $i;
			$ret[] = (string)$partial;
			if($depth > 1) {
				$fulls = $this->makeLevelmap($categories, $depth - 1, $partial);
				foreach($fulls as $something) {
					$ret[] = $partial.'.'.$something;
				}
			}
		}

		return $ret;
	}
    public function clean() 
    {
    	 // Remove articles from category
			$db = JFactory::getDBO(); 
			$query = "DELETE FROM #__assets WHERE ".$db->qn('title')." LIKE " .$db->q('Overload Sample%');
			$db->setQuery($query);
			$result=$db->query(); // Whoosh!
			if(!$result) { 
            exit('311'); 
        } 
			$query = "DELETE FROM #__categories WHERE ".$db->qn('title')." LIKE "  .$db->q('Overload%');
			$db->setQuery($query);
			$result=$db->query(); // Whoosh!
      if(!$result) { 
            exit('312'); 
        } 
      $query = "DELETE FROM #__content WHERE ".$db->qn('title')." LIKE ".$db->q('Overload Sample%');
			$db->setQuery($query);
			$result=$db->query(); // Whoosh!
			if(!$result) { 
            exit('313'); 
        } 
			/*
			$query = $db->getQuery(true);
			$query->delete('#__content')
				->where($db->qn('catid').' = '.$db->q($id));
			$db->setQuery($query);
			$db->query();
			*/
    }	 
    /** 
     * Begins the content overload process 
     * @return bool  
     */ 
    public function start() 
    { 
        /* 
        $categories = 10; 
        $depth = 1; 
        */ 
       $this->clean();
        $logger = true; 
        $depth = $this->depth; 
        $categories=$this->categories; 
        JLog::add('Calculating total number of categories', JLog::DEBUG); 
         
        $totalcats = 0; 
        for($i = $depth; $i > 0; $i --) { 
            $totalcats += pow($categories, $i); 
        } 
         $bartask = ($totalcats * $this->articles)+2;
             $this->a = new CliProgressBar();
             $this->a->initPBar($bartask, 13);
             $this->status = 1;
             $this->a->advancePBar($this->status, 'Overload start');
        //echo 'Creating level map for '.$totalcats."\n"; 
        JLog::add('Creating level map', JLog::DEBUG); 
         
        $killme = $this->makeLevelmap($categories, $depth); 
        $levelmap = array(); 
        foreach($killme as $key) { 
            $levelmap[$key] = 0; 
        } 
         
        $this->totalcats=$totalcats; 
        $this->donecats= 0; 
        $this->level=0; 
        $this->levelmap=$levelmap; 
         
        JLog::add('Starting the engines!', JLog::DEBUG); 
        //echo 'Starting the engines!'."\n"; 
         
        //$this->startTimer(); 
        $this->makeCategories(); 
        //return;     
        return $this->process(); 
    } 
    /** 
     * The main feature of this model: creating faux articles! 
     * @return type  
     */ 
    private function process() 
    { 
        $logger = true; 
        JLog::add('Entering main processing loop'); 
          //echo ('Entering main processing loop')."\n"; 
        $articles = $this->articles; 
        $levelmap = $this->levelmap; 
        $level = $this->level; 
         
        $currentArticle = 0; 
         
        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_content/tables'); 
         
         while(!empty($levelmap)) 
         { 
         	  $copy = $levelmap; 
            $level_id = array_shift($copy);
            if($level == 0) { 
                $keys = array_keys($levelmap); 
                $level = array_shift($keys); 
                JLog::add('Beginning content creation in category '.$level_id); 
               // echo('Beginning content creation in category '.$level_id)."\n"; 
                $startFromArticle = 0; 
            } else { 
                $startFromArticle = $this->getState('startfromarticle', 0); 
                JLog::add("Resuming content creation (article #$startFromArticle)", JLog::DEBUG); 
             //   echo("Resuming content creation (article #$startFromArticle)")."\n"; 
            } 
             
             
             
            JLog::add("Level ID $level_id", JLog::DEBUG); 
           // echo("Level ID $level_id")."\n"; 
             
            for($currentArticle = $startFromArticle; $currentArticle < $articles; $currentArticle++) { 
              //  if(!$this->haveEnoughTime()) break; 
                $this->createArticle_usingModel($level_id, $level, $currentArticle); 
            } 
             
            if($currentArticle == $articles) { 
                JLog::add("Finished processing category", JLog::DEBUG); 
            //    echo("Finished processing category")."\n"; 
                $currentArticle = 0; 
                $level = 0; 
                array_shift($levelmap); 
                $donecats = $this->donecats; 
                $donecats++; 
                $this->donecats=$donecats; 
            } 
         } 
         
        JLog::add("Updating model state", JLog::DEBUG); 
       // echo ("Updating model state")."\n"; 
        $this->levelmap=$levelmap; 
        $this->level=$level; 
        $this->startfromarticle=$currentArticle; 
         
        if(empty($levelmap)) { 
            JLog::add("We are finished!"); 
        //    echo("We are finished!")."\n"; 
            return true; 
        } 
         
        //$this->suspend(); 
        return false; 
    } 
     
   /**
	 * Generates categories based on the hierarchical level map generated by
	 * the model
	 */
	private function makeCategories()
	{
		$logger = true;
		JLog::add('Creating categories');

		$levelMap = $this->levelmap;
		foreach($levelMap as $key => $id) {
			$parts = explode('.',$key);
			$level = count($parts);
			$parent = ($level == 1) ? 1 : $levelMap[ implode('.',  array_slice($parts, 0, count($parts) - 1)) ];
			$id = $this->createCategory($level, $key, $parent);
			$levelMap[$key] = $id;

			// Remove articles from category
			$db = JFactory::getDBO(); 

			$query = 'DELETE FROM #__assets WHERE `id` IN (SELECT `asset_id` FROM `#__content` WHERE `catid` = '.$db->q($id).')';
			$db->setQuery($query);
			$db->query(); // Whoosh!

			$query = $db->getQuery(true);
			$query->delete('#__content')
				->where($db->qn('catid').' = '.$db->q($id));
			$db->setQuery($query);
			$db->query();
		}

		JLog::add("Updating levelmap in model state", JLog::DEBUG);

		$this->levelmap= $levelMap;
	}

     
    /** 
     * Create a single category and return its ID. If the category alias already 
     * exists, return the ID of that specific category alias. 
     *  
     * @param type $level 
     * @param type $levelpath 
     * @param type $parent_id 
     * @return type  
     */ 
    private function createCategory($level = 1, $levelpath = '1', $parent_id = 1) 
    { 
        $logger = true; 
        $title = 'Overload '; 
        $alias = 'overload-'; 
        $title .= $levelpath; 
        $alias .= str_replace('.', '-', $levelpath); 
         
        $data = array( 
            'parent_id'        => $parent_id, 
            'level'            => $level, 
            'extension'        => 'com_content', 
            'title'            => $title, 
            'alias'            => $alias, 
            'description'    => '<p>Sample content generated by OverloadCli</p>', 
            'access'        => 1, 
            'params'        => array('target' => '', 'image' => ''), 
            'metadata'        => array('page_title' => '', 'author' => '', 'robots' => '', 'tags' => ''), 
            'hits'            => 0, 
            'language'        => '*', 
            'associations'    => array(),             
            //'tags'            => array(array(null)),             
            'published'        => 1 
        ); 
        jimport('joomla.observer.mapper');  
        jimport('cms.helper.tags');  
        jimport('cms.table.corecontent');  
        // Categories is in legacy for CMS 3 so we have to check there.  
        JLoader::registerPrefix('J', JPATH_PLATFORM . '/legacy');  
        JLoader::Register('J', JPATH_PLATFORM . '/cms');   
       $app = JFactory::getApplication('administrator'); 
        $basePath = JPATH_ADMINISTRATOR . '/components/com_categories'; 
         
        require_once $basePath . '/models/category.php'; 
        $config = array('table_path' => $basePath . '/tables'); 
         
        $model = new CategoriesModelCategory($config); 
        //echo 'after model'."\n"; 
        $result = $model->save($data); 
        //$result =$this->saveCategory($data); 
         
        if($result === false) { 
            $db = JFactory::getDBO(); 
            $query = $db->getQuery(true); 
            $query 
                ->select('id') 
                ->from( $db->qn('#__categories') ) 
                ->where($db->qn('alias').' = '.$db->q($alias)); 
            $db->setQuery($query); 
            $id = $db->loadResult(); 
            JLog::add("Existing category $levelpath, ID $id", JLog::DEBUG); 
           // echo "Existing category $levelpath, ID $id"."\n"; 
            // Enable an existing category 
            $cat = $model->getItem($id); 
            if(!$cat->published) { 
                $cat->published = 1; 
            } 
            $cat = (array)$cat; 
            $model->save($cat); 
             
            return $id; 
        } else { 
            $id = $model->getState($model->getName().'.id'); 
            JLog::add("New category $levelpath, ID $id", JLog::DEBUG); 
        //    echo "New category $levelpath, ID $id"."\n"; 
            return $id; 
        } 
    } 
     
    /** 
     * Creates a faux article inside the specified category 
     *  
     * @param type $cat_id 
     * @param type $levelpath 
     * @param type $currentArticle  
     */ 
    private function createArticle($cat_id = '1', $levelpath = '1', $currentArticle = 1) 
    { 
        $data = $this->getArticleData($cat_id, $levelpath, $currentArticle); 
         
        $db = JFactory::getDBO(); 
        $data = (object)$data; 
        $data->attribs = json_encode($data->attribs); 
        $result = $db->insertObject('#__content', $data, 'id'); 
        if(!$result) { 
            die($db->getErrorMsg()); 
        } 
    } 
     
        /** 
     * Creates a faux article inside the specified category 
     *  
     * @param type $cat_id 
     * @param type $levelpath 
     * @param type $currentArticle  
     */ 
    private function createArticle_usingModel($cat_id = '1', $levelpath = '1', $currentArticle = 1) 
    { 
        $data = $this->getArticleData($cat_id, $levelpath, $currentArticle); 
         
        require_once JPATH_ADMINISTRATOR.'/components/com_content/models/article.php'; 
        $model = new ContentModelArticle(); 
        $result = $model->save($data); 
      //  echo ($data['title'])."\n";
         $this->status++;
         $this->a->advancePBar($this->status, ' ' . $data['title']);     
    } 
     
    private function getArticleData($cat_id = '1', $levelpath = '1', $currentArticle = 1, $addPictures = true)
	{
		$logger = true;

		$title = 'Overload Sample ';
		$alias = 'overload-sample-';
		$title .= $currentArticle.' in '.str_replace('.', '-', $cat_id);
		$alias .= $currentArticle.'-in-'.str_replace('.', '-', $cat_id);

		$url = str_replace('/administrator', '', JURI::base(true));
		$url = rtrim($url,'/');
		$picture1 = $addPictures ? '<img src="'.$url.'/images/sampledata/fruitshop/apple.jpg" align="left" />' : '';
		$picture2 = $addPictures ? '<img src="'.$url.'/images/sampledata/parks/animals/180px_koala_ag1.jpg" align="right" />' : '';

		$introtext = <<<ENDTEXT
$picture1<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec auctor velit blandit risus posuere sit amet sollicitudin enim dictum. Nunc a commodo magna. Cras mattis, purus et ornare dictum, velit mi dictum nisl, sed rutrum massa eros nec leo. Sed at nibh nec felis dignissim tristique. Mauris sed posuere velit. Curabitur vehicula dui libero. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean iaculis felis ac elit euismod vitae venenatis dui iaculis. Morbi nec ipsum sit amet erat scelerisque auctor ac eget elit. Phasellus ut mattis ipsum. In auctor lacinia porttitor. Aliquam erat volutpat. In hac habitasse platea dictumst. Pellentesque iaculis mi ut ante tempor pharetra.</p>
ENDTEXT;
		$fulltext = <<<ENDTEXT
<p>Aenean nisl velit, consectetur hendrerit ultricies eu, vehicula eu massa. Nunc elementum enim vitae tortor dignissim eget vulputate quam condimentum. Pellentesque ante felis, venenatis non malesuada a, sodales ut nunc. Morbi sed nulla <a href="http://www.joomla.org">sit amet erat cursus venenatis</a>. Nulla non diam id risus egestas varius vel nec nulla. Nullam pretium congue cursus. Nullam ultricies laoreet porttitor. Proin ultricies aliquam lacinia. Proin porta interdum enim eu ultrices. Maecenas id dui vitae nisl ultrices cursus quis et nisi. Sed rhoncus vestibulum eros vel faucibus. Nulla facilisi. Mauris lacus metus, aliquet eu iaculis vitae, tempor ac metus. Sed sem nunc, tempor vehicula condimentum at, ultricies a tellus. Proin dui velit, accumsan vitae facilisis mollis, tristique aliquet purus. Aliquam porta, orci nec feugiat semper, tortor nunc pulvinar lorem, sed ultricies mauris justo eu orci. Nullam urna leo, vehicula at interdum non, fringilla eget neque. Quisque dui metus, hendrerit ut porttitor non, dignissim eu ipsum.</p>
<p>Pellentesque ultricies adipiscing odio, <em>at interdum dui tempus ac</em>. Aliquam accumsan sem et tortor facilisis sagittis. Sed interdum erat in ante venenatis dignissim. Nulla neque metus, interdum a porta eu, lobortis quis libero. Maecenas condimentum lectus id nisi suscipit tempus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas at neque diam. Suspendisse arcu purus, eleifend accumsan imperdiet in, porta ac ante. Nam lobortis tincidunt erat, non ornare mauris vestibulum non. Vivamus feugiat nunc pretium mi pharetra dictum. Donec auctor tincidunt pulvinar. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p>
$picture2<p>Nunc feugiat porta faucibus. Nulla facilisi. Sed viverra laoreet mollis. Morbi ullamcorper lorem a lacus porttitor tristique. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean <strong>consequat</strong> tincidunt lacinia. Maecenas dictum volutpat lacus, nec malesuada ipsum congue sed. Sed nec neque erat. Donec eros urna, vulputate ac elementum sit amet, pharetra sit amet urna. Phasellus in lectus metus. Proin vitae diam augue, vel lacinia lectus. Ut tincidunt, dolor sit amet hendrerit gravida, augue mauris bibendum sapien, nec porta ipsum diam eget erat. In porta nisl eget odio placerat gravida commodo tortor feugiat. Donec in tincidunt dui. In in neque tellus. Phasellus velit lacus, viverra et sodales nec, porta in velit.</p>
<p>Etiam quis velit odio. Nunc dignissim enim vel enim blandit tempus. Integer pellentesque leo ac risus hendrerit sed consequat lacus elementum. Aenean placerat leo vitae nunc bibendum cursus. Ut ac dui diam. Vivamus massa tortor, consectetur at scelerisque eget, hendrerit et elit. Aliquam hendrerit quam posuere tellus sollicitudin sollicitudin. Ut eget lacinia metus. Curabitur vitae orci ac libero vestibulum commodo. Sed id nibh eu erat pretium tempus. Nullam suscipit fringilla tortor, ac pretium metus iaculis eu. Fusce pellentesque volutpat tortor, at interdum tortor blandit at. Morbi rhoncus euismod ultricies. Fusce sed massa at elit lobortis iaculis non id metus. Aliquam erat volutpat. Vivamus convallis mauris ut sapien tempus quis tempor nunc cursus. Quisque in lorem sem.</p>
ENDTEXT;
		jimport('joomla.utilities.date');
		$jNow = new JDate();

		if (version_compare(JVERSION, '3.0', 'ge')) {
			$now = $jNow->toSql();
		} else {
			$now = $jNow->toMysql();
		}

		$state  =    1;

		$data = array(
			'id'			=> 0,
			'title'			=> $title,
			'alias'			=> $alias,
			'introtext'		=> $introtext,
			'fulltext'		=> $fulltext,
			'state'			=> $state,
			'sectionid'		=> 0,
			'mask'			=> 0,
			'catid'			=> $cat_id,
			'created'		=> $now,
			'created_by_alias' => 'Overload',
			'attribs'		=> array(
				"show_title"=>"","link_titles"=>"","show_intro"=>"","show_category"=>"","link_category"=>"","show_parent_category"=>"","link_parent_category"=>"","show_author"=>"","link_author"=>"","show_create_date"=>"","show_modify_date"=>"","show_publish_date"=>"","show_item_navigation"=>"","show_icons"=>"","show_print_icon"=>"","show_email_icon"=>"","show_vote"=>"","show_hits"=>"","show_noauth"=>"","alternative_readmore"=>"","article_layout"=>""
			),
			'version'		=> 1,
			'parentid'		=> 0,
			'ordering'		=> 0,
			'metakey'		=> '',
			'metadesc'		=> '',
			'access'		=> 1,
			'hits'			=> 0,
			'featured'		=> 0,
			'language'		=> '*',
			'state'			=> $state,
			'metadata'      => array(
				"tags"=>json_encode($alias)
			)
		);

		return $data;
	}
	
    public function saveCategory($data) 
    { 
        $dispatcher = JEventDispatcher::getInstance(); 
        //$table = JTable::getInstance($type = 'Category', $prefix = 'CategoriesTable', $config = array()); 
        $table = JTable::getInstance('Category', 'CategoriesTable',  array()); 
        //$input = JFactory::getApplication()->input; 
        $pk = (!empty($data['id'])) ? $data['id'] : (int) ('OverloadModelProces' . '.id'); 
        $isNew = true; 
         
        if ((!empty($data['tags']) && $data['tags'][0] != '')) 
        { 
            $table->newTags = $data['tags']; 
        } 
         //echo 'import plugin'."\n"; 
                 // Fool the system into thinking we are running as JSite with Finder as the active component 
        JFactory::getApplication('site'); 
        $_SERVER['HTTP_HOST'] = 'domain.com'; 
        // Disable caching. 
        $config = JFactory::getConfig(); 
        $config->set('caching', 0); 
        $config->set('cache_handler', 'file'); 

        // Include the content plugins for the on save events. 
        JPluginHelper::importPlugin('content'); 
          
        //echo 'after import plugin'."\n"; 
        // Load the row if saving an existing category. 
        if ($pk > 0) 
        { 
            $table->load($pk); 
            $isNew = false; 
        } 

        // Set the new parent id if parent id not matched OR while New/Save as Copy . 
        if ($table->parent_id != $data['parent_id'] || $data['id'] == 0) 
        { 
            $table->setLocation($data['parent_id'], 'last-child'); 
        } 

         
        // Bind the data. 
        if (!$table->bind($data)) 
        { 
            $this->setError($table->getError()); 
            return false; 
        } 

        // Bind the rules. 
        if (isset($data['rules'])) 
        { 
            $rules = new JAccessRules($data['rules']); 
            $table->setRules($rules); 
        } 

        // Check the data. 
        if (!$table->check()) 
        { 
            //$this->setError($table->getError()); 
            return false; 
        } 

        // Trigger the onContentBeforeSave event. 
        $result = $dispatcher->trigger('onContentBeforeSave', array('save' . '.' .'OverloadModelProces', &$table, $isNew)); 
        if (in_array(false, $result, true)) 
        { 
            //$this->setError($table->getError()); 
            return false; 
        } 

        // Store the data. 
        if (!$table->store()) 
        { 
            //$this->setError($table->getError()); 
            return false; 
        } 

        $assoc = $this->getAssoc(); 
        if ($assoc) 
        { 

            // Adding self to the association 
            $associations = $data['associations']; 

            foreach ($associations as $tag => $id) 
            { 
                if (empty($id)) 
                { 
                    unset($associations[$tag]); 
                } 
            } 

            // Detecting all item menus 
            $all_language = $table->language == '*'; 

            if ($all_language && !empty($associations)) 
            { 
                JError::raiseNotice(403, JText::_('COM_CATEGORIES_ERROR_ALL_LANGUAGE_ASSOCIATED')); 
            } 

            $associations[$table->language] = $table->id; 

            // Deleting old association for these items 
            $db = JFactory::getDbo(); 
            $query = $db->getQuery(true) 
                ->delete('#__associations') 
                ->where($db->quoteName('context') . ' = ' . $db->quote('com_categories.item')) 
                ->where($db->quoteName('id') . ' IN (' . implode(',', $associations) . ')'); 
            $db->setQuery($query); 
            $db->execute(); 

            if ($error = $db->getErrorMsg()) 
            { 
                $this->setError($error); 
                return false; 
            } 

            if (!$all_language && count($associations)) 
            { 
                // Adding new association for these items 
                $key = md5(json_encode($associations)); 
                $query->clear() 
                    ->insert('#__associations'); 

                foreach ($associations as $id) 
                { 
                    $query->values($id . ',' . $db->quote('com_categories.item') . ',' . $db->quote($key)); 
                } 

                $db->setQuery($query); 
                $db->execute(); 

                if ($error = $db->getErrorMsg()) 
                { 
                //    $this->setError($error); 
                    return false; 
                } 
            } 
        } 

        // Trigger the onContentAfterSave event. 
        $dispatcher->trigger($this->event_after_save, array($this->option . '.' . $this->name, &$table, $isNew)); 

        // Rebuild the path for the category: 
        if (!$table->rebuildPath($table->id)) 
        { 
            //$this->setError($table->getError()); 
            return false; 
        } 

        // Rebuild the paths of the category's children: 
        if (!$table->rebuild($table->id, $table->lft, $table->level, $table->path)) 
        { 
            //$this->setError($table->getError()); 
            return false; 
        } 

        //$this->setState($this->getName() . '.id', $table->id); 

        // Clear the cache 
        //$this->cleanCache(); 

        return true; 
    } 
    
    public function getAssoc() 
    { 
        static $assoc = null; 

        if (!is_null($assoc)) 
        { 
            return $assoc; 
        } 

        $app = JFactory::getApplication(); 
        $extension = $this->getState('category.extension'); 

        $assoc = JLanguageAssociations::isEnabled(); 
        $extension = explode('.', $extension); 
        $component = array_shift($extension); 
        $cname = str_replace('com_', '', $component); 

        if (!$assoc || !$component || !$cname) 
        { 
            $assoc = false; 
        } 
        else 
        { 
            $hname = $cname . 'HelperAssociation'; 
            JLoader::register($hname, JPATH_SITE . '/components/' . $component . '/helpers/association.php'); 

            $assoc = class_exists($hname) && !empty($hname::$category_association); 
        } 

        return $assoc; 
    }  
} 

// Instantiate the application object, passing the class name to JCli::getInstance 
// and use chaining to execute the application. 
JApplicationCli::getInstance('OverloadCli')->execute();