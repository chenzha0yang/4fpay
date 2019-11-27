<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        出款请求
        |
        出款
    </title>
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
        .title2 {
            font-size: 34px;
            text-align: center;
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
        <div class="title2 m-b-md">
            <span>金额:{{ $data['amount'] }}</span>
            <br/>
            <br/>
            <span>订单号:{{ $data['order'] }}</span>
        </div>
        <div class="title m-b-md">
            {{ $data['body'] }}
        </div>
        <div class="links">
            <a>【Epoch·版权所有】</a>
        </div>
    </div>
</div>
</body>
</html>
