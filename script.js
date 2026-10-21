/* ==========================================================
   see the world from my view — سكربت المتجر
   يعمل في النسختين: (HTML+JS) و (PHP)
   ========================================================== */
(function () {
  'use strict';

  const body = document.body;
  body.classList.add('js');

  const BACKEND = body.dataset.backend === 'php';      // النسخة النهائية بـ PHP؟
  const ABOUT = body.dataset.about || 'about.html';
  const INDEX = body.dataset.index || 'index.html';
  const EXT = ABOUT.split('.').pop();                   // html أو php

  /* ---------- البيانات ---------- */
  const DEFAULT_PRODUCTS = [
    { id: 1, category: 'pots', name: 'نبتة هيبوستس في أصيص وردي', type: 'نبات', size: 'صغير', price: 8000, image: 'ases1' },
    { id: 2, category: 'pots', name: 'أصيص ورد جوري أحمر', type: 'ورد', size: 'وسط', price: 15000, image: 'ases2' },
    { id: 3, category: 'pots', name: 'نبتة خضراء في أصيص فخار', type: 'نبات', size: 'وسط', price: 10000, image: 'ases3' },
    { id: 4, category: 'pots', name: 'أصيص كالانشو وردي', type: 'ورد', size: 'وسط', price: 12000, image: 'ases4' },
    { id: 5, category: 'pots', name: 'أصيص ورد جوري كبير', type: 'ورد', size: 'كبير', price: 20000, image: 'ases5' },
    { id: 6, category: 'pots', name: 'تشكيلة مزهريات بأشكال مختلفة', type: 'مزهرية', size: 'وسط', price: 7000, image: 'ases7' },
    { id: 7, category: 'pots', name: 'مزهرية عباد الشمس', type: 'ورد', size: 'وسط', price: 9000, image: 'ases8' },
    { id: 8, category: 'pots', name: 'مزهرية طويلة بأغصان وردية', type: 'ديكور', size: 'كبير', price: 25000, image: 'ases9' },
    { id: 9, category: 'pots', name: 'مزهرية سوداء بأغصان الكرز', type: 'ديكور', size: 'وسط', price: 14000, image: 'ases10' },
    { id: 10, category: 'pots', name: 'مزهرية مخططة بأغصان بيضاء', type: 'ديكور', size: 'وسط', price: 11000, image: 'ases11' },
    { id: 11, category: 'bouquets', name: 'بوكيه ورد جوري أحمر', type: 'ورد', size: 'وسط', price: 25000, image: 'po' },
    { id: 12, category: 'bouquets', name: 'بوكيه توليب أحمر', type: 'ورد', size: 'وسط', price: 22000, image: 'pokay1' },
    { id: 13, category: 'bouquets', name: 'بوكيه ورد وردي وأصفر', type: 'ورد', size: 'وسط', price: 20000, image: 'pokay5' },
    { id: 14, category: 'bouquets', name: 'بوكيه أوريو', type: 'هدية', size: 'وسط', price: 18000, image: 'pokay6' },
    { id: 15, category: 'bouquets', name: 'وردة صفراء مغلفة', type: 'ورد', size: 'صغير', price: 5000, image: 'pokay7' },
    { id: 16, category: 'bouquets', name: 'بوكيه سناكس وورد', type: 'هدية', size: 'كبير', price: 30000, image: 'pokay8' }
  ];
  const DEFAULT_EXTRAS = {
    card: { name: 'بطاقة إهداء', price: 1000 },
    wrap: { name: 'تغليف هدية', price: 3000 },
    delivery: { name: 'توصيل للمنزل', price: 5000 }
  };
  const PRODUCTS = (window.PRODUCTS && window.PRODUCTS.length) ? window.PRODUCTS : DEFAULT_PRODUCTS;
  const EXTRAS = window.EXTRAS || DEFAULT_EXTRAS;

  /* ---------- أدوات مساعدة ---------- */
  const $ = (s, r) => (r || document).querySelector(s);
  const $$ = (s, r) => Array.from((r || document).querySelectorAll(s));
  const load = (k, d) => { try { const v = localStorage.getItem(k); return v ? JSON.parse(v) : d; } catch (e) { return d; } };
  const save = (k, v) => { try { localStorage.setItem(k, JSON.stringify(v)); } catch (e) { /* تجاهل */ } };
  const fmt = n => Number(n).toLocaleString('en-US') + ' جنيه';
  const pad = n => String(n).padStart(2, '0');
  const byId = id => PRODUCTS.find(p => p.id === id);

  function el(tag, props) {
    const e = document.createElement(tag);
    if (props) {
      Object.keys(props).forEach(k => {
        if (k === 'class') e.className = props[k];
        else if (k === 'text') e.textContent = props[k];
        else if (k.indexOf('on') === 0) e.addEventListener(k.slice(2), props[k]);
        else e.setAttribute(k, props[k]);
      });
    }
    for (let i = 2; i < arguments.length; i++) {
      if (arguments[i] != null) e.append(arguments[i]);
    }
    return e;
  }

  /* الصور: يجرّب الامتدادات المختلفة تلقائياً (jpg, png, webp ...) */
  const EXTS = ['jpg', 'jpeg', 'png', 'webp', 'jfif', 'JPG', 'PNG', 'jpg.jpg', 'png.png', 'jpeg.jpg', 'webp.webp'];   // يشمل الامتداد المكرر مثل po.jpg.jpg
  function smartImg(img, base) {
    if (/\.[a-zA-Z]+$/.test(base)) { img.src = 'images/' + base; return; }
    let i = 0;
    img.addEventListener('error', function () {
      i++;
      if (i < EXTS.length) {
        img.src = 'images/' + base + '.' + EXTS[i];
      } else {
        img.classList.add('noimg');
        img.removeAttribute('src');
      }
    });
    img.src = 'images/' + base + '.' + EXTS[0];
  }

  /* ---------- رسالة منبثقة صغيرة ---------- */
  const toastEl = $('#toast');
  let toastTimer;
  function toast(msg) {
    if (!toastEl) return;
    toastEl.textContent = msg;
    toastEl.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => toastEl.classList.remove('show'), 2200);
  }

  function showMsg(sel, msg, type) {
    const box = $(sel);
    if (!box) return;
    box.className = 'form-msg ' + (type || '');
    box.textContent = msg;
  }

  /* ---------- الوضع الليلي ---------- */
  const themeBtn = $('#themeBtn');
  function applyTheme(dark) {
    body.classList.toggle('dark', dark);
    if (themeBtn) themeBtn.textContent = dark ? '☀️' : '🌙';
  }
  applyTheme(load('theme', 'light') === 'dark');
  if (themeBtn) {
    themeBtn.addEventListener('click', () => {
      const dark = !body.classList.contains('dark');
      save('theme', dark ? 'dark' : 'light');
      applyTheme(dark);
    });
  }

  /* ---------- المستخدم الحالي ---------- */
  function currentUser() {
    return BACKEND ? (window.CURRENT_USER || null) : load('currentUser', null);
  }

  function renderUserBox() {
    if (BACKEND) return;                       // في PHP يُرسم من الخادم
    const box = $('#userBox');
    if (!box) return;
    box.textContent = '';
    const u = currentUser();
    if (u) {
      box.append(el('span', { text: 'أهلاً ' + u.name }),
        el('button', { type: 'button', id: 'logoutBtn', text: 'خروج', onclick: () => {
          localStorage.removeItem('currentUser');
          toast('تم تسجيل الخروج');
          setTimeout(() => { location.href = INDEX; }, 700);
        } }));
    } else {
      box.append(el('a', { href: INDEX, text: 'دخول' }));
    }
  }

  function renderGreeting() {
    const g = $('#greeting');
    if (!g) return;
    const h = new Date().getHours();
    const word = h < 12 ? 'صباح الورد 🌸' : (h < 18 ? 'نهارك سعيد ☀️' : 'مساء الخير 🌙');
    const u = currentUser();
    g.textContent = u ? word + ' يا ' + u.name : word;
  }

  /* ---------- سلة المشتريات ---------- */
  let cart = load('cart', []).filter(c => byId(c.id));
  if (window.CLEAR_CART) cart = [];
  const saveCart = () => save('cart', cart);
  saveCart();

  const cartCount = () => cart.reduce((s, c) => s + c.qty, 0);
  const cartSubtotal = () => cart.reduce((s, c) => s + (byId(c.id).price * c.qty), 0);

  function addToCart(id) {
    const item = cart.find(c => c.id === id);
    if (item) item.qty++; else cart.push({ id: id, qty: 1 });
    saveCart(); renderCart();
    toast('تمت الإضافة إلى السلة 🛒');
  }
  function changeQty(id, d) {
    const item = cart.find(c => c.id === id);
    if (!item) return;
    item.qty += d;
    if (item.qty < 1) cart = cart.filter(c => c.id !== id);
    saveCart(); renderCart();
  }
  function removeItem(id) {
    cart = cart.filter(c => c.id !== id);
    saveCart(); renderCart();
  }

  function renderCart() {
    const count = cartCount();
    $$('.cart-count').forEach(b => { b.textContent = count; b.style.display = count ? 'flex' : 'none'; });

    const box = $('#cartItems');
    if (box) {
      box.textContent = '';
      if (!cart.length) {
        box.append(el('p', { class: 'empty', text: 'السلة فارغة 🌸' }));
      } else {
        cart.forEach(it => {
          const p = byId(it.id);
          box.append(el('div', { class: 'cart-row' },
            el('div', { class: 'cart-info' }, el('strong', { text: p.name }), el('small', { text: fmt(p.price) })),
            el('div', { class: 'qty' },
              el('button', { type: 'button', text: '−', onclick: () => changeQty(p.id, -1) }),
              el('span', { text: it.qty }),
              el('button', { type: 'button', text: '+', onclick: () => changeQty(p.id, 1) })),
            el('button', { type: 'button', class: 'del', title: 'حذف', text: '✕', onclick: () => removeItem(p.id) })));
        });
      }
    }
    const total = $('#cartTotal');
    if (total) total.textContent = fmt(cartSubtotal());
    updateOrderSummary();
  }

  const panel = $('#cartPanel');
  const overlay = $('#overlay');
  function openCart() { if (panel) { panel.classList.add('open'); overlay.classList.add('show'); } }
  function closeCart() { if (panel) { panel.classList.remove('open'); overlay.classList.remove('show'); } }

  if ($('#cartBtn')) $('#cartBtn').addEventListener('click', openCart);
  if ($('#closeCart')) $('#closeCart').addEventListener('click', closeCart);
  if (overlay) overlay.addEventListener('click', closeCart);
  if ($('#clearCart')) $('#clearCart').addEventListener('click', () => { cart = []; saveCart(); renderCart(); });
  if ($('#checkoutBtn')) {
    $('#checkoutBtn').addEventListener('click', () => {
      if (!cart.length) { toast('السلة فارغة، أضيفي منتجات أولاً'); return; }
      closeCart();
      const order = $('#order');
      if (order) order.scrollIntoView({ behavior: 'smooth' });
      else location.href = ABOUT + '#order';
    });
  }

  /* ---------- ملخص الطلب (الصفحة الرئيسية للطلب) ---------- */
  function orderTotals() {
    let ext = 0;
    const chosen = [];
    $$('#orderForm input[name="extras[]"]:checked').forEach(c => {
      const x = EXTRAS[c.value];
      if (x) { ext += x.price; chosen.push(x); }
    });
    const sub = cartSubtotal();
    return { sub: sub, ext: ext, chosen: chosen, total: sub + ext };
  }

  function updateOrderSummary() {
    const hidden = $('#cartInput');
    if (hidden) hidden.value = JSON.stringify(cart.map(c => ({ id: c.id, qty: c.qty })));
    const box = $('#orderSummary');
    if (!box) return;
    box.textContent = '';
    if (!cart.length) {
      box.append(el('p', { class: 'empty' }, 'سلتك فارغة. اختاري منتجاتك من صفحة ',
        el('a', { href: 'pots.' + EXT, text: 'الأصص' }), ' أو ',
        el('a', { href: 'bouquets.' + EXT, text: 'البوكيهات' }), '.'));
      return;
    }
    const t = orderTotals();
    cart.forEach(it => {
      const p = byId(it.id);
      box.append(el('div', { class: 'sum-row' }, el('span', { text: p.name + ' × ' + it.qty }), el('span', { text: fmt(p.price * it.qty) })));
    });
    t.chosen.forEach(x => box.append(el('div', { class: 'sum-row' }, el('span', { text: x.name }), el('span', { text: fmt(x.price) }))));
    box.append(el('div', { class: 'sum-row sum-total' }, el('span', { text: 'الإجمالي' }), el('span', { text: fmt(t.total) })));
  }

  /* ---------- المنتجات: عرض + بحث + فلترة + ترتيب ---------- */
  const modal = $('#modal');
  let modalId = null;
  function openModal(id) {
    const p = byId(id);
    if (!p || !modal) return;
    modalId = id;
    const img = el('img', { id: 'modalImg', alt: p.name });   // عنصر جديد في كل مرة
    $('#modalImg').replaceWith(img);
    smartImg(img, p.image);
    $('#modalTitle').textContent = p.name;
    $('#modalMeta').textContent = p.type + ' • الحجم: ' + p.size;
    $('#modalPrice').textContent = fmt(p.price);
    modal.classList.add('show');
  }
  function closeModal() { if (modal) modal.classList.remove('show'); }
  if (modal) {
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
    $('#modalClose').addEventListener('click', closeModal);
    $('#modalAdd').addEventListener('click', () => { addToCart(modalId); closeModal(); });
  }
  document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeModal(); closeCart(); } });

  function productCard(p) {
    const img = el('img', { class: 'pimg', alt: p.name, loading: 'lazy' });
    smartImg(img, p.image);
    return el('article', { class: 'card product reveal visible' },
      el('div', { class: 'pimg-wrap', onclick: () => openModal(p.id) }, img),
      el('h3', { text: p.name }),
      el('p', { class: 'meta', text: p.type + ' • ' + p.size }),
      el('p', { class: 'price', text: fmt(p.price) }),
      el('div', { class: 'actions' },
        el('button', { type: 'button', class: 'btn btn-outline', text: 'تفاصيل', onclick: () => openModal(p.id) }),
        el('button', { type: 'button', class: 'btn', text: 'أضف للسلة 🛒', onclick: () => addToCart(p.id) })));
  }

  function renderProducts() {
    const grid = $('#productGrid');
    if (!grid) return;
    const cat = grid.dataset.category;
    const q = ($('#search') ? $('#search').value : '').trim().toLowerCase();
    const size = $('#sizeFilter') ? $('#sizeFilter').value : '';
    const sort = $('#sortSel') ? $('#sortSel').value : '';
    const list = PRODUCTS.filter(p => p.category === cat
      && (!size || p.size === size)
      && (!q || (p.name + ' ' + p.type).toLowerCase().indexOf(q) !== -1));
    if (sort === 'price-asc') list.sort((a, b) => a.price - b.price);
    else if (sort === 'price-desc') list.sort((a, b) => b.price - a.price);
    else if (sort === 'name') list.sort((a, b) => a.name.localeCompare(b.name, 'ar'));
    grid.textContent = '';
    list.forEach(p => grid.append(productCard(p)));
    const none = $('#noResults');
    if (none) none.hidden = list.length > 0;
  }
  ['#search', '#sizeFilter', '#sortSel'].forEach(s => {
    const n = $(s);
    if (n) n.addEventListener(n.tagName === 'INPUT' ? 'input' : 'change', renderProducts);
  });

  /* الشريط المتحرك */
  function renderStrip() {
    const track = $('#stripTrack');
    if (!track) return;
    const items = PRODUCTS.filter(p => p.category === track.dataset.category);
    if (!items.length) return;
    let half = [];
    while (half.length < 8) half = half.concat(items);
    half.concat(half).forEach(p => {
      const img = el('img', { alt: p.name });
      smartImg(img, p.image);
      track.append(el('div', { class: 'strip-item' }, img, el('span', { text: p.name })));
    });
    track.style.animationDuration = (half.length * 4) + 's';
  }

  /* ---------- التحقق من النماذج ---------- */
  const EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  const PHONE = /^\+?[0-9]{9,15}$/;

  function setError(input, msg) {
    const f = input.closest('.field');
    const e = f ? $('.err', f) : null;
    if (e) e.textContent = msg || '';
    input.classList.toggle('invalid', !!msg);
    input.classList.toggle('valid', !msg && input.type !== 'checkbox' && input.value.trim() !== '');
    return !msg;
  }

  function bindForm(form, validators, onValid) {
    const run = name => setError(form.elements[name], validators[name](form.elements[name]));
    Object.keys(validators).forEach(name => {
      const input = form.elements[name];
      if (!input) return;
      const ev = (input.type === 'checkbox' || input.tagName === 'SELECT') ? 'change' : 'input';
      input.addEventListener(ev, () => run(name));
      input.addEventListener('blur', () => run(name));
    });
    form.addEventListener('submit', e => {
      let ok = true;
      Object.keys(validators).forEach(n => { if (form.elements[n] && !run(n)) ok = false; });
      if (!ok) {
        e.preventDefault();
        const bad = $('.invalid', form);
        if (bad) bad.focus();
        return;
      }
      onValid(e);
    });
  }

  const vEmail = i => !i.value.trim() ? 'أدخلي البريد الإلكتروني' : (EMAIL.test(i.value.trim()) ? '' : 'البريد الإلكتروني غير صحيح');
  const hash = s => { let h = 5381; for (let i = 0; i < s.length; i++) h = ((h << 5) + h + s.charCodeAt(i)) | 0; return String(h); };

  /* ---------- تسجيل الدخول وإنشاء حساب ---------- */
  const loginForm = $('#loginForm');
  if (loginForm) {
    bindForm(loginForm, {
      email: vEmail,
      password: i => i.value ? '' : 'أدخلي كلمة المرور'
    }, e => {
      if (BACKEND) return;                                     // PHP يكمل
      e.preventDefault();
      const email = loginForm.elements.email.value.trim().toLowerCase();
      const user = load('users', []).find(u => u.email === email && u.pass === hash(loginForm.elements.password.value));
      if (!user) { showMsg('#loginMsg', 'البريد الإلكتروني أو كلمة المرور غير صحيحة', 'error'); return; }
      save('currentUser', { name: user.name, email: user.email });
      showMsg('#loginMsg', 'تم تسجيل الدخول بنجاح ✅', 'success');
      setTimeout(() => { location.href = ABOUT; }, 900);
    });
  }

  const registerForm = $('#registerForm');
  if (registerForm) {
    const rf = registerForm.elements;
    bindForm(registerForm, {
      name: i => i.value.trim().length >= 3 ? '' : 'الاسم لا يقل عن 3 أحرف',
      email: vEmail,
      phone: i => (!i.value.trim() || PHONE.test(i.value.replace(/[\s-]/g, ''))) ? '' : 'رقم الهاتف غير صحيح',
      password: i => i.value.length >= 6 ? '' : 'كلمة المرور لا تقل عن 6 أحرف',
      password2: i => i.value === rf.password.value ? '' : 'كلمتا المرور غير متطابقتين',
      terms: i => i.checked ? '' : 'يجب الموافقة على الشروط'
    }, e => {
      if (BACKEND) return;
      e.preventDefault();
      const email = rf.email.value.trim().toLowerCase();
      const users = load('users', []);
      if (users.some(u => u.email === email)) { showMsg('#registerMsg', 'هذا البريد مسجل من قبل', 'error'); return; }
      users.push({ name: rf.name.value.trim(), email: email, phone: rf.phone.value.trim(), city: rf.city.value, pass: hash(rf.password.value) });
      save('users', users);
      save('currentUser', { name: rf.name.value.trim(), email: email });
      showMsg('#registerMsg', 'تم إنشاء الحساب بنجاح ✅', 'success');
      setTimeout(() => { location.href = ABOUT; }, 900);
    });
    rf.password.addEventListener('input', () => { if (rf.password2.value) setError(rf.password2, rf.password2.value === rf.password.value ? '' : 'كلمتا المرور غير متطابقتين'); });
  }

  $$('.toggle-pass').forEach(b => b.addEventListener('click', () => {
    const input = document.getElementById(b.dataset.target);
    input.type = input.type === 'password' ? 'text' : 'password';
    b.textContent = input.type === 'password' ? '👁' : '🙈';
  }));

  /* تبويبات الصفحة الأولى */
  function showTab(name) {
    $$('.tab').forEach(t => t.classList.toggle('active', t.dataset.tab === name));
    $$('.tab-panel').forEach(p => p.classList.toggle('active', p.id === 'tab-' + name));
  }
  $$('.tab').forEach(t => t.addEventListener('click', () => showTab(t.dataset.tab)));
  if (location.hash === '#register') showTab('register');

  /* ---------- نموذج الطلب ---------- */
  const orderForm = $('#orderForm');
  if (orderForm) {
    const dateInput = orderForm.elements.date;
    if (dateInput) dateInput.min = new Date().toISOString().slice(0, 10);

    const note = $('#loginNote');
    if (note) note.hidden = !!currentUser();

    $$('#orderForm input[name="extras[]"]').forEach(c => c.addEventListener('change', updateOrderSummary));

    bindForm(orderForm, {
      name: i => i.value.trim().length >= 3 ? '' : 'الاسم لا يقل عن 3 أحرف',
      phone: i => PHONE.test(i.value.replace(/[\s-]/g, '')) ? '' : 'رقم الهاتف غير صحيح',
      address: i => i.value.trim().length >= 5 ? '' : 'اكتبي عنواناً واضحاً',
      date: i => (!i.value || i.value >= new Date().toISOString().slice(0, 10)) ? '' : 'اختاري تاريخاً قادماً'
    }, e => {
      if (!cart.length) { e.preventDefault(); showMsg('#orderMsg', 'السلة فارغة، أضيفي منتجات أولاً', 'error'); return; }
      if (!currentUser()) { e.preventDefault(); showMsg('#orderMsg', 'سجّلي الدخول أولاً لإتمام الطلب', 'error'); return; }
      updateOrderSummary();                                     // يحدّث حقل السلة المخفي
      if (BACKEND) return;                                      // PHP يحفظ الطلب في قاعدة البيانات
      e.preventDefault();
      const t = orderTotals();
      const orders = load('orders', []);
      const id = orders.length + 1;
      orders.push({
        id: id,
        user: currentUser().email,
        items: cart.map(c => ({ id: c.id, qty: c.qty })),
        total: t.total,
        date: new Date().toISOString()
      });
      save('orders', orders);
      cart = []; saveCart(); renderCart();
      orderForm.reset();
      $$('.valid', orderForm).forEach(i => i.classList.remove('valid'));
      showMsg('#orderMsg', 'تم استلام طلبك رقم #' + id + ' ✅ سنتواصل معك قريباً', 'success');
    });
  }

  /* ---------- عدّاد تنازلي لآخر موعد طلبات اليوم ---------- */
  function initCountdown() {
    if (!$('#countdown')) return;
    const tick = () => {
      const now = new Date();
      const end = new Date();
      end.setHours(20, 0, 0, 0);
      if (now >= end) end.setDate(end.getDate() + 1);
      const d = end - now;
      $('#cdH').textContent = pad(Math.floor(d / 3600000));
      $('#cdM').textContent = pad(Math.floor(d % 3600000 / 60000));
      $('#cdS').textContent = pad(Math.floor(d % 60000 / 1000));
    };
    tick();
    setInterval(tick, 1000);
  }

  /* ---------- ظهور تدريجي + زر للأعلى ---------- */
  function initReveal() {
    const items = $$('.reveal');
    if (!('IntersectionObserver' in window)) { items.forEach(i => i.classList.add('visible')); return; }
    const io = new IntersectionObserver(entries => {
      entries.forEach(en => { if (en.isIntersecting) { en.target.classList.add('visible'); io.unobserve(en.target); } });
    }, { threshold: 0.1 });
    items.forEach(i => io.observe(i));
  }
  const toTop = $('#toTop');
  if (toTop) {
    window.addEventListener('scroll', () => toTop.classList.toggle('show', window.scrollY > 300));
    toTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
  }

  /* ---------- التشغيل ---------- */
  renderUserBox();
  renderGreeting();
  renderCart();
  renderProducts();
  renderStrip();
  initCountdown();
  initReveal();
})();
