@extends("dcms::template/layout")

@section("content")

    <div class="main-header">
      <h1>Categories</h1>
      <ol class="breadcrumb">
        <li><a href="{!! URL::to('admin/dashboard') !!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="{!! URL::to('admin/products') !!}"><i class="fa fa-pencil"></i> Products</a></li>
        <li class="active"><i class="fa fa-pencil"></i> Categories</li>
      </ol>
    </div>

    <div class="main-content">
    	<div class="row">
				<div class="col-md-12">
					<div class="main-content-block">

  @if (Session::has('message'))
    <div class="alert alert-info">{!! Session::get('message') !!}</div>
  @endif

    <div class="btnbar btnbar-right"><a class="btn btn-small btn-primary" href="{!! URL::to('/admin/products/categories/create') !!}">Create new</a></div>

    <h2>Overview</h2>


    <table id="datatable" class="table table-hover table-condensed" style="width:100%">
        <thead>
            <tr>
                <th>Category</th>
                <th>Country</th>
                <th></th>
            </tr>
        </thead>
    </table>

    <script type="text/javascript">
        $(document).ready(function() {
            oTable = $('#datatable').DataTable({
                "pageLength": 50,
                "processing": true,
                "serverSide": true,
                "ajax": "{{ route('admin.products.categories.api.table') }}",
                "columns": [
                    {data: 'category', name: 'node.title'},
                    {data: 'country', name: 'country', searchable: false},
                    {data: 'edit', name: 'edit', orderable: false, searchable: false}
                ]
            });
        });
    </script>

    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/plug-ins/be7019ee387/integration/bootstrap/3/dataTables.bootstrap.css">

    <script type="text/javascript" language="javascript" src="//cdn.datatables.net/1.10.0/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" language="javascript" src="//cdn.datatables.net/plug-ins/be7019ee387/integration/bootstrap/3/dataTables.bootstrap.js"></script>

	      	</div>
      	</div>
      </div>
    </div>

@stop
