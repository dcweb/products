@extends("dcms::template/layout")

@section("content")

    <div class="main-header">
      <h1>Categories</h1>
      <ol class="breadcrumb">
        <li><a href="{!! URL::to('admin/dashboard') !!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="{!! URL::to('admin/products') !!}"><i class="fa fa-pencil"></i> Products</a></li>
        <li><a href="{!! URL::to('admin/products/categories') !!}"><i class="fa fa-pencil"></i> Categories</a></li>
        @if(isset($category))
		 	<li class="active">Edit</li>
        @else
	  		<li class="active">Create</li>
        @endif
      </ol>
    </div>


    <div class="main-content">
    	<div class="row">
				<div class="col-md-12">
					<div class="main-content-block">

                      @if(isset($category))
                        <h2>Edit category</h2>
                        {!! Form::model($category, array('route' => array('admin.products.categories.update', $category->id), 'method' => 'PUT')) !!}
                      @else
                        <h2>Create category</h2>
                        {!! Form::open(array('url' => 'admin/products/categories')) !!}
                      @endif

                      @if($errors->any())
                        <div class="alert alert-danger">{!! Html::ul($errors->all()) !!}</div>
                      @endif

                      <div class="form-group">
                      	{!! Form::label('Language', 'Language') !!}
                        <div class="clearfix"></div>
                        @foreach($oLanguages as $O)
                        <fieldset class="float-left">
                        	{!! Form::radio('language_id', $O->thelanguage_id , (isset($category) && $category->language_id == $O->thelanguage_id ? true: false), array('class' => 'radiolanguage','id'=>'language_id'.$O->thelanguage_id)) !!}
                          {!! Html::decode(Form::label( 'language_id'.$O->thelanguage_id, ' <img src="/packages/dcms/core/images/flag-'.strtolower($O->country). '.png"> - ' .$O->language, array('class' => ''))) !!}
                        </fieldset>
                        @endforeach

                        <div class="clearfix"></div>
                      </div>

                      <div class="form-group">
                        {!! Form::label('parent_id', 'Parent Category') !!}
                        {!! $categoryOptionValues !!}
                      </div>

                      <div class="form-group">
        	            {!! Form::label('sort', 'Sort') !!} <!-- Sort has some jQuery magic behind it.. since sorting in  controller is a bit different -->
                      	{!! Form::text('sort',  (isset($category)?$category->sort_id:''), array('class' => 'form-control')) !!}
                        {!! Form::hidden('nexttosiblingid', '', array('id'=>'nexttosiblingid', 'class' => 'form-control')) !!}
          	            {!! Form::hidden('oldsort', '', array('id'=>'oldsort', 'class' => 'form-control')) !!}
                      </div>

                      <div class="form-group">
        	              {!! Form::label('title', 'Title ') !!}
                      	{!! Form::text('title', (isset($category)?$category->title:''), array('class' => 'form-control')) !!}
                      </div>

                    <div class="form-group">
                      {!! Form::label('description', 'Description') !!}
                      {!! Form::textarea('description',  (isset($category)?$category->description:''), array('class' => 'form-control')) !!}
                    </div>

                      <div class="form-group">
                       {!! Form::label('image', 'Image') !!}

                       <div class="input-group">
                           {!! Form::text('image', Input::old('image'), array('class' => 'form-control')) !!}
                         <span class="input-group-btn">
                           {!! Form::button('Browse Server', array('class' => 'btn btn-primary browse-server', 'id'=>'browse_image')) !!}
                         </span>
                       </div>
                     </div>

					{!! Form::submit('Save', array('class' => 'btn btn-primary')) !!}
                        <a href="{!! URL::previous() !!}" class="btn btn-default">Cancel</a>
            	    {!! Form::close() !!}

	      	</div>
      	</div>
      </div>
    </div>

<script type="text/javascript" src="{!! asset('/packages/dcms/core/ckeditor/ckeditor.js') !!}"></script>
<script type="text/javascript" src="{!! asset('/packages/dcms/core/ckeditor/adapters/jquery.js') !!}"></script>
<script type="text/javascript" src="{!! asset('/packages/dcms/core/ckfinder/ckfinder.js') !!}"></script>
<script type="text/javascript" src="{!! asset('/packages/dcms/core/ckfinder/ckbrowser.js') !!}"></script>

<script type="text/javascript">
$(document).ready(function() {

	//CKFinder for CKEditor
	CKFinder.setupCKEditor( null, '/packages/dcms/core/ckfinder/' );

	//CKEditor
	$("textarea[id='description']").ckeditor();

    //CKFinder
    $(".browse-server").click(function() {
        BrowseServer( 'Images:/', 'thumbnail' );
    })

  function setParentIDDropdown()
  {
    $("#nexttosiblingid").val('');
    $(".parent_id").hide();
    language_id = ($("input:checked").attr("id").replace("language_id",""));
    $(".parent_id.language_id"+language_id).show();

    @if(isset($category->id))
      $('#parent_id option[value="{{$category->id}}"]').hide();
    @endif
  }

  //HELP SETTING THE SIBLING SORT
  var a = [];
  $(".parent_id").each(function(index){
    before  =  $(this).attr('class').indexOf('parent-');
    after   =  $(this).attr('class').indexOf(' depth-');
    a.push($(this).attr('class').substr(before,(after-before)));
  })
  //console.log(a);

  u = jQuery.unique(a);
  u = jQuery.unique(u); //same again, since it seems to have got some issues
  //console.log(u);

  console.log(u);

  jQuery.each(u,function(theindex){
    sort = 0;
    $("."+u[theindex]).each(function(){
      sort = sort + 1;
      //help setting the current sort and the oldsort
      // the oldsort is hidden it is needed, for sorting
      @if(isset($category))
          if($(this).val() == {{$category->id}})
          {
            $("#oldsort").val(sort);
            $("#sort").val(sort);
          }
      @endif

      $(this).attr('class', $(this).attr('class') + " sort-"+sort);
    })
  })

  //every change happens in the sort value, we have to find the current "sibbling" of new/editid object - being on the given sort -
  // this way we know if we want the new on the left or right of that sibbling
  $("#sort").keyup(function(){
    if($("#parent_id option:selected").val() == "") parent = "parent-0"
    else parent = "parent-"+$("#parent_id option:selected").val()

    $("#nexttosiblingid").val($("."+parent +".sort-"+$(this).val()).val());
  })


  if(typeof $("input:checked").attr("id") == "undefined"){
    $(".parent_id").hide(); //hide everything when create a new, and no language_id has been checked
  } else {
    setParentIDDropdown(); //make the dropdown with the correct language_id vissible basicly hides some options in the dropdown
  }

  $("#parent_id").change(function(){$("#sort").val('');}) // remove the sort when changing the parent_id (value in the dropdown)

	$(".radiolanguage").change(function(){
      $("#parent_id option:selected").removeAttr("selected"); //remove any selection
      setParentIDDropdown(); //make the dropdown with the correct language_id vissible
    }
  );
  //END OF SETTING THE SIBLING SORT

	//Bootstrap Tabs
	$(".tab-container .nav-tabs a").click(function (e) {
		e.preventDefault();
		$(this).tab('show');
	});
});

</script>

<script type="text/javascript" src="{!! asset('packages/dcms/core/js/bootstrap.min.js') !!}"></script>
@stop
