@once
<style>
/* === Live Chat styles === */
.chat-launcher{
  position:fixed; right:22px; bottom:22px; width:58px; height:58px;
  background:#179bd7; border:none; border-radius:12px; cursor:pointer;
  display:flex; align-items:center; justify-content:center;
  box-shadow:0 10px 24px rgba(0,0,0,.22); z-index:10010;
  transition:transform .15s ease, box-shadow .15s ease;
}
.chat-launcher:hover{ transform:translateY(-2px); box-shadow:0 14px 28px rgba(0,0,0,.28); }

.chat-widget{
  position:fixed; right:22px; bottom:90px; width:360px; max-width:92vw; height:520px;
  background:#fff; border-radius:16px; box-shadow:0 18px 40px rgba(0,0,0,.28);
  display:none; flex-direction:column; overflow:hidden; z-index:10010;
}
.chat-widget.open{ display:flex; }

.chat-header{
  height:58px; padding:10px 12px; display:flex; align-items:center; justify-content:space-between;
  background:#0ea5e9; color:#fff;
}
.chat-header .title{ font-weight:700; display:flex; align-items:center; gap:8px; }
.chat-header .sub{ font-weight:500; opacity:.9; margin-left:6px; font-size:12px; }
.dot-online{ width:8px; height:8px; border-radius:50%; background:#22c55e; display:inline-block; }
.icon-btn{ background:transparent; border:none; color:#fff; font-size:18px; margin-left:8px; cursor:pointer; }

.chat-body{ padding:14px; background:#f8fafc; flex:1; overflow-y:auto; }
.msg{ display:flex; gap:10px; margin-bottom:12px; }
.msg .avatar{ width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; background:#e2e8f0; color:#334155; flex:0 0 28px; font-size:16px; }
.msg .bubble{ max-width:78%; padding:10px 12px; border-radius:12px; background:#fff; box-shadow:0 1px 2px rgba(0,0,0,.06); }
.msg-user{ justify-content:flex-end; }
.msg-user .bubble{ background:#2563eb; color:#fff; border-top-right-radius:6px; }
.msg-bot .bubble{ border-top-left-radius:6px; }

.quick{ margin-top:10px; display:flex; gap:8px; flex-wrap:wrap; }
.chip{ border:1px solid #cfe6f7; background:#eff7fe; padding:6px 10px; border-radius:999px; font-size:12px; cursor:pointer; }

.chat-input{ display:flex; gap:8px; padding:10px; border-top:1px solid #e2e8f0; background:#fff; }
.chat-input .form-control{ height:42px; }
.chat-input .btn{ height:42px; }
@media (max-width:480px){ .chat-widget{ height:70vh; bottom:84px; } }
</style>
@endonce
