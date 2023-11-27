$(function(){
    "use strict";
    var today = new Date().toISOString().split('T')[0];
    if(document.getElementsByName("date")[0])
        document.getElementsByName("date")[0].setAttribute('min', today);

    $(document).on('click','.dlt',function(e){
        e.preventDefault();
        var link = $(this).attr("href");
        Swal.fire({
            title: 'Are you sure want to delete this record?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
          }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = link
            }
          })
    });

    $('.select2').select2({
      placeholder: "Select",
      /*allowClear: true*/
    });

    $("#branchSelector").modal('show');

    $('[data-bs-toggle="tooltip"]').tooltip();

    $(document).on("change", ".appTime", function(e){
        e.preventDefault();
        var form = document.getElementById('frmAppointment');
        var formData = new FormData(form);
        $.ajax({
            type: 'POST',
            url: '/ajax/appointment/time',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(res){
                var xdata = $.map(res, function(obj){
                    obj.text = obj.name || obj.id;
                    return obj;
                });
                $('.selAppTime').select2().empty();            
                $('.selAppTime').select2({data:xdata});
            },
            error: function(res){
                failed(res);
                console.log(res);
            }
        });
    });
    $(document).on("click", ".dltRow", function(){
        $(this).parent().parent().remove();
        calculateTotal();
        calculatePurchaseTotal();
    });

    $(document).on("change", ".selPdct", function(){
        var dis = $(this); var category = dis.data('category'); var batch = dis.data('batch');
        var pid = dis.val();
        $.ajax({
            type: 'GET',
            url: '/ajax/productprice/'+pid+'/'+category+'/'+batch,
            dataType: 'json',
            success: function(res){
                dis.parent().parent().find(".qty").val('1');
                dis.parent().parent().find(".price, .total").val(parseFloat(res.selling_price).toFixed(2));
                calculateTotal()
            }
        });
    });

    $(document).on("change", ".selBatch", function(){
        var dis = $(this); var category = dis.data('category');
        var batch = dis.val(); var pid = dis.parent().parent().find(".selPdct").val();
        var qty = dis.parent().parent().find(".qty").val();
        $.ajax({
            type: 'GET',
            url: '/ajax/productprice/'+pid+'/'+category+'/'+batch,
            dataType: 'json',
            success: function(res){
                if(!qty || qty == 0){
                    dis.parent().parent().find(".qty").val('1');
                    dis.parent().parent().find(".price, .total").val(parseFloat(res.selling_price).toFixed(2));
                }else{
                    dis.parent().parent().find(".price").val(parseFloat(res.selling_price).toFixed(2));
                    dis.parent().parent().find(".total").val(parseFloat(res.selling_price*qty).toFixed(2));
                }
                calculateTotal()
            },
            error(err){
                console.log(err)
            }
        });
    });

    $(document).on("keyup", ".qty, .discount, .advance", function(){
        calculateTotal();
    });

    $(document).on("keydown", ".readOnly", function(event) { 
        return false;
    });

    $(document).on("keyup", ".pQty, .pMrp, .pPPrice, .pSPrice", function(){
        calculatePurchaseTotal();
    });

    $(document).on("change", ".from_branch_id", function(){
        $('.tblPharmacyTransferBody').find(".select2").trigger("change");
    });

    $(document).on("change", ".pdctForMed", function(){
        $(this).parent().parent().find(".qty, .price, .total").val("0");
        $(this).parent().parent().find(".dosage, .duration").val("");
        $(this).parent().parent().find('.selEye').val("");
        $(this).parent().parent().find('.selEye').select2();
        calculateTotal()
    });

    $(document).on("change", ".selPdctForTransfer, .selPdct", function(){
        var dis = $(this); var product = dis.val(); var category = dis.data('category');
        var branch = $("#from_branch_id").val();
        $.ajax({
            type: 'GET',
            url: '/ajax/product/batch/'+branch+'/'+product+'/'+category,
            dataType: 'json',
            success: function(res){
                if(category == 'pharmacy'){
                    let data;
                    data += `<option value=''>Select</option>`;
                    var xdata = $.map(res, function(obj){
                        data += `<option value="${obj.batch_number}">${obj.batch_number} (${obj.balanceQty} Qty Available)</option>`
                    });                     
                    dis.parent().parent().find('.selBatch').html(data);
                    dis.parent().parent().find('.selBatch').select2({
                        placeholder: 'Select'
                    });
                }else{
                    dis.parent().parent().find(".qtyAvailable").text(res[0].balanceQty);
                    dis.parent().parent().find(".qtyMax").attr("max", res[0].balanceQty);
                }
            }
        });
    });

    $(document).on("change", ".selPdctType", function(){
        var dis = $(this); var type = $(this).val();
        $.ajax({
            type: 'GET',
            url: '/ajax/product/by/type/'+type,
            dataType: 'json',
            success: function(res){                
                var xdata = $.map(res, function(obj){
                    obj.text = obj.name || obj.id;
                    return obj;
                });
                dis.parent().parent().find('.selPdct, .selBatch').select2().empty();                     
                dis.parent().parent().find('.selPdct, .selBatch').select2().append('<option></option>');                     
                dis.parent().parent().find('.selPdct').select2({
                    placeholder: 'Select',
                    data: xdata,
                });
            }
        });
    });

    $(document).on("click", ".paymentDetails", function(){
        var drawer = $(this).data('drawer');
        var cid = $(this).data('consultation-id'); 
        $.ajax({
            type: 'GET',
            url: '/ajax/payment/details/'+cid,
            success: function(res){
                $("#"+drawer).drawer('toggle');
                $("#"+drawer).find(".drawer-body").html(res);
            },
            error: function(err){
                console.log(err)
            }
        });
    });

    $(document).on("click", ".dayBook", function(){
        var drawer = $(this).data('drawer');
        var fdate = $(this).data('from-date');
        var tdate = $(this).data('to-date');
        var branch = $(this).data('branch');
        var type = $(this).data('type');
        $.ajax({
            type: 'GET',
            url: '/ajax/daybook/details/',
            data: {'from_date': fdate, 'to_date': tdate, 'branch': branch, 'type': type},
            success: function(res){
                $("#"+drawer).drawer('toggle');
                $("#"+drawer).find(".drawer-content").html(res);
            },
            error: function(err){
                console.log(err)
            }
        });
    });

});

