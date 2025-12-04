/* ===========================
   CONFIG (desde PHP)
============================ */
const WA_NUMBER = (window.OLYMPUS_CONFIG && window.OLYMPUS_CONFIG.wa_number) || "51944753978";
const OWNER     = (window.OLYMPUS_CONFIG && window.OLYMPUS_CONFIG.owner)     || "Zeus LN";

/* ===========================
   DATOS (se llenan por fetch)
============================ */
let COCKTAILS = [];
let PACKS = [];

/* ===========================
   UTIL: cargar JSON
============================ */
function loadJson(path) {
  return fetch(path)
    .then(r => {
      if (!r.ok) throw new Error("No se pudo cargar " + path);
      return r.json();
    });
}

/* ===========================
   RENDER CÓCTELES
============================ */
function cardHTML(item){
  const tag = item.tag || "Bebida premium";
  return `
    <div class="card">
      <img src="${item.img}" alt="${item.name}">
      <h4>${item.name}</h4>
      <p>S/ ${item.price}</p>
      <div class="row">
        <span style="color:#bfbfbf">${tag}</span>
        <button class="btn-add" onclick="addToCart('${item.id}')">Añadir</button>
      </div>
    </div>`;
}

function renderCocteles() {
  const coctelesGrid = document.getElementById("cocteles-grid");
  if (!coctelesGrid) return;
  coctelesGrid.innerHTML = COCKTAILS.map(cardHTML).join('');
}

/* ===========================
   RENDER PACKS
============================ */
function packCardHTML(pack){
  const imgs = (pack.images || [])
    .map((src, i) => `
      <img src="${src}" class="${i === 0 ? 'active' : ''}" alt="${pack.name}">
    `)
    .join('');

  const desc = pack.description || 'Pack especial';

  return `
    <div class="card">
      <div class="pack-carousel" data-pack-id="${pack.id}">
        ${imgs}
      </div>
      <h4>${pack.name}</h4>
      <p>S/ ${pack.price}</p>
      <div class="row">
        <span style="color:#bfbfbf">${desc}</span>
        <button class="btn-add" onclick="addToCart('${pack.id}')">Añadir</button>
      </div>
    </div>
  `;
}

function renderPacks() {
  const packsGrid = document.getElementById("packs-grid");
  if (!packsGrid) return;
  packsGrid.innerHTML = PACKS.map(packCardHTML).join('');
}

/* ===========================
   CARRUSELES PACKS
============================ */
function initPackCarousels(){
  document.querySelectorAll('.pack-carousel').forEach(container => {
    const imgs = container.querySelectorAll('img');
    if (imgs.length === 0) return;
    let idx = 0;
    setInterval(() => {
      imgs[idx].classList.remove('active');
      idx = (idx + 1) % imgs.length;
      imgs[idx].classList.add('active');
    }, 3000);
  });
}

/* ===========================
   CARRITO
============================ */
let cart = [];

function addToCart(id){
  const all = COCKTAILS.concat(
    PACKS.map(p => ({
      ...p,
      img: p.images && p.images.length ? p.images[0] : ''
    }))
  );
  const p = all.find(x => x.id === id);
  if (!p) return;
  const ex = cart.find(x => x.id === id);
  if (ex) ex.qty++;
  else cart.push({...p, qty:1});
  renderCart();
}

/* --- RENDER PREMIUM DEL CARRITO --- */
function renderCart(){
  const list      = document.getElementById("cart-items");
  const totalSpan = document.getElementById("cart-total");
  if (!list || !totalSpan) return;

  list.innerHTML = "";

  if (cart.length === 0) {
    list.innerHTML = '<li class="cart-empty">No hay productos</li>';
    totalSpan.textContent = "0";
    return;
  }

  let total = 0;

  cart.forEach(item => {
    const subtotal = item.price * item.qty;
    total += subtotal;

    const li = document.createElement('li');

    li.innerHTML = `
      <div class="cart-item-title">${item.name}</div>
      <div class="cart-item-sub">
        S/ ${item.price} — Cant: ${item.qty} → 
        <strong>S/ ${subtotal.toFixed(2)}</strong>
      </div>
      <div class="cart-row">
        <div class="qty">
          <button type="button" onclick="changeQty('${item.id}', -1)">−</button>
          <span>${item.qty}</span>
          <button type="button" onclick="changeQty('${item.id}', 1)">+</button>
        </div>
        <button class="remove" type="button" onclick="removeItem('${item.id}')">✕</button>
      </div>
    `;

    list.appendChild(li);
  });

  totalSpan.textContent = total.toFixed(2);
}

