<?php
use App\Helpers\InventoryHelper;
?>
@extends('layout.mainlayout')

@section('title', 'Incoming Payments')

@section('distinct_head')

<link href="<?=URL::to('/');?>/cdnjs.cloudflare.com/ajax/libs/datatables/1.10.15/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css">

@endsection

@section('body_class', 'header-light sidebar-dark sidebar-expandheader-light sidebar-dark sidebar-expand')

@section('content')
<main class="main-wrapper clearfix">
  <!-- Page Title Area -->
  <div class="row page-title clearfix">
    {{ Breadcrumbs::render('payment.incoming') }}
      <!-- /.page-title-right -->

      <!-- /.page-title-right -->
  </div>
  <!-- /.page-title -->
  <!-- =================================== -->
  <!-- Different data widgets ============ -->
  <!-- =================================== -->
  <div class="widget-list">
      <div class="row">
          <div class="col-md-12 widget-holder">
              <div class="widget-bg">
                  <div class="widget-heading clearfix">
                      <h5 class="border-b-light-1 w-100 pb-1 mt-0 mb-2">{{'Incoming Payments'}}</h5>
                  </div>
                  <!-- /.widget-heading -->
                    <div class="widget-body clearfix dataTable-length-top-0">

                      @if ($message = Session::get('success'))
                      <div class="alert alert-icon alert-success border-success alert-dismissible fade show" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <i class="material-icons list-icon">check_circle</i>
                        <strong>Success</strong>: {{ $message }}
                      </div>
                      @endif
                       @if (session('errors'))
                               <div class="alert alert-icon alert-danger border-danger alert-dismissible fade show" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  <i class="material-icons">highlight_off</i>
                                   <strong>error</strong>: {{ session('errors') }}
                              </div>
                             @endif


                       <table class=" incomingpayment  custom-scroll table table-striped table-center table-responsive" >
                          <thead>
                              <tr class="bg-primary">
                                  <th>No</th>
                                  <th>Customer Name</th>
                                  <th>Invoice Number</th>
                                  <th>Invoice Amount</th>
                                  <th>Due Date</th>
                                  <th>Payment Form</th>
                                  <th>Payment Header</th>
                                  <th>Created Date</th>
                                  <th>Action</th>

                                </tr>
                             </thead>
                             <tbody>
                              @foreach ($incoming as $value)
                                  <tr>

                                    <td>{{ ++$i }}</td>
                                    <td>{{ $value->customer_name}}</td>
                                    <td>{{ $value->invoice_number}}</td>
                                    <td> <?=CommonHelper::covertToCurrency($value->invoice_amount)?></td>
                                    <td>{{ $value->due_date}}</td>
                                    <td>{{ $value->payment_form}}</td>
                                     <td>
                                        {{ $value->name}}
                                    </td>
                                    <td>{{$value->created_at->format('Y-m-d')}}</td>
                                     <td>
                                        <a href="javascript:void(0)" class ="payment_model " data-id="{{$value->id}}" onclick=" " ><i class="material-icons"  title="Paid">credit_card</i></a>
                                        <a  href="{{action('PaymentController@paidtransaction',$value->id)}}"
                                          class="<?=($value->remaining_amount == NULL) ? 'disabled' : ''?>" onclick=" "><i class="material-icons"  title="View Transaction">remove_red_eye</i></a>
                                        <a class="color-content table-action-style" href="{{ route('accountpayment.pdflisting',['id'=>$value->id]) }}"><i class="material-icons md-18">file_download</i></a>
                                         <?php
$invoiceId = InventoryHelper::getInvoiceEntityId($value->invoice_number);
?>
                                         <?php if (InventoryHelper::isCashVoucherExist($invoiceId)): ?>
                                        <?php if (isset($value->payment_status) && $value->payment_status == 2): ?>

                                          <a href="javascript:void(0)" class ="btn-generate-cashvoucher <?=(InventoryHelper::isCashVoucherGenerated($invoiceId) ? 'disabled' : '')?>" data-id="{{$value->id}}" onclick=" " <?=(InventoryHelper::isCashVoucherGenerated($invoiceId) ? 'disabled' : '')?>><i class="material-icons"  title="Generate Cash Voucher">content_paste</i></a>
                                        <?php endif;?>
                                      <?php endif;?>
                                    </td>
                                </tr>
                            @endforeach
                          </tbody>
                          <!-- <tfoot>
                            <tr>
                              <th>No</th>
                                  <th>Customer Name</th>
                                  <th>Invoice Number</th>
                                  <th>Invoice Amount</th>
                                  <th>Due Date</th>
                                  <th>Payment Form</th>
                                  <th>Payment Header</th>
                                  <th>Action</th>
                            </tr>
                          </tfoot> -->
                      </table>
                  </div>
              </div>
               <div class="modal fade bs-modal-lg payment_popup modal-color-scheme" tabindex="-1" id="payment_popup" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
          </div>
      </div>
     </div>
 </main>
