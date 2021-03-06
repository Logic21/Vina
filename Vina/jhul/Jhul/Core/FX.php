<?php namespace Jhul\Core;

/* @Author : Manish Dhruw [1D3N717Y12@gmail.com]
+=======================================================================================================================
|@Created : Wednesday 10 September 2014 11:08:29 AM IST
|
|@Updated :
|	-Thursday 04 June 2015 11:44:07 AM IST
|	-Fri 23 Oct 2015 10:59:34 PM IST
|	-[ 2016-Sep-30, 2016-Oct-07, 2017DEC03 ]
+---------------------------------------------------------------------------------------------------------------------*/

class FX
{
	use _AccessKey;

	protected $map = [];

	//public function root_system(){ return JHUL_SYSTEM_PATH ; }

	public function has( $name )
	{
		return isset($this->map[$name]);
	}

	//register path for namespace
	public function add( $name , $path = NULL )
	{
		if( is_array($name) )
		{
			foreach ( $name as $n => $p )
			{
				$this->add($n, $p);
			}

			return $this;
		}

		if( !$this->has($name)  )
		{
			$path = rtrim( $path, '/' ) ;

			if( file_exists($path) )
			{
				$this->map[$name] = $path ;

				return $this;
			}

			throw new \Exception( 'Invalid Path "'.$path.'" for name "'.$name.'" '  , 1);
		}

		throw new \Exception( 'Path name "'.$name.'" already exists') ;
	}

	public function map()
	{
		return $this->map;
	}

	// leftmost name of namespace
	public function lPop( $namespace, $depth = 1 )
	{
		$sprtr = $this->getSeperator( $namespace );

		$namespace = trim( $namespace, $sprtr );

		$pos = -1;

		while( $depth > 0 )
		{
			--$depth ;

			$pos = strpos( $namespace, $sprtr, $pos + 1 );
		}

		$pop = NULL;

		if( $pos )
		{
			$pop = substr( $namespace , 0, $pos  );
		}

		return $pop;
	}

	public function getSeperator( $namespace )
	{
		if( strpos( $namespace, '/' ) !== FALSE ) return '/';

		if( strpos( $namespace, '\\' ) !== FALSE ) return '\\';
	}


	//right most name of namespace
	public function rPop( $namespace, $sprtr = '\\' )
	{
		$namespace = trim($namespace, $sprtr );

		$pos = strrpos( $namespace , $sprtr ) ;

		return FALSE === $pos ? $namespace : substr( $namespace , $pos+1  );
	}

	public function rchop( $namespace )
	{
		return substr( $namespace, 0, strrpos( $namespace, '\\' ) );
	}

	public function getFromNamespace( $child, $namespace )
	{
		if( empty($child) )
		{
			return $this->rchop($namespace) ;
		}

		return $this->rchop($namespace).'\\'.$child ;
	}

	//return dir of namespace
	public function dir( $namespace )
	{
		if( strpos( $namespace, '/ ') ) return rPop( $namespace );

		return $this->rPop( dirname( $this->g( $namespace ) ), '/' ) ;
	}

	public function dirPath( $namespace )
	{
		return dirname( $this->g( $namespace) )  ;
	}

	public function getFile( $namespace, $ext = 'php')
	{
		return ( $file = $this->g( $namespace ) ) != NULL ? $file .'.'. $ext : NULL ;
	}

	public function filePath( $namespace, $ext = 'php')
	{
		return ( $file = $this->g( $namespace ) ) != NULL ? $file .'.'. $ext : NULL ;
	}

	// namespace to path conversion
	// does not returns file Path
	public function g( $namespace )
	{
		$namespace = trim( $namespace, '\\' ) ;

		$name = $this->lpop( $namespace );

		if( isset($this->map[$name]) )
		{
			$namespace = str_replace( $name, $this->map[$name], $namespace ) ;
		}

		return str_replace( '\\',  '/', $namespace );
	}


	public function loadConfigFile( $file, $required = TRUE, $params = [])
	{
		return $this->readConfigFile( $file, $required, $params ) ;
	}

	//loads configuration file
	public function readConfigFile( $file, $required = TRUE, $params = [] )
	{
		if( strrpos( $file, '\\' ) )
		{
			$file = $this->getFile($file);
		}

		if( strrpos( $file, '/' ) &&  !strpos($file, '.php') )
		{
			$file = $file.'.php';
		}

		if( is_file($file) )
		{

			$config = require( $file );

			if( is_array($config) ) return $config;

			throw new \Exception( 'Configuration file "'.$file.'" must return array' , 1);
		}

		if( !$required ) return [];

		throw new \Exception( 'configuration file "'.$file.'" Not Found ', 1);
	}

	public function name( $path )
	{
		if( ( $name = array_search($path, $this->map) ) ) return $name;

		throw new \Exception('Pathname not found for path "'.$path.'" ') ;
	}

	public function createAndPutContentsToFile( $content, $file, $overwrite = FALSE )
	{
		$path = pathinfo($file);

		if (!file_exists($path['dirname']))
		{
			mkdir($path['dirname'], 0755, true);
		}

		return $this->writeToFile( $content, $file, $overwrite );
	}


	public function writeToFile( $content, $file, $overwrite = FALSE )
	{



		$fp = fopen( $file , 'w' );

		if( flock($fp, LOCK_EX | LOCK_NB ) )
		{
			// truncate file
			ftruncate($fp, 0);
			fwrite($fp, $content);

			// flush output before releasing the lock
			fflush($fp);
			flock($fp, LOCK_UN);    // release the lock
		}
		else
		{
			//TODO show server busy page
			echo 'FX : Server Is Busy, Please Try Again later';
			exit ;
			throw new \Exception( 'Error Writing To File "'.$file.'" ' , 1);
		}

		fclose($fp);

		return TRUE ;
	}


	public function listFiles($dir , & $results = [])
	{
		$files = scandir($dir);

		foreach($files as $key => $value)
		{
			$path = realpath($dir.DIRECTORY_SEPARATOR.$value);

			if(!is_dir($path))
			{
				$results[] = $path;
			}

			else if($value != "." && $value != "..")
			{
				$this->listFiles($path, $results);
				$results[] = $path;
			}
		}

		return $results;
	}

	public function listFiles2()
	{

	}

}
