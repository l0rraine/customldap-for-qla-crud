@extends('adminpanel::layouts.empty')

@section('content')
    <script type="text/javascript">
        if (window != top)
            top.location.href = location.href;
    </script>

    <div class="row" style="margin-top:20px;">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <bold>请登录</bold>
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('login') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">中石化邮箱</label>

                            <div class="col-md-6">
                                <div class="input-group">
                                    <input id="email" name="email" class="form-control" type="text" placeholder="邮箱地址"
                                           value="{{ old('email') }}">
                                    <span class=" input-group-addon" style="font-weight: bold;">@sinopec
                                        .com</span>
                                </div>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">密码</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-3 col-md-offset-2">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                        自动登录
                                    </label>

                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">
                                    登录
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection

@push('pre_js')
<style type="text/css">
    .loginContainer {
        display: table;
        margin: 0 auto;
    }

    /*.loginBox {*/
    /*width: 450px;*/
    /*padding: 0 20px;*/
    /*border: 1px solid #aaa;*/
    /*color: #000;*/
    /*border-radius: 6px;*/
    /*background: white;*/
    /*box-shadow: 0 0 12px #222;*/
    /*background: -moz-linear-gradient(top, #fff, #efefef 8%);*/
    /*background: -webkit-gradient(linear, 0 0, 0 100%, from(#f6f6f6), to(#f4f4f4));*/
    /*font: 11px/1.5em 'Microsoft YaHei';*/
    /*behavior: url(/PIE.htc);*/
    /*}*/

    /*.loginBox .left {*/
    /*border-right: 1px solid #ccc;*/
    /*height: 100%;*/
    /*padding-right: 20px;*/
    /*}*/
</style>
@endpush