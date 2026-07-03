@extends('layouts.dashboard')

@section('css')
<style>
    /* Remove content-wrapper padding for full-height chat layout */
    .content-wrapper {
        padding: 0 !important;
        overflow: hidden !important;
    }
    .content-backdrop { display: none !important; }

    /* ── Chat shell ── */
    .ai-chat-shell {
        display: flex;
        height: calc(100vh - 64px);
        overflow: hidden;
        background: #f7f7f9;
    }

    /* ── History sidebar ── */
    .ai-sidebar {
        width: 280px;
        min-width: 280px;
        background: #fff;
        border-right: 1px solid rgba(46, 38, 61, .12);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .ai-sidebar-header {
        padding: 1rem;
        border-bottom: 1px solid rgba(46, 38, 61, .08);
        flex-shrink: 0;
    }
    .ai-sidebar-search {
        padding: .5rem 1rem .75rem;
        flex-shrink: 0;
    }
    .ai-history-list {
        flex: 1;
        overflow-y: auto;
        padding: .25rem .5rem;
    }
    .ai-history-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: .6rem .75rem;
        border-radius: .375rem;
        cursor: pointer;
        transition: background .15s;
        margin-bottom: 2px;
        gap: .5rem;
    }
    .ai-history-item:hover { background: rgba(102, 108, 255, .08); }
    .ai-history-item.active { background: rgba(102, 108, 255, .12); }
    .ai-history-item .item-text {
        flex: 1;
        overflow: hidden;
    }
    .ai-history-item .item-preview {
        font-size: .8rem;
        color: #4b4b4b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
    }
    .ai-history-item .item-date {
        font-size: .7rem;
        color: #a1acb8;
        display: block;
        margin-top: 1px;
    }
    .ai-history-item .btn-delete-conv {
        opacity: 0;
        transition: opacity .15s;
        padding: 2px 4px;
        line-height: 1;
        color: #ff4d49;
        background: none;
        border: none;
        border-radius: .25rem;
        flex-shrink: 0;
    }
    .ai-history-item:hover .btn-delete-conv { opacity: 1; }
    .ai-history-empty {
        text-align: center;
        padding: 2rem 1rem;
        color: #a1acb8;
        font-size: .85rem;
    }

    /* ── Main chat area ── */
    .ai-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .ai-chat-header {
        padding: .875rem 1.5rem;
        background: #fff;
        border-bottom: 1px solid rgba(46, 38, 61, .12);
        flex-shrink: 0;
        display: flex;
        align-items: center;
        gap: .75rem;
    }
    .ai-chat-header .header-icon {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: rgba(102, 108, 255, .12);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* ── Messages area ── */
    .ai-messages-area {
        flex: 1;
        overflow-y: auto;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }
    .ai-welcome {
        margin: auto;
        text-align: center;
        padding: 2rem;
        max-width: 420px;
    }
    .ai-welcome .welcome-icon {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: rgba(102, 108, 255, .1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.25rem;
    }

    /* Message row */
    .msg-row {
        display: flex;
        gap: .75rem;
        max-width: 85%;
    }
    .msg-row.user-row {
        align-self: flex-end;
        flex-direction: row-reverse;
    }
    .msg-row.assistant-row {
        align-self: flex-start;
    }
    .msg-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .85rem;
        flex-shrink: 0;
        margin-top: 2px;
    }
    .msg-avatar.user-avatar {
        background: var(--bs-primary);
        color: #fff;
    }
    .msg-avatar.assistant-avatar {
        background: rgba(102, 108, 255, .1);
        color: var(--bs-primary);
    }
    .msg-bubble-wrap { display: flex; flex-direction: column; gap: 3px; }
    .msg-bubble {
        padding: .65rem 1rem;
        border-radius: 1.1rem;
        font-size: .9rem;
        line-height: 1.6;
        word-break: break-word;
        white-space: pre-wrap;
    }
    .user-row .msg-bubble {
        background: var(--bs-primary);
        color: #fff;
        border-bottom-right-radius: .3rem;
    }
    .assistant-row .msg-bubble {
        background: #fff;
        color: #4b4b4b;
        border-bottom-left-radius: .3rem;
        box-shadow: 0 1px 6px rgba(46, 38, 61, .08);
    }
    .msg-time {
        font-size: .7rem;
        color: #a1acb8;
    }
    .user-row .msg-time { text-align: right; }

    /* Typing indicator */
    .typing-indicator .msg-bubble {
        display: flex;
        align-items: center;
        gap: 4px;
        padding: .75rem 1rem;
    }
    .typing-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #a1acb8;
        animation: typingPulse 1.4s infinite ease-in-out;
    }
    .typing-dot:nth-child(2) { animation-delay: .2s; }
    .typing-dot:nth-child(3) { animation-delay: .4s; }
    @keyframes typingPulse {
        0%, 60%, 100% { transform: scale(1); opacity: .5; }
        30% { transform: scale(1.3); opacity: 1; }
    }

    /* Error bubble */
    .msg-bubble.error-bubble {
        background: rgba(255, 77, 73, .08);
        color: #ff4d49;
        border: 1px solid rgba(255, 77, 73, .2);
    }

    /* ── Input area ── */
    .ai-input-area {
        background: #fff;
        border-top: 1px solid rgba(46, 38, 61, .12);
        padding: 1rem 1.5rem 1.25rem;
        flex-shrink: 0;
    }
    .ai-input-row {
        display: flex;
        align-items: flex-end;
        gap: .625rem;
    }
    .ai-textarea-wrap {
        flex: 1;
        position: relative;
    }
    #messageInput {
        resize: none;
        min-height: 52px;
        max-height: 180px;
        border-radius: .5rem;
        padding: .75rem 1rem;
        font-size: .9rem;
        line-height: 1.5;
        overflow-y: auto;
    }
    #dateInput {
        width: 150px;
        flex-shrink: 0;
        height: 52px;
        border-radius: .5rem;
        font-size: .85rem;
    }
    #sendBtn {
        width: 52px;
        height: 52px;
        border-radius: .5rem;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }
    .ai-input-hint {
        margin-top: .4rem;
        font-size: .72rem;
        color: #a1acb8;
    }
