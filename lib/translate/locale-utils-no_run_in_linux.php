<?php

class PoParser {
    var $entries = array();
    var $headers = array();
    var $sourceFile = null;
    var $options = array();

    public function __construct($options=array()) {
        $defaultOptions = array(
            'multiline-glue'=>'<##EOL##>',  // Token used to separate lines in msgid
            'context-glue'  => '<##EOC##>'  // Token used to separate ctxt from msgid
        );
        $this->options = array_merge($defaultOptions, $options);
    }

    public function parseFile($filepath) {
    	if (! is_file($filepath)) { die($filepath . " not found"); }

		$headers         = array();
        $hash            = array();
        $entry           = array();
        $justNewEntry    = false; // A new entry has been just inserted.
        $firstLine       = true;
        $lastPreviousKey = null; // Used to remember last key in a multiline previous entry.
        $state           = null;
        $lineNumber      = 0;


    	$archivopo= @fopen($filepath, "r");
    	$wholefile = file_get_contents($filepath, false, null, 0, 1000);
        $encoding  = mb_detect_encoding($wholefile);

        while (!feof($archivopo)) {
            $line  = trim(fgets($archivopo));
            $split = preg_split('/\s+/ ', $line, 2);
            $key   = $split[0];

            // If a blank line is found, or a new msgid when already got one
            if ($line === '' || ($key=='msgid' && isset($entry['msgid']))) {
                // Two consecutive blank lines
                if ($justNewEntry) {
                    $lineNumber++;
                    continue;
                }

                if ($firstLine) {
                    $firstLine = false;
                    if (self::isHeader($entry)) {
                        array_shift($entry['msgstr']);
                        $headers = $entry['msgstr'];
                    } else {
                        $hash[] = $entry;
                    }
                } else {
                    // A new entry is found!
                    $hash[] = $entry;
                }

                $entry           = array();
                $state           = null;
                $justNewEntry    = true;
                $lastPreviousKey = null;
                if ($line==='') {
                    $lineNumber++;
                    continue;
                }
            }

            $justNewEntry = false;
            $data         = isset($split[1]) ? $split[1] : null;

            switch ($key) {
                // Flagged translation
                case '#,':
                    $entry['flags'] = preg_split('/,\s*/', $data);
                    break;

                // # Translator comments
                case '#':
                    $entry['tcomment'] = !isset($entry['tcomment']) ? array() : $entry['tcomment'];
                    $entry['tcomment'][] = $data;
                    break;

                // #. Comments extracted from source code
                case '#.':
                    $entry['ccomment'] = !isset($entry['ccomment']) ? array() : $entry['ccomment'];
                    $entry['ccomment'][] = $data;
                    break;

                // Reference
                case '#:':
                    $entry['reference'][] = addslashes($data);
                    break;

                
                case '#|':      // #| Previous untranslated string
                case '#~':      // #~ Old entry
                case '#~|':     // #~| Previous-Old untranslated string. Reported by @Cellard

                    switch ($key) {
                        case '#|':  $key = 'previous';
                                    break;
                        case '#~':  $key = 'obsolete';
                                    break;
                        case '#~|': $key = 'previous-obsolete';
                                    break;
                    }

                    $tmpParts = explode(' ', $data);
                    $tmpKey   = $tmpParts[0];

                    if (!in_array($tmpKey, array('msgid','msgid_plural','msgstr','msgctxt'))) {
                        $tmpKey = $lastPreviousKey; // If there is a multiline previous string we must remember what key was first line.
                        $str = $data;
                    } else {
                        $str = implode(' ', array_slice($tmpParts, 1));
                    }

                    $entry[$key] = isset($entry[$key])? $entry[$key]:array('msgid'=>array(),'msgstr'=>array());

                    if (strpos($key, 'obsolete')!==false) {
                        $entry['obsolete'] = true;
                        switch ($tmpKey) {
                            case 'msgid':
                                $entry['msgid'][] = $str;
                                $lastPreviousKey = $tmpKey;
                                break;

                            case 'msgstr':
                                if ($str == "\"\"") {
                                    $entry['msgstr'][] = trim($str, '"');
                                } else {
                                    $entry['msgstr'][] = $str;
                                }
                                $lastPreviousKey = $tmpKey;
                                break;

                            default:
                                break;
                        }
                    }

                    if ($key!=='obsolete') {
                        switch ($tmpKey) {
                            case 'msgid':
                            case 'msgid_plural':
                            case 'msgstr':
                                $entry[$key][$tmpKey][] = $str;
                                $lastPreviousKey = $tmpKey;
                                break;

                            default:
                                $entry[$key][$tmpKey] = $str;
                                break;
                        }
                    }
                    break;


                // context
                // Allows disambiguations of different messages that have same msgid.
                // Example:
                //
                // #: tools/observinglist.cpp:700
                // msgctxt "First letter in 'Scope'"
                // msgid "S"
                // msgstr ""
                //
                // #: skycomponents/horizoncomponent.cpp:429
                // msgctxt "South"
                // msgid "S"
                // msgstr ""
                case 'msgctxt':
                    // untranslated-string
                case 'msgid':
                    // untranslated-string-plural
                case 'msgid_plural':
                    $state = $key;
                    $entry[$state][] = $data;
                    break;
                // translated-string
                case 'msgstr':
                    $state = 'msgstr';
                    $entry[$state][] = $data;
                    break;

                default:
                    if (strpos($key, 'msgstr[') !== false) {
                        // translated-string-case-n
                        $state = 'msgstr';
                        $entry[$state][] = $data;
                    } else {
                        // "multiline" lines
                        switch ($state) {
                            case 'msgctxt':
                            case 'msgid':
                            case 'msgid_plural':
                                if (is_string($entry[$state])) {
                                    // Convert it to array
                                    $entry[$state] = array($entry[$state]);
                                }
                                $entry[$state][] = $line;
                                break;

                            case 'msgstr':
                                // Special fix where msgid is ""
                                if ($entry['msgid'] == "\"\"") {
                                    $entry['msgstr'][] = trim($line, '"');
                                } else {
                                    $entry['msgstr'][] = $line;
                                }
                                break;

                            default:
                                throw new \Exception(
                                    'PoParser: Parse error! Unknown key "' . $key . '" on line ' . ($lineNumber+1)
                                );
                        }
                    }
                    break;
            }
            $lineNumber++;
        }
        fclose($archivopo);
		if ($state == 'msgstr') {
            $hash[] = $entry;
        }

        // - Cleanup header data
        $this->headers = array();
        foreach ($headers as $header) {
            $header = $this->clean( $header );
            $this->headers[] = "\"" . preg_replace("/\\n/", '\n', $header) . "\"";
        }

        // - Cleanup data,
        // - merge multiline entries
        // - Reindex hash for ksort
        $temp = $hash;
        $this->entries = array();
        foreach ($temp as $entry) {
            foreach ($entry as &$v) {
                $or = $v;
                $v = $this->clean($v);
                if ($v === false) {
                    // parse error
                    throw new \Exception(
                        'PoParser: Parse error! poparser::clean returned false on "' . htmlspecialchars($or) . '"'
                    );
                }
            }

            if (isset($entry['msgid']) && isset($entry['msgstr'])) {
                $id = $this->getEntryId($entry);
                $this->entries[$id] = $entry;
            }
        }
        return $this->entries;
    }


