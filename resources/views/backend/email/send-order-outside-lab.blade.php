<!DOCTYPE html>
<html>

<head>
    <title>Todays Lab Orders</title>
</head>

<body>
    Dear {{ $lab->name }}

    Please see the below<br /><br />

    <table width="100%" cellspacing="0" cellpadding="0" border="1px solid gray">
        <thead>
            <tr>
                <th>SL No</th>
                <th>SPH</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $key => $item)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $data->sph[$key] }}</td>
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