</style>
@endsection

@section('content')
<div class="ai-chat-shell">

    {{-- ══════════ History Sidebar ══════════ --}}
    <div class="ai-sidebar">
        <div class="ai-sidebar-header">
            <button id="newChatBtn" class="btn btn-primary btn-sm w-100">
                <i class="mdi mdi-plus me-1"></i> New Chat
            </button>
        </div>
        <div class="ai-sidebar-search">
            <input type="text" id="searchInput" class="form-control form-control-sm"
                placeholder="Search conversations…">
        </div>
        <div class="ai-history-list" id="historyList">
            <div class="ai-history-empty">
                <i class="mdi mdi-chat-outline mdi-24px d-block mb-1"></i>
                No conversations yet
            </div>
        </div>
    </div>

    {{-- ══════════ Main Chat ══════════ --}}
    <div class="ai-main">

        {{-- Header --}}
        <div class="ai-chat-header">
            <div class="header-icon">
                <i class="mdi mdi-robot-outline mdi-20px text-primary"></i>
            </div>
            <div>
                <div class="fw-semibold" style="font-size:.95rem; line-height:1.2;">AI Assistant</div>
                <div style="font-size:.75rem; color:#a1acb8;">Powered by intelligent automation</div>
            </div>
            <div class="ms-auto" id="sessionBadge" style="display:none;">
                <span class="badge bg-label-primary" style="font-size:.7rem;">
                    <i class="mdi mdi-link-variant me-1"></i>
                    <span id="sessionLabel">New Chat</span>
                </span>
            </div>
        </div>

        {{-- Messages --}}
        <div class="ai-messages-area" id="messagesArea">
            <div class="ai-welcome" id="welcomeScreen">
                <div class="welcome-icon">
                    <i class="mdi mdi-robot-outline mdi-36px text-primary"></i>
                </div>
                <h5 class="mb-2">How can I help you today?</h5>
                <p class="text-muted mb-0" style="font-size:.875rem;">
                    Type your message below. Optionally select a date for context-aware responses.
                </p>
            </div>
        </div>

        {{-- Input Area --}}
        <div class="ai-input-area">
            <div class="ai-input-row">
                <input type="date" id="dateInput" class="form-control"
                    title="Optional date for context" placeholder="Date (optional)">
                <div class="ai-textarea-wrap">
                    <textarea id="messageInput" class="form-control"
                        rows="1"
                        placeholder="Type your message…"></textarea>
                </div>
                <button id="sendBtn" class="btn btn-primary" title="Send message">
                    <span id="sendIcon"><i class="mdi mdi-send mdi-18px"></i></span>
                    <span id="loadingSpinner" style="display:none;">
                        <span class="spinner-border spinner-border-sm"></span>
                    </span>
                </button>
            </div>
            <div class="ai-input-hint">
                <i class="mdi mdi-keyboard-outline mdi-14px"></i>
                <strong>Enter</strong> to send &nbsp;·&nbsp; <strong>Shift + Enter</strong> for new line
            </div>
        </div>

    </div>