    /**
     * Updates an entry.
     * If entry not found returns false. If $createNew is true, a new entry will be created.
     * $entry is an array that can contain following indexes:
     *  - msgid: String Array. Required.
     *  - msgstr: String Array. Required.
     *  - reference: String Array.
     *  - msgctxt: String. Disambiguating context.
     *  - tcomment: String Array. Translator comments.
     *  - ccomment: String Array. Source comments.
     *  - msgid_plural: String Array.
     *  - flags: Array. List of entry flags. Example: array('fuzzy','php-format')
     *  - previous: Array: Contains previous untranslated strings in a sub array with msgid and msgstr. */
    public function setEntry($msgid, $entry, $createNew = true){
        // In case of new entry
        if (!isset($this->entries[$msgid])) {
            if ($createNew==false) {
                return;
            }
            $this->entries[$msgid] = $entry;
        }
        else {
            // Be able to change msgid.
            if( $msgid!==$entry['msgid'] ) {
                unset($this->entries[$msgid]);
                $new_msgid = is_array($entry['msgid'])? implode($this->options['multiline-glue'], $entry['msgid']):$entry['msgid'];
                $this->entries[$new_msgid] = $entry;
            }
            else {
                $this->entries[$msgid] = $entry;
            }
        }
    }


    public function writeFile($filepath) {
        $output = $this->compile();
        $result = file_put_contents($filepath, $output);
        if ($result===false) { return false; }
        return true;
    }

