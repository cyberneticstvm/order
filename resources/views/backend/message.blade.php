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

    function validateOrderForm() {
        let frm = document.forms["orderForm"];
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
        if (frm['hpresc'].value == '' && frm['spresc'].value == '') {
            let c = confirm("You haven't entered any prescription details. proceed?")
            if (!c) return false
        }
        if (frm['credit_used'].value > frm['available_credit'].value) {
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

    $(document).on('click', '.dlt', function(e) {
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

    $(document).on('click', '.proceed', function(e) {
        e.preventDefault();
        var link = $(this).attr("href");
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
    });
</script>