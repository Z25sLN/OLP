    <footer class="footer">
      <div class="footer-title">© <?= date('Y') ?> Olympus Bar</div>
      <div class="footer-author">Creado por <strong>Zeus LN</strong></div>
      <div class="footer-icons">
        <a class="footer-icon" href="https://wa.me/51944753978" target="_blank" aria-label="WhatsApp">
          <!-- aquí va el SVG de WhatsApp si quieres -->
        </a>
        <a class="footer-icon" href="https://www.instagram.com/olympus__fest" target="_blank" aria-label="Instagram">
          <!-- SVG de Instagram -->
        </a>
      </div>
    </footer>

  </div> <!-- .wrap -->

  <!-- CARRITO PREMIUM -->
  <aside id="cart">
    <div class="cart-header">
      <h3>Tu pedido</h3>
      <button class="cart-close" type="button" onclick="toggleCart()">×</button>
    </div>

    <div class="cart-body">
      <!-- Nombre del cliente -->
      <div class="cart-client">
        <label for="cart-name">Nombre del cliente</label>
        <input
          type="text"
          id="cart-name"
          placeholder="Ej: Juan, mesa 4, cumpleaños..."
        >
      </div>

      <!-- Lista de productos -->
      <ul class="cart-items" id="cart-items">
        <li class="cart-empty">No hay productos</li>
      </ul>

      <!-- Total -->
      <div class="cart-summary">
        <div class="summary-row">
          <span>Total final:</span>
          <span class="summary-total">S/ <span id="cart-total">0</span></span>
        </div>
      </div>

      <!-- Métodos de pago -->
      <div class="cart-payment">
        <div class="payment-dot"></div>
        <span><strong>Métodos de pago:</strong> Yape • Efectivo</span>
      </div>

      <!-- Botón WhatsApp -->
      <button class="cart-whatsapp" type="button" onclick="checkout()">
        <svg viewBox="0 0 32 32" aria-hidden="true">
          <path d="M16 3C9.4 3 4 8.1 4 14.3c0 2.6.9 5 2.5 7L4 29l7-2.3c1.9 1 4.1 1.5 6.3 1.5 6.6 0 12-5.1 12-11.3C29.3 8.1 22.6 3 16 3zm0 20.6c-2 0-3.9-.5-5.5-1.5l-.4-.2-4.1 1.3 1.3-3.9-.3-.4c-1.3-1.6-2-3.5-2-5.5 0-5.1 4.5-9.2 10-9.2 5.5 0 10 4.1 10 9.2 0 5.1-4.5 9.2-10 9.2zm5.4-6.9c-.3-.2-1.7-.9-1.9-1-.3-.1-.5-.2-.7.2-.2.3-.8 1-.9 1.1-.2.2-.3.2-.6.1-.3-.2-1.3-.5-2.5-1.6-.9-.8-1.5-1.8-1.7-2.1-.2-.3 0-.4.1-.6.1-.1.3-.3.4-.5.1-.2.2-.3.3-.5.1-.2 0-.4 0-.5 0-.1-.7-1.8-1-2.4-.3-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.7.3-.3.3-1.1 1-1.1 2.4 0 1.4 1.1 2.7 1.2 2.9.1.2 2.2 3.3 5.3 4.5.7.3 1.2.5 1.6.6.7.2 1.3.2 1.8.1.6-.1 1.7-.7 1.9-1.3.2-.6.2-1.1.2-1.3-.1-.2-.3-.2-.6-.4z"/>
        </svg>
        <span>Confirmar pedido por WhatsApp</span>
      </button>
    </div>
  </aside>

  <!-- MODAL -->
  <div class="modal-back" id="modal-back">
    <div class="modal">
      <h4>Reservar evento</h4>
      <div class="field"><label>Nombre</label><input id="ev-name"></div>
      <div class="field"><label>Teléfono</label><input id="ev-phone"></div>
      <div class="field"><label>Fecha</label><input id="ev-date" type="date"></div>
      <div class="field"><label>Hora</label><input id="ev-time" type="time"></div>
      <div class="field"><label>Personas</label><input id="ev-people" type="number" value="30"></div>
      <div class="field"><label>Detalles</label><textarea id="ev-msg"></textarea></div>
      <div style="text-align:right;margin-top:10px">
        <button style="background:#444;color:#fff;padding:8px;border-radius:6px;border:0" onclick="closeModal()">Cancelar</button>
        <button class="btn-primary" onclick="sendReservation()">Enviar</button>
      </div>
    </div>
  </div>

  <!-- JS principal del front -->
  <script src="<?= asset('assets/js/main.js') ?>"></script>
</body>
</html>
