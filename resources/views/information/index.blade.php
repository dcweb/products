@extends("dcms::template/layout")

@section("content")

    <div class="main-header">
      <h1>Products Information</h1>
      <ol class="breadcrumb">
        <li><a href="{!! URL::to('admin/dashboard') !!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active"><i class="fa fa-shopping-cart"></i> Products</li>
      </ol>
    </div>

    <div class="main-content">
    	<div class="row">
				<div class="col-md-12">
					<div class="main-content-block">

  @if (Session::has('message'))
    <div class="alert alert-info">{!! Session::get('message') !!}</div>
  @endif

    <h2>Overview</h2>

    <table id="datatable" class="table table-hover table-condensed" style="width:100%">
        <thead>
            <tr>
                <th>GroupID</th>
                <th>Title</th>
                <th>Regio</th>
                <th>ID</th>
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
                "ajax": "{{ route('admin.products.information.api.table') }}",
                "columns": [
                    {data: 'id', name: 'ID'},
                    {data: 'title', name: 'title'},
                    {data: 'regio', name: 'regio', searchable: false},
                    {data: 'id', name: 'id'},
                    {data: 'edit', name: 'edit', orderable: false, searchable: false}
                ]
            });
        });
    </script>

    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/plug-ins/be7019ee387/integration/bootstrap/3/dataTables.bootstrap.css">

    <script type="text/javascript" language="javascript" src="//cdn.datatables.net/1.10.0/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" language="javascript" src="//cdn.datatables.net/plug-ins/be7019ee387/integration/bootstrap/3/dataTables.bootstrap.js"></script>



<script type="text/javascript">
$(document).ready(function() {

	$(document).on("click", ".delete", function(e) {
		bootbox.confirm("Are you sure?", function(result) {
			Example.show("Confirm result: "+result);
		});
	});

});
</script>

<script type="text/javascript" src="{!! asset('packages/dcms/core/assets/js/bootstrap.min.js') !!}"></script>
<script type="text/javascript" src="{!! asset('packages/dcms/core/assets/js/bootbox.min.js') !!}"></script>

	      	</div>
      	</div>
      </div>
    </div>

@stop
