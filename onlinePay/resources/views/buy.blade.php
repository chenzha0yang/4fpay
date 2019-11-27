<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Online-Pay</title>
    <!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Raleway', sans-serif;
            font-weight: 100;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 64px;
        }

        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 40px;
        }
        .buyForm {
            display: none;
        }
    </style>
</head>
<body>
<div class="flex-center position-ref full-height">
    <div class="content">
        <div class="title m-b-md">
            正在跳转，请稍后······
        </div>
        <form action="{{ $action }}" method="{{ $method }}" name="buyForm" id="buyForm" class="buyForm" >
            @foreach($data as $key => $val)
                <input type="hidden" name="{{ $key }}" value="{{ $val }}" />
            @endforeach
        </form>

        <div class="links">
            <a>【Epoch·版权所有】</a>
        </div>
    </div>
</div>
<script>
window.onload = function() {
    document.buyForm.submit();
}
</script>
</body>
</html>