    public function compile()  {
        $output = '';
        if (count($this->headers) > 0) {
            $output.= "msgid \"\"\n";
            $output.= "msgstr \"\"\n";
            foreach ($this->headers as $header) {
                $output.= $header . "\n";
            }
            $output.= "\n";
        }

        $entriesCount = count($this->entries);
        $counter = 0;
        foreach ($this->entries as $entry) {
            $isObsolete = isset($entry['obsolete']) && $entry['obsolete'];
            $isPlural = isset($entry['msgid_plural']);

            if (isset($entry['previous'])) {
                foreach ($entry['previous'] as $key => $data) {

                    if (is_string($data)) {
                        $output.= "#| " . $key . " " . $this->cleanExport($data) . "\n";
                    } elseif (is_array($data) && count($data)>0) {
                        foreach ($data as $line) {
                            $output.= "#| " . $key . " " . $this->cleanExport($line) . "\n";
                        }
                    }

                }
            }

            if (isset($entry['tcomment'])) {
                foreach ($entry['tcomment'] as $comment) {
                    $output.= "# " . $comment . "\n";
                }
            }

            if (isset($entry['ccomment'])) {
                foreach ($entry['ccomment'] as $comment) {
                    $output.= '#. ' . $comment . "\n";
                }
            }

            if (isset($entry['reference'])) {
                foreach ($entry['reference'] as $ref) {
                    $output.= '#: ' . $ref . "\n";
                }
            }

            if (isset($entry['flags']) && !empty($entry['flags'])) {
                $output.= "#, " . implode(', ', $entry['flags']) . "\n";
            }

            if (isset($entry['@'])) {
                $output.= "#@ " . $entry['@'] . "\n";
            }

            if (isset($entry['msgctxt'])) {
                $output.= 'msgctxt ' . $this->cleanExport($entry['msgctxt'][0]) . "\n";
            }


            if ($isObsolete) {
                $output.= "#~ ";
            }

            if (isset($entry['msgid'])) {
                // Special clean for msgid
                if (is_string($entry['msgid'])) {
                    $msgid = explode("\n", $entry['msgid']);
                } elseif (is_array($entry['msgid'])) {
                    $msgid = $entry['msgid'];
                } else {
                    throw new \Exception('msgid not string or array');
                }

                $output.= 'msgid ';
                foreach ($msgid as $i => $id) {
                    if ($i > 0 && $isObsolete) {
                        $output.= "#~ ";
                    }
                    $output.= $this->cleanExport($id) . "\n";
                }
            }

            if (isset($entry['msgid_plural'])) {
                // Special clean for msgid_plural
                if (is_string($entry['msgid_plural'])) {
                    $msgidPlural = explode("\n", $entry['msgid_plural']);
                } elseif (is_array($entry['msgid_plural'])) {
                    $msgidPlural = $entry['msgid_plural'];
                } else {
                    throw new \Exception('msgid_plural not string or array');
                }

                $output.= 'msgid_plural ';
                foreach ($msgidPlural as $plural) {
                    $output.= $this->cleanExport($plural) . "\n";
                }
            }

            if (isset($entry['msgstr'])) {
                if ($isPlural) {
                    foreach ($entry['msgstr'] as $i => $t) {
                        $output.= "msgstr[$i] " . $this->cleanExport($t) . "\n";
                    }
                } else {
                    foreach ((array)$entry['msgstr'] as $i => $t) {
                        if ($i == 0) {
                            if ($isObsolete) {
                                $output.= "#~ ";
                            }

                            $output.= 'msgstr ' . $this->cleanExport($t) . "\n";
                        } else {
                            if ($isObsolete) {
                                $output.= "#~ ";
                            }

                            $output.= $this->cleanExport($t) . "\n";
                        }
                    }
                }
            }

            $counter++;
            // Avoid inserting an extra newline at end of file
            if ($counter < $entriesCount) {
                $output.= "\n";
            }
        }

        return $output;
    }



    protected function cleanExport($string){
        $quote = '"';
        $slash = '\\';
        $newline = "\n";
        $replaces = array(
            "$slash" => "$slash$slash",
            "$quote" => "$slash$quote",
            "\t" => '\t',
        );
        $string = str_replace(array_keys($replaces), array_values($replaces), $string);
        $po = $quote . implode("${slash}n$quote$newline$quote", explode($newline, $string)) . $quote;
        // remove empty strings
        return str_replace("$newline$quote$quote", '', $po);
    }

    protected function getEntryId(array $entry){
        if (isset($entry['msgctxt'])) {
            $id = implode($this->options['multiline-glue'], (array)$entry['msgctxt']) . $this->options['context-glue'] . implode($this->options['multiline-glue'], (array)$entry['msgid']);
        } else {
            $id = implode($this->options['multiline-glue'], (array)$entry['msgid']);
        }
        return $id;
    }

