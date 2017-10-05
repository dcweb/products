<?php
namespace Dcms\Products\Http\Controllers;

use App\Http\Controllers\Controller;

use Dcms\Products\Models\Product;
use Dcms\Products\Models\Information;
use Dcms\Products\Models\Attachment;
use Dcms\Products\Models\Price;
use Dcms\Products\Models\Category;

use View;
use Input;
use Session;
use Validator;
use Redirect;
use DB;
use Datatables;
use Auth;

class InformationController extends Controller {

	public $informatationColumNames = array();
	public $informatationColumNamesDefaults = array(); // TO DO - the input on the information tab are array based
	public $productColumNames = array();
	public $productColumnNamesDefaults = array(); // e.g. checkboxes left blank will result in NULL database value, if this is not what you want, you can set e.g. array('checkbox_name'=>'0');
	public $extendgeneralTemplate = "";
	public $informationTemplate = "";

	public function __construct()
	{
		$this->informationColumNames = array( 'title'=>'information_name'
    											,'description_short'=>'information_description_short'
    											,'description'=>'information_description'
    											,'sort_id'=>'information_sort_id'
    											,'product_category_id'=>'information_category_id'
    											,'url_slug'=>'information_name'
    											,'url_path'=>'information_name'
    											);

		// this is custom DCM should be out of the vendor folder
		$this->informationColumNames = array_merge($this->informationColumNames,array(  ));
		// end of customisation

		$this->productColumNames = array('code'=>'code'
											,'eancode'=>'eancode'
											,'image'=>'image'
											,'volume'=>'volume'
											,'volume_unit_id'=>'volume_unit_id'
										);

		$this->extendgeneralTemplate = null;
		$this->informationTemplate = null;
	}


