<?php
/** ---------------------------------------------------------------------
 * app/lib/Plugins/ExternalExport/WLPlugBagIt.php : 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2018 Whirl-i-Gig
 *
 * For more information visit http://www.CollectiveAccess.org
 *
 * This program is free software; you may redistribute it and/or modify it under
 * the terms of the provided license as published by Whirl-i-Gig
 *
 * CollectiveAccess is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTIES whatsoever, including any implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This source code is free and modifiable under the terms of
 * GNU General Public License. (http://www.gnu.org/copyleft/gpl.html). See
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * @package CollectiveAccess
 * @subpackage ExternalExport
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
 *
 * ----------------------------------------------------------------------
 */

  /**
    *
    */
include_once(__CA_LIB_DIR__."/Plugins/IWLPlugExternalExportFormat.php");
include_once(__CA_LIB_DIR__."/Plugins/IWLPlugExternalExportTransport.php");
include_once(__CA_LIB_DIR__."/Plugins/ExternalExport/BaseExternalExportFormatPlugin.php");

class WLPlugBagIt Extends BaseExternalExportFormatPlugin Implements IWLPlugExternalExportFormat {
	# ------------------------------------------------------
	
	
	# ------------------------------------------------------
	/**
	 *
	 */
	public function __construct() {
		parent::__construct();
		$this->info['NAME'] = 'BagIt';
		$this->description = _t('Export data in BagIt format');
	}
	# ------------------------------------------------------
    /**
     *
     */
	public function register() {
	    return true;
	}
    # ------------------------------------------------------
    /**
     *
     */
	public function init() {
	
	}
    # ------------------------------------------------------
    /**
     *
     */
	public function cleanup() {
	    return true;
	}
    # ------------------------------------------------------
    /**
     *
     */
	public function getDescription() {
	    return _t('BagIt export');
	}
    # ------------------------------------------------------
    /**
     *
     */
	public function checkStatus() {
	    return true;
	}
    # ------------------------------------------------------
    /**
     * Generate BagIt archive for provided record subject to target settings
     *
     * @param BaseModel $t_instance
     * @param array $target_info
     * @param array $options
     *
     * @return string path to generated BagIt file
     */
    public function process($t_instance, $target_info, $options=null) {
        require_once(__CA_MODELS_DIR__.'/ca_data_exporters.php');
        require_once(__CA_BASE_DIR__.'/vendor/scholarslab/bagit/lib/bagit.php');
        
        $output_config = caGetOption('output', $target_info, null);
        $target_options = caGetOption('options', $output_config, null);
        $bag_info = is_array($target_options['bag-info-data']) ? $target_options['bag-info-data'] : [];
        $name = $t_instance->getWithTemplate(caGetOption('name', $output_config, null));
        $tmp_dir = caGetTempDirPath();
        $staging_dir = $tmp_dir."/".uniqid("ca_bagit");
        @mkdir($staging_dir);
        
        $bag = new BagIt("{$staging_dir}/{$name}", true, true, true, []);
        
        // bag data
        $content_mappings = caGetOption('content', $output_config, []);
        foreach($content_mappings as $path => $content_spec) {
            switch($content_spec['type']) {
                case 'export':
                    // TODO: verify exporter exists
                    $data = ca_data_exporters::exportRecord($content_spec['exporter'], $t_instance->getPrimaryKey(), []);
                    $bag->createFile($data, $path);
                    break;
                case 'file':
                    $instance_list = [$t_instance];
                    if ($relative_to = caGetOption('relativeTo', $content_spec, null)) {
                        // TODO: support children, parent, hierarchy
                        $instance_list = $t_instance->getRelatedItems($relative_to, ['returnAs' => 'modelInstances']);
                    } 
                    $restrict_to_types = caGetOption('restrictToTypes', $content_spec, null);
                    $restrict_to_mimetypes = caGetOption('restrictToMimeTypes', $content_spec, null);
                    
                    foreach($instance_list as $t) {
                    	if (is_array($restrict_to_types) && sizeof($restrict_to_types) && !in_array($t->getTypeCode(), $restrict_to_types)) { continue; }
                        foreach($content_spec['files'] as $get_spec => $export_filename_spec) {
                        	$pathless_spec = preg_replace('!\.path$!', '', $get_spec);
                            if (!preg_match("!\.path$!", $get_spec)) { $get_spec .= ".path"; }
                            
                            $filenames = $t->get("{$pathless_spec}.filename",['returnAsArray' => true, 'filterNonPrimaryRepresentations' => false]);
                            $mimetypes = $t->get("{$pathless_spec}.mimetype",['returnAsArray' => true, 'filterNonPrimaryRepresentations' => false]);
                           
                            $ids = $t->get("{$pathless_spec}.id", ['returnAsArray' => true, 'filterNonPrimaryRepresentations' => false]);
                            $files = $t->get($get_spec, ['returnAsArray' => true, 'filterNonPrimaryRepresentations' => false]);
                            
                            $seen_files = [];
                            foreach($files as $i => $f) {
                            	$m = $mimetypes[$i];
                            	if(is_array($restrict_to_mimetypes) && sizeof($restrict_to_mimetypes) && !sizeof(array_filter($restrict_to_mimetypes, function($v) use ($m) { return caCompareMimetypes($m, $v); }))) { continue; }
                            
                            	$extension = pathinfo($f, PATHINFO_EXTENSION);
                            	$original_basename = pathinfo($filenames[$i], PATHINFO_FILENAME);
                            	$basename = pathinfo($f, PATHINFO_FILENAME);
                            	
                            	$e = $export_filename = self::processExportFilename($export_filename_spec, [
                            		'extension' => $extension,
                            		'original_filename' => $original_basename ? "{$original_basename}.{$extension}" : "{$basename}.{$extension}", 'original_basename' => $original_basename ? $original_basename : $basename,
                            		'filename' => "{$basename}.{$extension}", "basename" => $basename, 'id' => $ids[$i] ? $ids[$i] : $i
                            	], $t);
                            	
                            	// Detect and rename duplicate file names
                            	$i = 1;
                            	while(isset($seen_files[$e]) && $seen_files[$e]) {
                            		$e = pathinfo($export_filename, PATHINFO_FILENAME)."-{$i}.{$extension}";
                            		$i++;
                            	}
                                $bag->addFile($f, $e);
                                $seen_files[$export_filename] = true;
                            }
                        }
                    }
                    break;
                default:
                    // noop
                    break;
            }
        }
        
        // bag info
        foreach($bag_info as $k => $v) {
            $bag->setBagInfoData($t_instance->getWithTemplate($k), $t_instance->getWithTemplate($v));
        }
        
        $bag->update();
        $bag->package("{$tmp_dir}/{$name}");
        
        return "{$tmp_dir}/{$name}.tgz";
    }
    # ------------------------------------------------------
}