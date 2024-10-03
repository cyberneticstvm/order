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
                        @error('mobile')
                        <small class="text-danger">{{ $errors->first('mobile') }}</small>
                        @enderror
                        <button class="btn btn-info" type="submit">Fetch</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

</html>