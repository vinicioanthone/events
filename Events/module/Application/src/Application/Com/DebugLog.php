<?php

namespace Application\Com;

/*==================================================================*/
/*					Change to DEBUG mode							*/
/*==================================================================*/
define("DEBUG", false);			// For DEBUG.　commit with false. is ok to use but dont commit with true value
define("WARNING", true);			// When debugging, echo output (not generated basically)
define("ERROR", true);			// Severe error (for unauthorized access / attack detection)

/*==========================================================================*/
/*											    							*/
/*					Debug log output										*/
/*											    							*/
/*==========================================================================*/
/**
 *	Debug log output utility
 *
 */
class DebugLog
{
	/*==================================================================*/
	/*					public staticＩＦ			    				*/
	/*==================================================================*/
	/**
	 * Debug log output
	 * ※For debugging false Committing is OK for individual use but better not commit
	 * @param string $func     Function name
	 * @param string $line　   Output location
	 * @param string $message  Output characters	 
	 *
	 */
	public static function debugLog($class, $func, $line, $message)
	{
		if( DEBUG ) {
			echo "DEBUG:".$class;
			echo " ";
			echo $func;
			echo " L";
			echo $line;
			echo ":";
			echo $message;
			echo "<br/>";
		}
	}


	/**
	 * Warning log output
	 * ※When debugging, echo output (not generated basically)
	 * @param string $func    Function name
	 * @param string $line　   Output location
	 * @param string $message  Output characters		 
	 *
	 */
	public static function warningLog($class, $func, $line, $message)
	{
		if( WARNING ) {
			echo "WARNING:".$class;
			echo " ";
			echo $func;
			echo " L";
			echo $line;
			echo ":";
			echo $message;
			echo "<br/>";
		}
		else {
			// TODO File output
		}
	}


	/**
	 * Error log output
	 * ※Severe error (for unauthorized access / attack detection)
	 * @param string $func    Function name
	 * @param string $line　   Output location
	 * @param string $message  Output characters	 
	 *
	 */
	public static function errorLog($class, $func, $line, $message)
	{
		if( ERROR ) {
			echo "ERROR:".$class;
			echo " ";
			echo $func;
			echo " L";
			echo $line;
			echo ":";
			echo $message;
			echo "<br/>";
		}

		
		// TODO Output file

	}
}