function changeQty(id, d){
  const it = cart.find(x=>x.id===id);
  if(!it) return;
  it.qty += d;
  if(it.qty<=0) cart=cart.filter(x=>x.id!==id);
  renderCart();
}

function removeItem(id){
  cart = cart.filter(x=>x.id!==id);
  renderCart();
}

/* abrir/cerrar panel premium */
function toggleCart(){
  const c = document.getElementById("cart");
  if (!c) return;

  if (c.style.display === "block") {
    c.style.display = "none";
  } else {
    renderCart();            // aseguramos que esté actualizado
    c.style.display = "block";
  }
}

function closeCart(){
  const c = document.getElementById("cart");
  if (!c) return;
  c.style.display = "none";
}

/* ===========================
   WHATSAPP CHECKOUT
============================ */
function checkout(){
  if(cart.length===0) {
    alert("El carrito está vacío");
    return;
  }

  // Nombre del cliente desde el input del carrito
  const nameInput = document.getElementById('client-name');
  let customerName = nameInput ? nameInput.value.trim() : "";

  if (!customerName) {
    // respaldo: nombre del dueño / config
    customerName = OWNER || "Cliente";
  }

  let msg = "*Pedido Olympus Bar*\n\n";

  cart.forEach(i=>{
    msg += `${i.qty} x ${i.name} - S/ ${(i.price*i.qty).toFixed(2)}\n`;
  });

  msg += `\nTotal final: S/ ${document.getElementById('cart-total').textContent}\n`;
  msg += `\nCliente: ${customerName}`;

  const url = `https://wa.me/${WA_NUMBER}?text=${encodeURIComponent(msg)}`;
  window.open(url,'_blank');
}

/* ===========================
   MODAL EVENTOS
============================ */
function openModal(){
  const m = document.getElementById("modal-back");
  if (m) m.style.display = "flex";
}
function closeModal(){
  const m = document.getElementById("modal-back");
  if (m) m.style.display = "none";
}

function sendReservation(){
  let name = document.getElementById("ev-name").value || 'No indicado';
  let tel  = document.getElementById("ev-phone").value || 'No indicado';
  let date = document.getElementById("ev-date").value || 'No indicado';
  let time = document.getElementById("ev-time").value || 'No indicado';
  let ppl  = document.getElementById("ev-people").value || 'No indicado';
  let msg  = document.getElementById("ev-msg").value || '';

  let text = `*Reserva Olympus Bar*
Nombre: ${name}
Teléfono: ${tel}
Fecha: ${date} ${time}
Personas: ${ppl}
Detalles: ${msg}`;

  const url = `https://wa.me/${WA_NUMBER}?text=${encodeURIComponent(text)}`;
  window.open(url,'_blank');
}

/* ===========================
   CARRUSEL PRINCIPAL BANNER
============================ */
function initMainBanner(){
  const slides = document.querySelectorAll('.slide');
  if (slides.length === 0) return;

  setInterval(()=>{
    const act = document.querySelector('.slide.active') || slides[0];
    let idx = [...slides].indexOf(act);
    act.classList.remove('active');
    slides[(idx+1)%slides.length].classList.add('active');
  },4000);
}

/* ===========================
   INIT GENERAL
============================ */
document.addEventListener('DOMContentLoaded', () => {
  // Cargar cócteles
  loadJson('data/products.json')
    .then(data => {
      COCKTAILS = data || [];
      renderCocteles();
      renderCart();
    })
    .catch(err => console.error(err));

  // Cargar packs
  loadJson('data/packs.json')
    .then(data => {
      PACKS = data || [];
      renderPacks();
      initPackCarousels();
    })
    .catch(err => console.error(err));

  initMainBanner();
});
