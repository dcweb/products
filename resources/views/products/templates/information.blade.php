@section('information')

	@foreach($languageinformation as $key => $information)

      <div id="{!! $information->language . '-' . $information->country !!}" class="tab-pane {!! ($key == 0 ? 'active' : '') !!}">

        {!! Form::hidden('information_language_id[' . $key . ']', $information->language_id) !!}

        <div class="form-group">
          {!! Form::label('information_category_id[' . $key . ']', 'Category') !!}
          {!! isset($categoryOptionValues[$information->language_id])? Form::select('information_category_id[' . $key . ']', $categoryOptionValues[$information->language_id], (Input::old('information_category_id[' . $key . ']') ? Input::old('information_category_id[' . $key . ']') : $information->product_category_id), array('class' => 'form-control')):'' !!}
        </div>

        <div class="form-group">
          {!! Form::label('information_sort_id[' . $key . ']', 'Sort') !!}
          {!! Form::select('information_sort_id[' . $key . ']', $sortOptionValues[$information->language_id], (Input::old('information_sort_id[' . $key . ']') ? Input::old('information_sort_id[' . $key . ']') : $information->sort_id), array('class' => 'form-control')) !!}
        </div>

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
		      {!! Form::label('information_description_short[' . $key . ']', 'Short Description') !!}
		      {!! Form::textarea('information_description_short[' . $key . ']', (Input::old('information_description_short[' . $key . ']') ? Input::old('information_description_short[' . $key . ']') : $information->description_short ), array('class' => 'form-control')) !!}
		    </div>

        <div class="form-group">
          {!! Form::label('information_description[' . $key . ']', 'Long Description') !!}
          {!! Form::textarea('information_description[' . $key . ']', (Input::old('information_description[' . $key . ']') ? Input::old('information_description[' . $key . ']') : $information->description ), array('class' => 'form-control ckeditor')) !!}
        </div>


      </div>
	@endforeach
@overwrite
