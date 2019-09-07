<? 
/*--------------------------------------------------------*
*   makesymlink_PHS_CA_Media.php                          *
*   Bruce Klotz - Pelham Historical Society               */
	$makesymlink_PHS_CA_MediaV = "1.1.10";                 /*
*                                                         *
*  Create a symlink to an external directory for          *
*  media storage.                                         *
**********************************************************/
//  Set destination Directory:                              
    $todir="/home/users/web/b1269/ipw.pelhamhs/PHS_CA_Media";
    if ($_REQUEST['todir']){$todir =$_REQUEST['todir'];}
    
// Assume default source directory:    
    $fromdir = getcwd()."/media";
    if ($_REQUEST['fromdir']){$fromdir =$_REQUEST['fromdir'];}

    $make=$_REQUEST['make'];
/***********************************************************/    	
function echocheck($fromdir){
//  Check if the Source Directory or Symlink ($fromdir)exists and Return True if they dont, and display an Error and return False if they do.
    if (is_link($fromdir)){
    	 echo "<div style='font-weight:bold;color:red'><h1>!!!WARNING!!!</h1/>Source: $fromdir is already a link!!! <br/>
		         Type: <i> makesymlink_PHP_CA_Media.php?remove=1</i> to remove
		         <h1>!!!WARNING!!!</h1/></div>";
		   return false;
    }
    elseif (is_dir($fromdir)){
		   echo "<div style='font-weight:bold;color:red'>Source: $fromdir Directory FOUND and must be removed to proceed!<br/><br/>
		         WARNING!!!! THIS WILL DELETE ALL FILES IN THE MEDIA DIRECTORY - THIS CAN NOT BE UNDONE!!<br/>MAKE A BACKUP BEFORE REMOVING ";
		   echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; There are <b>".countFiles($fromdir)."</b> Files in the Directory!</div>";
		   return false;
    }
    return true; 
}
/*******************************************************/
function rrmdir($dir) {
//  Recursively Delete ($dir) Directory or Symlink
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
               if (filetype($dir."/".$object) == "dir"){ 
                 rrmdir($dir."/".$object);
               }else{
               	 unlink   ($dir."/".$object);
               }
            }
        }
        reset($objects);
        rmdir($dir);
    }
    return true;
}
/*******************************************************/
function countFiles($dir){
//  Return number of files in Directory ($dir)
    return exec("find ".$dir." -type f -print | wc -l");
} 
/********************************************************/
/*                  Begin Here                          */
    echo  "<html><head><title>Make Symlinks for CA</title></head>
           <body>";
    echo	"   [makesymlink_PHS_CA_Media V$makesymlink_PHS_CA_MediaV]<br/>";



		
	  if (!is_dir($todir)){
		    echo "Destination: $todir Directory NOT FOUND!<br/>";
		    $make=false;
	  }


	
		if($_REQUEST['remove']) {
	      if(is_link($fromdir)){
	             echo "REMOVING LINK: $fromdir...";
		           if ( unlink($fromdir)){echo"...REMOVED!<br/>";}else{echo" FAILED!<br/>";}
	      }
	      if(is_dir($fromdir)){
	             echo "REMOVING DIR: $fromdir...";
		           if (rrmdir($fromdir)){echo"...REMOVED!<br/>";}else{echo" FAILED!<br/>";}
	      }
    }		



  	if ($make and echocheck($fromdir)){
		    echo	"Making link ...<br/>from: <b>$fromdir</b><br/> to: <b>$todir</b> ";
		    if( symlink($todir,$fromdir)){echo"...OK";}else{echo"...FAILED!";} 	
	  }else{
		    echo  "<br/>
		           To create a SYMLINK <br/>&nbsp;&nbsp;&nbsp;&nbsp;From: <b>$fromdir</b><br/>&nbsp;&nbsp;&nbsp;&nbsp;To: <b>$todir</b><br/>
	             Type: <i> makesymlink_PHP_CA_Media.php?make=1 <br/></i><br/><br/>";
	  }


	
	  if (!$make){echocheck($fromdir);}
	  $removewhat = "[ nothing ]<br/>";
	  if (is_dir($fromdir)){$removewhat="DIR <br/> <b><i>$fromdir</i></b>";}
	  if(is_link($fromdir)){$removewhat="LINK <br/> <b><i>$fromdir</i></b>";}
	
	  echo "   <form action='".$_SERVER['PHP_SELF']."'>
	             <button type='submit' name='make' value='Make'>Make Link from<br/><b><i>$fromdir</i></b><br/>to<br><b><i>$todir</i></b></button>
	             &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	             <button type='submit' name='remove' value='remove'><br/>Remove $removewhat<br/><br/></button>
	           </form>";
    echo "</body></html>";
?>	