function addMedicineRowForOrder(category, attribute){
    $.ajax({
        type: 'GET',
        url: '/ajax/product/type/'+category+'/'+attribute,
        dataType: 'json',
        success: function(res){
            $(".medicineBox").append(`<tr><td class="text-center"><a href="javascript:void(0)" class="dltRow"><i class="fa fa-trash text-danger"></i></a></td><td><select class="select2 selPdctType" id="" name="product_type[]" required><option></option></select></td><td><select class="select2 selPdct pdctForMed" id="" name="product_id[]" data-category="pharmacy" data-batch="NA" required><option value="">Select</option></select></td><td><select class="select2 selBatch" name="batch_number[]" data-category="pharmacy" id="" required><option value="">Select</option></select></td><td><input type='number' name='qty[]' class='border-0 w-100 text-end qty' step='any' placeholder='0' /></td><td><input type='text' name='dosage[]' class='border-0 w-100' placeholder='Dosage' /></td><td><input type='text' name='duration[]' class='border-0 w-100' placeholder='Duration' /></td><td><select class='select2 selEye' name='eye[]'><option value="">Select</option><option value='left'>Left</option><option value='right'>Right</option><option value='both'>Both</option></select></td><td><input type="number" name='unit_price[]' class="w-100 border-0 text-end price" placeholder="0.00" min='1' step="any" required readonly /></td><td><input type="number" name='total[]' class="w-100 border-0 text-end total" placeholder="0.00" min='1' step="any" required readonly /></td></tr>`);
            var xdata = $.map(res, function(obj){
                obj.text = obj.name || obj.id;
                return obj;
            });                     
            $('.selPdctType').last().select2({
                placeholder: 'Select',
                data: xdata
            });
            $('.selPdct').last().select2({
                placeholder: 'Select',
            });
            $('.selBatch').last().select2({
                placeholder: 'Select',
            });
            $('.selEye').last().select2({
                placeholder: 'Select',
            });
        }
    });
}

