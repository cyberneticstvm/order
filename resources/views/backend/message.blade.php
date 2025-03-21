<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('form').submit(function() {
            $(this).find(".btn-submit").attr("disabled", true);
            $(this).find(".btn-submit").html("Loading...<span class='spinner-border spinner-border-sm' role='status' aria-hidden='true'></span>");
        });

        setInterval(checkPendingTransfer, 90000);
    });
</script>
<script>
    const toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });
</script>
@if(session()->has('success'))
<script>
    toast.fire({
        icon: 'success',
        title: "{{ session()->get('success') }}",
        color: 'green'
    })
</script>
@endif
@if(session()->has('error'))
<script>
    toast.fire({
        icon: 'error',
        title: "{{ session()->get('error') }}",
        color: 'red'
    })
</script>
@endif
@if(session()->has('warning'))
<script>
    toast.fire({
        icon: 'warning',
        title: "{{ session()->get('warning') }}",
        color: 'orange'
    })
</script>
@endif
<script>
    function success(res) {
        toast.fire({
            icon: 'success',
            title: res.success,
            color: 'green'
        });
    }

    function failed(res) {
        toast.fire({
            icon: 'error',
            title: res.error,
            color: 'red'
        });
    }

    function error(err) {
        var msg = JSON.parse(err.responseText);
        toast.fire({
            icon: 'error',
            title: msg.message,
            color: 'red'
        });
    }

    function notify(msg) {
        toast.fire({
            icon: 'warning',
            title: msg,
            color: 'orange'
        });
    }

    function checkPendingTransfer() {
        $.ajax({
            url: '/ajax/check/pending/transfers',
            type: 'get',
            success: function(response) {
                if (response.message != '')
                    notify(response.message);
            }
        });
    }

    function validateOrderForm() {
        let frm = document.forms["orderForm"];
        let disc_per = parseInt(frm['disc_per'].value);
        let disc = parseFloat(frm['discount'].value);
        let royal_disc = parseFloat(frm['royalty_discount'].value);
        let ord_tot = parseFloat(frm['order_total'].value);
        let disc_allowed = parseFloat((ord_tot * disc_per) / 100);
        let offerproduct = $('#orderForm .offerPdct').length ?? 0;
        let offerredproduct = $('#orderForm .offerredPdct').length ?? 0;
        let discfferproduct = $('#orderForm .discOffer').length ?? 0;
        let bogo = $('#orderForm .bogo').length ?? 0;
        let bogoffer = offerredproduct - discfferproduct;

        if (disc > 0 && royal_disc > 0 || royal_disc > 0 && offerredproduct > 0) {
            failed({
                'error': 'It seems like you have applied multiple offers in single order. Please recheck and try again'
            })
            return false;
        }

        if (bogoffer > 0 && discfferproduct > 0) {
            failed({
                'error': 'Only one offer is allowed for one order'
            })
            return false;
        }
        if (bogoffer > 0 && disc > 0) {
            failed({
                'error': 'Please remove discount'
            })
            return false;
        }
        if (bogo > 0 && offerredproduct == 0 && discfferproduct == 0) {
            failed({
                'error': 'Please add an offer product'
            })
            return false;
        }
        if (frm['advance'].value > 0 && frm['payment_mode'].value == '') {
            failed({
                'error': 'Please select advance payment mode!'
            })
            return false;
        }
        if (frm['order_date'].value == frm['expected_delivery_date'].value) {
            let c = confirm("Expected delivery date and order date are same. proceed?")
            if (!c) return false
        }
        if (parseFloat(disc) > parseFloat(disc_allowed) && "{{ Auth::user()?->roles?->first()?->name }}" != 'Administrator') {
            failed({
                'error': 'Discount amount is greater than allowed'
            })
            return false;
        }
        if (frm['spectacle_id'].value == '') {
            let c = confirm("You haven't entered any prescription details. proceed?")
            if (!c) return false
        }
        if (parseFloat(frm['credit_used'].value) > parseFloat(frm['available_credit'].value)) {
            failed({
                'error': 'Credit used is greater than available credit!'
            })
            return false;
        }
        return true;
    }

    function validateTransferForm() {
        let frm = document.forms["transferForm"];
        if (frm['from_branch_id'].value == frm['to_branch_id'].value) {
            failed({
                'error': 'From branch and To branch should not be same!'
            })
            return false;
        }
        return true;
    }

    function validateLabOrderForm() {
        let frm = document.forms["labForm"];
        if (frm['lab_id'].value == '') {
            failed({
                'error': 'Please select a Lab'
            })
            return false;
        }
        if (!$(".chkItem").is(":checked")) {
            failed({
                'error': 'Please select at least one order'
            })
            return false;
        }
        return true;
    }

    function validateLabStatusOrderForm() {
        let frm = document.forms["labStatusForm"];
        if (frm['status'].value == '') {
            failed({
                'error': 'Please select Status'
            })
            return false;
        }
        if (frm['status'].value == 'sent-to-lab' && frm['lab_id'].value == '') {
            failed({
                'error': 'Please select a Lab'
            })
            return false;
        }
        if (!$(".chkItem").is(":checked")) {
            failed({
                'error': 'Please select at least one Item'
            })
            return false;
        }
        let c = confirm("Are you sure want to proceed?")
        if (!c) {
            return false;
        }
        return true;
    }

    function validateStockOrderForm() {
        let frm = document.forms["stockForm"];
        if (!$(".chk").is(":checked")) {
            failed({
                'error': 'Please select at least one Item'
            })
            return false;
        }
        return true;
    }

    function validateRoyaltyCard() {
        let frm = document.forms["orderForm"];
        let card_type = frm['card_type'].value;
        let card_number = frm['card_number'].value;
        let disc = parseFloat(frm['discount'].value);
        let pcount = parseInt($('#orderForm .offerredPdct').length);
        let order_total = parseFloat(frm['order_total'].value);
        let products = $('[name="product_id[]"]').map(function() {
            return $(this).val();
        }).get();
        let pdctTotal = $('[name="total[]"]').map(function() {
            return $(this).val();
        }).get();

        if (!card_type || !card_number) {
            $(".royalty_discount").val(0.00);
            failed({
                'error': 'Card Type and Card Number should not be blank'
            })
        } else {
            if (disc > 0) {
                $(".royalty_discount").val(0.00);
                failed({
                    'error': 'Some discounts are already applied. Remove it and try again'
                })
            } else if (pcount > 0) {
                $(".royalty_discount").val(0.00);
                failed({
                    'error': 'Some offer products are already applied. Remove it and try again'
                })
            } else {
                $.ajax({
                    url: '/ajax/validate/royalcard',
                    type: 'post',
                    data: {
                        'ctype': card_type,
                        'cnumber': card_number,
                        'order_total': order_total,
                        'products': products,
                        'pdctTotal': pdctTotal,
                    },
                    success: function(response) {
                        if (response.type == 'success') {
                            success({
                                'success': response.message
                            })
                        } else if (response.type == 'warning') {
                            notify(response.message);
                        } else {
                            failed({
                                'error': response.message
                            })
                        }
                        $(".royalty_discount").val(parseFloat(response.discount).toFixed(2));
                        calculateTotal();
                    }
                });
            }
        }
        return false;
    }

    $(document).on('click', '.dlt', function(e) {
        e.preventDefault();
        var link = $(this).attr("href");
        Swal.fire({
            title: 'Are you sure want to delete this record?',
            text: "Confirm",
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

    $(document).on('click', '.proceed', function(e) {
        e.preventDefault();
        var link = $(this).attr("href");
        Swal.fire({
            title: 'Are you sure want to proceed?',
            text: "Confirm",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Proceed!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = link
            }
        })
    });

    function isConfirmed(link) {
        Swal.fire({
            title: 'Are you sure want to proceed?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Proceed!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = link
            }
        })
    }
</script>