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

class ProductController extends Controller
{
    public $informatationColumNames = array();
    public $informatationColumNamesDefaults = array(); // TO DO - the input on the information tab are array based
    public $productColumNames = array();
    public $productColumnNamesDefaults = array(); // e.g. checkboxes left blank will result in NULL database value, if this is not what you want, you can set e.g. array('checkbox_name'=>'0');
    public $extendgeneralTemplate = "";
    public $informationTemplate = "";
    public $information_group_id = "";

    public function __construct()
    {
        $this->informationColumNames = array('title' => 'information_name'
        , 'description' => 'information_description'
        , 'description_short' => 'information_description_short'
        , 'sort_id' => 'information_sort_id'
        , 'product_category_id' => 'information_category_id'
        , 'url_slug' => 'information_name'
        , 'url_path' => 'information_name',
        );

        $this->productColumNames = array('online' => 'online'
        , 'code' => 'code'
        , 'eancode' => 'eancode'
        , 'image' => 'image'
        , 'volume' => 'volume'
        , 'volume_unit_id' => 'volume_unit_id'
        , 'new' => 'new'
        , 'discontinued' => 'discontinued',
        );

        $this->extendgeneralTemplate = null;
        $this->informationTemplate = null;
        $this->information_group_id = null;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return View::make('dcmsproducts::products/index');
    }

    /**
     * $mDefaults contains an array of Price-Models
     *
     * @return the row to inject prices
     */
    public function getPriceRow($mDefaults = array(), $forceEmpty = false)
    {
        $rowstring = "";

        $openbody = true;
        $closebody = true;
        if ($forceEmpty === true && empty($mDefaults) === true) {
            $openbody = false;
            $closebody = false;
            $mDefaults[] = (object)array();
        }

        foreach ($mDefaults as $Price) {
            $country_option = "";

            foreach ($this->getCountries("array") as $countryid => $country) {
                $selected = "";
                if (isset($Price->country_id) && $countryid == $Price->country_id) {
                    $selected = "selected";
                }

                $country_option .= '<option value="' . $countryid . '" ' . $selected . '>' . $country . '</option>';
            }

            $tax_option = "";
            foreach ($this->getTaxClasses("array") as $taxid => $tax) {
                $selected = "";
                if (isset($Price->price_tax_id) && $taxid == $Price->price_tax_id) {
                    $selected = "selected";
                }

                $tax_option .= '<option value="' . $taxid . '" ' . $selected . ' >' . $tax . '</option>';
            }

            if ($openbody === true) {
                $rowstring .= '<tbody >';
            }

            //------------------------------------------------------------------------
            // 							TEMPLATE FOR THE PRICE ROW
            // 		the {INDEX} tag will be replaced in the form.blade, and this script to the attachment database id - or some text to identify its new
            //------------------------------------------------------------------------
            $rowstring .= ' <tr>
								<td>
									<select id="price-country-id[{INDEX}]" class="form-control" name="price-country-id[{INDEX}]">
										' . $country_option . '
									</select>
								</td>
								<td>
									<input id="price[{INDEX}]" name="price[{INDEX}]" class="form-control" type="text" value="' . (isset($Price->price) ? $Price->price : "") . '">
								</td>
								<td>
									<input id="price_purchase[{INDEX}]" name="price_purchase[{INDEX}]" class="form-control" type="text" value="' . (isset($Price->price_purchase) ? $Price->price_purchase : "") . '">
								</td>
								<td>
									<select id="price_valuta_id[{INDEX}]" name="price_valuta_id[{INDEX}]" class="form-control">
										<option value="1">euro</option>
									</select>
								</td>
								<td>
									<select id="price_tax_id[{INDEX}]" name="price_tax_id[{INDEX}]" class="form-control">
										' . $tax_option . '
									</select>
								</td>
								<td><a class="btn btn-default pull-right delete-table-row" href=""><i class="fa fa-trash-o"></i></a></td>
							</tr>';

            if (isset($Price->id) && intval($Price->id) > 0) {
                $rowstring = str_replace("{INDEX}", $Price->id, $rowstring);
            }
            $openbody = false;
        }

        if ($closebody === true) {
            $rowstring .= '</tbody>';
        }

        return $rowstring;
    }

