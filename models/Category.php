<?php

namespace Dcms\Products\Models;

use DB;

use \Baum\Node as Node ;

class Category extends Node
{
	protected $connection = 'project';
	protected $table  = "products_categories_language";
	protected $fillable = array('language_id', 'title');

	public static function OptionValueTreeArray($enableEmpty = false, $columns = array('*') , $columnMapper = array("id","title","language_id"))
	{
		$PageObj = DB::connection('project')
						->table('products_categories_language as node')
						->select(
									(DB::connection("project")->raw("CONCAT( REPEAT( '-', node.depth ), node.title) AS title")),
									"node.id",
									"node.parent_id",
									"node.language_id",
									"node.depth",
									(DB::connection("project")->raw('Concat("<img src=\'/packages/dcms/core/images/flag-",lcase(country),".png\' >") as regio'))
								)
						->leftJoin('languages','node.language_id','=','languages.id')
						->orderBy('node.lft')
					    ->get();

		$OptionValueArray = array();

		if (count($PageObj)>0) {
			foreach($PageObj as $lang) {
					if (array_key_exists($lang->language_id, $OptionValueArray)== false ){
                        $OptionValueArray[$lang->language_id] = array();
                    }

					//we  make an array with array[languageid][maincategoryid] = translated category;
					$columnMapper_zero = $columnMapper[0];
					$columnMapper_one = $columnMapper[1];
					$OptionValueArray[$lang->language_id][$lang->$columnMapper_zero]=/*str_repeat('-',$lang->level).' '.*/$lang->$columnMapper_one;
			}
		} elseif($enableEmpty === true) {
			$Languages = Language::all();

			foreach($Languages as $Lang) {
				$OptionValueArray[$Lang->id][1] = "- ROOT -";
			}
		}
		return $OptionValueArray;
	}
}
