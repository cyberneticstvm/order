<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('form').submit(function() {
            $(".btn-submit").attr("disabled", true);
            $(".btn-submit").html("Loading...<span class='spinner-border spinner-border-sm' role='status' aria-hidden='true'></span>");
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
                'error': 'Please select payment mode!'
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
</script>