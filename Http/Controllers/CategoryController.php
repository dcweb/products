<?php
namespace Dcms\Products\Http\Controllers;

use App\Http\Controllers\Controller;

use Dcms\Products\Models\Category;

use View;
use Input;
use Session;
use Validator;
use Redirect;
use DB;
use Datatables;
use Auth;
use Config;

class CategoryController extends Controller {

	public static function QueryTree()
	{
		$tree = DB::connection('project')
															->table('products_categories_language as node')
															->select(
																				(DB::connection("project")->raw("CONCAT( REPEAT( '-', node.depth ), node.title) AS category")),
																				"node.id",
																				"node.parent_id",
																				"node.language_id",
																				"node.depth",
																				(DB::connection("project")->raw('Concat("<img src=\'/packages/dcms/core/images/flag-",lcase(country),".png\' >") as regio'))
																			)
															->leftJoin('languages','node.language_id','=','languages.id')
															->orderBy('node.lft')
														->get();
		return $tree;
	}

	public static function CategoryDropdown($models = null,$selected_id = null, $enableNull = true, $name="parent_id", $key = "id",$value="category")
	{
		$dropdown = "empty set";
		if(!is_null($models) && count($models)>0)
		{
			$dropdown = '<select name="'.$name.'" class="form-control" id="parent_id">'."\r\n";

			if($enableNull == true)	$dropdown .= '<option value="">None</option>'; //epty value will result in NULL database value;

			foreach($models as $model)
			{
				$selected = "";
				if(!is_null($selected_id) && $selected_id == $model->$key) $selected = "selected";

				//altering these tag properties can affect the form (jQuery)
				$dropdown .= '<option '.$selected.' value="'.$model->$key.'" class="'.$name.' language_id'.$model->language_id.' parent-'.(is_null($model->parent_id)?0:$model->parent_id).' depth-'.$model->depth.'">'.$model->$value.'</option>'."\r\n";
			}
			$dropdown .= '</select>'."\r\n"."\r\n";
		}
		return $dropdown;
	}


	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{

		//Category::rebuild();

		// load the view and pass the categories
		return View::make('dcmsproducts::categories/index');
	}


