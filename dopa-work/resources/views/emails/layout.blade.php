<!DOCTYPE html>
<html lang="{{ $locale ?? 'ar' }}" dir="{{ ($locale ?? 'ar') === 'ar' ? 'rtl' : 'ltr' }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>@yield('email_title')</title>
<style>
  *{box-sizing:border-box;margin:0;padding:0;}
  body{background:#f9fafb;font-family:{{ ($locale ?? 'ar') === 'ar' ? '"Cairo",sans-serif' : '"Inter",sans-serif' }};direction:{{ ($locale ?? 'ar') === 'ar' ? 'rtl' : 'ltr' }};color:#1f2937;}
  .wrap{max-width:560px;margin:32px auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 2px 16px rgba(0,0,0,.07);}
  .header{background:linear-gradient(135deg,#ea580c,#f97316);padding:28px 32px;text-align:center;}
  .logo{font-size:28px;font-weight:900;color:#fff;letter-spacing:-1px;direction:ltr;display:inline-block;}
  .logo span{color:#fed7aa;}
  .body{padding:32px;}
  .title{font-size:20px;font-weight:700;color:#111827;margin-bottom:8px;}
  .text{font-size:14px;color:#6b7280;line-height:1.7;margin-bottom:16px;}
  .btn{display:inline-block;background:linear-gradient(135deg,#f97316,#ea580c);color:#fff !important;text-decoration:none;padding:12px 28px;border-radius:12px;font-weight:700;font-size:14px;margin:8px 0;}
  .card{background:#fff7ed;border:1px solid #fed7aa;border-radius:12px;padding:16px 20px;margin:16px 0;}
  .card-row{display:flex;justify-content:space-between;align-items:center;padding:6px 0;border-bottom:1px solid #ffedd5;font-size:13px;}
  .card-row:last-child{border-bottom:none;}
  .card-label{color:#92400e;font-weight:600;}
  .card-val{color:#1f2937;font-weight:700;}
  .footer{background:#f9fafb;padding:20px 32px;text-align:center;border-top:1px solid #f3f4f6;}
  .footer p{font-size:11px;color:#9ca3af;}
  .footer a{color:#ea580c;text-decoration:none;}
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <div class="logo">dopa<span>work</span></div>
  </div>
  <div class="body">
    @yield('email_body')
  </div>
  <div class="footer">
    <p>© {{ date('Y') }} Dopa Work · <a href="{{ url('/') }}">dopawork.jo</a></p>
    <p style="margin-top:6px;">{{ ($locale ?? 'ar') === 'ar' ? 'هذا البريد أُرسل تلقائياً، لا ترد عليه.' : 'This is an automated email, please do not reply.' }}</p>
  </div>
</div>
</body>
</html>
