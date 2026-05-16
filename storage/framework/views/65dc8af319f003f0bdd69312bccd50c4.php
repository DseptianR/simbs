<div class="chat-widget" id="chatWidget">
    <div class="chat-bubble" id="chatBubble">
        <span class="chat-emoji">👋</span>
        <span class="chat-text">Ada yang bisa dibantu?</span>
    </div>
    <button class="chat-btn" id="chatBtn" aria-label="Buka Chat">
        <i class="fas fa-paper-plane"></i>
    </button>
    <div class="chat-popup" id="chatPopup">
        <div class="chat-popup-header">
            <div class="chat-agent">
                <div class="agent-avatar"><i class="fas fa-recycle"></i></div>
                <div>
                    <strong>SIMBS Bot</strong>
                    <span class="agent-status"><i class="fas fa-circle"></i> Online</span>
                </div>
            </div>
            <div style="display:flex;gap:8px;align-items:center;">
                <button id="chatClearBtn" title="Hapus percakapan"
                    style="background:rgba(255,255,255,.15);border:none;color:#fff;width:28px;height:28px;border-radius:50%;cursor:pointer;font-size:13px;display:flex;align-items:center;justify-content:center;">🗑</button>
                <button class="chat-popup-close" id="chatCloseBtn"><i class="fas fa-times"></i></button>
            </div>
        </div>
        <div class="chat-popup-body" id="chatMsgBody">
            <div class="chat-message received">
                <p>Halo! 👋 Saya <strong>SIMBS Bot</strong>. Ada yang bisa saya bantu seputar Bank Sampah?</p>
            </div>
            <div id="chatQuickReplies" style="display:flex;flex-wrap:wrap;gap:6px;margin-top:10px;">
                <button class="qr-btn" data-q="Apa itu bank sampah?">Apa itu bank sampah?</button>
                <button class="qr-btn" data-q="Cara daftar nasabah">Cara daftar</button>
                <button class="qr-btn" data-q="Harga sampah terkini">Harga sampah</button>
                <button class="qr-btn" data-q="Cara tarik saldo">Cara tarik saldo</button>
            </div>
            
            <div id="chatTyping" style="display:none;padding:6px 0 0;">
                <div class="chat-message received" style="margin:0;">
                    <p style="display:flex;gap:4px;align-items:center;padding:4px 0;">
                        <span class="tdot"></span><span class="tdot"></span><span class="tdot"></span>
                    </p>
                </div>
            </div>
        </div>
        <div class="chat-popup-footer">
            <input type="text" id="chatMsgInput" class="chat-input" placeholder="Tulis pesan..." maxlength="500" autocomplete="off">
            <button class="chat-send" id="chatMsgSend" type="button"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>
</div>