	public function getDatatable()
	{

	        return Datatables::queryBuilder(DB::connection('project')
																->table('products_categories_language as node')
																->select(
																					(DB::connection("project")->raw("CONCAT( REPEAT( '-', node.depth ), node.title) AS category")),
																					"node.id",
																					(DB::connection("project")->raw('Concat("<img src=\'/packages/dcms/core/images/flag-",lcase(country),".png\' >") as country'))
																				)
																->leftJoin('languages','node.language_id','=','languages.id')
																->orderBy('node.lft')
											)
	                        ->addColumn('edit',function($model){
																			return '<form method="POST" action="/admin/products/categories/'.$model->id.'" accept-charset="UTF-8" class="pull-right"> <input name="_token" type="hidden" value="'.csrf_token().'"> <input name="_method" type="hidden" value="DELETE">
																									<a class="btn btn-xs btn-default" href="/admin/products/categories/'.$model->id.'/edit"><i class="fa fa-pencil"></i></a>
																									<button class="btn btn-xs btn-default" type="submit" value="Delete this product category" onclick="if(!confirm(\'Are you sure to delete this item?\')){return false;};"><i class="fa fa-trash-o"></i></button>
																						</form>';})
															->rawColumns(['country','edit'])
	                        ->make(true) ;
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$oLanguages = DB::connection("project")->table("languages")->select((DB::connection("project")->raw(" '' as title, '' as description")), "id","id as thelanguage_id", "language","country","language_name")->get();

		// load the create form (app/views/categories/create.blade.php)
		return View::make('dcmsproducts::categories/form')
			->with('oLanguages',$oLanguages)
			->with('categoryOptionValues',$this->CategoryDropdown($this->QueryTree()));
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$rules = array('title'=>'required'
									,'language_id'=>'required');

		$validator = Validator::make(Input::all(), $rules);

		// process the login
		if ($validator->fails()) {
			return Redirect::to('admin/products/categories/create')
				->withErrors($validator)
				->withInput();
		} else {

			$theParent = null;
			//find root ellement
			if(Input::has("parent_id") && intval(Input::get("parent_id"))>0 ){
				$theParent = Category::find(Input::get("parent_id"));
			}

			if(is_null($theParent)){
				//$theParent = Category::where('language_id','=',Input::get("language_id"))->where('depth','=','0')->first();
			}

			$node = new Category;
			$node->language_id = Input::get('language_id');
			$node->title = Input::get('title');
			$node->url_slug 		= str_slug(Input::get('title'));
			$node->url_path 		= str_slug(Input::get('title'));
			$node->save();

			if(is_null($theParent)){
				$node->makeRoot();
			} else {
				$node->makeChildOf($theParent);
			}

			if(Input::has('nexttosiblingid') && intval(Input::get('nexttosiblingid'))>0)	$node->moveToLeftOf(intval(Input::get('nexttosiblingid')));
		}

		Session::flash('message', 'Successfully created category!');
		return Redirect::to('admin/products/categories');
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//	get the category
		$category = Category::find($id);

	 	$oLanguages = DB::connection("project")->select('SELECT languages.id as thelanguage_id, language, country, language_name, products_categories_language.*
			FROM  products_categories_language
			LEFT JOIN languages on products_categories_language.language_id = languages.id
			WHERE  languages.id is not null AND  products_categories_language.id= ?
			UNION
			SELECT languages.id , language, country, language_name, \'\' , \'\' , languages.id , \'\' , \'\' , \'\' , \'\' , \'\' , \'\' , \'\' , \'\' , \'\', \'\' , \'\' , \'\'
			FROM languages
			WHERE id NOT IN (SELECT language_id FROM products_categories_language WHERE id = ?) ORDER BY 1
			', array($id,$id));

		return View::make('dcmsproducts::categories/form')
			->with('category', $category)
			->with('oLanguages',$oLanguages)
			->with('categoryOptionValues',$this->CategoryDropdown($this->QueryTree(),$category->parent_id));
	}


	/**
	 * copy the model
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function copy($id)
	{
		//NOT SUPPORTED
		/*

			$Newcategory->admin =  Auth::guard('dcms')->user()->username;
			$Newcategory->save();
	*/
		return Redirect::to('admin/products/categories');
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		// validate
		$rules = array('title'=>'required','language_id'=>'required');
		$validator = Validator::make(Input::all(), $rules);

		// process the login
		if ($validator->fails()) {
			return Redirect::to('admin/products/categories/' . $id . '/edit')
				->withErrors($validator)
				->withInput();
		} else {

				$node = Category::find($id);
				$node->language_id = Input::get('language_id');
				$node->title = Input::get('title');
				$node->url_slug 		= str_slug(Input::get('title'));
				$node->url_path 		= str_slug(Input::get('title'));
				$node->save();

				$setRoot = false;
				$theParent = null;
				$moveParent = true;

				if( intval(Input::get('parent_id')) > 0 &&  Input::get('parent_id') <> $node->parent_id) {
					//move to a new parentid
					$moveParent = true;
					$theParent = Category::find(Input::get("parent_id"));
				}elseif(intval(Input::get('parent_id')) <= 0  &&  Input::get('parent_id') <> $node->parent_id) {
					//move to a ROOT of the same, or other language
					$moveParent = true;
					//$theParent = Category::where('language_id','=',Input::get("language_id"))->where('depth','=','0')->first();
					if(is_null($theParent))$setRoot = true;
				}else{
					//we stay in the same parent
					$moveParent = false;
				}
				if($setRoot == true) $node->makeRoot();
				elseif(!is_null($theParent)) $node->makeChildOf($theParent);


				if($moveParent == false && Input::has('nexttosiblingid') && intval(Input::get('nexttosiblingid'))>0 && Input::get("oldsort") < Input::get("sort")) $node->moveToRightOf(intval(Input::get('nexttosiblingid')));
				elseif($moveParent == false && Input::has('nexttosiblingid') && intval(Input::get('nexttosiblingid'))>0  && Input::get("oldsort") > Input::get("sort"))	$node->moveToLeftOf(intval(Input::get('nexttosiblingid')));
				elseif($moveParent == false && Input::get("oldsort") < Input::get("sort") ) $node->makeLastChildOf($node->parent_id);
				elseif($moveParent == true && Input::has('nexttosiblingid') && intval(Input::get('nexttosiblingid'))>0 ) $node->moveToRightOf(intval(Input::get('nexttosiblingid')));

			// redirect
			Session::flash('message', 'Successfully updated category!');
			return Redirect::to('admin/products/categories');
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		// delete
		$category = Category::find($id);
		$category->delete();

		// redirect
		Session::flash('message', 'Successfully deleted the category!');
		return Redirect::to('admin/products/categories');
	}
}
