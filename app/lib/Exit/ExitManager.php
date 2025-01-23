<?php
/** ---------------------------------------------------------------------
 * app/lib/Exit/ExitManager.php :
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2025 Whirl-i-Gig
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
 * @subpackage Exit
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
 *
 * ----------------------------------------------------------------------
 */
namespace Exit;

class ExitManager {
	# -------------------------------------------------------
	/**
	 *
	 */
	protected $format = 'XML';
	
	/**
	 *
	 */
	private static $s_data_buffer_size = 10;
	
	# -------------------------------------------------------
	/**
	 *
	 */
	public function __construct(?string $format='XML') {
		$this->setFormat($format);
	}
	# -------------------------------------------------------
	/**
	 * Returns list of tables to be exported
	 *
	 */
	public function getExportTableNames(?array $options=null) : array {
		$tables = caGetPrimaryTables(true, [
			'ca_relationship_types'
		], ['returnAllTables' => true]);
		
		return $tables;
	}
	# -------------------------------------------------------
	/**
	 *
	 */
	public function setFormat(string $format) : bool {
		$format = strtoupper($format);
		if(!in_array($format, ['XML'], true)) { return false; }
		$this->format = $format;
		return true;
	}
	# -------------------------------------------------------
	/**
	 *
	 */
	public function getFormat() : string {
		return $this->format;
	}
	# -------------------------------------------------------
	/**
	 * 
	 *
	 */
	public function export(string $directory, ?array $options=null) : bool {
		$tables = $this->getExportTableNames();
		
		foreach($tables as $table) {
			$this->exportTable($table, $directory, $options);
		}
		return true;
	}
	# -------------------------------------------------------
	/**
	 * 
	 *
	 */
	public function exportTable(string $table, string $directory, ?array $options=null) : bool {
		// Get rows
		$qr = $table::findAsSearchResult('*');
		$n = $qr->numHits();
		if($n === 0) { return true; }
		
		if(!($t = \Datamodel::getInstanceByTableName($table, true))) {
			throw new ApplicationException(_t('Invalid table: %1', $table));
		}
		
		// Generate table header
		$header = [
			'table' => $table,
			'name' => \Datamodel::getTableProperty($table, 'NAME_PLURAL'),
			'count' => $n,
			'types' => $t->getTypeList(),
			'exportDate' => date('c')
		];
		
		// Generate data dictionary
		$dictionary = [];
		if(!$t->isRelationship()) { 
			// @TODO: generate dictionary for sub-fields
			$dictionary = [
				'preferred_labels' => [
					'name' => 'Preferred labels',
					'description' => '',
					'type' => 'container',
					'canRepeat' => true
				],
				'nonpreferred_labels' => [
					'name' => 'Non-preferred labels',
					'description' => '',
					'type' => 'container',
					'canRepeat' => true
				]
			];
		}
			
		$pk = $t->primaryKey();
		$intrinsics = array_filter($t->getFields(), function($v) use ($pk) {
			if($v === "hier_{$pk}") { return false; }
			return !in_array($v, [
				'hier_left', 'hier_right', 
				'access_inherit_from_parent', 'acl_inherit_from_ca_collections', 'acl_inherit_from_parent', 
				'idno_sort', 'idno_sort_num', 'media_metadata', 'media_content',
				'deleted', 'submission_user_id', 'submission_group_id', 'submission_status_id',
				'submission_via_form', 'submission_session_id'
			]);
		});
		$intrinsic_info = $t->getFieldsArray();
		foreach($intrinsic_info as $f => $d) {
			if(!in_array($f, $intrinsics, true)) {
				unset($intrinsic_info[$f]);
				continue;
			}
			if ($f === 'locale_id') { $f = 'locale'; }
			$dictionary[$f] = [
				'name' => $d['LABEL'],
				'description' => $d['DESCRIPTION'],
				'type' => $this->_intrinsicTypeToDictionaryType($d['FIELD_TYPE']),
				'canRepeat' => false
			];
		}
		
		
		$md = \ca_metadata_elements::getElementsAsList(true, $table, null, true, true, true);
	
		foreach($md as $f => $d) {
			// @TODO: generate dictionary for container sub-elements
			$dictionary[$f] = [
				'name' => $d['display_label'],
				'description' => \ca_metadata_elements::getElementDescription($f),
				'type' => $this->_attributeTypeToDictionaryType($d['datatype']),
				'canRepeat' => true
			];
		}
		
		// Marshall data X rows at a time
		$data = [];
		
		$format = $this->getFormatWriter($this->getFormat(), $directory, $table, $options);
		$format->setHeader($header);
		$format->setDictionary($dictionary);
		while($qr->nextHit()) {
			// Intrinsics
			$acc = $this->_getIntrinsics($table, $intrinsic_info, $qr);
			
			// Labels
			if(!$t->isRelationship()) {
				$acc['preferred_labels'] = $this->_getLabels($table, true, $qr);
				$acc['nonpreferred_labels'] = $this->_getLabels($table, false, $qr);
			}
			
			// Attributes
			if(is_array($md)) {
				$acc = array_merge($acc, $this->_getAttributes($table, $md, $qr));
			}
			
			$acc['_guid'] = $qr->get("{$table}._guid");
			
			$data[] = $acc;
			
			if(sizeof($data) >= self::$s_data_buffer_size) {
				$format->process($data, ['primaryKey' => $t->primaryKey()]);
				$data = [];
				break;
			}
		}
		if(sizeof($data) > 0) {
			$format->process($data);
		}
		
		$format->write();
		
		return true;
	}
	# -------------------------------------------------------
	/**
	 *
	 */
	private function _getIntrinsics(string $table, array $intrinsic_info, \SearchResult $qr) : array {
		$acc = [];
		foreach($intrinsic_info as $f => $info) {
			switch($info['FIELD_TYPE']) {
				case FT_MEDIA:
					$path = $qr->get("{$table}.{$f}.original.path");
					$path = str_replace(__CA_BASE_DIR__, '', $path);
					$acc[$f] = $path;
					break;
				default:
					$acc[$f] = $qr->get("{$table}.{$f}");
					
					if($f === 'locale_id') {
						$acc['locale'] = \ca_locales::IDToCode($acc[$f]);
						unset($acc[$f]);
					} elseif(isset($info['LIST_CODE'])) {
						$acc[$f] = [
							[
								'_id' => $acc[$f],
								'_idno' => caGetListItemIdno($acc[$f])
							]
						];
					} elseif(isset($info['LIST'])) {
						$id = caGetListItemIDForValue($info['LIST'], $acc[$f]);
						print_R($l);
						$acc[$f] = [
							[
								'_id' => $id,
								'_idno' => caGetListItemIdno($id)
							]
						];
					}
					break;
			}
		}
		
		return $acc;
	}
	# -------------------------------------------------------
	/**
	 *
	 */
	private function _getLabels(string $table, bool $preferred, \SearchResult $qr) : array {
		$key = $preferred ? "preferred_labels" : "nonpreferred_labels";
		$l_acc = [];
		$pk = \Datamodel::primaryKey($table);
		if(is_array($labels = $qr->get("{$table}.{$key}", ['returnWithStructure' => true]))) {
			foreach($labels as $l) {
				$l_acc = array_merge($l_acc, $l);
			}
			foreach($l_acc as $i => $l) {
				unset($l_acc[$i]['name_sort']);
				unset($l_acc[$i]['item_type_id']);
				unset($l_acc[$i][$pk]);
				if($l_acc[$i]['locale_id']) { 
					$l_acc[$i]['locale'] = \ca_locales::IDToCode($l_acc[$i]['locale_id']); 
					unset($l_acc[$i]['locale_id']);
				}
			}
		}
		
		return $l_acc;
	}
	# -------------------------------------------------------
	/**
	 *
	 */
	private function _getAttributes(string $table, array $attributes, \SearchResult $qr) : array {
		$acc = [];
		foreach($attributes as $mdcode => $e) {
			$d = $qr->get("{$table}.{$mdcode}", ['returnWithStructure' => true]);
			
			// @TODO: add source info
			$d_acc = [];
			if(is_array($d)) {
				foreach($d as $locale_id => $values) {
					if((int)$e['datatype'] === __CA_ATTRIBUTE_VALUE_LIST__) {
						foreach($values as $vx) {
							$vx = [
								'_id' => $vx[$mdcode],
								'_idno' => caGetListItemIdno($vx[$mdcode])
							];
							$d_acc[] = array_merge([
								'_datatype' => $e['datatype'],
								'locale' => \ca_locales::IDToCode($locale_id)
							], $vx);
						}
					} else {
						foreach($values as $vx) {
							$d_acc[] = array_merge([
								'_datatype' => $e['datatype'],
								'locale' => \ca_locales::IDToCode($locale_id)
							], $vx);
						}
					}
				}
			}
			$acc[$mdcode] = $d_acc;
		}
		return $acc;
	}
	# -------------------------------------------------------
	/**
	 *
	 */
	private function getFormatWriter(string $format, string $directory, string $file, ?array $options) : \Exit\Formats\BaseExitFormat {
		$format = strtoupper($format);
		
		$p = "\\Exit\Formats\\{$format}";
		return new $p($directory, $file, $options);
	}
	# -------------------------------------------------------
	/**
	 *
	 */
	private function _intrinsicTypeToDictionaryType(int $type) : ?string {
		switch($type) {
			case FT_NUMBER:
				return 'number';
			case FT_TEXT:
				return 'text';			
			case FT_TIMESTAMP:
				return 'timestamp';			
			case FT_DATETIME:
				return 'unixtime';				
			case FT_HISTORIC_DATETIME:
				return 'historic_datetime';				
			case FT_DATERANGE:
				return 'unixtimerange';			
			case FT_HISTORIC_DATERANGE:
				return 'historic_datetime_range';				
			case FT_BIT:
				return 'bit';				
			case FT_FILE:
				return 'file';				
			case FT_MEDIA:
				return 'media';				
			case FT_PASSWORD:
				return 'text';				
			case FT_VARS:
				return 'json';				
			case FT_TIMECODE:
				return 'timecode';				
			case FT_DATE:
				return 'unixtime';				
			case FT_HISTORIC_DATE:
				return 'historic_datetime';				
			case FT_TIME:
				return 'time';			
			case FT_TIMERANGE:
				return 'timerange';				
		}
		return null;
	}
	# -------------------------------------------------------
	/**
	 *
	 */
	private function _attributeTypeToDictionaryType(int $type) : ?string {
		switch($type) {
			case __CA_ATTRIBUTE_VALUE_TEXT__:
				return 'text';
			case __CA_ATTRIBUTE_VALUE_CONTAINER__:
				return 'json';			
			case __CA_ATTRIBUTE_VALUE_CURRENCY__:
				return 'currency';			
			case __CA_ATTRIBUTE_VALUE_DATERANGE__:
				return 'historic_datetime_range';			
			case __CA_ATTRIBUTE_VALUE_FILE__:
				return 'file';			
			case __CA_ATTRIBUTE_VALUE_FLOORPLAN__:
				return 'json';	
			case __CA_ATTRIBUTE_VALUE_GEOCODE__:
				return 'geocode';	
			case __CA_ATTRIBUTE_VALUE_GEONAMES__:
				return 'geonames';
			case __CA_ATTRIBUTE_VALUE_INFORMATIONSERVICE__:
				return 'json';	
			case __CA_ATTRIBUTE_VALUE_LCSH__:
				return 'lcsh';	
			case __CA_ATTRIBUTE_VALUE_INTEGER__:
				return 'number';
			case __CA_ATTRIBUTE_VALUE_MEDIA__:
				return 'media';	
			case __CA_ATTRIBUTE_VALUE_NUMERIC__:
				return 'number';	
			case __CA_ATTRIBUTE_VALUE_TIMECODE__:
				return 'timecode';	
			case __CA_ATTRIBUTE_VALUE_LIST__:
				return 'listitem';
			case __CA_ATTRIBUTE_VALUE_WEIGHT__:
				return 'weight';	
			case __CA_ATTRIBUTE_VALUE_LENGTH__:
				return 'length';	
			case __CA_ATTRIBUTE_VALUE_URL__:
				return 'url';
			case __CA_ATTRIBUTE_VALUE_OBJECTREPRESENTATIONS__:		
			case __CA_ATTRIBUTE_VALUE_ENTITIES__:
			case __CA_ATTRIBUTE_VALUE_PLACES__:	
			case __CA_ATTRIBUTE_VALUE_OCCURRENCES__:	
			case __CA_ATTRIBUTE_VALUE_COLLECTIONS__:				
			case __CA_ATTRIBUTE_VALUE_STORAGELOCATIONS__:			
			case __CA_ATTRIBUTE_VALUE_LOANS__:		
			case __CA_ATTRIBUTE_VALUE_MOVEMENTS__:		
			case __CA_ATTRIBUTE_VALUE_OBJECTS__:	
			case __CA_ATTRIBUTE_VALUE_OBJECTLOTS__:	
				return 'reference';		
		}
		return null;
	}
	# -------------------------------------------------------
}