<div class="modal fade bs-modal-md" id="generate-cashvoucher-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            {!! Form::open(array('method'=>'POST','id'=>'generate-cashvoucher-form','class'=>'form-horizontal','autocomplete'=>'nope','enctype'=>'multipart/form-data')) !!}
            {{ Form::hidden('payment_id', '', array('id' => 'cash_voucher_payment_id')) }}
            <div class="modal-header">
                <button type="button" class="close p-0 m-0" data-dismiss="modal" aria-hidden="true">×</button>
                <h5 class="modal-title" id="myLargeModalLabel">Generate Cash Voucher</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="paymentstatus">Status <span class="text-danger">*</span></label>
                        <select class="form-control height-35" name="paymentstatus" id="paymentstatus" data-placeholder="Select">
                            <option value="">Select</option>
                            <option value="cash">Cash</option>
                            <option value="cheque">Cheque</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn-update-cashvoucher" class="btn btn-info ripple text-left">Submit</button>
                <button type="button" class="btn btn-danger ripple text-left" data-dismiss="modal">Close</button>
            </div>
            {!! Form::close() !!}
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

@endsection
@section('distinct_footer_script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.12.0/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.12.0/additional-methods.min.js"></script>

<script src="<?=URL::to('/');?>/cdnjs.cloudflare.com/ajax/libs/datatables/1.10.15/js/jquery.dataTables.min.js"></script>
 <script src="https://cdn.datatables.net/buttons/1.6.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.0/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.0/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.0/js/buttons.print.min.js"></script>
<script type="text/javascript">
  var buttonCommon = {
        exportOptions: {
            format: {
                body: function ( data, row, column, node ) {
                    if (column === 3) {
                      data = data.replace('₹ ', '');
                    }
                    return data;
                }
            }
        }
    };
$(document).ready(function(){
    $(document).on("click","#btn-update-cashvoucher", function(){
        var statusFlag = true;
        if($("#paymentstatus").val() == '')
        {
            $("#paymentstatus").after('<label id="paymentstatus-error" class="error" for="paymentstatus">Status is required</label>');
            statusFlag = false;
        }
        else
        {
            $("#paymentstatus-error").remove();
            statusFlag = true;
        }
        if(statusFlag == true)
        {
            $.ajax({
                type: 'post',
                url: '<?=URL::to('/payment/generatecashvoucher');?>',
                data: $("#generate-cashvoucher-form").serialize(),
                beforeSend: function(){
                    $("#btn-update-cashvoucher").prop("disabled",true);
                    showLoader();
                },
                success: function(response){
                  var res = JSON.parse(response);
                  $("#btn-update-cashvoucher").prop("disabled", false);
                  $("#generate-cashvoucher-modal").modal('hide');
                  if(res.status)
                  {
                      incomingTable.draw();
                      window.location.href = '<?=URL::to('/payment/viewcashvoucher/');?>/'+res.invoice_id;
                  }
                  else
                  {
                      swal({
                          title: 'Oops!',
                          text: res.message,
                          type: 'error',
                          showCancelButton: true,
                          showConfirmButton: false,
                          confirmButtonClass: 'btn btn-danger',
                          cancelButtonText: 'Ok'
                        });
                  }
                }
              });
        }
    });
    $(document).on("click", ".btn-generate-cashvoucher", function(){
      var paymentId = $(this).data('id');
      if(paymentId != '')
      {
          $("#cash_voucher_payment_id").val(paymentId);
          $("#paymentstatus").val('');
          $("#generate-cashvoucher-modal").modal('show');
      }
    });
    var incomingTable = $('.incomingpayment').DataTable({
        "dom":"<'row'<'col col-lg-3'l>><'row'<'col'B><'col'f>>" +
              "<'row'<'col-sm-12'tr>>"+"<'row'<'col-sm-5'i><'col-sm-7'p>>",

        "order": [1,"desc"],
          "language": {
        "infoEmpty": "No matched records found",
        "zeroRecords": "No matched records found",
        "emptyTable": "No data available in table",
        //"sProcessing": "<div id='loader'></div>"
      },
      "buttons": [
    $.extend( true, {}, buttonCommon, {
      extend: 'csv',
      footer: false,
      title: 'Approved_incoming_payments',
      className: "btn btn-primary btn-sm px-3",
      exportOptions: {
          columns: [0,1,2,3,4,5,6,7],
          orthogonal: 'export'
      }
    }),
    $.extend( true, {}, buttonCommon, {
      extend: 'excel',
      footer: false,
      title: 'Approved_incoming_payments',
      className: "btn btn-primary btn-sm px-3",
      exportOptions: {
          columns: [0,1,2,3,4,5,6,7],
          orthogonal: 'export'
      }
    })
  ],

      "deferLoading": <?=$totalcount?>,
      "processing": true,
      "serverSide": true,
      "serverMethod": "GET",
      "ajax":{
        "url": "{{action('PaymentController@payment_incomingresponse')}}",
        "data": function(data, callback){
          data._token = "{{ csrf_token() }}";
        },
      },
      "columnDefs": [ {
    "targets": [0,5,6,7,8],
    "orderable": false

    }
  ]
    });
    $('.incomingpayment').wrap('<div class="scroll-lg"></div>');
$(document).on('click','.payment_model',function() {

      var id = $(this).attr('data-id');

      if(id != '') {
                jQuery.ajax({
                type: "GET",
                dataType: "json",
                url: "{{action('PaymentController@getpayment')}}",
                data: {
                "_token": '{{ csrf_token() }}',
                "id": id,

                },
                success: function(data) {
                  console.log(data.result);
                  if(data.result == false)
                  {
                      window.location.href = '{{route('payment.paidpayment')}}';
                  }else{
                    $('.modal-content').html(data.html);
                    $('#payment_popup').modal('show');
                  }

                },

            });
        }
  });
  });
</script>
@endsection