<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        {{ trans("transView.{$payWay}") }}
        {{ trans('transView.pay') }}
    </title>
    <link type="text/css" rel="stylesheet" href="{{ asset('css/qrcode/style.css').'?v='.rand() }}">
    <script src="{{ asset('js/qrcode/jquery-1.8.0.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/qrcode/jquery.min.js') }}"></script>
    <script src="{{ asset('js/qrcode/jquery.qrcode.js') }}"></script>
    <script src="{{ asset('js/qrcode/utf.js') }}" type="text/javascript"></script>
    <script>
        var timeCounter = (function() {
            var int;
            var total = 1800;
            return function(elemID) {
                var obj = document.getElementById(elemID);
                var s = (total%60) < 10 ? ('0' + total%60) : total%60;
                var h = total/3600 < 10 ? ('0' + parseInt(total/3600)) : parseInt(total/3600);
                var m = (total-h*3600)/60 < 10 ? ('0' + parseInt((total-h*3600)/60)) : parseInt((total-h*3600)/60);
                obj.innerHTML = m + '分-' + s + '秒';
                total--;
                int = setTimeout("timeCounter('" + elemID + "')", 1000);
                if(total < 0) {
                    clearTimeout(int);
                    alert("订单已超时！");
                    window.close();
                }
            }
        })()
    </script>
</head>
<body aos-easing="ease-out-back" aos-duration="1000" aos-delay="0"  onLoad="timeCounter('timeCounter')">
<main>
    <div class="logo"></div>
    <div class="countPay">
        <p class="textOne">{{ trans('transView.orderMoney') }}： <span class="icon">¥</span> <span class="number">{{ $amount }}</span> <span class="yuan">元</span></p>
        <p class="textTwo">{{ trans('transView.orderNum') }}：<span class="number">{{ $order }}</span></p>
        <div class="qr">
            <img src="{{ $qrCodeUrl }}" id="code">
        </div>
        <p class="textThree"><span class="fontBr">{{ trans('transView.msg')[0] }}</span> <span class="fontBig" id="timeCounter">  {{ trans('transView.msg')[1] }} </span> {{ trans('transView.msg')[2] }}！</p>
        <p class="textFour"> {{ trans('transView.please') }} <span class="fontBig">{{ trans("transView.{$payWay}") }}</span> {{ trans('transView.scan') }}！</p>
    </div>
</main>
<p class="notic"><span class="fontBr"> {{ trans('transView.tipsMsg') }}</span></p>
<script>
    (function(doc, win) {
        var docEl = doc.documentElement,
            resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
            recalc = function() {
                var clientWidth = docEl.clientWidth;
                if (!clientWidth) return;
                if (clientWidth >= 750) {
                    docEl.style.fontSize = '100px';
                } else {
                    docEl.style.fontSize = 100 * (clientWidth / 750) + 'px';
                }
            };

        if (!doc.addEventListener) return;
        win.addEventListener(resizeEvt, recalc, false);
        doc.addEventListener('DOMContentLoaded', recalc, false);
    })(document, window);
</script>
</body>
</html>