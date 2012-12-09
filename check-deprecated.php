<?php 

/**
* @param find deprecated functions in given directory and sub directories
* @param output(print): the file name, file root/directory, line number, contents of the affacted line, link to help in php.net and more info
* @author  m3dana@gmail.com [While working at Chapter Eight Limited mehdi.dana@chaptereight.com]
* @version 3.5
* flags explained:
* $dir: define the root directory (scpript will take care of child directories),by default get the current directory
* $find_close_match: relax the search if it's true, it will show matches even if they're not function
* $show_more_info: if it's true, it will show more details, i.e. show even functions with 0 match.
* $dep_str: array of deprecated functions - array('deprecated fuunction', 'link to help page'),
* $output_json: it it's true, the output will be json
* $check_only_php_file: if it's true, it will search for any file extension
* ready_togo(); to run script
*/

// change flag here 
$dir=getcwd();
$find_close_match = false; 			 
$show_more_info= true;
$outprint_json=false;
$check_only_php_file=true;
$dep_str = array(
	array('ereg', 'http://www.php.net/manual/en/function.ereg.php'),
	array('call_user_method', 'http://www.php.net/manual/en/function.call-user-method.php'),
	array('call_user_method_array', 'http://www.php.net/manual/en/function.call-user-method-array.php'),
	array('define_syslog_variables', 'http://www.php.net/manual/en/function.define-syslog-variables.php'),
	array('ereg_replace', 'http://www.php.net/manual/en/function.ereg-replace.php'),
	array('eregi', 'http://www.php.net/manual/en/function.eregi.php'),
	array('eregi_replace', 'http://www.php.net/manual/en/function.eregi-replace.php'),
	array('set_magic_quotes_runtime', 'http://www.php.net/manual/en/function.set-magic-quotes-runtime.php'),
	array('session_register', 'http://www.php.net/manual/en/function.session-register.php'),
	array('session_unregister', 'http://www.php.net/manual/en/function.session-unregister.php'),
	array('session_is_registered', 'http://www.php.net/manual/en/function.session-is-registered.php'),
	array('set_socket_blocking', 'http://www.php.net/manual/en/function.set-socket-blocking.php'),
	array('split', 'http://www.php.net/manual/en/function.split.php'), 
	array('spliti', 'http://www.php.net/manual/en/function.spliti.php'),
	array('sql_regcase', 'http://www.php.net/manual/en/function.sql-regcase.php'),
	array('define_syslog_variables', 'http://nl.php.net/manual/en/network.configuration.php#ini.define-syslog-variables'),
	array('register_globals', 'http://nl.php.net/manual/en/ini.core.php#ini.register-globals'),
	array('register_long_arrays', 'http://nl.php.net/manual/en/ini.core.php#ini.register-long-arrays'),
	array('safe_mode', 'http://nl.php.net/manual/en/ini.sect.safe-mode.php#ini.safe-mode'),
	array('magic_quotes_gpc', 'http://nl.php.net/manual/en/info.configuration.php#ini.magic-quotes-gpc'),
	array('magic_quotes_runtime', 'http://nl.php.net/manual/en/info.configuration.php#ini.magic-quotes-runtime'),
	array('magic_quotes_sybase', 'http://nl.php.net/manual/en/sybase.configuration.php#ini.magic-quotes-sybase')
				);

ready_togo(); // run the script





/**
* @input a diretory and a string
* @return nested array of files
* this function loop through all folders and sub folders within given folder
*/

function getReadFilesFromDir($dir, $str) { 
	global $find_close_match;
	global $check_only_php_file;
  	$files = array(); 
	if(is_dir($dir)){
	  	if ($handle = opendir($dir)) { 
			while (false !== ($file = readdir($handle))) { 
				if ($file != "." && $file != "..") { 
					if(is_dir($dir.'/'.$file)) { 
					    $dir2 = $dir.'/'.$file; 
					    $files[] = getReadFilesFromDir($dir2, $str); 
					} 
					else { 
						// if it is a php file
						$file_ext = substr(strrchr($file,'.'),1);
						$content='';
						if(($file_ext == 'php') && ($check_only_php_file==true) ) {
							$content =@file_get_contents($dir.'/'.$file, true);
						}elseif($check_only_php_file==false ){
							$content =@file_get_contents($dir.'/'.$file, true);
						}else{
						}
							if($find_close_match==false){
								$s = "".$str."\(";
							}else{
								$s = $str;
							}
							if(preg_match("/$s/", $content)==true){
									$here='';
									foreach(array_flatten_recursive(find_str_line($content, $s)) as $v){
										$here = $here.'<br />'.$v;
									}
								 $files[] = $dir."/".$file."<br /><font color='red'>".$here."<br /></font><br />"; 
							}
						}
				} 
			} 
			closedir($handle); 
		}
	} 
	return $files;  			
} 