    protected function clean($x)  {
        if (is_array($x)) {
            foreach ($x as $k => $v) {
                $x[$k] = $this->clean($v);
            }
        } else {
            // Remove double quotes from start and end of string
            if ($x == '') {
                return '';
            }
            if ($x[0] == '"') {
                $x = substr($x, 1, -1);
            }
            // Escapes C-style escape secuences (\a,\b,\f,\n,\r,\t,\v) and converts them to their actual meaning
            $x = stripcslashes($x);
        }
        return $x;
    }

    protected static function isHeader(array $entry){
        if (empty($entry) || !isset($entry['msgstr'])) {
            return false;
        }

        $headerKeys = array(
            'Project-Id-Version:' => false,
            //  'Report-Msgid-Bugs-To:' => false,
            //  'POT-Creation-Date:'    => false,
            'PO-Revision-Date:' => false,
            //  'Last-Translator:'      => false,
            //  'Language-Team:'        => false,
            'MIME-Version:' => false,
            //  'Content-Type:'         => false,
            //  'Content-Transfer-Encoding:' => false,
            //  'Plural-Forms:'         => false
        );
        $count = count($headerKeys);
        $keys = array_keys($headerKeys);

        $headerItems = 0;
        foreach ($entry['msgstr'] as $str) {
            $tokens = explode(':', $str);
            $tokens[0] = trim($tokens[0], "\"") . ':';

            if (in_array($tokens[0], $keys)) {
                $headerItems++;
                unset($headerKeys[$tokens[0]]);
                $keys = array_keys($headerKeys);
            }
        }
        return ($headerItems == $count) ? true : false;
    }
}

/**
 * php.mo 0.1 by Joss Crowcroft (http://www.josscrowcroft.com)
 * 
 * Converts gettext translation '.po' files to binary '.mo' files in PHP.
 * 
 * Usage: 
 * <?php require('php-mo.php'); phpmo_convert( 'input.po', [ 'output.mo' ] ); ?>
 * 
 * NB:
 * - If no $output_file specified, output filename is same as $input_file (but .mo)
 * - Returns true/false for success/failure
 * - No warranty, but if it breaks, please let me know
 * 
 * More info:
 * https://github.com/josscrowcroft/php.mo
 * 
 * Based on php-msgfmt by Matthias Bauer (Copyright © 2007), a command-line PHP tool
 * for converting .po files to .mo.
 * (http://wordpress-soc-2007.googlecode.com/svn/trunk/moeffju/php-msgfmt/msgfmt.php)
 * 
 * License: GPL v3 http://www.opensource.org/licenses/gpl-3.0.html
 */

/**
 * The main .po to .mo function
 */
function phpmo_convert($input, $output = false) {
	if ( !$output )
		$output = str_replace( '.po', '.mo', $input );

	$hash = phpmo_parse_po_file( $input );
	if ( $hash === false ) {
		return false;
	} else {
		phpmo_write_mo_file( $hash, $output );
		return true;
	}
}

function phpmo_clean_helper($x) {
	if (is_array($x)) {
		foreach ($x as $k => $v) {
			$x[$k] = phpmo_clean_helper($v);
		}
	} else {
		if ($x[0] == '"')
			$x = substr($x, 1, -1);
		$x = str_replace("\"\n\"", '', $x);
		$x = str_replace('$', '\\$', $x);
	}
	return $x;
}

