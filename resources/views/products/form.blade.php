@extends("dcms::template/layout")

@section("content")

    <div class="main-header">
      <h1>Products</h1>
      <ol class="breadcrumb">
        <li><a href="{!! URL::to('admin/dashboard') !!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="{!! URL::to('admin/products') !!}"><i class="fa fa-shopping-cart"></i> Products</a></li>
        @if(isset($product))
          <li class="active">Edit</li>
        @else
          <li class="active">Create</li>
        @endif
      </ol>
      @if(isset($product))
        <p class="edit">Last edited by {{ $product->admin }} on {{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $product->updated_at)->format('d-m-Y H:i:s') }}</p>
      @endif
    </div>

    <div class="main-content">
    	<div class="row">

          @if(isset($product))
            {!! Form::model($product, array('route' => array('admin.products.update', $product->id), 'method' => 'PUT')) !!}
          @else
            {!! Form::open(array('url' => 'admin/products')) !!}
          @endif

          @if(!is_null($extendgeneralTemplate["template"]))
          @include($extendgeneralTemplate["template"], array('model'=>$extendgeneralTemplate["model"],'product'=>(isset($product)?$product:null)))
          @endif

            <div class="col-md-12">

            <div class="main-content-tab tab-container">
              @if (!is_array($categoryOptionValues) || count($categoryOptionValues)<=0 ) 	Please first create a <a href="{!! URL::to('admin/products/categories/create') !!}"> product category </a>  @else
              <ul class="nav nav-tabs" role="tablist">
                <li class="active"><a href="#data" role="tab" data-toggle="tab">Data</a></li>
                <li><a href="#information" role="tab" data-toggle="tab">Information</a></li>
                <li><a href="#price" role="tab" data-toggle="tab">Price</a></li>
                @yield('extratabs')
                <li><a href="#attachments" role="tab" data-toggle="tab">Attachments</a></li>
              </ul>

              <div class="tab-content">
                @if($errors->any())
                  <div class="alert alert-danger">{!! Html::ul($errors->all()) !!}</div>
                @endif
  								<div id="data" class="tab-pane active">
									  <!-- #data -->

									  <!-- #Code -->
									  <div class="form-group">
                      {!! Form::label('code', 'Code') !!}
                      {!! Form::text('code', Input::old('code'), array('class' => 'form-control')) !!}
                    </div>

									  <!-- #EAN CODE-->
                    <div class="form-group">
                      {!! Form::label('eancode', 'EAN Code') !!}
                      {!! Form::text('eancode', Input::old('eancode'), array('class' => 'form-control')) !!}
                    </div>

                    <!-- #ONLINE-->
                    <div class="form-group">
                      {!! Form::checkbox('online', '1', null, array('class' => 'form-checkbox','id'=>'online'))  !!}
                      {!! Html::decode(Form::label('online', 'Online', array('class' => (isset($product) && $product->online==1)?'checkbox active':'checkbox'))) !!}
                    </div>

                    <!-- #NEW-->
                    <div class="form-group">
                      {!! Form::checkbox('new', '1', null, array('class' => 'form-checkbox','id'=>'new'))  !!}
                      {!! Html::decode(Form::label('new', 'New', array('class' => (isset($product) && $product->new==1)?'checkbox active':'checkbox'))) !!}
                    </div>

                    <!-- #DISCONTINUED-->
                    <div class="form-group">
                      {!! Form::checkbox('discontinued', '1', null, array('class' => 'form-checkbox','id'=>'discontinued'))  !!}
                      {!! Html::decode(Form::label('discontinued', 'Discontinued', array('class' => (isset($product) && $product->discontinued==1)?'checkbox active':'checkbox'))) !!}
                    </div>

                    <div class="form-group">
                      {!! Form::label('image', 'Image') !!}
                      <div class="input-group">
                          {!! Form::text('image', Input::old('image'), array('class' => 'form-control')) !!}
                        <span class="input-group-btn">
                          {!! Form::button('Browse Server', array('class' => 'btn btn-primary browse-server' , 'id'=>'browse_image')) !!}
                        </span>
                      </div>
                    </div>

									  <!-- #Volume + unitclass (kg - l - g - ...) -->
                    <div class="row">
                      <div class="col-sm-10">
                        <div class="form-group">
                          {!! Form::label('volume', 'Volume') !!}
                          {!! Form::text('volume', Input::old('volume'), array('class' => 'form-control')) !!}
                        </div>
                      </div>
                      <div class="col-sm-2">
                        <div class="form-group">
                          {!! Form::label('volume_unit_id', 'Unit') !!}
                          {!! Form::select('volume_unit_id', (array(null=>'') + $volumeclasses), Input::old('volume_unit_id'), array('class' => 'form-control')); !!}
                        </div>
                      </div>
                    </div>

                    @yield('extendedgeneral')
  								<!-- #data -->
                </div>

        				<div id="information" class="tab-pane">
									<!-- #information -->
                      <div class="tab-container">

                      @if(isset($languageinformation))

                          <ul class="nav nav-tabs" role="tablist">
                            @foreach($languageinformation as $key => $language)
                                  <li class="{!! ($key == 0 ? 'active' : '') !!}"><a href="{!! '#' . $language->language . '-' . $language->country !!}" role="tab" data-toggle="tab"><img src="{!! asset('/packages/dcms/core/images/flag-' . strtolower($language->country) . '.png') !!}" width="16" height="16" /> {!! $language->language_name !!}</a></li>
                            @endforeach
                          </ul>

                          <div class="tab-content">
                          		@if(!isset($informationtemplate) || is_null($informationtemplate) )
                                @include('dcmsproducts::products/templates/information', array('languageinformation'=>$languageinformation,'sortOptionValues'=>$sortOptionValues))
                                @yield('information')
                              @else
	                              @include($informationtemplate, array('languageinformation'=>$languageinformation,'sortOptionValues'=>$sortOptionValues))
                                @yield('information')
                              @endif
                          </div>

                      @endif

                      </div>
										<!-- #information -->
                  </div>

        					<div id="price" class="tab-pane">
										<!-- #price -->
                    <table class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>Country</th>
                          <th>Retail Price</th>
                          <th>Purchase Price</th>
                          <th>Unit</th>
                          <th>Vat</th>
                          <th></th>
                        </tr>
                      </thead>

                      @if(isset($rowPrices))
                      {!! $rowPrices !!}
                      @endif

                    <tfoot>
                      <tr>
                        <td colspan="6"><a class="btn btn-default pull-right add-table-row" href=""><i class="fa fa-plus"></i></a></td>
                      </tr>
                    </tfoot>
      						</table>
									<!-- #price -->
                </div>
                @yield('extratabcontainter')
        					<div id="attachments" class="tab-pane">
                    <div class="tab-container">
                    @if(isset($languageinformation))
                      <ul class="nav nav-tabs" role="tablist">
                        @foreach($languageinformation as $key => $language)
                              <li class="{!! ($key == 0 ? 'active' : '') !!}"><a href="{!! '#attachment' . $language->language . '-' . $language->country !!}" role="tab" data-toggle="tab"><img src="{!! asset('/packages/dcms/core/images/flag-' . strtolower($language->country) . '.png') !!}" width="16" height="16" /> {!! $language->language_name !!}</a></li>
                        @endforeach
                      </ul>
                      <div class="tab-content">
                        @foreach($languageinformation as $key => $language)
                      	<div id="attachment{!! $language->language . '-' . $language->country !!}" class="tab-pane {!! ($key == 0 ? 'active' : '') !!}">
                          <table class="table table-bordered table-striped languagehelperid-{{$language->language_id}}  ">
                            <thead>
                              <tr>
			                          <th>File</th>
                                <th>Filename</th>
                                <th></th>
                              </tr>
                            </thead>
                            @if(isset($rowAttachmentsByLang[$language->language_id]))
                            	{!! $rowAttachmentsByLang[$language->language_id] !!}
                            @endif
                            <tfoot>
                              <tr>
                                <td colspan="5"><a class="btn btn-default pull-right add-table-row" href=""><i class="fa fa-plus"></i></a></td>
                              </tr>
                            </tfoot>
                          </table>
	                      </div>
                        <!-- #attachments -->
                        @endforeach
                      </div>
                    @endif
                    </div>

                </div>
  						</div><!-- end tab-content -->
               @endif
            </div><!-- end main-content-tab -->
          </div><!-- end col-md-12 -->

          <div class="col-md-12">
            <div class="main-content-block">
              {!! Form::submit('Save', array('class' => 'btn btn-primary')) !!}
              <a href="{!! URL::previous() !!}" class="btn btn-default">Cancel</a>
           	</div>
         	</div>

        {!! Form::close() !!}

    </div><!-- end row -->
  </div><!-- end main-content -->