</div>
@endsection

@section('custom-js')
<script>
(function () {
    'use strict';

    /* ── State ── */
    let currentSessionId = generateUUID();
    let activeHistoryId  = null;   // the id used to represent the current session in the sidebar
    let isSending        = false;

    const routes = {
        send    : '{{ route("ai.send") }}',
        history : '{{ route("ai.history") }}',
        show    : (id) => '{{ url("ai/show") }}/' + id,
        destroy : (id) => '{{ url("ai/destroy") }}/' + id,
    };

    /* ── DOM refs ── */
    const messagesArea  = document.getElementById('messagesArea');
    const messageInput  = document.getElementById('messageInput');
    const dateInput     = document.getElementById('dateInput');
    const sendBtn       = document.getElementById('sendBtn');
    const sendIcon      = document.getElementById('sendIcon');
    const loadingSpinner= document.getElementById('loadingSpinner');
    const historyList   = document.getElementById('historyList');
    const searchInput   = document.getElementById('searchInput');
    const newChatBtn    = document.getElementById('newChatBtn');
    const welcomeScreen = document.getElementById('welcomeScreen');
    const sessionBadge  = document.getElementById('sessionBadge');
    const sessionLabel  = document.getElementById('sessionLabel');

    /* ─────────────────────────────────────────
       Utility
    ───────────────────────────────────────── */
    function generateUUID() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
            const r = Math.random() * 16 | 0;
            return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
        });
    }

    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]').content;
    }

    function scrollToBottom() {
        messagesArea.scrollTop = messagesArea.scrollHeight;
    }

    function autoResizeTextarea() {
        messageInput.style.height = 'auto';
        messageInput.style.height = Math.min(messageInput.scrollHeight, 180) + 'px';
    }

    function setSending(state) {
        isSending = state;
        sendBtn.disabled = state;
        messageInput.disabled = state;
        sendIcon.style.display  = state ? 'none'  : 'flex';
        loadingSpinner.style.display = state ? 'inline-block' : 'none';
    }

    function hideWelcome() {
        if (welcomeScreen) welcomeScreen.remove();
    }

    function showSessionBadge() {
        sessionLabel.textContent = 'Active Session';
        sessionBadge.style.display = 'block';
    }

    /* ─────────────────────────────────────────
       Render helpers
    ───────────────────────────────────────── */
    function renderUserMessage(text, time) {
        hideWelcome();
        const row = document.createElement('div');
        row.className = 'msg-row user-row';
        row.innerHTML = `
            <div class="msg-avatar user-avatar">
                <i class="mdi mdi-account mdi-16px"></i>
            </div>
            <div class="msg-bubble-wrap">
                <div class="msg-bubble">${escapeHtml(text)}</div>
                <span class="msg-time">${time || 'Just now'}</span>
            </div>`;
        messagesArea.appendChild(row);
        scrollToBottom();
        return row;
    }

    function renderAssistantMessage(text, time, isError) {
        const row = document.createElement('div');
        row.className = 'msg-row assistant-row';
        const bubbleClass = isError ? 'msg-bubble error-bubble' : 'msg-bubble';
        const icon = isError
            ? '<i class="mdi mdi-alert-circle-outline mdi-16px text-danger"></i>'
            : '<i class="mdi mdi-robot-outline mdi-16px text-primary"></i>';
        row.innerHTML = `
            <div class="msg-avatar assistant-avatar">${icon}</div>
            <div class="msg-bubble-wrap">
                <div class="${bubbleClass}">${escapeHtml(text)}</div>
                <span class="msg-time">${time || ''}</span>
            </div>`;
        messagesArea.appendChild(row);
        scrollToBottom();
        return row;
    }

    function renderTypingIndicator() {
        const row = document.createElement('div');
        row.className = 'msg-row assistant-row typing-indicator';
        row.id = 'typingIndicator';
        row.innerHTML = `
            <div class="msg-avatar assistant-avatar">
                <i class="mdi mdi-robot-outline mdi-16px text-primary"></i>
            </div>
            <div class="msg-bubble-wrap">
                <div class="msg-bubble">
                    <span class="typing-dot"></span>
                    <span class="typing-dot"></span>
                    <span class="typing-dot"></span>
                </div>
            </div>`;
        messagesArea.appendChild(row);
        scrollToBottom();
        return row;
    }

    function removeTypingIndicator() {
        const indicator = document.getElementById('typingIndicator');
        if (indicator) indicator.remove();
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

    /* ─────────────────────────────────────────
       History sidebar
    ───────────────────────────────────────── */
    function loadHistory(query) {
        const url = routes.history + (query ? '?q=' + encodeURIComponent(query) : '');
        fetch(url, { headers: { 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(res => {
                if (!res.success) return;
                renderHistory(res.data);
            })
            .catch(() => {});
    }

    function renderHistory(items) {
        if (!items || items.length === 0) {
            historyList.innerHTML = `
                <div class="ai-history-empty">
                    <i class="mdi mdi-chat-outline mdi-24px d-block mb-1"></i>
                    No conversations yet
                </div>`;
            return;
        }

        historyList.innerHTML = items.map(item => `
            <div class="ai-history-item ${item.id === activeHistoryId ? 'active' : ''}"
                 data-id="${item.id}" data-session="${item.session_id}">
                <div class="item-text">
                    <span class="item-preview">${escapeHtml(item.preview)}</span>
                    <span class="item-date">${item.created_at}</span>
                </div>
                <button class="btn-delete-conv" data-id="${item.id}" title="Delete conversation">
                    <i class="mdi mdi-trash-can-outline mdi-16px"></i>
                </button>
            </div>`
        ).join('');
    }

    /* ─────────────────────────────────────────
       Load conversation
    ───────────────────────────────────────── */
    function loadConversation(id) {
        activeHistoryId = id;
        document.querySelectorAll('.ai-history-item').forEach(el => {
            el.classList.toggle('active', parseInt(el.dataset.id) === id);
        });

        fetch(routes.show(id), { headers: { 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(res => {
                if (!res.success) return;
                // Clear messages area
                messagesArea.innerHTML = '';
                currentSessionId = res.session_id;
                showSessionBadge();

                res.data.forEach(msg => {
                    renderUserMessage(msg.user_message, msg.created_at);
                    if (msg.status === 'completed' && msg.ai_response) {
                        renderAssistantMessage(msg.ai_response, msg.created_at, false);
                    } else if (msg.status === 'failed') {
                        renderAssistantMessage(
                            msg.error_message || 'An error occurred with this message.',
                            msg.created_at, true
                        );
                    }
                });
            })
            .catch(() => {});
    }

    /* ─────────────────────────────────────────
       Delete conversation
    ───────────────────────────────────────── */
    function deleteConversation(id) {
        if (!confirm('Delete this entire conversation?')) return;

        fetch(routes.destroy(id), {
            method : 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken(),
                'Accept'      : 'application/json',
                'Content-Type': 'application/json',
            },
        })
        .then(r => r.json())
        .then(res => {
            if (!res.success) return;
            // If we just deleted the active session, start a new chat
            if (activeHistoryId === id) startNewChat();
            loadHistory(searchInput.value.trim());
        })
        .catch(() => {});
    }

    /* ─────────────────────────────────────────
       Send message
    ───────────────────────────────────────── */
    function sendMessage() {
        const text = messageInput.value.trim();
        if (!text || isSending) return;

        setSending(true);

        const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        renderUserMessage(text, time);
        const typingRow = renderTypingIndicator();

        const payload = {
            message   : text,
            session_id: currentSessionId,
            date      : dateInput.value || null,
        };

        messageInput.value = '';
        messageInput.style.height = 'auto';

        fetch(routes.send, {
            method : 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken(),
                'Accept'      : 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(payload),
        })
        .then(r => r.json())
        .then(res => {
            removeTypingIndicator();
            if (res.success) {
                activeHistoryId = res.data.id;
                showSessionBadge();
                renderAssistantMessage(res.data.ai_response, res.data.created_at, false);
                loadHistory(searchInput.value.trim());
            } else {
                renderAssistantMessage(
                    res.message || 'An error occurred. Please try again.',
                    '', true
                );
            }
        })
        .catch(() => {
            removeTypingIndicator();
            renderAssistantMessage('Network error. Please check your connection and try again.', '', true);
        })
        .finally(() => {
            setSending(false);
            messageInput.focus();
        });
    }

    /* ─────────────────────────────────────────
       New chat
    ───────────────────────────────────────── */
    function startNewChat() {
        currentSessionId = generateUUID();
        activeHistoryId  = null;
        sessionBadge.style.display = 'none';

        messagesArea.innerHTML = `
            <div class="ai-welcome" id="welcomeScreen">
                <div class="welcome-icon">
                    <i class="mdi mdi-robot-outline mdi-36px text-primary"></i>
                </div>
                <h5 class="mb-2">How can I help you today?</h5>
                <p class="text-muted mb-0" style="font-size:.875rem;">
                    Type your message below. Optionally select a date for context-aware responses.
                </p>
            </div>`;

        document.querySelectorAll('.ai-history-item').forEach(el => el.classList.remove('active'));
        messageInput.focus();
    }

    /* ─────────────────────────────────────────
       Event listeners
    ───────────────────────────────────────── */
    newChatBtn.addEventListener('click', startNewChat);

    messageInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    messageInput.addEventListener('input', autoResizeTextarea);

    sendBtn.addEventListener('click', sendMessage);

    // History item click (delegation)
    historyList.addEventListener('click', function (e) {
        const deleteBtn = e.target.closest('.btn-delete-conv');
        if (deleteBtn) {
            e.stopPropagation();
            deleteConversation(parseInt(deleteBtn.dataset.id));
            return;
        }
        const item = e.target.closest('.ai-history-item');
        if (item) {
            loadConversation(parseInt(item.dataset.id));
        }
    });

    // Debounced search
    let searchTimer;
    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => loadHistory(this.value.trim()), 300);
    });

    /* ─────────────────────────────────────────
       Init
    ───────────────────────────────────────── */
    loadHistory('');
    messageInput.focus();
}());
</script>
@endsection
