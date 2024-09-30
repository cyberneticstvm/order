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
        <div class="row">
            <div class="col-12 text-center">
                <img src="./backend/assets/images/logo/devi-logo.png" width='50%' />
            </div>
            <div class="col-12 text-center">
                Customer Name: {{ $order->name }}<br />
                Bill Number: {{ $order->ino() }}<br />
                Bill Date: {{ $order->invoice_generated_at->format('d.M.Y h:i') }}<br />
                Bill Amount: {{ $order->invoice_total }}<br />
            </div>
        </div>
    </div>

</body>

</html>