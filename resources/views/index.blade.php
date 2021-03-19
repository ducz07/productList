@extends('layouts.master')

@section('content')

<div class="col-md-12 mt-1 mb-20" style="text-align: end;"><button type="button" id="addProduct" class="btn btn-primary">Add Product</button></div>
<div class="card">
<div class="card-header text-center font-weight-bold">
    <h2>Product List</h2>
</div>
<div class="card-body">
    <table class="table table-bordered" id="product_table">
        <thead>
            <tr>
                <th>Id</th>
                <th>Image</th>
                <th>Product name</th>
                <th>Description</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Uploaded by</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>
</div>

<div class="modal fade" id="product-modal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
        <h4 class="modal-title" id="productModalTitle"></h4>
        </div>
        <div class="modal-body">
        <form action="javascript:void(0)" id="productForm" name="productForm" class="form-horizontal" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" id="id">
            <input type="hidden" name="url" id="url" value="{{ route('product.index') }}">
            <div class="form-group">
            <label for="name" class="col-sm-4 control-label">Product Name</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="product_name" name="product_name" placeholder="Enter Product Name" maxlength="50" required="">
            </div>
            </div>  
            <div class="form-group">
            <label for="name" class="col-sm-2 control-label">Description</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="description" name="description" placeholder="Enter description" maxlength="50" required="">
            </div>
            </div>
            <div class="form-group">
            <label class="col-sm-2 control-label">Quantity</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="qty" name="qty" placeholder="Enter quantity" required="">
            </div>
            </div>    
            <div class="form-group">
            <label class="col-sm-2 control-label">Price</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="price" name="price" placeholder="Enter price" required="">
            </div>
            </div>          
            <div class="form-group">
            <label class="col-sm-4 control-label">Product Image</label>
            <div class="col-sm-6 pull-left">
                <input type="file" class="form-control" id="image" name="image" required="">
            </div>               
            <div class="col-sm-6 pull-right">
                <img id="preview-image" src="https://www.riobeauty.co.uk/images/product_image_not_found.gif"
                    alt="preview image" style="max-height: 250px;">
            </div>
            </div>
            <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-primary" id="btn-save" value="addNewProduct">Save changes
            </button>
            </div>
        </form>
        </div>
        <div class="modal-footer">
        
        </div>
    </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
     
 $(document).ready( function () {
    $.ajaxSetup({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $('#image').change(function(){
           
    let reader = new FileReader();
    reader.onload = (e) => { 
      $('#preview-image').attr('src', e.target.result); 
    }
    reader.readAsDataURL(this.files[0]); 
  
   });
   var url = $('#url').val();
   console.log(url);
   $('#product_table').DataTable({
           processing: true,
           serverSide: true,
           ajax: url,
           columns: [
                    {data: 'id', name: 'id', 'visible': false},
                    { data: 'image', name: 'image' , orderable: false},
                    { data: 'product_name', name: 'product_name' },
                    { data: 'description', name: 'description' },
                    { data: 'qty', name: 'qty' },
                    { data: 'price', name: 'price' },
                    { data: 'uploaded_by', name: 'uploaded_by' },
                    {data: 'action', name: 'action', orderable: false},
                 ],
          order: [[0, 'desc']]
    });
    $('#addProduct').click(function () {
       $('#productForm').trigger("reset");
       $('#productModalTitle').html("Add Product");
       $('#product-modal').modal('show');
       $("#image").attr("required", "true");
       $('#id').val('');
       $('#preview-image').attr('src', 'https://www.riobeauty.co.uk/images/product_image_not_found.gif');
    });
 
    $('body').on('click', '.edit', function () {
        var id = $(this).data('id');
         
        $.ajax({
            type:"POST",
            url: "{{ route('product.edit') }}",
            data: { id: id },
            dataType: 'json',
            success: function(res){
              $('#productModalTitle').html("Edit Product");
              $('#product-modal').modal('show');
              $('#id').val(res.id);
              $('#product_name').val(res.product_name);
              $('#description').val(res.description);
              $('#qty').val(res.qty);
              $('#price').val(res.price);
              $('#image').removeAttr('required');
           }
        });
    });

    $('body').on('click', '.delete', function () {
        var id = $(this).data('id');
        swal({
            title: "Delete Product?",
            text: "Are you sure you want to delete this product? Once deleted, you will not be able to recover it. Continue?",
            type: "warning",
            showCancelButton: true,
            showLoaderOnConfirm: true,
            confirmButtonText: "Yes, delete!",
            closeOnConfirm: false,
            confirmButtonColor: "#e11641"
        },
        () => {
            $.ajax({
                url: "{{ route('product.destroy') }}",
                type: "POST",
                dataType: 'JSON',
                data: { id: id },
                success: function(res) {
                    console.log(res);
                    if (res.status == 200) {
                        $('#product_table').DataTable().ajax.reload();
                        swal('Deleted!', res.msg, 'success');
                    } else {
                        swal('Oops!', res.msg, 'error');
                    }
                }
            })
        });
    });


   $('#productForm').submit(function(e) {
     e.preventDefault();
  
     var formData = new FormData(this);
  
     $.ajax({
        type:'POST',
        url: "{{ route('product.store')}}",
        data: formData,
        cache:false,
        contentType: false,
        processData: false,
        success: (data) => {
          $("#product-modal").modal('hide');
          var oTable = $('#product_table').dataTable();
          oTable.fnDraw(false);
          $("#btn-save").html('Submit');
          $("#btn-save"). attr("disabled", false);
        },
        error: function(data){
           console.log(data);
         }
       });
   });
});
</script>
@endpush