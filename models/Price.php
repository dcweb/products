<?php

namespace Dcms\Products\Models;

use Dcms\Core\Models\EloquentDefaults;

class Price extends EloquentDefaults
{
	protected $connection = 'project';
	protected $table  = "products_price";
	protected $fillable = array('country_id', 'product_id');

	public function product()
    {
		return $this->hasOne('\Dcms\Products\Models\Product', 'id', 'product_id');
    }
}
