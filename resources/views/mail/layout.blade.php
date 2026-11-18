<!DOCTYPE html>
<html>
<body style="margin:0;padding:0;background:#f4f4f7;font-family:Arial,Helvetica,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="padding:24px 0;">
    <tr><td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;">
        <tr>
          <td style="background:#7c3aed;color:#ffffff;padding:20px 30px;font-size:22px;font-weight:bold;">
            {{ config('app.name') }}
          </td>
        </tr>
        <tr>
          <td style="padding:30px;color:#333333;font-size:15px;line-height:1.6;">
            @yield('content')
          </td>
        </tr>
        <tr>
          <td style="background:#f9fafb;padding:16px 30px;color:#888888;font-size:12px;text-align:center;">
            &copy; {{ date('Y') }} {{ config('app.name') }}. Ye automatic email hai.
          </td>
        </tr>
      </table>
    </td></tr>
  </table>
</body>
</html>