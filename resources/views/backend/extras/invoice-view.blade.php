<!DOCTYPE html>
<html lang="en">

<head>
    <title>Devi Eye Hospitals</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 text-center">
                <img src="/public/backend/assets/images/logo/devi-logo.png" width='40%' />
            </div>
            <div class="col-6 mt-3">
                <table class="table table-bordered table-stripped">
                    <thead>
                        <tr>
                            <td>Customer Name</td>
                            <td>{{ $order->name }}</td>
                        </tr>
                        <tr>
                            <td>Bill Number</td>
                            <td>{{ $order->ino() }}</td>
                        </tr>
                        <tr>
                            <td>Bill Date</td>
                            <td>{{ $order->invoice_generated_at->format('d.M.Y h:i a') }}</td>
                        </tr>
                        <tr>
                            <td>Bill Amount</td>
                            <td>{{ $order->invoice_total  }}</td>
                        </tr>
                        <tr>
                            <td>Address</td>
                            <td>{{ $order->place  }}</td>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

</body>

</html>