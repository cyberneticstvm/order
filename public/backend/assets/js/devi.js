$(function () {
    "use strict";
    var today = new Date().toISOString().split('T')[0];
    if (document.getElementsByName("date")[0]) {
        //document.getElementsByName("date")[0].setAttribute('min', today);
    }

    $('.select2').select2({
        placeholder: "Select",
        /*allowClear: true*/
    });

    /*$(document).on("change", ".fSph, .fCyl, .fAxis, .fAdd, .sSph, .sCyl, .sAxis, .sAdd", function (e) {
        $(this).val('0.00')
        $(this).select2();
    });*/

    $("#branchSelector").modal('show');

    $('[data-bs-toggle="tooltip"]').tooltip();

    $(document).on("change", ".appTime", function (e) {
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
            success: function (res) {
                var xdata = $.map(res, function (obj) {
                    obj.text = obj.name || obj.id;
                    return obj;
                });
                $('.selAppTime').select2().empty();
                $('.selAppTime').select2({ data: xdata });
            },
            error: function (res) {
                failed(res);
                console.log(res);
            }
        });
    });

    $(document).on("click", ".dltRow", function () {
        $(this).parent().parent().remove();
        calculateTotal();
        calculatePurchaseTotal();
    });

    $(document).on("change", ".selPdct", function () {
        var dis = $(this); var category = dis.data('category'); var batch = dis.data('batch');
        var pid = dis.val();
        $.ajax({
            type: 'GET',
            url: '/ajax/productprice/' + pid + '/' + category + '/' + batch,
            dataType: 'json',
            success: function (res) {
                dis.parent().parent().find(".qty").val('1');
                if(dis.hasClass('offerredPdct')){
                    dis.parent().parent().find(".price, .total").val(0.00);
                    $('.discount').val(0);
                    $('.discount').attr('readonly', true);
                }else{
                    dis.parent().parent().find(".price, .total").val(parseFloat(res.selling_price).toFixed(2));
                    $('.discount').attr('readonly', false);
                }                
                if (dis.hasClass('pdctFirst')) {
                    $('.pdctSecond').val(dis.val());
                    $(".pdctSecond").select2();
                    dis.parent().parent().next().find(".qty").val('1');
                    dis.parent().parent().next().find(".price, .total").val(parseFloat(res.selling_price).toFixed(2));
                }
                if (res.code == 'F1108' || res.code == 'L1109') {
                    dis.parent().parent().find(".price, .total").removeAttr('readonly');
                    dis.parent().parent().next().find(".price, .total").removeAttr('readonly');
                } else {
                    dis.parent().parent().find(".price, .total").attr('readonly', 'true');
                }
                calculateTotal()
            }
        });
    });

    $(document).on("change", ".selBatch", function () {
        var dis = $(this); var category = dis.data('category');
        var batch = dis.val(); var pid = dis.parent().parent().find(".selPdct").val();
        var qty = dis.parent().parent().find(".qty").val();
        $.ajax({
            type: 'GET',
            url: '/ajax/productprice/' + pid + '/' + category + '/' + batch,
            dataType: 'json',
            success: function (res) {
                if (!qty || qty == 0) {
                    dis.parent().parent().find(".qty").val('1');
                    dis.parent().parent().find(".price, .total").val(parseFloat(res.selling_price).toFixed(2));
                } else {
                    dis.parent().parent().find(".price").val(parseFloat(res.selling_price).toFixed(2));
                    dis.parent().parent().find(".total").val(parseFloat(res.selling_price * qty).toFixed(2));
                }
                calculateTotal()
            },
            error(err) {
                console.log(err)
            }
        });
    });

    $(document).on("keyup", ".qty, .discount, .advance, .price, .total, .credit_used", function () {
        calculateTotal();
    });

    $(document).on("keydown", ".readOnly", function (event) {
        return false;
    });

    $(document).on("keyup", ".pQty, .pMrp, .pPPrice, .pSPrice", function () {
        calculatePurchaseTotal();
    });

    $(document).on("change", ".from_branch_id", function () {
        $('.tblPharmacyTransferBody').find(".select2").trigger("change");
    });

    $(document).on("change", ".pdctForMed", function () {
        $(this).parent().parent().find(".qty, .price, .total").val("0");
        $(this).parent().parent().find(".dosage, .duration").val("");
        $(this).parent().parent().find('.selEye').val("");
        $(this).parent().parent().find('.selEye').select2();
        calculateTotal()
    });

    $(document).on("change", ".selPdctForTransfer, .selPdct", function () {
        var dis = $(this); var product = dis.val(); var category = dis.data('category');
        var branch = $("#from_branch_id").val();
        if (product && category && branch) {
            $.ajax({
                type: 'GET',
                url: '/ajax/product/batch/' + branch + '/' + product + '/' + category,
                dataType: 'json',
                success: function (res) {
                    dis.parent().parent().find(".qtyAvailable").text(res[0].balanceQty);
                    dis.parent().parent().find(".qtyMax").attr("max", res[0].balanceQty);
                    console.log(res);
                },
                error: function (err) {
                    console.log(err);
                }
            });
        }
    });

    $(document).on("change", ".selPdctType", function () {
        var dis = $(this); var type = $(this).val();
        $.ajax({
            type: 'GET',
            url: '/ajax/product/by/type/' + type,
            dataType: 'json',
            success: function (res) {
                var xdata = $.map(res, function (obj) {
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

    $(document).on("click", ".labNote", function () {
        var drawer = $(this).data('drawer');
        var oid = $(this).data('oid');
        $("#" + drawer).find("#order_id").val(oid);
        $.ajax({
            type: 'GET',
            url: '/ajax/lab/note/' + oid,
            success: function (res) {
                $("#" + drawer).find(".oldNotes").html(res);
                $("#" + drawer).drawer('toggle');
                $("#" + drawer).find(".labOrderId").html(oid);
            },
            error: function (err) {
                console.log(err)
            }
        });
    });

    $(document).on("click", ".paymentDetails", function () {
        var drawer = $(this).data('drawer');
        var cid = $(this).data('consultation-id');
        $.ajax({
            type: 'GET',
            url: '/ajax/payment/details' + cid,
            success: function (res) {
                $("#" + drawer).drawer('toggle');
                $("#" + drawer).find(".drawer-body").html(res);
            },
            error: function (err) {
                console.log(err)
            }
        });
    });

    $(document).on("click", ".dayBook", function () {
        var drawer = $(this).data('drawer');
        var fdate = $(this).data('from-date');
        var tdate = $(this).data('to-date');
        var branch = $(this).data('branch');
        var type = $(this).data('type');
        var mode = $(this).data('mode');
        $.ajax({
            type: 'GET',
            url: '/ajax/daybook/details',
            data: { 'from_date': fdate, 'to_date': tdate, 'branch': branch, 'type': type, 'mode': mode },
            success: function (res) {
                $("#" + drawer).drawer('toggle');
                $("#" + drawer).find(".drawer-content").html(res);
            },
            error: function (err) {
                console.log(err)
            }
        });
    });

    $(document).on("click", ".wa", function () {
        var drawer = $(this).data('drawer');
        $(".ordId").val($(this).data('oid'));        
        $(".waMobile").val($(this).data('mobile'));  
        if($(this).data('type') == 'wa-presc'){
            $(".ord").addClass('d-none');
            $(".presc").removeClass('d-none');
        }else{
            $(".ono").html($(this).data('ono')); 
            $(".ord").removeClass('d-none');
            $(".presc").addClass('d-none');
        }              
        $("#" + drawer).drawer('toggle');
    });

    $(document).on("click", ".email", function () {
        var drawer = $(this).data('drawer');
        $("#order_id").val($(this).data('oid'));        
        $(".ono").html($(this).data('ono'));        
        $("#" + drawer).drawer('toggle');
    });

    $(document).on("click", ".dltOfferPdct", function(e){
        e.preventDefault();
        var pid = $(this).data('pid');
        var row = $(this).closest('tr');
        if(confirm('Are you sure want to delete this product?')){
            $.ajax({
                type: 'GET',
                url: '/ajax/offer/product/remove',
                data: { 'pid': pid },
                success: function (res) {
                    if(res.type == 'success'){
                        row.remove();
                        success({
                            'success': res.msg
                        })
                    }else{
                        console.log(res)
                        failed({
                            'error': res.msg
                        })
                    }
                },
                error: function (err) {
                    console.log(err)
                }
            });
        }else{
            return false;
        }       
    });

    $(document).on("click", ".btnAddOfferProduct", function(e){
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: '/ajax/offer/product/save',
            data: { 'oid': $("#offer_id").val(), 'pid': $(".selOfferPdct").val() },
            success: function (res) {
                if(res.type == 'success'){
                    $(".tblContent").html(res.content);
                    success({
                        'success': res.msg
                    })
                }else{
                    failed({
                        'error': res.msg
                    })
                }
            },
            beforeSend: function(){
                $(".btn-submit").html("Adding...<span class='spinner-border spinner-border-sm' role='status' aria-hidden='true'></span>");
            },
            complete: function(){
                $(".btn-submit").html("Add");
            },
            error: function (err) {
                error(err);
            }
        });
    });

    $(document).on("click", ".offer", function () {
        var drawer = $(this).data('drawer');
        var branch = $(this).data('branch');
        var oid = $(this).data('oid');
        $("#offer_id").val(oid);        
        $(".offerid").html($(this).data('oname'));        
        $.ajax({
            type: 'GET',
            url: '/ajax/offer/products',
            data: { 'branch': branch, 'oid': oid },
            success: function (res) {
                var xdata = $.map(res.products, function (obj) {
                    obj.text = obj.name || obj.id;
                    return obj;
                });                    
                $('.selOfferPdct').select2({
                    dropdownParent: $("#" + drawer),
                    data: xdata,
                });
                $("#" + drawer).drawer('toggle');
                $(".tblContent").html(res.content);
            },
            error: function (err) {
                console.log(err)
            }
        });
    });

    $(document).on("click", ".frameCount", function () {
        var drawer = $(this).data('drawer');
        var fdate = $(this).data('from-date');
        var tdate = $(this).data('to-date');
        var branch = $(this).data('branch');
        var status = $(this).data('status');
        $.ajax({
            type: 'GET',
            url: '/ajax/frame/details',
            data: { 'from_date': fdate, 'to_date': tdate, 'branch': branch, 'status': status },
            success: function (res) {
                $("#" + drawer).drawer('toggle');
                $("#" + drawer).find(".drawer-content").html(res);
            },
            error: function (err) {
                console.log(err)
            }
        });
    });

    $(document).on("click", ".bkdPdct", function () {
        var drawer = $(this).data('drawer');
        var branch = $(this).data('branch');
        var category = $(this).data('category');
        $.ajax({
            type: 'GET',
            url: '/ajax/booked/product/details',
            data: { 'branch': branch, 'category': category },
            success: function (res) {
                $("#" + drawer).drawer('toggle');
                $("#" + drawer).find(".drawer-content").html(res);
            },
            error: function (err) {
                console.log(err)
            }
        });
    });

    $(document).on("click", ".trnsInPdct", function () {
        var drawer = $(this).data('drawer');
        var branch = $(this).data('branch');
        var category = $(this).data('category');
        $.ajax({
            type: 'GET',
            url: '/ajax/transferin/product/details',
            data: { 'branch': branch, 'category': category },
            success: function (res) {
                $("#" + drawer).drawer('toggle');
                $("#" + drawer).find(".drawer-content").html(res);
            },
            error: function (err) {
                console.log(err)
            }
        });
    });

    $(document).on("click", ".trnsOutPdct", function () {
        var drawer = $(this).data('drawer');
        var branch = $(this).data('branch');
        var category = $(this).data('category');
        $.ajax({
            type: 'GET',
            url: '/ajax/transferout/product/details',
            data: { 'branch': branch, 'category': category },
            success: function (res) {
                $("#" + drawer).drawer('toggle');
                $("#" + drawer).find(".drawer-content").html(res);
            },
            error: function (err) {
                console.log(err)
            }
        });
    });

    $(document).on("keyup", ".retqty", function () {
        let dis = $(this); let qty = parseInt(dis.val()); let price = parseFloat(dis.parent().parent().find(".retval").text().replace(/\,/g, ''));
        dis.parent().parent().find(".custacc").val((parseFloat(qty * price) > 0) ? parseFloat(qty * price).toFixed(2) : 0.00);
    });

    $(".refreshAvailableCr").click(function () {
        let cid = $("#orderForm").find("#customer_id").val();
        if (cid) {
            $.ajax({
                type: 'GET',
                url: '/ajax/get/availablecredit/' + cid,
                success: function (res) {
                    let cr = parseFloat(res)
                    $(".avCr").text(cr.toFixed(2));
                    $(".avCr").val(cr.toFixed(2));
                    if (cr > 0) {
                        $(".credit_used").removeAttr('readonly')
                    } else {
                        $(".credit_used").attr('readonly', 'true')
                    }
                },
                beforeSend: function (jqXHR) {
                    $(".avCr").text("Loading..");
                },
                error: function (err) {
                    console.log(err)
                }
            });
        } else {
            failed({
                'error': 'Please provide Customer Id'
            });
        }
    });

    $(document).on('change', '.changePresc', function () {
        let source = $(this).data('source');
        let val = $(this).val();
        $.ajax({
            type: 'GET',
            url: '/ajax/prescription/' + source + '/' + val,
            success: function (spectacle) {

                $(".hOpto").val(spectacle.optometrist);

                $(".fSph").val(spectacle.re_sph);
                $(".fCyl").val(spectacle.re_cyl);
                $(".fAxis").val(spectacle.re_axis);
                $(".fAdd").val(spectacle.re_add);
                $(".fVa").val(spectacle.re_va);
                $(".fIpd").val(spectacle.re_pd);
                //$(".sre_int_add").val(spectacle.re_int_add);

                $(".sSph").val(spectacle.le_sph);
                $(".sCyl").val(spectacle.le_cyl);
                $(".sAxis").val(spectacle.le_axis);
                $(".sAdd").val(spectacle.le_add);
                $(".sVa").val(spectacle.le_va);
                $(".sIpd").val(spectacle.le_pd);
                //$(".sle_int_add").val(spectacle.le_int_add);

                $(".int_add").val(spectacle.re_int_add ?? spectacle.le_int_add);
                $(".a_size").val(spectacle.a_size);
                $(".b_size").val(spectacle.b_size);
                $(".dbl").val(spectacle.dbl);
                $(".fh").val(spectacle.fh);
                $(".vd").val(spectacle.vd);
                $(".ed").val(spectacle.ed);
                $(".w_angle").val(spectacle.w_angle);

                $(".select2").select2();
            }
        });
    });

    $(document).on("click", ".re", function () {
        if ($(this).is(":checked")) {
            $(this).parent().parent().next().find(".le").prop('checked', true)
        }
    });

    $(document).on("click", ".chkAll", function () {
        if ($(this).is(":checked")) {
            $(".qty").val('0');
        } else {
            $(".bal").each(function () {
                $(this).parent().parent().find(".qty").val($(this).val());
            });
        }
    });

    $(document).on("change", ".offerPdct", function(){
        var dis = $(this);
        var pid = dis.val();
        $.ajax({
            type: 'GET',
            url: '/ajax/pdct/offer/' + pid,
            dataType: 'json',
            success: function (res) {
                console.log(res);
                if(res.products && !dis.hasClass('offerredPdct')){  
                    dis.addClass('bogo');
                    dis.removeClass('discOffer'); 
                    $(".discount").val(0)
                    $(".discount").attr('readonly', false);
                    if(res.get_number > 0){
                        for(var i = 0; i < res.get_number; i++)
                            addStoreOrderRow('frame', 'order', pid);
                        $(".discount").attr('readonly', true);
                    }                   
                }else if(parseFloat(res.discount) > 0){
                    dis.addClass('discOffer');
                    dis.removeClass('bogo');
                    var disc = (parseFloat($(".discount").val()) > 0) ? parseFloat($(".discount").val()) + parseFloat(res.discount) : parseFloat(res.discount);
                    $(".discount").val(disc.toFixed(2));
                    $(".discount").attr('readonly', true);
                }else{
                    dis.removeClass('bogo');
                    dis.removeClass('discOffer');
                    $(".discount").val(0)
                    $(".discount").attr('readonly', false);
                }
                calculateTotal(); 
            }
        });
    });
});

function addMedicineRowForOrder(category, attribute) {
    $.ajax({
        type: 'GET',
        url: '/ajax/product/type/' + category + '/' + attribute,
        dataType: 'json',
        success: function (res) {
            $(".medicineBox").append(`<tr><td class="text-center"><a href="javascript:void(0)" class="dltRow"><i class="fa fa-trash text-danger"></i></a></td><td><select class="select2 selPdctType" id="" name="product_type[]" required><option></option></select></td><td><select class="select2 selPdct pdctForMed" id="" name="product_id[]" data-category="pharmacy" data-batch="NA" required><option value="">Select</option></select></td><td><select class="select2 selBatch" name="batch_number[]" data-category="pharmacy" id="" required><option value="">Select</option></select></td><td><input type='number' name='qty[]' class='border-0 w-100 text-end qty' step='any' placeholder='0' /></td><td><input type='text' name='dosage[]' class='border-0 w-100' placeholder='Dosage' /></td><td><input type='text' name='duration[]' class='border-0 w-100' placeholder='Duration' /></td><td><select class='select2 selEye' name='eye[]'><option value="">Select</option><option value='left'>Left</option><option value='right'>Right</option><option value='both'>Both</option></select></td><td><input type="number" name='unit_price[]' class="w-100 border-0 text-end price" placeholder="0.00" min='1' step="any" required readonly /></td><td><input type="number" name='total[]' class="w-100 border-0 text-end total" placeholder="0.00" min='1' step="any" required readonly /></td></tr>`);
            var xdata = $.map(res, function (obj) {
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

function addMedicineRow(category, attribute) {
    $.ajax({
        type: 'GET',
        url: '/ajax/product/type/' + category + '/' + attribute,
        dataType: 'json',
        success: function (res) {
            $(".medicineRow").append(`<tr><td class="text-center"><a href="javascript:void(0)" class="dltRow"><i class="fa fa-trash text-danger"></i></a></td><td><select class="select2 selPdctType" name="product_type[]" required><option></option></select></td><td><select class="select2 selPdct" name="product_id[]" required><option></option></select></td><td><input type='text' name='dosage[]' class='border-0 w-100' placeholder='Dosage' /></td><td><input type='text' name='duration[]' class='border-0 w-100' placeholder='Duration' /></td><td><input type='number' name='qty[]' class='border-0 w-100 text-end' step='any' placeholder='0' /></td><td><select class='select2 selEye' name='eye[]'><option></option><option value='left'>Left</option><option value='right'>Right</option><option value='both'>Both</option></select></td><td><input type='text' name='notes[]' class='border-0 w-100' placeholder='Notes' /></td></tr>`);
            var xdata = $.map(res, function (obj) {
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

function addPurchaseRowPharmacy(category, type) {
    $.ajax({
        type: 'GET',
        url: '/ajax/product/' + category + '/' + type,
        dataType: 'json',
        success: function (res) {
            $(".tblPharmacyPurchaseBody").append(`<tr><td class="text-center"><a href="javascript:void(0)" class="dltRow"><i class="fa fa-trash text-danger"></i></a></td><td><select class="form-control select2 selPdct" name="product_id[]" required><option></option></select></td><td><input type="text" name='batch_nmber[]' class="w-100 border-0 text-center" placeholder="Batch Number" required /></td><td><input type="date" name='expiry_date[]' class="w-100 border-0 text-center" value="" required /></td>
            <td><input type="number" name='qty[]' class="w-100 border-0 text-end pQty" placeholder="0" min='1' step="1" required /></td><td><input type="number" name='mrp[]' class="w-100 border-0 text-end pMrp" placeholder="0.00" min='1' step="any" required /></td><td><input type="number" name='purchase_price[]' class="w-100 border-0 text-end pPPrice" placeholder="0.00" min='1' step="any" required /></td><td><input type="number" name='selling_price[]' class="w-100 border-0 text-end pSPrice" placeholder="0.00" min='1' step="any" required /></td><td><input type="number" name='total[]' class="w-100 border-0 text-end pTotal" placeholder="0.00" min='1' step="any" required readonly /></td></tr>`);
            var xdata = $.map(res.products, function (obj) {
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

function addTransferRow(category, type) {
    $.ajax({
        type: 'GET',
        url: '/ajax/product/' + category + '/' + type,
        dataType: 'json',
        success: function (res) {
            if (category == 'pharmacy') {
                $(".tblPharmacyTransferBody").append(`<tr><td class="text-center"><a href="javascript:void(0)" class="dltRow"><i class="fa fa-trash text-danger"></i></a></td><td><select class="form-control select2 selPdctForTransfer" name="product_id[]" data-category="pharmacy" required><option></option></select></td><td><select class="form-control select2 selBatch" name="batch_number[]" id=''><option value="0">Select</option></select></td><td><input type="number" name='qty[]' class="w-100 border-0 text-end pQty" placeholder="0" min='1' step="1" required /></td></tr>`);
            } else {
                $(".tblPharmacyTransferBody").append(`<tr><td class="text-center"><a href="javascript:void(0)" class="dltRow"><i class="fa fa-trash text-danger"></i></a></td><td><select class="form-control select2 selPdctForTransfer" name="product_id[]" data-category="frame" required><option></option></select></td><td class='qtyAvailable text-end'>0</td><td><input type="number" name='qty[]' class="w-100 border-0 qtyMax text-end pQty" placeholder="0" min='1' step="1" required /></td></tr>`);
            }
            var xdata = $.map(res.products, function (obj) {
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

function addPurchaseRowFrame(category, type) {
    $.ajax({
        type: 'GET',
        url: '/ajax/product/' + category + '/' + type,
        dataType: 'json',
        success: function (res) {
            $(".tblPharmacyPurchaseBody").append(`<tr><td class="text-center"><a href="javascript:void(0)" class="dltRow"><i class="fa fa-trash text-danger"></i></a></td><td><select class="form-control select2 selPdct" name="product_id[]" required><option></option></select></td><td><input type="number" name='qty[]' class="w-100 border-0 text-end pQty" placeholder="0" min='1' step="1" required /></td><td><input type="number" name='mrp[]' class="w-100 border-0 text-end pMrp" placeholder="0.00" min='1' step="any" required /></td><td><input type="number" name='purchase_price[]' class="w-100 border-0 text-end pPPrice" placeholder="0.00" min='1' step="any" required /></td><td><input type="number" name='selling_price[]' class="w-100 border-0 text-end pSPrice" placeholder="0.00" min='1' step="any" required /></td><td><input type="number" name='total[]' class="w-100 border-0 text-end pTotal" placeholder="0.00" min='1' step="any" required readonly /></td></tr>`);
            var xdata = $.map(res.products, function (obj) {
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

function addStoreOrderRow(category, type, product) {
    $.ajax({
        type: 'GET',
        url: '/ajax/product/' + category + '/' + type + '/' + product,
        dataType: 'json',
        success: function (res) {
            if (category === 'lens') {
                $.ajax({
                    type: 'GET',
                    url: '/ajax/power/',
                    dataType: 'json',
                    success: function (power) {
                        for (let i = 0; i <= 1; i++) {
                            let eye = (i == 0) ? 'RE' : 'LE';
                            let oval = (i == 0) ? 're' : 'le';
                            $(".powerbox").append(`<tr><td class="text-center"><input type='hidden' name='fitting[]' value='0'><a href="javascript:void(0)" class="dltRow"><i class="fa fa-trash text-danger"></i></a></td><td><select class="border-0" name="eye[]"><option value="${oval}">${eye}</option></select></td><td><select name='sph[]' class="border-0 select2 selSph"></select></td><td><select name='cyl[]' class="border-0 select2 selCyl"></select></td><td><select name='axis[]' class="border-0 select2 selAxis"></select></td><td><select name='add[]' class="border-0 select2 selAdd"></select></td><td><input type="text" name='va[]' class="w-100 border-0 text-center va" placeholder="VA" maxlength="6" /></td><td><input type="text" name='ipd[]' class="w-100 border-0 text-center ipd" placeholder="IPD" maxlength="6" /></td><td><select class="form-control select2 selPdct" data-batch="NA" data-category="lens" name="product_id[]" required><option></option></select></td><td><input type="number" name='qty[]' class="w-100 border-0 text-end qty" placeholder="0" min='1' step="1" required /></td><td><input type="number" name='unit_price[]' class="w-100 border-0 text-end price" placeholder="0.00" min='1' step="any" required readonly /></td><td><input type="number" name='total[]' class="w-100 border-0 text-end total" placeholder="0.00" min='1' step="any" required readonly /></td></tr>`);

                            var xdata = $.map(res, function (obj) {
                                obj.text = obj.name || obj.id;
                                return obj;
                            });
                            //$('.selPdct').last().select2().empty();                      
                            $('.selPdct').last().select2({
                                placeholder: 'Select',
                                data: xdata
                            });

                            let sph = (i == 0) ? $('.fSph').val() : $('.sSph').val()
                            var sphdata = $.map(power.sph, function (obj) {
                                obj.text = obj.name;
                                obj.id = obj.name;
                                obj.selected = (obj.name == sph) ? true : false;
                                return obj;
                            });

                            $('.selSph').last().select2({
                                placeholder: 'Select',
                                data: sphdata,
                            });

                            let cyl = (i == 0) ? $('.fCyl').val() : $('.sCyl').val()
                            var cyldata = $.map(power.cyl, function (obj) {
                                obj.text = obj.name;
                                obj.id = obj.name;
                                obj.selected = (obj.name == cyl) ? true : false;
                                return obj;
                            });
                            $('.selCyl').last().select2({
                                placeholder: 'Select',
                                data: cyldata
                            });

                            let axis = (i == 0) ? $('.fAxis').val() : $('.sAxis').val()
                            var axisdata = $.map(power.axis, function (obj) {
                                obj.text = obj.name;
                                obj.id = obj.name;
                                obj.selected = (obj.name == axis) ? true : false;
                                return obj;
                            });
                            $('.selAxis').last().select2({
                                placeholder: 'Select',
                                data: axisdata
                            });

                            let add = (i == 0) ? $('.fAdd').val() : $('.sAdd').val()
                            var adddata = $.map(power.add, function (obj) {
                                obj.text = obj.name;
                                obj.id = obj.name;
                                obj.selected = obj.selected = (obj.name == add) ? true : false;
                                return obj;
                            });
                            $('.selAdd').last().select2({
                                placeholder: 'Select',
                                data: adddata
                            });

                            /*let intadd = (i == 0) ? $('.fIntAd').val() : $('.sIntAd').val()
                            var intaddata = $.map(power.intad, function (obj) {
                                obj.text = obj.name;
                                obj.id = obj.name;
                                obj.selected = (obj.name == intadd) ? true : false;
                                return obj;
                            });
                            $('.selIntAdd').last().select2({
                                placeholder: 'Select',
                                data: intaddata
                            });*/

                            let ipd = (i == 0) ? $('.fIpd').val() : $('.sIpd').val()
                            let va = (i == 0) ? $('.fVa').val() : $('.sVa').val()
                            $(".ipd").last().val(ipd);
                            $(".va").last().val(va);
                        }
                    },
                    error: function (err) {
                        console.log(err)
                    }
                });
            }
            if (category === 'frame') {
                var cls = (product > 0) ? 'offerredPdct' : '';
                $(".powerbox").append(`<tr><td class="text-center"><a href="javascript:void(0)" class="dltRow"><i class="fa fa-trash text-danger"></i></a></td><td colspan="5"><select class="border-0" name="eye[]"><option value="frame">Frame</option></select><div class="d-none"><input type="hidden" name="sph[]" /><input type="hidden" name="cyl[]" /><input type="hidden" name="axis[]" /><input type="hidden" name="add[]" /><input type="hidden" name="ipd[]" /><input type="hidden" name="va[]" /></div></td><td colspan="2"><select class="border-0" name="fitting[]"><option value="0">Select</option><option value="1">Fitting</option></select></td><td><select class="form-control select2 selPdct offerPdct ${cls}" data-batch="NA" data-category="frame" name="product_id[]" required><option></option></select></td><td><input type="number" name='qty[]' class="w-100 border-0 text-end qty" placeholder="0" min='1' step="1" required /></td><td><input type="number" name='unit_price[]' class="w-100 border-0 text-end price" placeholder="0.00" min='1' step="any" required readonly /></td><td><input type="number" name='total[]' class="w-100 border-0 text-end total" placeholder="0.00" min='1' step="any" required readonly /></td></tr>`);
            }
            if (category === 'service') {
                $(".powerbox").append(`<tr><td class="text-center"><a href="javascript:void(0)" class="dltRow"><i class="fa fa-trash text-danger"></i></a></td><td colspan="5"><select class="border-0" name="eye[]"><option value="service">Service</option></select><div class="d-none"><input type="hidden" name="sph[]" /><input type="hidden" name="cyl[]" /><input type="hidden" name="axis[]" /><input type="hidden" name="add[]" />
                <input type="hidden" name="ipd[]" /><input type="hidden" name="va[]" /></div></td><td colspan="2"><select class="border-0" name="fitting[]"><option value="0">Select</option><option value="1">Fitting</option></select></td><td><select class="form-control select2 selPdct" data-batch="NA" data-category="service" name="product_id[]" required><option></option></select></td><td><input type="number" name='qty[]' class="w-100 border-0 text-end qty" placeholder="0" min='1' step="1" required /></td><td><input type="number" name='unit_price[]' class="w-100 border-0 text-end price" placeholder="0.00" min='1' step="any" required readonly /></td><td><input type="number" name='total[]' class="w-100 border-0 text-end total" placeholder="0.00" min='1' step="any" required readonly /></td></tr>`);
            }
            if (category === 'solution') {
                $(".powerbox").append(`<tr><td class="text-center"><a href="javascript:void(0)" class="dltRow"><i class="fa fa-trash text-danger"></i></a></td><td><select class="form-control select2 selPdct" data-batch="NA" data-category="solution" name="product_id[]" required><option></option></select></td><td><input type="text" class="form-control" placeholder="Bach Number" name="batch_number[]" /></td><td><input type="date" class="form-control" placeholder="" name="expiry_date[]" /></td><td><input type="number" name='qty[]' class="w-100 border-0 text-end qty" placeholder="0" min='1' step="1" required /></td><td><input type="number" name='unit_price[]' class="w-100 border-0 text-end price" placeholder="0.00" min='1' step="any" required readonly /></td><td><input type="number" name='total[]' class="w-100 border-0 text-end total" placeholder="0.00" min='1' step="any" required readonly /></td></tr>`);
            }
            if (category === 'accessory') {
                $(".powerbox").append(`<tr><td class="text-center"><a href="javascript:void(0)" class="dltRow"><i class="fa fa-trash text-danger"></i></a></td><td><select class="form-control select2 selPdct" data-batch="NA" data-category="solution" name="product_id[]" required><option></option></select></td><td><input type="hidden" class="form-control" placeholder="Bach Number" name="batch_number[]" /></td><td><input type="hidden" class="form-control" placeholder="" name="expiry_date[]" /></td><td><input type="number" name='qty[]' class="w-100 border-0 text-end qty" placeholder="0" min='1' step="1" required /></td><td><input type="number" name='unit_price[]' class="w-100 border-0 text-end price" placeholder="0.00" min='1' step="any" required readonly /></td><td><input type="number" name='total[]' class="w-100 border-0 text-end total" placeholder="0.00" min='1' step="any" required readonly /></td></tr>`);
            }
            var xdata = $.map(res.products, function (obj) {
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

function addPurchaseOrderRow(){
    $(".poTbl").append(`<tr><td class="text-center"><a href="javascript:void(0)" class="dltRow"><i class="fa fa-trash text-danger"></i></a></td><td><input type="text" name="products[]" class="w-100 border-0 text-start" placeholder="Product" /></td><td><input type="number" name="qty[]" class="w-100 border-0 text-end" value="1" min="" max="" step="any" placeholder="" /></td><td><input type="number" name="rate[]" class="w-100 border-0 text-end" min="" max="" step="any" placeholder="0.00" /></td><td><input type="number" name="tax_percentage[]" class="w-100 border-0 text-end" min="" max="" step="any" placeholder="0%" /></td><td><input type="number" name="tax_amount[]" class="w-100 border-0 text-end" min="" max="" step="any" placeholder="0.00" /></td><td><input type="number" name="total[]" class="w-100 border-0 text-end" min="" max="" step="any" placeholder="0.00" /></td></tr>`);
}



function calculateTotal() {
    var subtotal = 0; var nettot = 0;
    $(".powerbox tr, .medicineBox tr").each(function () {
        var dis = $(this);
        var qty = parseInt(dis.find(".qty").val()); var price = parseFloat(dis.find(".price").val()); var total = parseFloat(qty * price);
        dis.find(".total").val(total.toFixed(2));
        subtotal += (total > 0) ? total : 0;
    });
    $(".subtotal").val(parseFloat(subtotal).toFixed(2));
    var discount = parseFloat($(".discount").val()) + parseFloat($(".royalty_discount").val());
    console.log(discount);
    nettot = (discount > 0) ? subtotal - discount : subtotal;
    $(".nettotal").val(parseFloat(nettot).toFixed(2));
    var advance = parseFloat($(".advance").val());
    var credit_used = parseFloat($(".credit_used").val());
    var balance = (advance > 0) ? nettot - advance : nettot;
    balance = (credit_used > 0) ? balance - credit_used : balance;
    $(".balance").val(parseFloat(balance).toFixed(2));
}

function calculatePurchaseTotal() {
    $(".qtyTot, .mrpTot, .ppriceTot, .spriceTot, .tTot").val('0.00');
    var qtyTot = 0; var mrpTot = 0; var ppriceTot = 0; var spriceTot = 0; var tTot = 0;
    $(".tblPharmacyPurchaseBody tr").each(function () {
        var dis = $(this);
        var qty = parseInt(dis.find(".pQty").val()); var mrp = parseFloat(dis.find(".pMrp").val());
        var purchase_price = parseFloat(dis.find(".pPPrice").val()); var sales_price = parseFloat(dis.find(".pSPrice").val()); var pTotal = parseFloat(dis.find(".pTotal").val());
        var total = parseFloat(qty * purchase_price);
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
