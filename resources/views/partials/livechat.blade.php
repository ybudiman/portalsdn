{{-- Floating Chat (Launcher) --}}
<button id="chatLauncher" type="button" class="chat-launcher" aria-label="Buka chat" aria-expanded="false">
  <svg width="26" height="26" viewBox="0 0 24 24" fill="none" aria-hidden="true">
    <path d="M20 12a7 7 0 0 1-7 7H8l-4 3v-4a7 7 0 1 1 16-6Z" fill="white" opacity=".95"/>
    <circle cx="9"  cy="12" r="1.5" fill="#1784c7"/>
    <circle cx="12" cy="12" r="1.5" fill="#1784c7"/>
    <circle cx="15" cy="12" r="1.5" fill="#1784c7"/>
  </svg>
</button>

{{-- Chat Panel --}}
<div id="chatWidget" class="chat-widget" aria-hidden="true">
  <div class="chat-header">
    <div class="title">
      <span class="dot-online"></span> <span>Live Chat</span>
      <small class="sub">Terhubung</small>
    </div>
    <div class="actions">
      <button id="chatMin"   type="button" class="icon-btn" aria-label="Kecilkan"><i class="ti ti-minus"></i></button>
      <button id="chatClose" type="button" class="icon-btn" aria-label="Tutup"><i class="ti ti-x"></i></button>
    </div>
  </div>

  <div id="chatBody" class="chat-body"><!-- messages injected by JS --></div>

  <form id="chatForm" class="chat-input">
    @csrf
    <input id="chatMessage" type="text" class="form-control" placeholder="Ketik pesan lalu Enter…" autocomplete="off">
    <button class="btn btn-primary" type="submit"><i class="ti ti-send"></i></button>
  </form>
</div>

{{-- pastikan z-index di atas overlay/drag-target --}}
<style>
  :root { --chat-z: 2147483000; }
  .chat-launcher, .chat-widget { z-index: var(--chat-z) !important; pointer-events: auto !important; }
</style>

@push('myscript')
<script>
(() => {
  // cegah double init bila partial ke-load lagi
  if (window.__livechat_inited__) return;
  window.__livechat_inited__ = true;

  // ====== CONFIG ======
  const N8N_URL   = "{{ config('services.n8n.webhook_url', 'https://n8n.spherio.id/webhook/chat/relay') }}";
  const USER_ID   = "{{ Auth::id() ?? 'guest' }}";
  const USER_NAME = "{{ Auth::user()->name ?? 'Guest' }}";
  const STORE_KEY = `livechat:${USER_ID}`;
  const UI_KEY    = `livechat_ui:${USER_ID}`;

  // ====== ELEMENTS ======
  const launcher = document.getElementById('chatLauncher');
  const widget   = document.getElementById('chatWidget');
  const closer   = document.getElementById('chatClose');
  const minim    = document.getElementById('chatMin');
  const form     = document.getElementById('chatForm');
  const input    = document.getElementById('chatMessage');
  const bodyEl   = document.getElementById('chatBody');

  // ====== STATE (persisted) ======
  let state = safeParse(localStorage.getItem(STORE_KEY)) || { messages: [] };
  let ui    = safeParse(localStorage.getItem(UI_KEY))    || { open: false };

  function safeParse(s){ try{ return JSON.parse(s) }catch{ return null } }
  function persist(){ localStorage.setItem(STORE_KEY, JSON.stringify(state)); }
  function persistUI(){ localStorage.setItem(UI_KEY, JSON.stringify(ui)); }

  // ====== RENDER ======
  function escapeHtml(s){return s.replace(/[&<>"']/g, m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));}
  function scrollBottom(){ bodyEl.scrollTop = bodyEl.scrollHeight; }

  function renderMessage(msg){
    if(msg.role === 'user'){
      const el=document.createElement('div');
      el.className='msg msg-user';
      el.innerHTML=`<div class="bubble">${escapeHtml(msg.text)}</div>`;
      bodyEl.appendChild(el);
    }else{
      const el=document.createElement('div');
      el.className='msg msg-bot';
      const chipsHtml = (msg.chips && msg.chips.length)
        ? `<div class="quick">${msg.chips.map(c=>`<button class="chip" data-q="${escapeHtml(c)}" type="button">${escapeHtml(c)}</button>`).join('')}</div>`
        : '';
      el.innerHTML=`<div class="avatar"><i class="ti ti-robot"></i></div>
                    <div class="bubble">${escapeHtml(msg.text||'')}${chipsHtml}</div>`;
      bodyEl.appendChild(el);
    }
  }

  function renderAll(){
    bodyEl.innerHTML = '';
    if(state.messages.length === 0){
      state.messages.push({
        role:'bot',
        text:'Halo 👋\nSilakan pilih salah satu:',
        chips:['Produk & Harga','Status Order','Buat Tiket Support','Hubungi Sales']
      });
      persist();
    }
    state.messages.forEach(renderMessage);
    scrollBottom();
  }

  // ====== UI OPEN/CLOSE ======
  function setChat(open){
    ui.open = open;
    widget.classList.toggle('open', open);
    launcher?.setAttribute('aria-expanded', open ? 'true' : 'false');
    if(open) input?.focus();
    persistUI();
  }

  // bubble toggle: buka/tutup
  launcher?.addEventListener('click', () => setChat(!widget.classList.contains('open')));
  minim  ?.addEventListener('click', () => setChat(false));
  closer ?.addEventListener('click', () => setChat(false));

  // ====== MESSAGE HELPERS ======
  function pushUser(text){
    const msg = { role:'user', text };
    state.messages.push(msg); persist();
    renderMessage(msg); scrollBottom();
  }
  function pushBot(text, chips){
    const msg = { role:'bot', text, chips: chips||[] };
    state.messages.push(msg); persist();
    renderMessage(msg); scrollBottom();
  }

  // quick chips -> kirim pesan
  bodyEl.addEventListener('click', (e)=>{
    const b = e.target.closest('.chip'); if(!b) return;
    sendMessage(b.dataset.q);
  });

  // form submit
  form?.addEventListener('submit', (e)=>{
    e.preventDefault();
    const text = input.value.trim(); if(!text) return;
    sendMessage(text);
  });

  async function sendMessage(text){
    pushUser(text);
    input.value = '';
    try{
      const resp = await fetch(N8N_URL, {
        method:'POST',
        headers:{ 'Content-Type':'application/json' },
        body: JSON.stringify({
          message: text,
          sessionId: USER_ID,
          user: { id: USER_ID, name: USER_NAME }
        })
      });
      const data = await resp.json();
      pushBot(data.reply ?? 'Terima kasih! Pesan kamu diterima.');
    }catch(err){
      pushBot('Maaf, koneksi chat bermasalah.');
    }
  }

  // ====== INIT ======
  renderAll();
  setChat(!!ui.open);
})();
</script>
@endpush
