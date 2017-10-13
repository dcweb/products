<?php

namespace Dcms\Products;
/**
*
* @author web <web@groupdc.be>
*/
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class ProductsServiceprovider extends ServiceProvider{
 /**
  * Indicates if loading of the provider is deferred.
  *
  * @var bool
  */
 protected $defer = false;

 public function boot()
 {

   $this->loadViewsFrom(realpath(__DIR__.'/resources/views'), 'dcmsproducts');
   $this->setupRoutes($this->app->router);
   // this  for conig
   $this->publishes([
    //  __DIR__.'/public/assets' => public_path('packages/dcms/products'),
      __DIR__.'/config/dcms_sidebar.php' => config_path('dcms/products/dcms_sidebar.php'),
   ]);

   if(!is_null(config('dcms.products'))){
       $this->app['config']['dcms_sidebar'] =  array_replace_recursive($this->app["config"]["dcms_sidebar"], config('dcms.products.dcms_sidebar'));
   }
 }
 /**
  * Define the routes for the application.
  *
  * @param  \Illuminate\Routing\Router  $router
  * @return void
  */
 public function setupRoutes(Router $router)
 {
   $router->group(['namespace' => 'Dcms\Products\Http\Controllers'], function($router)
   {
     require __DIR__.'/Http/routes.php';
   });
 }

 public function register()
 {
   //$this->registerArticles();
 }

 private function registerArticles()
 {
     $this->app->bind('products',function($app){
            return new Products($app);
        });
 }

}

 ?>
