<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <title>Document</title>
</head>
<body>
    <div class="login-layout w-100 d-flex justify-content-center align-items-center">
        <div class="login-form p-3 text-center">
            <h3>Login</h3>
            <form action="{{ route('auth-login.login') }}" method="post">
                @csrf
                {{ csrf_field() }}
                @method('POST')
                <div class="form-group">
                    <label for="">Name</label>
                    <input type="text" class="form-control" name="name">
                </div>

                <div class="form-group mt-2">
                    <label for="">password</label>
                    <input type="password" class="form-control" name="password">
                </div>
                
                <button class="btn btn-primary btn-sm mt-2" type="submit">Login</button>
            </form>
        </div>
    </div>
</body>
</html>