<style>
.tdot{width:7px;height:7px;background:#9ca3af;border-radius:50%;display:inline-block;animation:tdotAnim 1.2s infinite ease-in-out}
.tdot:nth-child(2){animation-delay:.2s}.tdot:nth-child(3){animation-delay:.4s}
@keyframes tdotAnim{0%,60%,100%{transform:translateY(0)}30%{transform:translateY(-6px)}}
.qr-btn{background:#f0fdf6;border:1.5px solid #a3e6be;color:#155230;border-radius:20px;padding:5px 12px;font-size:12px;font-weight:600;cursor:pointer;transition:all .15s;font-family:inherit}
.qr-btn:hover{background:#22874f;color:#fff;border-color:#22874f}
.chat-msg-sent{display:flex;justify-content:flex-end;margin-bottom:10px}
.chat-msg-sent p{background:#22874f;color:#fff;border-radius:18px 18px 4px 18px;padding:10px 14px;max-width:80%;font-size:13.5px;line-height:1.5;margin:0}
.chat-message.received p{border-radius:18px 18px 18px 4px}
.chat-popup-body {
    overflow-y: auto !important;
    max-height: 340px;
</style>

<script>
(function(){
    var CSRF = '';
    var m = document.querySelector('meta[name="csrf-token"]');
    if(m) CSRF = m.getAttribute('content');

    var popup   = document.getElementById('chatPopup');
    var msgBody = document.getElementById('chatMsgBody');
    var input   = document.getElementById('chatMsgInput');
    var sendBtn = document.getElementById('chatMsgSend');
    var typing  = document.getElementById('chatTyping');
    var qr      = document.getElementById('chatQuickReplies');
    var bubble  = document.getElementById('chatBubble');
    var busy    = false;
    var history = [];

    document.getElementById('chatBtn').onclick = function(){
        var isOpen = popup.classList.contains('active');
        if(isOpen){
            popup.classList.remove('active');
        } else {
            popup.classList.add('active');
            if(bubble) bubble.style.display = 'none';
            input.focus();
        }
    };

    document.getElementById('chatCloseBtn').onclick = function(){
        popup.classList.remove('active');
    };

    document.getElementById('chatClearBtn').onclick = function(){
        history = [];
        var msgs = msgBody.querySelectorAll('.chat-message, .chat-msg-sent');
        for(var i=1;i<msgs.length;i++) msgs[i].remove();
        if(qr) qr.style.display = 'flex';
    };

    var qrBtns = document.querySelectorAll('.qr-btn');
    for(var i=0;i<qrBtns.length;i++){
        qrBtns[i].onclick = function(){
            send(this.getAttribute('data-q'));
        };
    }

    sendBtn.onclick = function(){ send(input.value); };
    input.onkeydown = function(e){
        if(e.key==='Enter'&&!e.shiftKey){ e.preventDefault(); send(input.value); }
    };

    function send(text){
        text = (text||'').trim();
        if(!text || busy) return;
        if(qr) qr.style.display = 'none';

        addBubble('sent', text);
        history.push({role:'user',content:text});
        input.value = '';
        busy = true;
        sendBtn.disabled = true;
        typing.style.display = 'block';
        scroll();

        var payload = { message: text, history: history.slice(-10) };

        fetch('/chatbot', {
            method: 'POST',
            headers: Object.assign(
                {'Content-Type':'application/json','Accept':'application/json'},
                CSRF ? {'X-CSRF-TOKEN': CSRF} : {}
            ),
            body: JSON.stringify(payload)
        })
        .then(async function(res){
            var status = res.status;
            var raw = '';
            try { raw = await res.text(); } catch(e){ raw = ''; }

            var reply = 'Maaf, terjadi kesalahan. Coba lagi. 🙏';
            try {
                var d = raw ? JSON.parse(raw) : null;
                if(d && d.reply) reply = d.reply;
                else if(raw) reply = raw;
            } catch(e){
                if(raw) reply = raw;
            }

            if(status && status !== 200) {
                reply = reply.indexOf('Maaf') === 0
                    ? reply + ' (HTTP ' + status + ')'
                    : 'Maaf, gagal (HTTP ' + status + '). ' + reply;
            }

            typing.style.display = 'none';
            addBubble('received', reply);
            history.push({role:'assistant',content:reply});
            busy = false;
            sendBtn.disabled = false;
            input.focus();
        })
        .catch(function(err){
            typing.style.display = 'none';
            addBubble('received','Maaf, koneksi bermasalah. Coba lagi. 🙏');
            busy = false;
            sendBtn.disabled = false;
            input.focus();
        });
    }

    function addBubble(type, text){
        var div = document.createElement('div');
        var p   = document.createElement('p');
        p.innerHTML = text.replace(/\n/g,'<br>');
        div.appendChild(p);
        div.className = type === 'sent' ? 'chat-msg-sent' : 'chat-message received';
        msgBody.insertBefore(div, typing);
        scroll();
    }

    function scroll(){ msgBody.scrollTop = msgBody.scrollHeight; }
})();
</script><?php /**PATH C:\laragon\www\banksampah\resources\views/partials/chat-widget.blade.php ENDPATH**/ ?>