/* Parse gettext .po files. */
/* @link http://www.gnu.org/software/gettext/manual/gettext.html#PO-Files */
function phpmo_parse_po_file($in) {
	// read .po file
	$fh = fopen($in, 'r');
	if ($fh === false) {
		// Could not open file resource
		return false;
	}

	// results array
	$hash = array ();
	// temporary array
	$temp = array ();
	// state
	$state = null;
	$fuzzy = false;

	// iterate over lines
	while(($line = fgets($fh, 65536)) !== false) {
		$line = trim($line);
		if ($line === '')
			continue;

		list ($key, $data) = preg_split('/\s/', $line, 2);
		
		switch ($key) {
			case '#,' : // flag...
				$fuzzy = in_array('fuzzy', preg_split('/,\s*/', $data));
			case '#' : // translator-comments
			case '#.' : // extracted-comments
			case '#:' : // reference...
			case '#|' : // msgid previous-untranslated-string
				// start a new entry
				if (sizeof($temp) && array_key_exists('msgid', $temp) && array_key_exists('msgstr', $temp)) {
					if (!$fuzzy)
						$hash[] = $temp;
					$temp = array ();
					$state = null;
					$fuzzy = false;
				}
				break;
			case 'msgctxt' :
				// context
			case 'msgid' :
				// untranslated-string
			case 'msgid_plural' :
				// untranslated-string-plural
				$state = $key;
				$temp[$state] = $data;
				break;
			case 'msgstr' :
				// translated-string
				$state = 'msgstr';
				$temp[$state][] = $data;
				break;
			default :
				if (strpos($key, 'msgstr[') !== FALSE) {
					// translated-string-case-n
					$state = 'msgstr';
					$temp[$state][] = $data;
				} else {
					// continued lines
					switch ($state) {
						case 'msgctxt' :
						case 'msgid' :
						case 'msgid_plural' :
							$temp[$state] .= "\n" . $line;
							break;
						case 'msgstr' :
							$temp[$state][sizeof($temp[$state]) - 1] .= "\n" . $line;
							break;
						default :
							// parse error
							fclose($fh);
							return FALSE;
					}
				}
				break;
		}
	}
	fclose($fh);
	
	// add final entry
	if ($state == 'msgstr')
		$hash[] = $temp;

	// Cleanup data, merge multiline entries, reindex hash for ksort
	$temp = $hash;
	$hash = array ();
	foreach ($temp as $entry) {
		foreach ($entry as & $v) {
			$v = phpmo_clean_helper($v);
			if ($v === FALSE) {
				// parse error
				return FALSE;
			}
		}
		$hash[$entry['msgid']] = $entry;
	}

	return $hash;
}

/* Write a GNU gettext style machine object. */
/* @link http://www.gnu.org/software/gettext/manual/gettext.html#MO-Files */
function phpmo_write_mo_file($hash, $out) {
	// sort by msgid
	ksort($hash, SORT_STRING);
	// our mo file data
	$mo = '';
	// header data
	$offsets = array ();
	$ids = '';
	$strings = '';

	foreach ($hash as $entry) {
		$id = $entry['msgid'];
		if (isset ($entry['msgid_plural']))
			$id .= "\x00" . $entry['msgid_plural'];
		// context is merged into id, separated by EOT (\x04)
		if (array_key_exists('msgctxt', $entry))
			$id = $entry['msgctxt'] . "\x04" . $id;
		// plural msgstrs are NUL-separated
		$str = implode("\x00", $entry['msgstr']);
		// keep track of offsets
		$offsets[] = array (
			strlen($ids
		), strlen($id), strlen($strings), strlen($str));
		// plural msgids are not stored (?)
		$ids .= $id . "\x00";
		$strings .= $str . "\x00";
	}

	// keys start after the header (7 words) + index tables ($#hash * 4 words)
	$key_start = 7 * 4 + sizeof($hash) * 4 * 4;
	// values start right after the keys
	$value_start = $key_start +strlen($ids);
	// first all key offsets, then all value offsets
	$key_offsets = array ();
	$value_offsets = array ();
	// calculate
	foreach ($offsets as $v) {
		list ($o1, $l1, $o2, $l2) = $v;
		$key_offsets[] = $l1;
		$key_offsets[] = $o1 + $key_start;
		$value_offsets[] = $l2;
		$value_offsets[] = $o2 + $value_start;
	}
	$offsets = array_merge($key_offsets, $value_offsets);

	// write header
	$mo .= pack('Iiiiiii', 0x950412de, // magic number
	0, // version
	sizeof($hash), // number of entries in the catalog
	7 * 4, // key index offset
	7 * 4 + sizeof($hash) * 8, // value index offset,
	0, // hashtable size (unused, thus 0)
	$key_start // hashtable offset
	);
	// offsets
	foreach ($offsets as $offset)
		$mo .= pack('i', $offset);
	// ids
	$mo .= $ids;
	// strings
	$mo .= $strings;

	file_put_contents($out, $mo);
}
/*
$test=new PoParser();
$resultado=$test->parseFile('general.po');
$guardar['msgid']="NOTICIAS";
$guardar['msgstr']="Son noticias y eventos muy molones que tiene que llenar un par de lineas para ser los mejores";
$guardar['reference'][]="Edit by CMS in " . date("Y-m-d H:i:s");
$test->setEntry('NOTICIAS',$guardar);
$test->writeFile('salida.po');
phpmo_convert( 'salida.po');

print_r($test->entries); */

?>