    /**
     * $mDefaults contains an array of Price-Models
     *
     * @return the row to inject prices
     */
    public function getAttachmentsRow($mDefaults = array(), $forceEmpty = false, $languageToArray = false, $selectedLanguage_id = null)
    {
        $aRowString = array();
        $rowstring = "";

        $encloseBody = true;

        if ($forceEmpty === true && empty($mDefaults) === true) {
            $encloseBody = false;
            $mDefaults[] = (object)array();
        }

        foreach ($mDefaults as $Attachment) {
            $countrieslangauge_option = "";
            foreach ($this->getCountriesLanguages("array") as $language_id => $language_COUNTRY) {
                $selected = "";
                if (isset($Attachment->language_id) && $language_id == $Attachment->language_id) {
                    $selected = "selected";
                } elseif (isset($selectedLanguage_id) && !is_null($selectedLanguage_id) && $language_id == $selectedLanguage_id) {
                    $selected = "selected";
                }

                $countrieslangauge_option .= '<option value="' . $language_id . '" ' . $selected . ' class="optie ' . $selectedLanguage_id . '">' . $language_COUNTRY . '</option>';
            }

            $setTheLanguageID = null;
            if (isset($Attachment->language_id)) {
                $setTheLanguageID = $Attachment->language_id;
            } elseif (isset($selectedLanguage_id) && !is_null($selectedLanguage_id)) {
                $setTheLanguageID = $selectedLanguage_id;
            }

            //------------------------------------------------------------------------
            // 							TEMPLATE FOR THE PRICE ROW
            // 		the {INDEX} tag will be replaced in the form.blade, and this script to the attachment database id - or some text to identify its new
            //------------------------------------------------------------------------
            $rowstring .= ' <tr>
								<td>
									<div class="input-group">
										<input type="hidden" id="attachment-language-id[{INDEX}]" class="form-control" name="attachment-language-id[{INDEX}]" value="' . $setTheLanguageID . '">
										<input type="text" name="attachment-file[{INDEX}]" value="' . (isset($Attachment->file) ? $Attachment->file : "") . '" class="form-control" id="attachmentfile{INDEX}" />
										<span class="input-group-btn">
											<button class="btn btn-primary browse-server-files" id="browse_attachmentfile{INDEX}" type="button">Browse Files</button>
										</span>
									</div>
								</td>
								</td>
								<td>
									<input id="attachmentfilename[{INDEX}]" name="attachment-filename[{INDEX}]" class="form-control" type="text" value="' . (isset($Attachment->filename) ? $Attachment->filename : "") . '">
								</td>
								<td><a class="btn btn-default pull-right delete-table-row" href=""><i class="fa fa-trash-o"></i></a></td>
							</tr>';

            if (isset($Attachment->id) && intval($Attachment->id) > 0) {
                $rowstring = str_replace("{INDEX}", $Attachment->id, $rowstring);
            }

            if ($languageToArray) {
                if (!isset($aRowString[$Attachment->language_id])) {
                    $aRowString[$Attachment->language_id] = "";
                }

                $aRowString[$Attachment->language_id] .= $rowstring;
                $rowstring = "";
            }

        }
        if ($encloseBody === true) {
            $rowstring = '<tbody>' . $rowstring . '</tbody>';
        }

        if ($languageToArray == true && $encloseBody == true) {
            foreach ($aRowString as $langid => $data) {
                $aRowString[$langid] = '<tbody>' . $data . '</tbody>';
            }
        }

        if ($languageToArray == true) {
            return $aRowString;
        } else {
            return $rowstring;
        }
    }


    public function getTableRow()
    {
        if (Input::get("data") === "price") {
            return $this->getPriceRow(null, true);
        } elseif (Input::get("data") === "attachments") {
            $language_id = null;
            if (Input::has('language_id')) {
                $language_id = Input::get('language_id');
            }
            return $this->getAttachmentsRow(null, true, false, $language_id);
        }
    }