function addMedicineRow(category, attribute){
    $.ajax({
        type: 'GET',
        url: '/ajax/product/type/'+category+'/'+attribute,
        dataType: 'json',
        success: function(res){
            $(".medicineRow").append(`<tr><td class="text-center"><a href="javascript:void(0)" class="dltRow"><i class="fa fa-trash text-danger"></i></a></td><td><select class="select2 selPdctType" name="product_type[]" required><option></option></select></td><td><select class="select2 selPdct" name="product_id[]" required><option></option></select></td><td><input type='text' name='dosage[]' class='border-0 w-100' placeholder='Dosage' /></td><td><input type='text' name='duration[]' class='border-0 w-100' placeholder='Duration' /></td><td><input type='number' name='qty[]' class='border-0 w-100 text-end' step='any' placeholder='0' /></td><td><select class='select2 selEye' name='eye[]'><option></option><option value='left'>Left</option><option value='right'>Right</option><option value='both'>Both</option></select></td><td><input type='text' name='notes[]' class='border-0 w-100' placeholder='Notes' /></td></tr>`);
            var xdata = $.map(res, function(obj){
                obj.text = obj.name || obj.id;
                return obj;
            });                     
            $('.selPdctType').last().select2({
                placeholder: 'Select',
                data: xdata
            });
            $('.selPdct').last().select2({
                placeholder: 'Select',
            });
            $('.selEye').last().select2({
                placeholder: 'Select',
            });
        }
    });
}

function addPurchaseRowPharmacy(category){
    $.ajax({
        type: 'GET',
        url: '/ajax/product/'+category,
        dataType: 'json',
        success: function(res){
            $(".tblPharmacyPurchaseBody").append(`<tr><td class="text-center"><a href="javascript:void(0)" class="dltRow"><i class="fa fa-trash text-danger"></i></a></td><td><select class="form-control select2 selPdct" name="product_id[]" required><option></option></select></td><td><input type="text" name='batch_nmber[]' class="w-100 border-0 text-center" placeholder="Batch Number" required /></td><td><input type="date" name='expiry_date[]' class="w-100 border-0 text-center" value="" required /></td>
            <td><input type="number" name='qty[]' class="w-100 border-0 text-end pQty" placeholder="0" min='1' step="1" required /></td><td><input type="number" name='mrp[]' class="w-100 border-0 text-end pMrp" placeholder="0.00" min='1' step="any" required /></td><td><input type="number" name='purchase_price[]' class="w-100 border-0 text-end pPPrice" placeholder="0.00" min='1' step="any" required /></td><td><input type="number" name='selling_price[]' class="w-100 border-0 text-end pSPrice" placeholder="0.00" min='1' step="any" required /></td><td><input type="number" name='total[]' class="w-100 border-0 text-end pTotal" placeholder="0.00" min='1' step="any" required readonly /></td></tr>`);
            var xdata = $.map(res, function(obj){
                obj.text = obj.name || obj.id;
                return obj;
            });
            //$('.selPdct').last().select2().empty();                      
            $('.selPdct').last().select2({
                placeholder: 'Select',
                data: xdata
            });
        }
    });
}

function addTransferRow(category){
    $.ajax({
        type: 'GET',
        url: '/ajax/product/'+category,
        dataType: 'json',
        success: function(res){
            if(category == 'pharmacy'){
                $(".tblPharmacyTransferBody").append(`<tr><td class="text-center"><a href="javascript:void(0)" class="dltRow"><i class="fa fa-trash text-danger"></i></a></td><td><select class="form-control select2 selPdctForTransfer" name="product_id[]" data-category="pharmacy" required><option></option></select></td><td><select class="form-control select2 selBatch" name="batch_number[]" id=''><option value="0">Select</option></select></td><td><input type="number" name='qty[]' class="w-100 border-0 text-end pQty" placeholder="0" min='1' step="1" required /></td></tr>`);                
            }else{
                $(".tblPharmacyTransferBody").append(`<tr><td class="text-center"><a href="javascript:void(0)" class="dltRow"><i class="fa fa-trash text-danger"></i></a></td><td><select class="form-control select2 selPdctForTransfer" name="product_id[]" data-category="frame" required><option></option></select></td><td class='qtyAvailable text-end'>0</td><td><input type="number" name='qty[]' class="w-100 border-0 qtyMax text-end pQty" placeholder="0" min='1' step="1" required /></td></tr>`);
            }
            var xdata = $.map(res, function(obj){
                obj.text = obj.name || obj.id;
                return obj;
            });                      
            $('.selPdctForTransfer').last().select2({
                placeholder: 'Select',
                data: xdata
            });
            $(".selBatch").last().select2();
        }
    });
}

