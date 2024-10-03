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
                <form method="post" action="{{ route('vehicle.fetch.details') }}">
                    @csrf
                    <div class="input-group mb-3">
                        {{ html()->text($name = 'mobile', old('mobile'))->class('form-control')->placeholder('Mobile Number') }}

                        <button class="btn btn-info" type="submit">Fetch</button>
                    </div>
                    @error('mobile')
                    <small class="text-danger">{{ $errors->first('mobile') }}</small>
                    @enderror
                </form>
                <div class="">
                    <table class="table table-bordered table-stripped">
                        <thead>
                            <tr>
                                <th>SL No.</th>
                                <th>Vehicle Number</th>
                                <th>Contact Number</th>
                                <th>Last Payment Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vehicles as $key => $vehicle)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $vehicle->reg_number }}</td>
                                <td>{{ $vehicle->contact_number }}</td>
                                <td>{{ $vehicle->payment->first()?->created_at?->format('d.M.Y') ?? 'Na' }}</td>
                            </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</body>

</html>