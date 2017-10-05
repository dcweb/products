<?php

namespace Dcms\Products\Models;

use Dcms\Core\Models\EloquentDefaults;

class Information extends EloquentDefaults
{
	protected $connection = 'project';
	protected $table  = "products_information";
	protected $fillable = array('language_id', 'title', 'description');

	protected function setPrimaryKey($key)
	{
		$this->primaryKey = $key;
	}

	public function products()
	{
		return $this->belongsToMany('\Dcms\Products\Models\Product', 'products_to_products_information', 'product_information_id', 'product_id')->withTimestamps();
	}

	public function productcategory()
	{
		return $this->belongsTo('\Dcms\Products\Models\Category', 'product_category_id', 'id');
	}

	public function otherlanguages()
	{
		return $this->hasMany('\Dcms\Products\Models\Information', 'information_group_id', 'information_group_id')->whereNotNull('information_group_id');
	}

	public function pages()
	{
		return $this->belongsToMany('\Dcms\Pages\Models\Page', 'pages_to_products_information_group', 'information_id', 'page_id')->withTimestamps();
	}
}
