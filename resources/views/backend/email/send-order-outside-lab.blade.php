<!DOCTYPE html>
<html>

<head>
    <title>Todays Lab Orders</title>
</head>

<body>
    Dear {{ $lab->name }}<br /><br />

    Please see the below<br /><br />

    <table width="100%" cellspacing="0" cellpadding="0" border="1px solid gray">
        <thead>
            <tr>
                <th>SL No</th>
                <th>Order ID</th>
                <th>Eye</th>
                <th>Product</th>
                <th>Qty</th>
                <th>SPH</th>
                <th>CYL</th>
                <th>AXIS</th>
                <th>ADD</th>
                <th>IPD</th>
                <th>A_Size</th>
                <th>B_Size</th>
                <th>DBL</th>
                <th>FH</th>
                <th>ED</th>
                <th>Note</th>
                <th>Frame Type</th>
                <th>Customer Name</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $key => $item)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $data[$key]['order_id'] }}</td>
                <td>{{ strtoupper($data[$key]['eye']) }}</td>
                <td>{{ $data[$key]['product'] }}</td>
                <td>{{ $data[$key]['qty'] }}</td>
                <td>{{ $data[$key]['sph'] }}</td>
                <td>{{ $data[$key]['cyl'] }}</td>
                <td>{{ $data[$key]['axis'] }}</td>
                <td>{{ $data[$key]['add'] }}</td>
                <td>{{ $data[$key]['ipd'] }}</td>
                <td>{{ $data[$key]['a_size'] }}</td>
                <td>{{ $data[$key]['b_size'] }}</td>
                <td>{{ $data[$key]['dbl'] }}</td>
                <td>{{ $data[$key]['fh'] }}</td>
                <td>{{ $data[$key]['ed'] }}</td>
                <td>{{ $data[$key]['special_lab_note'] }}</td>
                <td>{{ $data[$key]['frame_type'] }}</td>
                <td>{{ $data[$key]['customer'] }}</td>
            </tr>
            @empty
            @endforelse
        </tbody>
    </table>

    <br />
    <br />
    Regards,<br />
    Speczone.
</body>

</html>