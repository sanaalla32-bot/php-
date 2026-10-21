
  <footer class="footer">
    <p>&copy; see the world from my view</p>
    <p><a href="#top">&#8593; العودة للأعلى</a></p>
  </footer>

  <div class="overlay" id="overlay"></div>

  <aside class="cart-panel" id="cartPanel">
    <div class="cart-head">
      <h3>🛒 سلة المشتريات</h3>
      <button class="icon-btn" id="closeCart" type="button">✕</button>
    </div>
    <div class="cart-items" id="cartItems"></div>
    <div class="cart-foot">
      <div class="cart-total"><span>المجموع</span><strong id="cartTotal">0 جنيه</strong></div>
      <button class="btn" id="checkoutBtn" type="button">إتمام الطلب</button>
      <button class="btn btn-outline" id="clearCart" type="button">تفريغ السلة</button>
    </div>
  </aside>

  <div class="modal" id="modal">
    <div class="modal-box">
      <button class="icon-btn modal-close" id="modalClose" type="button">✕</button>
      <img id="modalImg" alt="">
      <h3 id="modalTitle"></h3>
      <p id="modalMeta"></p>
      <p class="price" id="modalPrice"></p>
      <button class="btn" id="modalAdd" type="button">أضف للسلة 🛒</button>
    </div>
  </div>

  <div class="toast" id="toast"></div>
  <button class="to-top" id="toTop" type="button" title="للأعلى">&#8593;</button>
<?php
$prods = $pdo->query('SELECT id, category, name, type, size, price, image FROM products ORDER BY id')->fetchAll();
foreach ($prods as &$p) { $p['id'] = (int)$p['id']; $p['price'] = (int)$p['price']; }
unset($p);
$extrasJs = [];
foreach (EXTRAS as $k => $x) { $extrasJs[$k] = ['name' => $x['name'], 'price' => $x['price']]; }
$u = current_user();
$jsUser = $u ? ['name' => $u['name'], 'email' => $u['email']] : null;
$flags = JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP;
?>
  <script>
    window.PRODUCTS = <?= json_encode($prods, $flags) ?>;
    window.EXTRAS = <?= json_encode($extrasJs, $flags) ?>;
    window.CURRENT_USER = <?= json_encode($jsUser, $flags) ?>;
    window.CLEAR_CART = <?= !empty($clearCart) ? 'true' : 'false' ?>;
  </script>
  <script src="script.js"></script>
</body>
</html>
