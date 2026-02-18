<?php
session_start();
echo "<!doctype html><meta charset='utf-8'><title>Payment Failed</title>
<style>body{font-family:sans-serif;max-width:700px;margin:60px auto;padding:0 20px}.card{border:1px solid #e5e7eb;border-radius:12px;padding:24px;background:#fff;box-shadow:0 4px 20px rgba(0,0,0,.06)}.bad{color:#dc2626;font-weight:700}</style>
<div class='card'>
  <h1 class='bad'>❌ Payment Failed or Cancelled</h1>
  <p>Your eSewa payment was not completed. You can try again from the cart.</p>
  <p><a href='cart'>Back to Cart</a></p>
</div>";