    /**
     * return the requested json data.
     *
     * @return json data
     */
    public function json()
    {
        $term = Input::get("term");

        //the json autoload tool needs some
        if (Input::has("language")) {
            $language_id = intval(Input::get("language"));
            $pData = Information::select('id', 'title as label', 'description', 'description_short')->where('title', 'LIKE', '%' . $term . '%')->where('language_id', '=', $language_id)->get()->toJson();
        } else {
            $language_id = intval(Input::get("language"));
            $pData = Information::select('id', 'title as label', 'description', 'description_short')->where('title', 'LIKE', '%' . $term . '%')->get()->toJson();
        }
        return $pData;
    }

    /**
     * get the data for DataTable JS plugin.
     *
     * @return Response
     */
    public function getDatatable()
    {
        return Datatables::queryBuilder(
            DB::connection("project")->table("products")->select(
                "products.id",
                "products.online",
                "products.code",
                "products.eancode",
                "products_to_products_information.product_information_id as info_id",
                "title",
                DB::raw('CONCAT(volume, " ", volume_unit) AS volume'),
                (DB::connection("project")->raw("concat(\"<img src='/packages/dcms/core/images/flag-\", lcase(selling.country),\".png' > \") as country")),
                "selling.id as country_id"
            )
                ->leftJoin('products_to_products_information', 'products.id', '=', 'products_to_products_information.product_id')
                ->leftJoin('products_information', 'products_information.id', '=', 'products_to_products_information.product_information_id')
                ->leftJoin('products_volume_units', 'products.volume_unit_id', '=', 'products_volume_units.id')
                ->leftJoin('languages', 'products_information.language_id', '=', 'languages.id')
                ->leftJoin('products_price', function ($join) {
                    $join->on('products_price.product_id', '=', 'products.id');
                    $join->on('products_price.country_id', '=', 'languages.country_id');
                })
                ->leftJoin('countries as selling', 'products_price.country_id', '=', 'selling.id')
                ->leftJoin('countries as settings', 'languages.country_id', '=', 'settings.id')
        )
            ->addColumn('edit', function ($model) {
                return '<form method="POST" action="/admin/products/' . (isset($model->info_id) ? $model->info_id : $model->id) . '" accept-charset="UTF-8" class="pull-right"> <input name="_token" type="hidden" value="' . csrf_token() . '"> <input name="_method" type="hidden" value="DELETE">
                						<input type="hidden" name="table" value="' . (isset($model->info_id) ? "information" : "product") . '"/>
                						<input type="hidden" name="product_id" value="' . $model->id . '"/>
        								<a class="btn btn-xs btn-default" href="/admin/products/' . $model->id . '/edit"><i class="fa fa-pencil"></i></a>
        								<!--<a class="btn btn-xs btn-default" href="/admin/products/' . $model->id . '/copy/' . $model->country_id . '"><i class="fa fa-copy"></i></a>-->
        								<button class="btn btn-xs btn-default" type="submit" value="Delete this product category" onclick="if(!confirm(\'Are you sure to delete this item?\')){return false;};"><i class="fa fa-trash-o"></i></button>
			                         </form>';
            })
            ->rawColumns(['country', 'edit'])
            ->make(true);
    }

    //return the model/object (id, country, language) or an array e.g. array(language_id => language-COUNTRY)
    public function getCountriesLanguages($returnType = "array")
    {
        //RFC 3066
        $oCountriesLanguages = DB::connection("project")->select('SELECT id, country, language FROM languages ');

        if ($returnType === "model") {
            return $oCountriesLanguages;
        } else {
            $aCountryLanguage = array();

            if (!is_null($oCountriesLanguages) && count($oCountriesLanguages) > 0) {
                foreach ($oCountriesLanguages as $M) {
                    $aCountryLanguage[$M->id] = strtolower($M->language) . "-" . strtoupper($M->country);
                }
            }
            return $aCountryLanguage;
        }
    }

