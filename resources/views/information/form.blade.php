@extends("dcms::template/layout")

@section("content")

    <div class="main-header">
      <h1>Products Information</h1>
      <ol class="breadcrumb">
        <li><a href="{!! URL::to('admin/dashboard') !!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="{!! URL::to('admin/products') !!}"><i class="fa fa-shopping-cart"></i> Products</a></li>
        <li><a href="{!! URL::to('admin/products/information') !!}"> Information</a></li>
        <li class="active">Edit</li>
      </ol>
    </div>

    <div class="main-content">
    	<div class="row">
				<div class="col-md-12">
            {!! Form::open(array('route' => array('admin.products.information.update', 0), 'method' => 'PUT')) !!}

            <div class="main-content-tab tab-container">

              <ul class="nav nav-tabs" role="tablist">
                @foreach($languageinformation as $key => $language)
                      <li class="{!! ($key == 0 ? 'active' : '') !!}"><a href="{!! '#' . $language->language . '-' . $language->country !!}" role="tab" data-toggle="tab"><img src="{!! asset('/packages/dcms/core/images/flag-' . strtolower($language->country) . '.png') !!}" width="16" height="16" /> {!! $language->language_name !!}</a></li>
                @endforeach
              </ul>

              <div class="tab-content">
                @if($errors->any())
                  <div class="alert alert-danger">{!! Html::ul($errors->all()) !!}</div>
                @endif

                	@foreach($languageinformation as $key => $information)

                      <div id="{!! $information->language . '-' . $information->country !!}" class="tab-pane {!! ($key == 0 ? 'active' : '') !!}">

                        {!! Form::hidden('information_language_id[' . $key . ']', $information->language_id) !!}

                        <div class="row">
                          <div class="col-sm-10">
                            <div class="form-group">
                              {!! Form::label('information_name[' . $key . ']', 'Product Name') !!}
                              {!! Form::text('information_name[' . $key . ']', (Input::old('information_name[' . $key . ']') ? Input::old('information_name[' . $key . ']') : $information->title ), array('class' => 'form-control')) !!}
                            </div>
                          </div>
                          <div class="col-sm-2">
                            <div class="form-group">
                              {!! Form::label('information_id[' . $key . ']', 'ID') !!}
                              <div class="input-group">
                                  {!! Form::text('information_id[' . $key . ']', (Input::old('information_id[' . $key . ']') ? Input::old('information_id[' . $key . ']') : $information->information_id ), array('class' => 'form-control', 'readonly')) !!}
                                <span class="input-group-btn">
                                  {!! Form::button('Reset', array('class' => 'btn btn-primary information-id-reset')) !!}
                                </span>
                              </div>
                            </div>
                          </div>
                        </div>

                        <div class="form-group">
                          {!! Form::label('information_category_id[' . $key . ']', 'Category') !!}
                          {!! isset($categoryOptionValues[$information->language_id])? Form::select('information_category_id[' . $key . ']', $categoryOptionValues[$information->language_id], (Input::old('information_category_id[' . $key . ']') ? Input::old('information_category_id[' . $key . ']') : $information->product_category_id), array('class' => 'form-control')):'' !!}
                        </div>

                        <div class="form-group">
                          {!! Form::checkbox('information_new[' . $key . ']',1, (Input::old('information_new[' . $key . ']') ? Input::old('information_new[' . $key . ']') : $information->new), array('class' => 'form-checkbox' , 'id' => 'information_new[' . $key . ']'))!!}
                          {!! Html::decode(Form::label('information_new[' . $key . ']', "New Product", array('class' => ($information->new==1)?'checkbox active':'checkbox'))) !!}
                        </div>

                        <div class="form-group">
                          {!! Form::label('information_sort_id[' . $key . ']', 'Sort') !!}
                          {!! Form::select('information_sort_id[' . $key . ']', $sortOptionValues[$information->language_id], (Input::old('information_sort_id[' . $key . ']') ? Input::old('information_sort_id[' . $key . ']') : $information->sort_id), array('class' => 'form-control')) !!}
                        </div>

                        <div class="form-group">
                          {!! Form::label('information_composition[' . $key . ']', 'Composition') !!}
                          {!! Form::text('information_composition[' . $key . ']', (Input::old('information_composition[' . $key . ']') ? Input::old('information_composition[' . $key . ']') : $information->composition ), array('class' => 'form-control')) !!}
                        </div>


                        <div class="form-group">
                          {!! Form::label('image', 'Image') !!}
                          <div class="input-group">
                              {!! Form::text('information_image['.$key.']', (Input::old('information_image[' . $key . ']') ? Input::old('information_image[' . $key . ']') : $information->image) , array('class' => 'form-control','id'=>'information_image'.$key)) !!}
                            <span class="input-group-btn">
                              {!! Form::button('Browse Server', array('class' => 'btn btn-primary browse-server', 'id'=>'browse_information_image'.$key)) !!}
                            </span>
                          </div>
                        </div>
                        <!-- delete the file browser since 17.06.2015 this goes to attachments (you can attach images as well than the image field can be removed as well) -->
                        <!-- reason: startec has 2 attachments TechFiche, strooiinstellingen -->
                        <div class="form-group">
                          {!! Form::label('information_description_short[' . $key . ']', 'Short Description') !!}
                          {!! Form::textarea('information_description_short[' . $key . ']', (Input::old('information_description_short[' . $key . ']') ? Input::old('information_description_short[' . $key . ']') : $information->description_short ), array('class' => 'form-control')) !!}
                        </div>
                        <div class="form-group">
                          {!! Form::label('information_description[' . $key . ']', 'Long Description') !!}
                          {!! Form::textarea('information_description[' . $key . ']', (Input::old('information_description[' . $key . ']') ? Input::old('information_description[' . $key . ']') : $information->description ), array('class' => 'form-control ckeditor')) !!}
                        </div>

                        <div class="form-group">
                          {!! Form::label('information_guarantee[' . $key . ']', 'Guarantee') !!}
                          {!! Form::textarea('information_guarantee[' . $key . ']', (Input::old('information_guarantee[' . $key . ']') ? Input::old('information_guarantee[' . $key . ']') : $information->guarantee), array('class' => 'form-control ckeditor')) !!}
                        </div>

                        <div class="form-group">
                          {!! Form::label('information_manual[' . $key . ']', 'Manual') !!}
                          {!! Form::textarea('information_manual[' . $key . ']', (Input::old('information_manual[' . $key . ']') ? Input::old('information_manual[' . $key . ']') : $information->manual), array('class' => 'form-control ckeditor')) !!}
                        </div>

                        <div class="form-group">
                          {!! Form::label('information_doses[' . $key . ']', 'Dose') !!}
                          {!! Form::text('information_doses[' . $key . ']', (Input::old('information_doses[' . $key . ']') ? Input::old('information_doses[' . $key . ']') : $information->doses), array('class' => 'form-control')) !!}
                        </div>

                        <div class="form-group clearfix period-group">
                          {!! Form::label('information_period[' . $key . ']', 'Period', array('class' => 'full')) !!}
                          <div class="clearfix"></div>
                          {!! Form::hidden('information_period[' . $key . ']', (Input::old('information_period[' . $key . ']') ? Input::old('information_period[' . $key . ']') : $information->period), array('class' => 'form-control')) !!}

                          @for($monthid=1; $monthid<=12 ; $monthid++)
                        			<div class="checkbox-group">
                              	{!! Form::checkbox('periodehelper['.$monthid.']', 1, ((isset($information->period) && isset($information->period) && substr($information->period,($monthid-1),1) == 1)?true:false), array('class' => 'form-checkbox periodehelper' ,  'id' => 'periode-'.$monthid)) !!}
                                {!! Form::label('periode-'.$monthid, date('M',mktime(0,0,0,$monthid)), array('class' => (isset($information->period) && isset($information->period) && substr($information->period,($monthid-1),1) == 1)?'checkbox active':'checkbox')) !!}
                              </div>
                           @endfor

                        </div>

                        <div class="form-group clearfix period-box" id="period[{!! $key !!}]">
          {!! Form::label('information_period[' . $key . ']', 'Period', array('class' => 'full')) !!}
          {!! Form::hidden('information_period[' . $key . ']', (Input::old('information_period[' . $key . ']') ? Input::old('information_period[' . $key . ']') : $information->period), array('class' => 'form-control')) !!}
          <div class="clearfix"></div>
          <div class="checkbox-group">
           {!! Form::label('jan', 'Jan') !!}
            <input type="hidden" name="jan" data-palette='[{"0": "#dadada"},{"1": "#81bb26"},{"2": "#c7dd9b"}]' value="0">
          </div>
          <div class="checkbox-group">
          {!! Form::label('feb', 'Feb') !!}
          <input type="hidden" name="feb" data-palette='[{"0": "#dadada"},{"1": "#81bb26"},{"2": "#c7dd9b"}]' value="0">
          </div>
          <div class="checkbox-group">
          {!! Form::label('feb', 'Mar') !!}
          <input type="hidden" name="mar" data-palette='[{"0": "#dadada"},{"1": "#81bb26"},{"2": "#c7dd9b"}]' value="0">
          </div>
          <div class="checkbox-group">
          {!! Form::label('feb', 'Apr') !!}
          <input type="hidden" name="apr" data-palette='[{"0": "#dadada"},{"1": "#81bb26"},{"2": "#c7dd9b"}]' value="0">
          </div>
          <div class="checkbox-group">
          {!! Form::label('feb', 'May') !!}
          <input type="hidden" name="may" data-palette='[{"0": "#dadada"},{"1": "#81bb26"},{"2": "#c7dd9b"}]' value="0">
          </div>
          <div class="checkbox-group">
          {!! Form::label('feb', 'Jun') !!}
          <input type="hidden" name="jun" data-palette='[{"0": "#dadada"},{"1": "#81bb26"},{"2": "#c7dd9b"}]' value="0">
          </div>
          <div class="checkbox-group">
          {!! Form::label('feb', 'Jul') !!}
          <input type="hidden" name="jul" data-palette='[{"0": "#dadada"},{"1": "#81bb26"},{"2": "#c7dd9b"}]' value="0">
          </div>
          <div class="checkbox-group">
          {!! Form::label('feb', 'Aug') !!}
          <input type="hidden" name="aug" data-palette='[{"0": "#dadada"},{"1": "#81bb26"},{"2": "#c7dd9b"}]' value="0">
          </div>
          <div class="checkbox-group">
          {!! Form::label('feb', 'Sep') !!}
          <input type="hidden" name="sep" data-palette='[{"0": "#dadada"},{"1": "#81bb26"},{"2": "#c7dd9b"}]' value="0">
          </div>
          <div class="checkbox-group">
          {!! Form::label('feb', 'Oct') !!}
          <input type="hidden" name="oct" data-palette='[{"0": "#dadada"},{"1": "#81bb26"},{"2": "#c7dd9b"}]' value="0">
          </div>
          <div class="checkbox-group">
          {!! Form::label('feb', 'Nov') !!}
          <input type="hidden" name="nov" data-palette='[{"0": "#dadada"},{"1": "#81bb26"},{"2": "#c7dd9b"}]' value="0">
          </div>
          <div class="checkbox-group">
          {!! Form::label('feb', 'Dec') !!}
          <input type="hidden" name="dec" data-palette='[{"0": "#dadada"},{"1": "#81bb26"},{"2": "#c7dd9b"}]' value="0">
          </div>
          <script>
            $period = $("input[id='information_period[{!! $key !!}]']").val();
            $months = ["jan", "feb", "mar", "apr", "may", "jun", "jul", "aug", "sep", "oct", "nov", "dec"];

            for (var i=0; i < $period.length; i++) {
              $("div[id='period[{!! $key !!}]'] .checkbox-group input[name='"+ $months[i] +"']").val($period.charAt(i));
            }
      </script>
      </div>


                      </div>
                	@endforeach

              </div><!-- end tab-content -->

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
  <script type="text/javascript" src="{!! asset('packages/dcms/core/assets/js/bootstrap.min.js') !!}"></script>
  <script type="text/javascript" src="{{ asset('packages/dcms/core/assets/js/jquery-ui-autocomplete.min.js') }}"></script>
  <link rel="stylesheet" type="text/css" href="{{ asset('packages/dcms/core/assets/css/jquery-ui-autocomplete.css') }}">


  <script type="text/javascript" src="{{ asset('/packages/dcms/core/ckeditor/ckeditor.js') }}"></script>
  <script type="text/javascript" src="{{ asset('/packages/dcms/core/ckeditor/adapters/jquery.js') }}"></script>
  <script type="text/javascript" src="{{ asset('/packages/dcms/core/ckfinder/ckfinder.js') }}"></script>
  <script type="text/javascript" src="{{ asset('/packages/dcms/core/ckfinder/ckbrowser.js') }}"></script>

  <link rel="stylesheet" type="text/css" href="{{ asset('/packages/dcms/core/js/color-picker/palette-color-picker.css') }}">
  <script type="text/javascript" src="{{ asset('/packages/dcms/core/js/color-picker/palette-color-picker.min.js') }}"></script>

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

  	//UI Autocomplete Product Detail
  	$(".tab-pane input[id^='information_name']").autocomplete({
  		source: function (request, response) {
  			var language = this.element.closest(".tab-pane").find("input[name^='information_language_id']").val();
  			$.getJSON("{{ route('admin.products.api.pim') }}?term=" + request.term + "&language=" + language, function (data) {
  				response(data);
  			});
  		},
  		select: function( event, ui ) {
  			$(this).val( ui.item.label );

  			$x = $(this);
  			$.each(ui.item, function(i,v){
  					$x.closest(".tab-pane").find("[id^='information_"+i+"']").val( v );
  				}); //end of each function

  			return false;
  		},
  		minLength: 3,
  		delay: 200
  	});

  	$('.input-group .information-id-reset').click(function() {
  			$(this).closest(".tab-pane").find("input[id^='information_id']").val( "" );
  			return false;
  	});

  	$("body").on("click",".browse-server-files", function(){
  		var returnid = $(this).attr("id").replace("browse_","") ;
  		BrowseServer( 'Files:/', returnid);
  		});

    //Color picker
    $period = $("input[id='information_period']");
    console.log('pieter');
    $(".period-box input").each(function(){
        $inputName = $(this).attr('name');
        $('[name="'+ $inputName +'"]').paletteColorPicker({
              clear_btn: null,
            timeout: 700
        });
      });

    //$( ".period-box input[type='hidden']" ).change(function() {
      console.log('pieter');

    //});

  });
  </script>

@stop
