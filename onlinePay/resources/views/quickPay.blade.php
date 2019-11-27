<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Online-Pay</title>
    <!-- Styles -->
    <link type="text/css" rel="stylesheet" href="{{ asset('css/quickPay/bootstrap.min.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ asset('css/quickPay/font-awesome.min.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ asset('css/quickPay/AdminLTE.min.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ asset('css/quickPay/toastr.min.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ asset('css/quickPay/member.css') }}">
    <script src="{{ asset('js/quickPay/jquery-2.2.3.min.js') }}"></script>
    <style>
        html, body {
            background-color: #fff;
            font-family: 'Raleway', sans-serif;
            font-weight: 100;
            height: 100vh;
            margin: 0;
        }

        .content {
            text-align: center;
        }
        .links{
            text-align: center;
            position: relative;
            bottom: 20%;
        }
        .links > a {
            color: #FFFFFF;
            padding: 0 25px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }
    </style>
</head>
<body class="layout-top-nav skin-yellow-light" id="quickPageBody" scroll="no">
<div class="wrapper">
    <div class="content-wrapper">
        <section class="content tab_content col-md-4">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">我要充值</h3>
                </div>
                <form class="form-horizontal" method="post" id="postForm" name="postForm" action="{{ $action }}" target="_blank">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="merchant_id" id="merchant_id" value="">
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">充值金额(元)</label>
                            <div class="col-sm-6">
                                <input type="number" id="amount" name="amount" class="form-control" onkeyup="value=value.replace(/[^\d.]/g,'')">
                            </div>
                        </div>
                        <div class="tab_list" id="tab_type_gateway">

                            {{--支付类型--}}
                            <div class="form-group">
                                <label class="col-sm-4 control-label">支付类型</label>
                                <div class="col-sm-6">
                                    <select class="form-control" name="payWay_id" id="payWay_id" onchange="changePayWay()">
                                        <option value="0">选择支付方式</option>
                                        @foreach($data as $key => $val)
                                            @if($key == 'bank')
                                        <option value="1">网银</option>
                                            @elseif($key == 'wechat')
                                        <option value="2">微信</option>
                                            @elseif($key == 'alipay')
                                        <option value="3">支付宝</option>
                                            @elseif($key == 'qqpay')
                                        <option value="4">QQ钱包</option>
                                            @elseif($key == 'tenpay')
                                        <option value="5">财付通</option>
                                            @elseif($key == 'visapay')
                                        <option value="6">银联扫码</option>
                                            @elseif($key == 'jdpay')
                                        <option value="7">京东钱包</option>
                                            @elseif($key == 'bdpay')
                                        <option value="8">百度扫码</option>
                                            {{--@elseif($key == 'wechat')--}}
                                        {{--<option value="8">银联快捷</option>--}}
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label">通道类型</label>
                                <div class="col-sm-6">
                                    <select class="form-control" name="pay_id" id="pay_id" onchange="changePassage()">
                                        <option value="0">选择通道</option>
                                        @foreach($data as $key => $val)
                                            @foreach($val as $k => $v)
                                                <option  class='passage' style="display: none;" name="{{ $key }}"  value="{{ $v->merchant_id }}-{{ $v->pay_id }}">{{ $v->payName }}</option>
                                            @endforeach
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="form-group" id="banks-show" style="display: none">
                                <label class="col-sm-4 control-label">银行类型</label>
                                <div class="col-sm-6">
                                    <select class="form-control" name="bank_code" id="bank_code">
                                        <option value="0">选择支付银行</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">&nbsp;</label>
                            <div class="col-sm-4">
                                <button type="button" id="ajaxPost" class="btn btn-info " style="width: 164%;">充值</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
    <div class="links">
        <a>【Epoch·版权所有】</a>
    </div>
</div>
<script>
    var banks = '{{ $banks }}';
    var bankObj = JSON.parse((escape2Html(banks)));
    let bank,payid,merchant_id,payWayid;

    function changePayWay() {
        payWayid = $("#payWay_id").val();
        if (payWayid == 1) {
            // 网银支付
            $('.passage').css('display','none');
            $("option[name='bank']").css('display','block');  //有网银支付的通道
            $('#banks-show').css('display','block');//选择银行列表
        } else if (payWayid == 2) {
            //微信
            $('.passage').css('display','none');
            $("option[name='wechat']").css('display','block');  //有网银支付的通道
            $('#banks-show').css('display','none');//选择银行列表
        } else if (payWayid == 3) {
            //支付宝
            $('.passage').css('display','none');
            $("option[name='alipay']").css('display','block');  //有网银支付的通道
            $('#banks-show').css('display','none');//选择银行列表
        } else if (payWayid == 4) {
            //QQ钱包
            $('.passage').css('display','none');
            $("option[name='qqpay']").css('display','block');  //有网银支付的通道
            $('#banks-show').css('display','none');//选择银行列表
        } else if (payWayid == 5 ) {
            //财付通
            $('.passage').css('display','none');
            $("option[name='tenpay']").css('display','block');  //有网银支付的通道
            $('#banks-show').css('display','none');//选择银行列表
        } else if (payWayid == 6) {
            //银联扫码
            $('.passage').css('display','none');
            $("option[name='visapay']").css('display','block');  //有网银支付的通道
            $('#banks-show').css('display','none');//选择银行列表
        } else if (payWayid == 7) {
            //京东钱包
            $('.passage').css('display','none');
            $("option[name='jdpay']").css('display','block');  //有网银支付的通道
            $('#banks-show').css('display','none');//选择银行列表
        } else if (payWayid == 8) {
            //百度钱包
            $('.passage').css('display','none');
            $("option[name='bdpay']").css('display','block');  //有网银支付的通道
            $('#banks-show').css('display','none');//选择银行列表
        }
    }
    
    function changePassage() {  //选择通道
        payid = $("#pay_id").val().split("-")[1];
        let bankHtml = '<option value="0">选择支付银行</option>';
        for( var k in bankObj){
            if (k == payid) {
                $('#bank_code').children().remove(); // 移除之前银行列表

                for (var i=0; i < bankObj[k].length; i++) {
                    bankHtml += '<option value="'+ bankObj[k][i].bank_code +'">' + bankObj[k][i].bank_name + '</option>';
                }
            }
        }
        $('#bank_code').append(bankHtml);
    }

    function escape2Html(str) {
        var arrEntities={'lt':'<','gt':'>','nbsp':' ','amp':'&','quot':'"'};
        return str.replace(/&(lt|gt|nbsp|amp|quot);/ig,function(all,t){return arrEntities[t];});
    }
    
    $("#ajaxPost").click(function () {
        bank = $('#bank_code').val();
        var amount = $("#amount").val();
        var bankCode   = $("#bank_code").val();
        merchant_id  = $("#pay_id").val().split("-")[0];
        if(!amount || amount == '' || amount == null || amount == 0){
            alert("请输入支付金额");return;
        }
        if(merchant_id == '' || merchant_id == null || merchant_id == 0){
            alert("请选择通道");return;
        } else {
            $("#merchant_id").val(merchant_id);
        }

        if(payWayid == '' || payWayid == null || payWayid == 0){
            alert("请选择支付类型");return;
        }
        if(bank != 0){
            if(!bankCode || bankCode == 0){
                alert("请选择银行");return;
            }
        }
        document.postForm.submit();
    })

</script>
</body>
</html>
