<?php
// 홈페이지 문의 폼 → 센터 이메일로 전송
$TO   = 'info@adhcatlanta.com';
$FROM = 'noreply@firstonegroup.org';

function clean($s, $max) { $s = trim((string)$s); $s = str_replace(["\r", "\0"], '', $s); return mb_substr($s, 0, $max, 'UTF-8'); }
$lang = (isset($_POST['lang']) && $_POST['lang'] === 'en') ? 'en' : 'ko';
$T = $lang === 'en'
  ? ['ok' => 'Thank you. Your message has been sent. We will contact you soon.', 'err' => 'Sorry, the message could not be sent. Please call us at (770) 447-4898.', 'miss' => 'Please enter your name and phone number.', 'back' => 'Back to homepage']
  : ['ok' => '감사합니다. 문의가 전송되었습니다. 확인 후 곧 연락드리겠습니다.', 'err' => '죄송합니다. 전송하지 못했습니다. (770) 447-4898로 전화 주세요.', 'miss' => '이름과 전화번호를 입력해 주세요.', 'back' => '홈페이지로 돌아가기'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /'); exit; }
if (!empty($_POST['website'])) { $msg = $T['ok']; }               // 스팸 로봇은 조용히 무시
else {
  $name = clean($_POST['name'] ?? '', 100);
  $phone = clean($_POST['phone'] ?? '', 40);
  $body = clean($_POST['message'] ?? '', 3000);
  $name = str_replace("\n", ' ', $name); $phone = str_replace("\n", ' ', $phone);
  if ($name === '' || $phone === '') { $msg = $T['miss']; }
  else {
    $subject = '=?UTF-8?B?' . base64_encode('[홈페이지 문의] ' . $name) . '?=';
    $text = "홈페이지 문의·견학 신청이 들어왔습니다.\n\n이름: $name\n전화번호: $phone\n언어: " . ($lang === 'en' ? 'English' : '한국어') . "\n시간: " . date('Y-m-d H:i') . "\n\n문의 내용:\n" . ($body === '' ? '(없음)' : $body) . "\n";
    $headers = "From: 홈페이지 문의 <$FROM>\r\nMIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: 8bit";
    $msg = @mail($TO, $subject, $text, $headers, "-f$FROM") ? $T['ok'] : $T['err'];
  }
}
?><!doctype html>
<html lang="<?= $lang ?>"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>행복건강복지센터</title>
<style>body{margin:0;font-family:-apple-system,"Apple SD Gothic Neo","Noto Sans KR",sans-serif;background:#f3f8ff;color:#1d1d1f;display:flex;min-height:100vh;align-items:center;justify-content:center;padding:24px;box-sizing:border-box}
.box{max-width:520px;background:#fff;border:2px solid #d3e2f7;border-top:6px solid #2563c9;border-radius:28px;padding:36px;text-align:center;font-size:20px;line-height:1.6}
a{display:inline-block;margin-top:24px;background:#2563c9;color:#fff;text-decoration:none;font-weight:700;padding:14px 28px;border-radius:999px;font-size:18px}</style></head>
<body><div class="box"><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?><br><a href="/#contact"><?= $T['back'] ?></a></div></body></html>