/**
* @input a nested array
* @return a one dimention array (flat array)
*/

function array_flatten_recursive($array) { 
    if($array) { 
        $flat = array(); 
        foreach(new RecursiveIteratorIterator(new RecursiveArrayIterator($array), RecursiveIteratorIterator::SELF_FIRST) as $key=>$value) { 
            if(!is_array($value)) { 
                $flat[] = $value; 
            } 
        } 
        return $flat; 
    } else { 
        return false; 
    } 
} 

/**
* replace string for all match found in big string(text)
*/
function str_replace_once($needle , $replace , $haystack){
    $pos = strpos($haystack, $needle);
		if ($pos === false) {
			return $haystack;
		}
    return substr_replace($haystack, $replace, $pos, strlen($needle));
}

/**
* @input text and a key
* @return an array of all founded indexes
* find all match in contents
*/
function all_match($str, $s){
	$iarray = array();
	$first_index = strpos($str, $s);
	$from = $first_index-1;
	$numberOfChar = 50;
	$here = substr($str, $from, $numberOfChar);
	$iarray[] = $here;
			$last_index = strripos($str, $s);
			if($first_index != $last_index){
				$str1 = str_replace_once($s, ' *** ', $str);
				$iarray[] = all_match($str1, $s);
			}else{
			}
	return $iarray;
}

/**
* @input text
* @return an array of line number plus content of the line
*/
// find all match plus line number 
function find_str_line($str, $key){
	$txtlines = explode("\n", $str);
	$tnarray = array();
	foreach($txtlines as $value){
		if(preg_match("/$key/", $value)==true){
			$line_number = array_search($value, $txtlines)+1;
			$out = '#'.$line_number.'  '.$value.'<br />';
			$tnarray[] = $out;
		}
	}
	return $tnarray;
}

/**
* this is the main function, it will echo (print) the result of given input
* It may print a file twice (each time for different deprecated functions)
* It could output result as json format, (to do this change the option in flag)
*/
function ready_togo(){	
	global $dir;
	global $dep_str;
	global $show_more_info;
	global $outprint_json;
	if(is_array($dep_str) && is_dir($dir)){
		if($outprint_json==true){
			foreach($dep_str as $value){
				print('<pre>');
				print_r(getReadFilesFromDir($dir, $value[0]));
				print('<pre>');
			}
		exit; // don't run the rest of code
		}
		foreach($dep_str as $value){
			echo "<a href='#".$value[0]."'>".$value[0]."</a> | <a name='top'></a>";
		}
		echo "<hr />";
		$all_found=0;
		foreach($dep_str as $value){							
			$chkd = getReadFilesFromDir($dir, $value[0]);
			$efound = 0;
			$faltted_array=array_flatten_recursive($chkd);
			$all_found= $all_found+count($faltted_array);
				if(count($faltted_array)==0 && $show_more_info==false){
					// dont show some info
				}else{
					if(is_array($chkd)){
						echo "<h2><a id='".$value[0]."' href='$value[1]' target='_blank' >".$value[0]."</a></h2>"; 
						foreach($faltted_array as $k=>$v){	// the actual output is a nested array, print friendly
							echo "<a href='$value[1]' target='_blank' >".$value[0]."</a><br />"; 
							echo $v;
							$efound++;
						}
					echo '<br /><b>'.$efound.'</b> file found for '.$value[0];
					echo " <a href='#top'>Go Top</a>";
				}
			}
		}
		echo '<br /><hr /><b>'.$all_found.'</b> files found for all diretories';// print the number of all files found
	}else{
		echo "<h3><font color='red'>Oops, there is a problem here </font></h3>
		Check if folder is defined. Check if deprecated inputs are correct";
	}
}

?>