@stop

@section("script")

<script type="text/javascript" src="{!! asset('/packages/dcms/core/assets/js/bootstrap.min.js') !!}"></script>
<script type="text/javascript" src="{!! asset('/packages/dcms/core/assets/js/jquery-ui-autocomplete.min.js') !!}"></script>
<link rel="stylesheet" type="text/css" href="{!! asset('/packages/dcms/core/assets/css/jquery-ui-autocomplete.css') !!}">

<script type="text/javascript" src="{!! asset('/packages/dcms/core/ckeditor/ckeditor.js') !!}"></script>
<script type="text/javascript" src="{!! asset('/packages/dcms/core/ckeditor/adapters/jquery.js') !!}"></script>
<script type="text/javascript" src="{!! asset('/packages/dcms/core/ckfinder/ckfinder.js') !!}"></script>
<script type="text/javascript" src="{!! asset('/packages/dcms/core/ckfinder/ckbrowser.js') !!}"></script>

<link rel="stylesheet" href="{!! asset('/packages/dcms/core/assets/js/color-picker/palette-color-picker.css') !!}">
<script src="{!! asset('packages/dcms/core/assets/js/color-picker/palette-color-picker.js') !!}"></script>

<script type="text/javascript">
$(document).ready(function() {

	//CKFinder for CKEditor
	CKFinder.setupCKEditor( null, '/packages/dcms/core/ckfinder/' );

	//CKEditor
	$("textarea.ckeditor").ckeditor();

	//CKFinder
	$(".browse-server").click(function() {
		var returnid = $(this).attr("id").replace("browse_","") ;
		BrowseServer( 'image:/', returnid);
	})
	//CKFinder
	/*
	$(".browse-server-files").click(function() {
		var returnid = $(this).attr("id").replace("browse_","") ;
		BrowseServer( 'Files:/', returnid);
	})
*/
	//Bootstrap Tabs
	$(".tab-container .nav-tabs a").click(function (e) {
		e.preventDefault();
		$(this).tab('show');
	})

    //ColorPicker
    $(".period-box .checkbox-group input").each(function(){
      $inputName = $(this).attr('name');
      $('[name="'+ $inputName +'"]').paletteColorPicker({
          clear_btn: null,
        timeout: 700
      });
    });

	//UI Autocomplete Product Detail
	$("#information .tab-pane input[id^='information_name']").autocomplete({
		source: function (request, response) {
			var language = this.element.closest(".tab-pane").find("input[name^='information_language_id']").val();
			$.getJSON("{!! route('admin.products.api.pim') !!}?term=" + request.term + "&language=" + language, function (data) {
				response(data);
			});
		},
		select: function( event, ui ) {
			$(this).val( ui.item.label );
			$x = $(this);
      i = 0;

			$.each(ui.item, function(i,v){
				$x.closest(".tab-pane").find("[id^='information_"+i+"']").val( v );
			}); //end of each function

      $periodUpdate = $x.closest(".tab-pane").find(".period-box input:first").val();

      $x.closest(".tab-pane").find(".period-box .checkbox-group input").each(function(){
        $inputName = $(this).attr('name');
        $('[name="'+ $inputName +'"]').data('paletteColorPickerPlugin').destroy();
        $(this).val($periodUpdate.charAt(i));
        $(this).paletteColorPicker({
          clear_btn: null,
          timeout: 700
        });
        i++;
      });

		return false;
		},
		minLength: 3,
		delay: 200

	});

	$('#information .input-group .information-id-reset').click(function() {
			$(this).closest(".tab-pane").find("input[id^='information_id']").val( "" );
			return false;
	});

	//Add table row
	$.fn.addtablerow = function( options ) {

		$(this).each(function() {

			var table = $( this );

			var rows = table.closest('tbody tr').length;

			table.find('.add-table-row').click(function() {
        language_id = table.attr("class").replace('table table-bordered table-striped languagehelperid-','');
        geturl  = options.source;
        geturl = geturl.replace("{LANGUAGE_ID}",language_id);

				$.get( geturl, function( data ) {
					if (!table.find('tbody').length) table.find('thead').after("<tbody></tbody>");

          data = data.replace(/{INDEX}/g, "extra"+language_id.trim()+"-"+rows);
          table.find('tbody').append( data );
          //$("#attachment-language-id[extra"+rows+"] option[value='"+language_id+"']").attr('selected','selected');
					rows++;
					deltablerow(table.find('.delete-table-row').last());
				});
				return false;
			});

			deltablerow(table.find('.delete-table-row'));
			browsetablerow(table.find('.browse-server-files'));

			function browsetablerow(e) {
				e.click (function() {
					var returnid = $(this).attr("id").replace("browse_","");
					BrowseServer( 'Files:/', returnid);
				});
			}

			function deltablerow(e) {
				e.click (function() {
					$(this).closest("tr").remove();
					if (!table.find('tbody tr').length) table.find('tbody').remove();
					return false;
				});
			}

		});

	};

	$("body").on("click",".browse-server-files", function(){
		var returnid = $(this).attr("id").replace("browse_","") ;
		BrowseServer( 'Files:/', returnid);
		});

	$("#price table").addtablerow({
		source: "{!! URL::to('admin/products/api/tablerow?data=price') !!}" //generate the row with the dropdown fields/empty boxes/etc.
	});

	$("#attachments table").addtablerow({
		source: "{!! URL::to('admin/products/api/tablerow?data=attachments&language_id={LANGUAGE_ID}') !!}" //generate the row with the dropdown fields/empty boxes/etc.
	});



});
</script>

@stop
