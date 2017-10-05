<?php

namespace Dcms\Products\Models;

use Dcms\Core\Models\EloquentDefaults;

use Auth;

class Product extends EloquentDefaults
{
	protected $connection = 'project';
  	protected $table  = "products";

	public function information()
    {
        return $this->belongsToMany('\Dcms\Products\Models\Information', 'products_to_products_information', 'product_id', 'product_information_id')->withTimestamps();
    }

	public function price()
	{
		return $this->belongsTo('\Dcms\Products\Models\Price','product_id');
	}
}
