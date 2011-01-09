<?php echo '<?xml version="1.0" encoding="UTF-8"?>'."\n"; ?>
<rss version="2.0">

<channel>
<title>SendToServer Feed</title>
<link><?php echo "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?></link>
<description>Feed of all uploaded links and files</description>
<language>en</language>
<lastBuildDate><?php echo date('D, d M Y H:i:s O'); ?></lastBuildDate>
<?php 
$maxstrlen = 50;
$filepath = '/webdav/';
$protocol = 'https://';
$server = $protocol.$_SERVER['HTTP_HOST'];
$files = array();
$folders = array();
if ($handle = opendir('.'.$filepath)) {
    while (false !== ($file = readdir($handle))) {
        if (preg_match('/(URL|Image)-(\d{14}).*/', $file)) {
						$files[$file] = preg_replace('/.+-(\d{14}).*/', '$1', $file);
				} /*else if (preg_match('/^Image-\d{14}$/', $file)) {
						$folders[] = $file;
				}*/
		}
		closedir($handle);
}

function print_enclosure(&$value, $key) {echo ' '.$key.'="'.$value.'"';}

arsort($files);
foreach (array_keys($files) as $file) {
	echo "\n".'<item>'."\n";
	$match = '';
	$link = '';
	$title = '';
	$date = array();
	$enclosure = array();

	// 
	if (is_dir(".$filepath".$file)) {
		if ($handle = opendir(".$filepath".$file)) {	
			while (false !== ($binfile = readdir($handle))) {
				if (!preg_match('/^(\.|\.\.)$/', $binfile)) {
					$title = $binfile;
					$link = $server.$filepath.$file."/".$binfile."\n";
					// filesize returns nothing :-(
					//$enclosure['length'] = filesize('.'.$filepath.$file.'/'.$binfile);
					$enclosure['url']	= trim($link);
					// finfo needs php 5.3
				/*	$finfo = finfo_open(FILEINFO_MIME_TYPE);
					$enclosure['type'] = finfo_file($finfo, ".$filepath".$file."/".$binfile);
					finfo_close($finfo);*/
					break;
				}
			}
		}
	} else {
		$content = file_get_contents(".$filepath".$file);
		if (preg_match('/^[a-zA-Z]+[:\/\/]+[A-Za-z0-9\-_]+\\.+[A-Za-z0-9\.\/%&=\?\-_]+$/i', $content, $match)) {
			$title = $match[0];
			$link  = $match[0];
		} else {
			$lines = preg_split('/\n/', $content);
			$title = (strlen($lines[0]) < $maxstrlen) ? $lines[0] : substr($lines[0],0 ,$maxstrlen);
			$link = $server.$filepath.$file;
		}
	} 
	preg_match('/^(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})$/', $files[$file], $date);
	echo '<title>'.htmlspecialchars(trim($title)).'</title>'."\n";
  echo '<link>'.htmlspecialchars(trim($link)).'</link>'."\n";
	echo '<gid>'.preg_replace('/(.+-\d{14}).*/', '$1', $file).'</gid>'."\n";
	echo '<pubDate>'.date('D, d M Y H:i:s O', mktime($date[4], $date[5], $date[6], $date[2], $date[3], $date[1])).'</pubDate>'."\n";
	if (isset($enclosure['url'])) {
		echo '<enclosure';
		array_walk($enclosure, 'print_enclosure');
		echo ' />'."\n";
	}
	echo '</item>'."\n";
}
?>

</channel>
<?php //phpinfo(); ?>
</rss>
