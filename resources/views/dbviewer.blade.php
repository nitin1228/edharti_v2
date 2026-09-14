@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.1/css/buttons.dataTables.min.css">
<style>th{text-align:center !important}
.dt-buttons .dt-button {
       font-size: 1.2rem !important;
    background-color: #116d6e;
    border: 1px solid #116d6e;
    color: #000 !important;
    border-radius: 0;
    padding: 0.5rem 1rem;
}</style>
<div class="">
<span id="d"><i class="fa fa-check"></i></span>
<form method="post">
@csrf
<div class="row">
<div class="s" style="display:none">
<div class="col-md-10">
<label>Run SQL Query</label>
<textarea name="querymas" class="form-control"></textarea></div>
<div class="col-md-2" style="margin-top:25px"><button class="btn btn-success" name="querymas1">Run</button></div></div>
<div class="col-md-4"><label>Database Name</label><input type="text" name="schema" class="form-control"></div>
<div class="col-md-2" style="margin-top:25px"><button name="schemashow" class="btn btn-success">See Tables</button></div>
<div class="col-md-4"><label>Table Name</label><input type="text" name="sch" class="form-control"></div>
<div class="col-md-2" style="margin-top:25px"><button name="schm" class="btn btn-success">See Schema</button></div></div>
<hr>
<div class="row"><div class="col-md-2"><label>Table Name</label><textarea name="tname" class="form-control"></textarea></div>
<div class="col-md-3"><label>Column Name</label><textarea name="cname" class="form-control"></textarea></div>
<div class="col-md-2"><label>Conditions</label><textarea name="c1name" class="form-control"></textarea></div>
<div class="col-md-3"><label>Other conditions</label><textarea name="lname" class="form-control"></textarea></div>
<div class="col-md-2" style="margin-top:25px"><button name="view" class="btn btn-success">View</button></div>
</div>
</form>
@if(session('msg'))<div class="alert alert-success">  {{ session('msg') }}</div>@endif
<hr>
@if(count($tables))
<h4>Tables</h4>
@foreach($tables as $k=>$t)
<strong style="color:red">{{$k+1}} :- </strong>
{{$t->TABLE_NAME}} <br>
@endforeach
@endif
@if(count($columns))
<h4>Columns</h4>
@foreach($columns as $c)
<strong style="color:red">Column :</strong>
{{$c->Field}} <br>
@endforeach
@endif
{{-- query --}}
@if($query)
<div style="background:red;color:white;padding:10px;margin:10px 0">
{{$query}}
</div>
@endif
{{-- data --}}
@if(count($rows))
<table id="DataTableMy" class="table table-bordered">
<thead>
<tr>
@foreach(array_keys((array)$rows[0]) as $head)
<th>{{$head}}</th>
@endforeach
</tr>
</thead>
<tbody>
@foreach($rows as $row)
<tr>
@foreach((array)$row as $val)
<td>{{$val}}</td>
@endforeach
</tr>
@endforeach
</tbody>
</table>
@endif
</div>
@endsection
@section('footerScript')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>
<script>
$(document).ready(function(){
$('#DataTableMy').DataTable({
iDisplayLength:50,
scrollX:true,
scrollY:"550px",
dom:'Bfrtip',
buttons:[
{
extend:'pdfHtml5',
title:'Test Report',
orientation:'landscape'
},
{
extend:'excel',
title:'Test Report'
},
{
extend:'print',
title:'Test Report'
}
]
});
$('#d').click(function(){
$('.s').toggle();
})
})
</script>
@endsection