function addPurchaseRowFrame(category){
    $.ajax({
        type: 'GET',
        url: '/ajax/product/'+category,
        dataType: 'json',
        success: function(res){
            $(".tblPharmacyPurchaseBody").append(`<tr><td class="text-center"><a href="javascript:void(0)" class="dltRow"><i class="fa fa-trash text-danger"></i></a></td><td><select class="form-control select2 selPdct" name="product_id[]" required><option></option></select></td><td><input type="number" name='qty[]' class="w-100 border-0 text-end pQty" placeholder="0" min='1' step="1" required /></td><td><input type="number" name='mrp[]' class="w-100 border-0 text-end pMrp" placeholder="0.00" min='1' step="any" required /></td><td><input type="number" name='purchase_price[]' class="w-100 border-0 text-end pPPrice" placeholder="0.00" min='1' step="any" required /></td><td><input type="number" name='selling_price[]' class="w-100 border-0 text-end pSPrice" placeholder="0.00" min='1' step="any" required /></td><td><input type="number" name='total[]' class="w-100 border-0 text-end pTotal" placeholder="0.00" min='1' step="any" required readonly /></td></tr>`);
            var xdata = $.map(res, function(obj){
                obj.text = obj.name || obj.id;
                return obj;
            });
            //$('.selPdct').last().select2().empty();                      
            $('.selPdct').last().select2({
                placeholder: 'Select',
                data: xdata
            });
        }
    });
}

function addStoreOrderRow(category){
    $.ajax({
        type: 'GET',
        url: '/ajax/product/'+category,
        dataType: 'json',
        success: function(res){
            if(category === 'lens'){
                $(".powerbox").append(`<tr><td class="text-center"><a href="javascript:void(0)" class="dltRow"><i class="fa fa-trash text-danger"></i></a></td><td><select class="border-0" name="eye[]"><option value="re">RE</option><option value="le">LE</option><option value="both">Both</option></select></td><td><input type="text" name='sph[]' class="w-100 border-0 text-center" placeholder="SPH" maxlength="6" /></td><td><input type="text" name='cyl[]' class="w-100 border-0 text-center" placeholder="CYL" maxlength="6" /></td><td><input type="number" name='axis[]' class="w-100 border-0 text-center" placeholder="AXIS" step="any" max="360" /></td><td><input type="text" name='add[]' class="w-100 border-0 text-center" placeholder="ADD" maxlength="6" /></td><td><input type="text" name='dia[]' class="w-100 border-0 text-center" placeholder="DIA" maxlength="6" /></td><td><select class="border-0" name="thickness[]"><option value="not-applicable">Not applicable</option><option value="thin">Thin</option><option value="maximum-thin">Maximum Thin</option><option value="normal-thick">Normal Thick</option></select></td><td><input type="text" name='ipd[]' class="w-100 border-0 text-center" placeholder="IPD" maxlength="6" /></td><td><select class="form-control select2 selPdct" data-batch="NA" data-category="lens" name="product_id[]" required><option></option></select></td><td><input type="number" name='qty[]' class="w-100 border-0 text-end qty" placeholder="0" min='1' step="1" required /></td><td><input type="number" name='unit_price[]' class="w-100 border-0 text-end price" placeholder="0.00" min='1' step="any" required readonly /></td><td><input type="number" name='total[]' class="w-100 border-0 text-end total" placeholder="0.00" min='1' step="any" required readonly /></td></tr>`);
            }
            if(category === 'frame'){
                $(".powerbox").append(`<tr><td class="text-center"><a href="javascript:void(0)" class="dltRow"><i class="fa fa-trash text-danger"></i></a></td><td colspan="8"><select class="border-0" name="eye[]"><option value="frame">Frame</option></select><div class="d-none"><input type="hidden" name="sph[]" /><input type="hidden" name="cyl[]" /><input type="hidden" name="axis[]" /><input type="hidden" name="add[]" /><input type="hidden" name="dia[]" /><input type="hidden" name="ipd[]" /><input type="hidden" name="thickness[]" /></div></td><td><select class="form-control select2 selPdct" data-batch="NA" data-category="frame" name="product_id[]" required><option></option></select></td><td><input type="number" name='qty[]' class="w-100 border-0 text-end qty" placeholder="0" min='1' step="1" required /></td><td><input type="number" name='unit_price[]' class="w-100 border-0 text-end price" placeholder="0.00" min='1' step="any" required readonly /></td><td><input type="number" name='total[]' class="w-100 border-0 text-end total" placeholder="0.00" min='1' step="any" required readonly /></td></tr>`);
            }
            if(category === 'service'){
                $(".powerbox").append(`<tr><td class="text-center"><a href="javascript:void(0)" class="dltRow"><i class="fa fa-trash text-danger"></i></a></td><td colspan="8"><select class="border-0" name="eye[]"><option value="service">Service</option></select><div class="d-none"><input type="hidden" name="sph[]" /><input type="hidden" name="cyl[]" /><input type="hidden" name="axis[]" /><input type="hidden" name="add[]" />
                <input type="hidden" name="dia[]" /><input type="hidden" name="ipd[]" /><input type="hidden" name="thickness[]" /></div></td><td><select class="form-control select2 selPdct" data-batch="NA" data-category="service" name="product_id[]" required><option></option></select></td><td><input type="number" name='qty[]' class="w-100 border-0 text-end qty" placeholder="0" min='1' step="1" required /></td><td><input type="number" name='unit_price[]' class="w-100 border-0 text-end price" placeholder="0.00" min='1' step="any" required readonly /></td><td><input type="number" name='total[]' class="w-100 border-0 text-end total" placeholder="0.00" min='1' step="any" required readonly /></td></tr>`);
            }
            var xdata = $.map(res, function(obj){
                obj.text = obj.name || obj.id;
                return obj;
            });
            //$('.selPdct').last().select2().empty();                      
            $('.selPdct').last().select2({
                placeholder: 'Select',
                data: xdata
            });
        }
    });
}