    //return the model/object or an array e.g. array(counrty_id => counryname)
    public function getCountries($returnType = "array")
    {
        $oCountries = DB::connection("project")->select('SELECT id, country_name FROM countries');
        if ($returnType === "model") {
            return $oCountries;
        } else {
            $aCountries = array();

            foreach ($oCountries as $c) {
                $aCountries[$c->id] = $c->country_name;
            }
            return $aCountries;
        }
    }

    public function getTaxClasses($returnType = "array")
    {
        //volumeclasses
        //there is no model for VOLUMES so no eloquent querying here
        $oTaxClasses = DB::connection("project")->select('SELECT id, tax as tax FROM products_price_tax');

        if ($returnType === "model") {
            return $oTaxClasses;
        } else {
            //there was no support for the lists() method
            $aTaxClasses = array();
            foreach ($oTaxClasses as $v) {
                $aTaxClasses[$v->id] = $v->tax;
            }
            return $aTaxClasses;
        }
    }

    public function getVolumesClasses($returnType = "array")
    {
        //volumeclasses
        //there is no model for VOLUMES so no eloquent querying here
        $oVolumeClasses = DB::connection("project")->select('SELECT id, volume_unit as volume FROM products_volume_units ORDER BY 2');

        if ($returnType === "model") {
            return $oVolumeClasses;
        } else {
            //there was no support for the lists() method
            $aVolumesClasses = array();

            foreach ($oVolumeClasses as $v) {
                $aVolumesClasses[$v->id] = $v->volume;
            }
            return $aVolumesClasses;
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
        $languageinformation = $this->getInformation();

        return View::make('dcmsproducts::products/form')
            ->with('languageinformation', $languageinformation)
            ->with('extendgeneralTemplate', array('template' => $this->extendgeneralTemplate, 'model' => $this->getExtendedModel()))
            ->with('informationtemplate', $this->informationTemplate)//giving null will make a fallback to the default productinformation template on the package
            ->with('volumeclasses', $this->getVolumesClasses("array"))
            ->with('taxclasses', $this->getTaxClasses("array"))
            ->with('categoryOptionValues', Category::OptionValueTreeArray(false))
            ->with('sortOptionValues', $this->getSortOptions($languageinformation, 1));
    }

    protected function validateProductForm()
    {
        // validate
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'code' => 'required',
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

    protected function saveProductPrice(Product $Product)
    {
        $input = Input::get();
        $donotdeleteids = array();

        //---------------------------------------------
        // PRODUCT PRICE (Availability per country)
        //---------------------------------------------
        if (isset($input["price-country-id"]) && count($input["price-country-id"]) > 0) {
            foreach ($input['price-country-id'] as $price_id => $countryid) {
                $pPrice = null;
                $pPrice = Price::find($price_id);  //we make an update when we get an PIM-id(products_data.id) from the form
                if (is_null($pPrice) === true) {  // if we couln't find a Model for the given PIM-id we need to create/add a new one.
                    $pPrice = new Price;
                }
                $pPrice->country_id = $input['price-country-id'][$price_id];
                $pPrice->price = str_replace(",", ".", $input['price'][$price_id]);
                $pPrice->price_purchase = str_replace(",", ".", $input['price_purchase'][$price_id]);
                $pPrice->product_id = $Product->id;
                $pPrice->price_valuta_id = $input['price_valuta_id'][$price_id];
                $pPrice->price_tax_id = $input['price_tax_id'][$price_id];

                $pPrice->save();

                $donotdeleteids[$pPrice->id] = $pPrice->id;
            }
        }

        //delete all un-used or recently deleted prices
        $Price = Price::where('product_id', '=', $Product->id);
        if (count($donotdeleteids) > 0) $Price->whereNotIn('id', $donotdeleteids);
        $Price->delete();
    }

    //save the attachments to the model
    protected function saveProductAttachments(Product $Product)
    {
        $input = Input::get();
        $donotdeleteids = array();

        //---------------------------------------------
        // PRODUCT Attachemnts (Availability per language_id)
        //---------------------------------------------
        if (isset($input["attachment-language-id"]) && count($input["attachment-language-id"]) > 0) {
            foreach ($input['attachment-language-id'] as $attachment_id => $language_id) {
                $mAttachment = null;
                $mAttachment = Attachment::find($attachment_id);  //we make an update when we get an PIM-id(products_data.id) from the form
                if (is_null($mAttachment) === true) {  // if we couln't find a Model for the given PIM-id we need to create/add a new one.
                    $mAttachment = new Attachment;
                }
                $mAttachment->product_id = $Product->id;
                $mAttachment->language_id = $language_id;
                $mAttachment->file = str_replace(",", ".", $input['attachment-file'][$attachment_id]);
                $mAttachment->filename = str_replace(",", ".", $input['attachment-filename'][$attachment_id]);//$Attachment->id;
                $mAttachment->save();
                $donotdeleteids[$mAttachment->id] = $mAttachment->id;
            }
        }

        //delete all un-used or recently deleted prices
        $Attachment = Attachment::where('product_id', '=', $Product->id);
        if (count($donotdeleteids) > 0) $Attachment->whereNotIn('id', $donotdeleteids);
        $Attachment->delete();
    }

    /**
     * Store the product based on a productid or a new product
     *
     * @return Product Object
     */
    protected function saveProductProperties($productid = null)
    {
        // do check if the given id is existing.
        if (!is_null($productid) && intval($productid) > 0) {
            $Product = Product::find($productid);
        }

        if (!isset($Product) || is_null($Product)) {
            $Product = new Product;
        }

        foreach ($this->productColumNames as $column => $inputname) {
            if (!Input::has($inputname) && array_key_exists($inputname, $this->productColumnNamesDefaults)) {
                $Product->$column = $this->productColumnNamesDefaults[$inputname];
            } else {
                $Product->$column = Input::get($inputname);
            }
        }

        $Product->save();
        $Product->information()->detach(); //detach any information setting, this will be set up using teh saveProductInformation() method

        return $Product;
    }

    /**
     * Save the ProductInformation to the given product (object)
     * this can be filtered by givin a single languageid - the filter helps for returning the model of the saved Information
     * by default you get back the last saved Information object
     * @return Product Object
     */
    protected function saveProductInformation(Product $Product, $givenlanguage_id = null)
    {
        $input = Input::get();

        $pInformation = null; //pInformation = object product Information

        if (isset($input["information_language_id"]) && count($input["information_language_id"]) > 0) {
            foreach ($input["information_language_id"] as $i => $language_id) {
                if ((is_null($givenlanguage_id) || ($language_id == $givenlanguage_id)) && (strlen(trim($input[$this->informationColumNames['title']][$i])) > 0)) {
                    $pInformation = null; //reset when in a loop
                    $newInformation = true;

                    if(intval($input["information_id"][$i]) > 0 ) {
                        $pInformation = Information::find($input["information_id"][$i]);
                    }

                    if(!isset($pInformation) || is_null($pInformation) ) {
                        $pInformation = new Information;
                    } else {
                        $newInformation = false;
                    }

                    $oldSortID = null;
                    if ($newInformation == false && !is_null($pInformation->sort_id) && intval($pInformation->sort_id) > 0) {
                        $oldSortID = intval($pInformation->sort_id);
                    }

                    foreach ($this->informationColumNames as $column => $inputname) {
                        if (isset($input[$inputname][$i])) {
                            $pInformation->$column = $input[$inputname][$i];
                        } else {
                            $pInformation->$column = null;
                        }

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
					$Product->information()->attach($pInformation->id);

                    $sort_incrementstatus = "0"; //the default
                    if (is_null($oldSortID) || $oldSortID == 0) {
                        //update all where sortid >= input::sortid
                        $updateInformations = Information::where('language_id', '=', $language_id)->where('sort_id', '>=', $input[$this->informationColumNames['sort_id']][$i])->where('id', '<>', $pInformation->id)->get(array('id', 'sort_id'));
                        $sort_incrementstatus = "+1";
                    } elseif ($oldSortID > $input[$this->informationColumNames['sort_id']][$i]) {
                        $updateInformations = Information::where('language_id', '=', $language_id)->where('sort_id', '>=', $input[$this->informationColumNames['sort_id']][$i])->where('sort_id', '<', $oldSortID)->where('id', '<>', $pInformation->id)->get(array('id', 'sort_id'));
                        $sort_incrementstatus = "+1";
                    } elseif ($oldSortID < $input[$this->informationColumNames['sort_id']][$i]) {
                        $updateInformations = Information::where('language_id', '=', $language_id)->where('sort_id', '>', $oldSortID)->where('sort_id', '<=', $input[$this->informationColumNames['sort_id']][$i])->where('id', '<>', $pInformation->id)->get(array('id', 'sort_id'));
                        $sort_incrementstatus = "-1";
                    }

                    if ($sort_incrementstatus <> "0") {
                        if (isset($updateInformations) && count($updateInformations) > 0) {

                            foreach ($updateInformations as $uInformation) {
                                if ($sort_incrementstatus == "+1") {
                                    $uInformation->sort_id = intval($uInformation->sort_id) + 1;
                                    $uInformation->save();
                                } elseif ($sort_incrementstatus == "-1") {
                                    $uInformation->sort_id = intval($uInformation->sort_id) - 1;
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
        $information_group_id = 0; //define var
        $ProductInformationObject = $Product->information; //set it to a var, so the query will run once in this part.

        if ($ProductInformationObject->count() > 0) {
            foreach ($ProductInformationObject as $I) {
                if (intval($I->information_group_id) > 0) {
                    if (!is_null($I->information_group_id) && intval($I->information_group_id) > 0) {
                        $information_group_id = intval($I->information_group_id);
                        break;
                    }
                }
            }
            if ($information_group_id <= 0) {
                $igi = Information::orderBy('information_group_id', 'desc')->take(1)->get();
                $information_group_id = intval($igi[0]->information_group_id) + 1;
            }

            $this->information_group_id = $information_group_id;

            foreach ($ProductInformationObject as $I) {
                $I = Information::find($I->id);
                $I->information_group_id = $information_group_id;
                $I->save();
            }
        }
        return $pInformation;
    }

    public function getInformation($id = null)
    {
        if (is_null($id)) {
            return DB::connection("project")->table("languages")->select((DB::connection("project")->raw("'' as title, '' as description, '' as description_short,  NULL as sort_id, (select max(sort_id) from products_information where language_id = languages.id) as maxsort, '' as information_id, '' as id , '' as product_category_id")), "id as language_id", "language", "language_name", "country")->get();
        } else {
            return DB::connection("project")->select('
														SELECT products_information.language_id ,sort_id, (select max(sort_id) from products_information as X  where X.language_id = products_information.language_id) as maxsort, language, language_name, country, products_information.title, products_information.description, products_information.description_short, products_information.id as information_id, product_category_id
														FROM  products
														INNER JOIN products_to_products_information on products.id = products_to_products_information.product_id
														INNER JOIN products_information on products_to_products_information.product_information_id = products_information.id
														INNER JOIN languages on languages.id = products_information.language_id
														WHERE products.id = ?

														UNION
														SELECT languages.id as language_id , 0, (select max(sort_id) from products_information where language_id = languages.id), language, language_name, country, \'\' as title, \'\' as description, \'\' as description_short, \'\' as information_id , \'\'
														FROM languages
														WHERE id NOT IN (SELECT language_id FROM products_information WHERE id IN (SELECT product_information_id FROM products_to_products_information WHERE product_id = ?)) ORDER BY 1 ', array($id, $id));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        $product = Product::find($id);

        $mPrices = DB::connection("project")->select('SELECT products_price.id , country_id, product_id, country_name, price, price_purchase, price_valuta_id, price_tax_id FROM products_price INNER JOIN countries ON countries.id = products_price.country_id WHERE product_id = ? ', array($id));
        $rowPrices = $this->getPriceRow($mPrices);

        $mAttachments = DB::connection("project")->select('SELECT products_attachments.id , language_id, product_id, file,filename FROM products_attachments WHERE product_id = ? ORDER BY 2 ', array($id));
        $rowAttachments = $this->getAttachmentsRow($mAttachments);
        $rowAttachmentsByLang = $this->getAttachmentsRow($mAttachments, false, true);

        $languageinformation = $this->getInformation($id);

        return View::make('dcmsproducts::products/form')
            ->with('product', $product)
            ->with('extendgeneralTemplate', array('template' => $this->extendgeneralTemplate, 'model' => $this->getExtendedModel($id)))
            ->with('informationtemplate', $this->informationTemplate)//giving null will make a fallback to the default productinformation template on the package
            ->with('languageinformation', $languageinformation)
            ->with('volumeclasses', $this->getVolumesClasses("array"))
            ->with('rowPrices', $rowPrices)
            ->with('rowAttachments', $rowAttachments)
            ->with('rowAttachmentsByLang', $rowAttachmentsByLang)
            ->with('categoryOptionValues', Category::OptionValueTreeArray(false))
            ->with('sortOptionValues', $this->getSortOptions($languageinformation));
    }

    public function getSortOptions($model, $setExtra = 0)
    {
        $SortOptions = array();
        foreach ($model as $M) {
            $increment = 0;
            if ($setExtra > 0) $increment = $setExtra;
            if (intval($M->information_id) <= 0 && !is_null($M->maxsort)) $increment = 1;

            $maxSortID = $M->maxsort;
            if (is_null($maxSortID)) {
                $maxSortID = 1;
            }

            for ($i = 1; $i <= ($maxSortID + $increment); $i++) {
                $SortOptions[$M->language_id][$i] = $i;
            }
        }
        return $SortOptions;
    }


    /**
     * copy the model
     *
     * @param  int $product_id
     * @param  int $country_id //helps limiting the prices copy - we don't need the copied product in all countries..
     * @return Response
     */
    public function copy($product_id, $country_id = 0)
    {
        //COPY THE PRODUCT
        $Newproduct = Product::find($product_id)->replicate();
        $Newproduct->save();

        //COPY THE Information
        $ProductsInformation = Product::with('Information')->where('id', '=', $product_id)->get();

        if (!is_null($ProductsInformation)) {
            foreach ($ProductsInformation as $P) {
                foreach ($P->Information as $pInformation) {
                    $Newproduct->information()->attach($pInformation->id);
                }
            }
        }

        //COPY THE Attachment
        $Attachements = Attachment::where('product_id', '=', $product_id)->get();
        if (!is_null($Attachements)) {
            foreach ($Attachements as $A) {
                $newAttachement = $A->replicate();
                $newAttachement->product_id = $Newproduct->id;
                $newAttachement->save();
            }
        }

        //COPY THE Prices
        $Prices = Price::where('product_id', '=', $product_id)->where('country_id', '=', $country_id)->get();
        if (!is_null($Prices)) {
            foreach ($Prices as $P) {
                $newPrice = $P->replicate();
                $newPrice->product_id = $Newproduct->id;
                $newPrice->save();
            }
        }

        return Redirect::to('admin/products');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        if ($this->validateProductForm() === true) {
            $Product = $this->saveProductProperties();
            $this->saveProductInformation($Product);
            $this->saveProductPrice($Product);
            $this->saveProductAttachments($Product);

            Session::flash('message', 'Successfully created Product!');
            return Redirect::to('admin/products');
        } else {
            return $this->validateProductForm();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id)
    {
        if ($this->validateProductForm() === true) {
            $Product = $this->saveProductProperties($id);
            $this->saveProductInformation($Product);
            $this->saveProductPrice($Product);
            $this->saveProductAttachments($Product);

            Session::flash('message', 'Successfully updated Product!');
            return Redirect::to('admin/products');
        } else {
            return $this->validateProductForm();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        if (Input::get('table') == 'product') {
            Product::find($id)->delete();
            Price::where("product_id", "=", $id)->delete();
        } else {
            $Information = Information::find($id);
            $Information->products()->detach(Input::get("product_id"));
        }

        Session::flash('message', 'Successfully deleted the Product!');
        return Redirect::to('admin/products');
    }
}
