document.querySelectorAll('[data-chat-group-form]').forEach((form) => {
    const members = [...form.querySelectorAll('[data-chat-member]')];
    const count = form.querySelector('[data-chat-member-count]');
    const update = () => { count.textContent = `${members.filter((member) => member.checked).length} selected`; };
    members.forEach((member) => member.addEventListener('change', update));
    update();
});

const room = document.querySelector('[data-chat-room]');

if (room && window.fetch) {
    const stream = room.querySelector('[data-chat-stream]');
    const log = room.querySelector('[data-chat-log]');
    const empty = room.querySelector('[data-chat-empty]');
    const older = room.querySelector('[data-chat-older]');
    const form = room.querySelector('[data-chat-composer]');
    const body = form.elements.namedItem('body');
    const clientId = form.elements.namedItem('client_id');
    const send = form.querySelector('[data-chat-send]');
    const error = form.querySelector('[data-chat-error]');
    const status = form.querySelector('[data-chat-status]');
    const preview = document.querySelector(`[data-chat-preview="${room.dataset.groupId}"]`);
    const ids = new Set([...log.querySelectorAll('[data-chat-message]')].map((node) => node.dataset.chatMessage));
    let cursor = Number(room.dataset.cursor);
    let polling = false;
    let sending = false;
    let expired = false;
    let timer;

    const showError = (message) => {
        error.textContent = message;
        error.hidden = !message;
    };
    const readResponse = async (response) => {
        if (response.redirected || [401, 403, 419].includes(response.status)) {
            expired = true;
            throw new Error('Your session or group access has changed. Reload this page to continue.');
        }
        const data = await response.json().catch(() => ({}));
        if (!response.ok) {
            throw new Error(data.errors?.body?.[0] || (response.status === 429
                ? 'You are sending too quickly. Wait a moment and try again.'
                : 'Could not complete this request. Please try again.'));
        }
        return data;
    };
    const request = async (url, options = {}) => {
        try {
            return await readResponse(await fetch(url, {
                credentials: 'same-origin',
                signal: typeof AbortSignal !== 'undefined' && AbortSignal.timeout ? AbortSignal.timeout(15000) : undefined,
                ...options,
                headers: { Accept: 'application/json', ...options.headers },
            }));
        } catch (failure) {
            if (['TypeError', 'TimeoutError', 'AbortError'].includes(failure.name)) {
                throw new Error('Connection interrupted. Your draft is kept. Please try again.');
            }
            throw failure;
        }
    };
    const insertMessages = (html, forceBottom = false) => {
        const nearBottom = stream.scrollHeight - stream.scrollTop - stream.clientHeight < 100;
        const fragment = document.createElement('template');
        fragment.innerHTML = html;
        let added = 0;
        fragment.content.querySelectorAll('[data-chat-message]').forEach((node) => {
            const id = node.dataset.chatMessage;
            if (ids.has(id)) {
                return;
            }
            const next = [...log.children].find((item) => Number(item.dataset.chatMessage) > Number(id));
            log.insertBefore(node, next || null);
            ids.add(id);
            added += 1;
        });
        empty.hidden = ids.size > 0;
        if (added && preview) {
            preview.textContent = log.lastElementChild.querySelector('.admin-chat-bubble').textContent.slice(0, 65);
        }
        if (forceBottom || (nearBottom && added)) {
            stream.scrollTop = stream.scrollHeight;
        }
        return added;
    };
    const schedule = (delay = 4500) => {
        clearTimeout(timer);
        if (!expired && !document.hidden) {
            timer = setTimeout(poll, delay);
        }
    };
    const poll = async () => {
        if (polling || expired || document.hidden) {
            return;
        }
        polling = true;
        let delay = 4500;
        try {
            const url = new URL(room.dataset.feedUrl, window.location.href);
            url.searchParams.set('after', cursor);
            const data = await request(url);
            const added = insertMessages(data.html);
            cursor = Math.max(cursor, Number(data.last_id || 0));
            status.textContent = added ? `${added} new ${added === 1 ? 'message' : 'messages'}.` : 'Conversation is up to date.';
            if (data.has_more) {
                delay = 100;
            }
        } catch (failure) {
            status.textContent = expired ? failure.message : 'Unable to refresh. Reconnecting automatically…';
            delay = 10000;
        } finally {
            polling = false;
            schedule(delay);
        }
    };

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (sending) {
            return;
        }
        if (!body.value.trim() || [...body.value].length > 5000) {
            showError(!body.value.trim() ? 'Write a message before sending.' : 'Keep your message to 5,000 characters or fewer.');
            body.setAttribute('aria-invalid', 'true');
            body.focus();
            return;
        }
        if (expired) {
            showError('Reload this page to sign in again before sending.');
            return;
        }
        sending = true;
        send.disabled = true;
        send.textContent = 'Sending…';
        showError('');
        body.removeAttribute('aria-invalid');
        const sentDraft = body.value;
        try {
            const data = await request(form.action, { method: 'POST', body: new FormData(form) });
            insertMessages(data.html, true);
            const acknowledgedDraft = sentDraft.trim() === data.body;
            if (body.value === sentDraft && acknowledgedDraft) {
                body.value = '';
            }
            clientId.value = data.next_client_id;
            status.textContent = acknowledgedDraft ? 'Message sent.' : 'Previous message already sent. Your updated draft is ready to send.';
            schedule(100);
        } catch (failure) {
            showError(failure.message);
        } finally {
            sending = false;
            send.disabled = false;
            send.textContent = 'Send →';
        }
    });
    body.addEventListener('input', () => {
        body.removeAttribute('aria-invalid');
        showError('');
    });
    older.addEventListener('click', async () => {
        older.disabled = true;
        older.textContent = 'Loading…';
        const height = stream.scrollHeight;
        const top = stream.scrollTop;
        try {
            const url = new URL(room.dataset.feedUrl, window.location.href);
            url.searchParams.set('before', older.dataset.before);
            const data = await request(url);
            insertMessages(data.html);
            older.hidden = !data.has_more;
            if (data.first_id) {
                older.dataset.before = data.first_id;
            }
            stream.scrollTop = top + stream.scrollHeight - height;
            status.textContent = 'Earlier messages loaded.';
        } catch (failure) {
            showError(failure.message);
        } finally {
            older.disabled = false;
            older.textContent = 'Load Earlier Messages';
        }
    });
    document.addEventListener('visibilitychange', () => {
        clearTimeout(timer);
        if (!document.hidden) {
            schedule(100);
        }
    });
    stream.scrollTop = stream.scrollHeight;
    schedule();
}