function calculateTotal(){
    var subtotal = 0; var nettot = 0;
    $(".powerbox tr, .medicineBox tr").each(function(){ 
        var dis = $(this); 
        var qty = parseInt(dis.find(".qty").val()); var price = parseFloat(dis.find(".price").val()); var total = parseFloat(qty*price);
        dis.find(".total").val(total.toFixed(2));
        subtotal += (total > 0) ? total: 0;
    });
    $(".subtotal").val(parseFloat(subtotal).toFixed(2));
    var discount = parseFloat($(".discount").val());
    nettot = (discount > 0) ? subtotal - discount : subtotal;
    $(".nettotal").val(parseFloat(nettot).toFixed(2));
    var advance = parseFloat($(".advance").val());
    var balance = (advance > 0) ? nettot-advance : nettot;
    $(".balance").val(parseFloat(balance).toFixed(2));
}

function calculatePurchaseTotal(){
    $(".qtyTot, .mrpTot, .ppriceTot, .spriceTot, .tTot").val('0.00');
    var qtyTot = 0; var mrpTot = 0; var ppriceTot = 0; var spriceTot = 0; var tTot = 0;
    $(".tblPharmacyPurchaseBody tr").each(function(){ 
        var dis = $(this); 
        var qty = parseInt(dis.find(".pQty").val()); var mrp = parseFloat(dis.find(".pMrp").val());
        var purchase_price = parseFloat(dis.find(".pPPrice").val()); var sales_price = parseFloat(dis.find(".pSPrice").val()); var pTotal = parseFloat(dis.find(".pTotal").val());
        var total = parseFloat(qty*purchase_price);
        dis.find(".pTotal").val(total.toFixed(2));
        qtyTot += (qty > 0) ? qty : 0;
        mrpTot += (mrp > 0) ? mrp : 0;
        ppriceTot += (purchase_price > 0) ? purchase_price : 0;
        spriceTot += (sales_price > 0) ? sales_price : 0;
        tTot += (pTotal > 0) ? pTotal : 0;
        $(".qtyTot").val(parseInt(qtyTot));
        $(".mrpTot").val(parseFloat(mrpTot).toFixed(2));
        $(".ppriceTot").val(parseFloat(ppriceTot).toFixed(2));
        $(".spriceTot").val(parseFloat(spriceTot).toFixed(2));
        $(".tTot").val(parseFloat(tTot).toFixed(2));
    });
}
