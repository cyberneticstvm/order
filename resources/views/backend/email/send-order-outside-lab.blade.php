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
                <th>SPH</th>
                <th>CYL</th>
                <th>AXIS</th>
                <th>ADD</th>
                <th>VA</th>
                <th>IPD</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $key => $item)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ strtoupper($data[$key]['eye']) }}</td>
                <td>{{ $data[$key]['order_id'] }}</td>
                <td>{{ $data[$key]['sph'] }}</td>
                <td>{{ $data[$key]['cyl'] }}</td>
                <td>{{ $data[$key]['axis'] }}</td>
                <td>{{ $data[$key]['add'] }}</td>
                <td>{{ $data[$key]['va'] }}</td>
                <td>{{ $data[$key]['ipd'] }}</td>
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