<?php

namespace Dcms\Products\Models;

use Dcms\Core\Models\EloquentDefaults;

class Attachment extends EloquentDefaults
{
	protected $connection = 'project';
	protected $table  = "products_attachments";
	protected $fillable = array('language_id', 'product_id');

	public function product()
    {
        return $this->hasOne('Dcms\Products\Models\Product', 'id', 'product_id');
    }
}