	/**
	 * get the data for DataTable JS plugin.
	 *
	 * @return Response
	 */
	public function getDatatable()
	{

		return Datatables::queryBuilder(	DB::connection("project")
													->table("products_information")
													->select(
																"products_information.id",
																"information_group_id",
																"title",
																(DB::connection("project")->raw("concat(\"<img src='/packages/dcms/core/images/flag-\", lcase(languages.country),\".png' > \") as regio"))
															)
													->leftJoin('languages','products_information.language_id', '=' , 'languages.id')
													->orderBy('information_group_id')
										)
			                        ->addColumn('edit', function($model){ return '<form method="POST" action="/admin/products/information/'.$model->id.'" accept-charset="UTF-8" class="pull-right"> <input name="_token" type="hidden" value="'.csrf_token().'"> <input name="_method" type="hidden" value="DELETE">
											<a class="btn btn-xs btn-default" href="/admin/products/information/'.(!is_null($model->information_group_id)?$model->information_group_id:"i-".$model->id).'/edit"><i class="fa fa-pencil"></i></a>

										</form>';})
			                        ->rawColumns(['regio','edit'])
			                        ->make(true) ;
	}


		/**
		 * get the data for DataTable JS plugin.
		 *
		 * @return Response
		 */
		public function getPlantRelationTable($plant_id = 0)
		{
						$query = DB::connection('project')
														->table('products_information as x')
														->select(
																(DB::connection("project")->raw('
																					information_group_id,
																					case when  locate(\'-SEP\',GROUP_CONCAT(title  order by  language_id SEPARATOR \'-SEP-\')) > 0 then substring(GROUP_CONCAT(title   order by  language_id SEPARATOR \'-SEP-\'), 1, (locate(\'-SEP\',GROUP_CONCAT(title  order by  language_id SEPARATOR \'-SEP-\')) - 1)) else title end as title,
																					case when (select count(*) from products_information_group_to_plants where products_information_group_to_plants.information_group_id = x.information_group_id and plant_id = "'.$plant_id.'") > 0 then 1 else 0 end as checked
																				')
															))
															->whereNotNull('x.information_group_id')
															->groupBy('x.information_group_id')
															->orderBy("language_id");

							return Datatables::queryBuilder($query)
											->addColumn('radio', function($model){return '<input type="checkbox" name="information_group_id[]" value="'.$model->information_group_id.'" '.($model->checked == 1?'checked="checked"':'').' id="chkbox_'.$model->information_group_id.'" > ';})
											->rawColumns(['radio'])
											->make(true);
		}


	/**
	 * get the data for DataTable JS plugin.
	 *
	 * @return Response
	 */
	public function getArticleRelationTable($article_id = 0)
	{

		$query = DB::connection('project')
										->table('products_information as x')
										->select(
												(DB::connection("project")->raw('
																	information_group_id,
																	case when  locate(\'-SEP\',GROUP_CONCAT(title  order by  language_id SEPARATOR \'-SEP-\')) > 0 then substring(GROUP_CONCAT(title   order by  language_id SEPARATOR \'-SEP-\'), 1, (locate(\'-SEP\',GROUP_CONCAT(title  order by  language_id SEPARATOR \'-SEP-\')) - 1)) else title end as title,
																	case when (select count(*) from articles_to_products_information_group where articles_to_products_information_group.information_group_id = x.information_group_id and article_id = "'.$article_id.'") > 0 then 1 else 0 end as checked
																')
											))
											->whereRaw('id IN (SELECT product_information_id FROM products_to_products_information WHERE product_id IN ( select id from products where online = 1 ) )')
											->whereNotNull('x.information_group_id')
											->groupBy('x.information_group_id')
											->orderBy("language_id");

		return Datatables::queryBuilder($query)
						   ->addColumn('radio', function($model){return '<input type="checkbox" name="information_group_id[]" value="'.$model->information_group_id.'" '.($model->checked == 1?'checked="checked"':'').' id="chkbox_'.$model->information_group_id.'" > '.$model->information_group_id;})

						   ->rawColumns(['radio'])
						   ->make(true) ;
	}


	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return View::make('dcmsproducts::information/index');
	}

	/**
	 * return the requested json data.
	 *
	 * @return json data
	 */
	public function json()
	{
		$term = Input::get("term");
		$language_id = intval(Input::get("language"));
		//the json autoload tool needs some
		$pData = Information::select('id','title as label', 'description')->where('title','LIKE','%'.$term.'%')->where('language_id','=',$language_id)->get()->toJson();
		return $pData;
	}

	public function getCountriesLanguages($returnType = "array")
	{
		//RFC 3066
		$oCountriesLanguages = DB::connection("project")->select('SELECT id, country, language FROM languages ');

		if ($returnType === "model")
		{
			return $oCountriesLanguages;
		}
		else
		{
			$aCountryLanguage = array();
			if(!is_null($oCountriesLanguages) && count($oCountriesLanguages)>0)
			{
				foreach($oCountriesLanguages as $M)
				{
					$aCountryLanguage[$M->id] = strtolower($M->language)."-".strtoupper($M->country);
				}
			}
			return $aCountryLanguage;
		}
	}

	public function getCountries($returnType = "array")
	{
		$oCountries =  DB::connection("project")->select('SELECT id, country_name FROM countries');
		if ($returnType === "model") {
			return $oCountries;
		}
		else
		{
			$aCountries = array();
			foreach($oCountries as $c)
			{
				$aCountries[$c->id] = $c->country_name;
			}
			return $aCountries;
		}
	}


	//return the model to fill the form
	public function getExtendedModel()
	{
		//do nothing sit back and make the extension hook up.
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return "disabled - since there must be some volume on an information";
	}


	protected function validateProductForm()
	{
		// validate
		// read more on validation at http://laravel.com/docs/validation
		$rules = array(
			'code'       => 'required',
		);
		$validator = Validator::make(Input::all(), $rules);

		// process the login
		if ($validator->fails()) {
			return Redirect::back()//to('admin/products/' . $id . '/edit')
				->withErrors($validator)
				->withInput();
		} else {
			return true;
		}
	}

	/**
	 * Save the ProductInformation to the given product (object)
	 * this can be filtered by givin a single languageid - the filter helps for returning the model of the saved Information
	 * by default you get back the last saved Information object

	 * @return Product Object
	 */
	protected function saveProductInformation()
	{
		$input = Input::get();

		$pInformation = null; //pInformation = object product Information
		$aSavedInformationIDs = array();

		if (isset($input["information_language_id"]) && count($input["information_language_id"])>0)
		{
			foreach($input["information_language_id"] as $i => $language_id)
			{
				if ( (strlen(trim($input[$this->informationColumNames['title']][$i]))>0) )
				{
					$pInformation = null; //reset when in a loop
					$newInformation = true;
					if(intval($input["information_id"][$i]) > 0 )$pInformation = Information::find($input["information_id"][$i]);
					if(!isset($pInformation) || is_null($pInformation) ) $pInformation = new Information; else $newInformation = false;

					$oldSortID = null;
					if ($newInformation == false && !is_null($pInformation->sort_id) && intval($pInformation->sort_id)>0) $oldSortID = intval($pInformation->sort_id);

					foreach($this->informationColumNames as $column => $inputname)
					{
						if(isset($input[$inputname][$i])&&$column <> 'new' ) $pInformation->$column = $input[$inputname][$i];
						elseif(isset($input[$inputname][$i])&&$column == 'new' ) $pInformation->$column = intval($input[$inputname][$i]);
						elseif($column == 'new')$pInformation->$column = 0;
						else $pInformation->$column = null;

						/*
							// TO DO
							//this should go array based input::has('some array name')
							if( !Input::has($inputname) && array_key_exists($inputname,$this->productColumnNamesDefaults))
							{
								$Product->$column = $this->productColumnNamesDefaults[$inputname];
							}
							else
							{
								$pInformation->$column = $input[$inputname][$i];
							}
						*/

					}

					$pInformation->language_id 	= $input["information_language_id"][$i];//$language_id;
					$pInformation->product_category_id = ($input[$this->informationColumNames['product_category_id']][$i]==0?NULL:$input[$this->informationColumNames['product_category_id']][$i]);
					$pInformation->url_slug 		= str_slug($input[$this->informationColumNames['url_slug']][$i]);
					$pInformation->url_path 		= str_slug($input[$this->informationColumNames['url_path']][$i]);
					$pInformation->save();

					$aSavedInformationIDs[] = $pInformation->id;

					$sort_incrementstatus = "0"; //the default
					if(is_null($oldSortID) || $oldSortID == 0)
					{
						//update all where sortid >= input::sortid
						$updateInformations = Information::where('language_id','=',$language_id)->where('sort_id','>=',$input[$this->informationColumNames['sort_id']][$i])->where('id','<>',$pInformation->id)->get(array('id','sort_id'));
						$sort_incrementstatus = "+1";
					}
					elseif ($oldSortID > $input[$this->informationColumNames['sort_id']][$i])
					{
						$updateInformations = Information::where('language_id','=',$language_id)->where('sort_id','>=',$input[$this->informationColumNames['sort_id']][$i])->where('sort_id','<',$oldSortID)->where('id','<>',$pInformation->id)->get(array('id','sort_id'));
						$sort_incrementstatus = "+1";
					}
					elseif ($oldSortID < $input[$this->informationColumNames['sort_id']][$i])
					{
						$updateInformations = Information::where('language_id','=',$language_id)->where('sort_id','>',$oldSortID)->where('sort_id','<=',$input[$this->informationColumNames['sort_id']][$i])->where('id','<>',$pInformation->id)->get(array('id','sort_id'));
						$sort_incrementstatus = "-1";
					}

					if ($sort_incrementstatus <> "0")
					{
						if (isset($updateInformations) && count($updateInformations)>0)
						{
							//$uInformation for object Information :: update the Information
							foreach($updateInformations as $uInformation)
							{
								if($sort_incrementstatus == "+1")
								{
									$uInformation->sort_id = intval($uInformation->sort_id) + 1;
									$uInformation->save();
								}
								elseif($sort_incrementstatus == "-1")
								{
									$uInformation->sort_id = intval($uInformation->sort_id) - 1 ;
									$uInformation->save();
								}
							}//end foreach($updateInformations as $Information)
						}//end 	if (count($updateInformations)>0)
					}//$sort_incrementstatus <> "0"
				}//end if($language_id ==$language_id
			}//foreach($input["information_language_id"] as $i => $language_id)
		}//if (isset($input["information_language_id"]) && count($input["information_language_id"])>0)

		// based on the Product->id we can find the attached info's
		// if the attached info's contain a information_group_id we can use this for the rest of the info's
		// otherwise create a new group_id
		$information_group_id = 0;

		if (count($aSavedInformationIDs)>0) {
				$Information = Information::whereIn('id',$aSavedInformationIDs)->get();

				foreach($Information as $I) {
						if(!is_null($I->information_group_id)) {
							$information_group_id = $I->information_group_id;
							break;
						}
				}

                if ($information_group_id<=0) {
					$igi = Information::orderBy('information_group_id','desc')->take(1)->get();
					$information_group_id =	intval($igi[0]->information_group_id)+1;
				}

				foreach($Information as $I) {
					$I = Information::find($I->id);
					$I->information_group_id = $information_group_id;
					$I->save();
				}
		}

        return $pInformation;
	}

	public function getInformation($information_group_id = null, $information_id = null )
	{
			if (!is_null($information_group_id)) {
				return DB::connection("project")->select('
														SELECT products_information.language_id ,sort_id, (select max(sort_id) from products_information as X  where X.language_id = products_information.language_id) as maxsort, language, language_name, country, products_information.title, products_information.description,products_information.description_short,   products_information.id as information_id, product_category_id
														FROM  products_information
														INNER JOIN languages on languages.id = products_information.language_id
														WHERE information_group_id = ?

														UNION
														SELECT languages.id as language_id , 0, (select max(sort_id) from products_information where language_id = languages.id), language, language_name, country, \'\' as title, \'\' as description, \'\' as description_short,  \'\' as information_id , \'\' , \'\'
														FROM languages
														WHERE id NOT IN (SELECT language_id FROM products_information WHERE  information_group_id = ?) ORDER BY 1  ', array($information_group_id,$information_group_id));
			} elseif(!is_null($information_id)) {
				return DB::connection("project")->select('
														SELECT products_information.language_id ,sort_id, (select max(sort_id) from products_information as X  where X.language_id = products_information.language_id) as maxsort, language, language_name, country, products_information.title, products_information.description, products_information.description_short,   products_information.id as information_id, product_category_id
														FROM  products_information
														INNER JOIN languages on languages.id = products_information.language_id
														WHERE products_information.id = ?

														UNION
														SELECT languages.id as language_id , 0, (select max(sort_id) from products_information where language_id = languages.id), language, language_name, country, \'\' as title, \'\' as description, \'\' as description_short,  \'\' as information_id , \'\' 
														FROM languages
														WHERE id NOT IN (SELECT language_id FROM products_information WHERE  id = ?) ORDER BY 1  ', array($information_id,$information_id));

			}
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//check if we get the id of the property information_group_id
		if(strstr($id,"i-")===false){
			$Information = $this->getInformation($id);//  Information::where("information_group_id","=",$id)->get();
		} else {
			$Information = $this->getInformation(null, str_replace("i-","",$id));
		}

		// show the edit form and pass the product
		return View::make('dcmsproducts::information/form')
									->with('languageinformation', $Information)
									->with('sortOptionValues',$this->getSortOptions($Information))
									->with('categoryOptionValues',Category::OptionValueTreeArray(false));
	}

	public function getSortOptions($model,$setExtra = 0)
	{
		$SortOptions = array();

		foreach($model as $M) {
			$increment = 0;
			if ($setExtra > 0) {
                $increment = $setExtra;
            }

            if(intval($M->information_id)<=0 && !is_null($M->maxsort)) {
                $increment = 1;
            }

			$maxSortID  = $M->maxsort;
			if (is_null($maxSortID) ) {
                $maxSortID = 1;
            }

			for($i = 1; $i<=($maxSortID+$increment); $i++) {
				$SortOptions[$M->language_id][$i] = $i;
			}
		}
		return $SortOptions;
	}

	/**
	 * copy the model
	 *
	 * @param  int  $product_id
	 * @param  int  $country_id //helps limiting the prices copy - we don't need the copied product in all countries..
	 * @return Response
	 */
	public function copy($product_id,$country_id = 0)
	{
	//	return Redirect::to('admin/products');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{

	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$this->saveProductInformation();
		Session::flash('message', 'Successfully updated Information!');

		return Redirect::to('admin/products/information');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Session::flash('message', 'Successfully deleted the Product!');
		return Redirect::to('admin/products');
	}
}
