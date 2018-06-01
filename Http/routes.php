<?php



Route::group(['middleware' => ['web']], function () {

	Route::group( array("prefix" => "admin", "as"=>"admin."), function() {

    	Route::group(['middleware' => 'auth:dcms'], function() {

    		//PRODUCTS
    		Route::group( array("prefix" => "products", "as"=>"products."), function() {

    			//CATEGORIES
    			Route::group( array("prefix" => "categories","as"=>"categories."), function() {
    				Route::get("{id}/copy", array("as"=>"{id}.copy", "uses" => "CategoryController@copy"));
    				Route::any("api/table", array("as"=>"api.table", "uses" => "CategoryController@getDatatable"));
    				Route::any("rebuildTree", array("as"=>"rebuildTree", "uses" => "CategoryController@rebuildTree"));
    			});
    			Route::resource("categories","CategoryController");

    			//INFORMATION
    			Route::group( array("prefix" => "information","as"=>"information."), function() {
    				Route::any("api/table", array("as"=>"api.table", "uses" => "InformationController@getDatatable"));
    				Route::any("api/articlerelationtable/{article_id?}", array("as"=>"api.articlerelationtable", "uses" => "InformationController@getArticleRelationTable"));
    				Route::any("api/plantrelationtable/{plant_id?}", array("as"=>"api.plantrelationtable", "uses" => "InformationController@getPlantRelationTable"));
    			});
    			Route::resource("information","InformationController");

    			//API
    			Route::group( array("prefix" => "api","as"=>"api."), function() {
    				Route::any("table", array("as"=>"table", "uses" => "ProductController@getDatatable"));
    				Route::any("tablerow", array("as"=>"tablerow", "uses" => "ProductController@getTableRow"));
    				Route::get("pim", array("as"=>"pim", "uses" => "ProductController@json"));
    				Route::get("xls", array("as"=>"xls", "uses" => "ProductController@json"));
    				Route::any("relation/table/{article_id?}", array("as"=>"relation.table", "uses" => "ProductController@getRelationDatatable"));
    			});
    			Route::get("{id}/copy/{country_id?}", array("as"=>"{id}.copy.{country_id}", "uses" => "ProductController@copy"));
    		});
    		Route::resource("products","ProductController");
    });
  });
});



 ?>
