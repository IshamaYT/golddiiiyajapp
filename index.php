<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Дія</title>
    <style>
        :root {
            --bg-grad: linear-gradient(160deg, #a1c4fd 0%, #e2f1f8 50%, #d8f3a8 100%);
            --doc-bg: rgba(255, 255, 255, 0.45);
            --text: #000;
            --green-start: #b7eb3c;
            --green-end: #9cd425;
            --nav-color: #8e8e93;
            --nav-active: #ffffff;
        }

        * { 
            box-sizing: border-box; 
            -webkit-tap-highlight-color: transparent;
            user-select: none;
            /* ПРИБИРАЄМО СКРОЛБАРИ ДЛЯ ВСІХ ЕЛЕМЕНТІВ */
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE/Edge */
        }
        *::-webkit-scrollbar { 
            display: none; /* Chrome/Safari */
        }

        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: var(--bg-grad);
            background-attachment: fixed;
            color: var(--text);
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            padding-top: env(safe-area-inset-top);
            padding-bottom: env(safe-area-inset-bottom);
        }

        /* Екрани (Сторінки) */
        .screen {
            display: none;
            flex: 1;
            overflow-y: auto;
            padding: 0 16px;
            width: 100%;
        }
        .screen.active {
            display: flex;
            flex-direction: column;
        }

        /* --- ЕКРАН ДОКУМЕНТІВ --- */
        #screen-docs {
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .controls {
            position: absolute;
            top: 20px;
            width: 100%;
            max-width: 380px;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            z-index: 20;
            padding-right: 8px;
        }

        .btn-control {
            background: rgba(255,255,255,0.7);
            border: 1px solid #000;
            color: #000;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 20px;
            cursor: pointer;
            backdrop-filter: blur(10px);
        }
        .btn-control.active {
            background: var(--green-start);
        }

        .flip-container {
            width: 100%;
            max-width: 380px;
            aspect-ratio: 380 / 500;
            perspective: 1000px;
            position: relative;
            margin-top: 30px;
            flex-shrink: 0;
        }

        .flipper {
            position: relative;
            width: 100%;
            height: 100%;
            transition: transform 0.6s cubic-bezier(0.4, 0.0, 0.2, 1);
            transform-style: preserve-3d;
            cursor: pointer;
        }

        .flipper.flipped {
            transform: rotateY(180deg);
        }

        .front, .back {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .front {
            background: var(--doc-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            display: flex;
            flex-direction: column;
        }

        .doc-header {
            padding: 24px 24px 0 24px;
            font-size: 22px;
            font-weight: 600;
            letter-spacing: -0.5px;
        }

        .doc-top {
            display: flex;
            padding: 20px 24px;
            gap: 16px;
            flex: 1;
        }

        .photo-container {
            width: 40%;
            max-width: 140px;
            aspect-ratio: 3 / 4;
            border-radius: 12px;
            overflow: hidden;
            background: #ddd;
            position: relative;
            flex-shrink: 0;
        }
        .photo-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .photo-container input {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .info {
            display: flex;
            flex-direction: column;
            gap: 16px;
            font-size: 14px;
            flex: 1;
            padding-top: 10px;
        }
        .info-label { font-weight: 500; color: #222; display: block; margin-bottom: 4px; }
        .info-val { 
            font-weight: 500; font-size: 16px; display: block; 
            background: transparent; border: none; font-family: inherit; color: inherit;
            width: 100%; padding: 0; outline: none; pointer-events: none;
        }

        .war-bar {
            background: linear-gradient(90deg, var(--green-start) 0%, var(--green-end) 100%);
            padding: 16px 24px; font-size: 13px; font-weight: 500; flex-shrink: 0;
        }

        .doc-name {
            padding: 24px; font-size: 26px; font-weight: 500; line-height: 1.15;
            text-transform: uppercase; flex-shrink: 0;
        }
        
        .doc-name textarea {
            background: transparent; border: none; font-family: inherit; font-size: inherit;
            font-weight: inherit; color: inherit; width: 100%; resize: none; outline: none;
            text-transform: uppercase; line-height: 1.15; pointer-events: none; padding: 0; margin: 0;
            overflow: hidden; /* Запобігає появі скролбару всередині */
        }

        .back {
            background: #ffffff; transform: rotateY(180deg);
            display: flex; justify-content: center; align-items: center; padding: 20px;
        }

        .qr-image-wrap {
            width: 100%; max-width: 280px; aspect-ratio: 1/1; position: relative; cursor: pointer;
        }

        .qr-image-wrap img {
            width: 100%; height: 100%; display: block; object-fit: contain;
        }

        .pagination {
            display: flex; gap: 8px; margin-top: 30px; margin-bottom: 20px;
        }
        .pagination .dot {
            width: 6px; height: 6px; border-radius: 50%; background: rgba(255,255,255,0.5);
        }
        .pagination .dot.active { background: #ffffff; }

        .editing .info-val, .editing .doc-name textarea {
            pointer-events: auto; border-bottom: 1px dashed #999;
        }

        /* --- ЕКРАН МЕНЮ --- */
        #screen-menu {
            padding-top: 20px;
            padding-bottom: 20px;
        }
        .menu-header h1 {
            font-size: 34px; font-weight: 600; margin: 10px 0 5px 0; letter-spacing: -1px;
        }
        .menu-header p {
            font-size: 14px; color: #555; margin: 0 0 20px 0;
        }

        .menu-list {
            background: #ffffff;
            border-radius: 16px;
            margin-bottom: 16px;
            overflow: hidden;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 16px;
            font-size: 16px;
            font-weight: 500;
            position: relative;
            cursor: pointer;
        }

        .menu-item:not(:last-child)::after {
            content: '';
            position: absolute;
            bottom: 0; left: 50px; right: 16px;
            height: 1px; background: #e5e5ea;
        }

        .menu-icon {
            width: 24px; height: 24px; margin-right: 16px;
            display: flex; justify-content: center; align-items: center;
            position: relative;
        }
        .menu-icon svg {
            width: 100%; height: 100%; fill: none; stroke: #000; stroke-width: 1.5;
            stroke-linecap: round; stroke-linejoin: round;
        }

        .menu-badge {
            position: absolute; top: -2px; right: -2px;
            width: 8px; height: 8px; background: #ff3b30; border-radius: 50%;
        }

        .btn-exit {
            background: #000; color: #fff; border: none; border-radius: 30px;
            width: 100%; padding: 16px; font-size: 16px; font-weight: 600;
            margin-top: 10px; margin-bottom: 30px; cursor: pointer;
            text-decoration: underline;
        }

        .footer-link {
            text-align: center; font-size: 13px; color: #000;
            text-decoration: underline; margin-bottom: 20px;
        }

        /* --- НАВІГАЦІЯ --- */
        .nav-bar {
            background: #000000;
            height: 83px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 12px 0 12px;
            color: var(--nav-color);
            font-size: 10px;
            font-weight: 500;
            padding-bottom: calc(env(safe-area-inset-bottom) + 5px);
            flex-shrink: 0;
            position: relative;
            z-index: 100;
        }

        .nav-item {
            display: flex; flex-direction: column; align-items: center; gap: 5px;
            flex: 1; position: relative; cursor: pointer;
        }

        .nav-item svg { 
            width: 28px; height: 28px; fill: none; stroke: currentColor;
            stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round;
        }
        
        .nav-item.active { color: var(--nav-active); }

        .notification-dot {
            position: absolute; top: -2px; right: 22px;
            width: 8px; height: 8px; background-color: #ff3b30; border-radius: 50%;
        }
    </style>
</head>
<body>

    <!-- ЕКРАН 1: ДОКУМЕНТИ -->
    <div class="screen active" id="screen-docs">
        <div class="controls">
            <button class="btn-control" id="editBtn" onclick="toggleEdit(event)">Редагувати</button>
        </div>

        <div class="flip-container">
            <div class="flipper" id="flipper">
                
                <!-- ПЕРЕДНЯ СТОРОНА -->
                <div class="front">
                    <div class="doc-header">еДокумент</div>
                    <div class="doc-top">
                        <div class="photo-container">
                            <img id="profileImg" src="" alt="Фото">
                            <input type="file" accept="image/*" id="fileInput" onchange="loadImage(event)">
                        </div>
                        <div class="info">
                            <div>
                                <span class="info-label">Дата народження:</span>
                                <input type="text" class="info-val" id="dobInput" value="30.08.2009" maxlength="10">
                            </div>
                            <div>
                                <span class="info-label">РНОКПП:</span>
                                <input type="text" class="info-val" id="ipnInput" value="4005405631" maxlength="10">
                            </div>
                        </div>
                    </div>
                    <div class="war-bar">Документ діє під час воєнного стану.</div>
                    <div class="doc-name">
                        <textarea id="nameInput" rows="3" oninput="autoResize(this)">УЗУН&#10;КИРИЛО&#10;ІЛЛІЧ</textarea>
                    </div>
                </div>

                <!-- ЗАДНЯ СТОРОНА (QR) -->
                <div class="back">
                    <div class="qr-image-wrap" onclick="document.getElementById('qrFileInput').click()">
                        <img id="qrDisplay" src="" alt="QR Code">
                        <input type="file" id="qrFileInput" accept="image/*" style="display: none;" onchange="loadCustomQr(event)">
                    </div>
                </div>

            </div>
        </div>
        
        <div class="pagination">
            <div class="dot active"></div>
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>
    </div>

    <!-- ЕКРАН 2: МЕНЮ -->
    <div class="screen" id="screen-menu">
        <div class="menu-header">
            <h1>Меню</h1>
            <p>Версія Дії: 4.36.4.2854</p>
        </div>

        <div class="menu-list">
            <div class="menu-item">
                <div class="menu-icon">
                    <svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    <div class="menu-badge"></div>
                </div>
                Повідомлення
            </div>
        </div>

        <div class="menu-list">
            <div class="menu-item">
                <div class="menu-icon">
                    <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                Дія.Підпис
            </div>
            <div class="menu-item">
                <div class="menu-icon">
                    <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                </div>
                Історія підписань
            </div>
        </div>

        <div class="menu-list">
            <div class="menu-item">
                <div class="menu-icon">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                </div>
                Налаштування
            </div>
            <div class="menu-item">
                <div class="menu-icon">
                    <svg viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                </div>
                Оновити застосунок
            </div>
            <div class="menu-item">
                <div class="menu-icon">
                    <svg viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                </div>
                Підключені пристрої
            </div>
        </div>

        <div class="menu-list">
            <div class="menu-item">
                <div class="menu-icon">
                    <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                </div>
                Служба підтримки
            </div>
            <div class="menu-item">
                <div class="menu-icon">
                    <svg viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                </div>
                Копіювати номер пристрою
            </div>
            <div class="menu-item">
                <div class="menu-icon">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                </div>
                Питання та відповіді
            </div>
        </div>

        <button class="btn-exit">ВИЙТИ</button>
        <div class="footer-link">Повідомлення про обробку персональних даних</div>
    </div>

    <!-- НИЖНЯ НАВІГАЦІЯ -->
    <div class="nav-bar">
        <div class="nav-item" onclick="switchScreen('docs', this)">
            <svg viewBox="0 0 24 24">
                <rect x="3" y="5" width="18" height="14" rx="2" />
                <path d="M7 9h10M7 13h6" />
            </svg>
            <span>Стрічка</span>
        </div>
        <div class="nav-item active" onclick="switchScreen('docs', this)">
            <svg viewBox="0 0 24 24">
                <rect x="5" y="3" width="14" height="18" rx="2" />
                <path d="M9 8h6M9 12h6M9 16h4" />
                <path d="M15 3v4h4" fill="none" />
            </svg>
            <span>Документи</span>
        </div>
        <div class="nav-item" onclick="switchScreen('docs', this)">
            <svg viewBox="0 0 24 24">
                <rect x="3" y="3" width="18" height="18" rx="4" />
                <path d="M12 7l1.5 3.5L17 12l-3.5 1.5L12 17l-1.5-3.5L7 12l3.5-1.5z" />
            </svg>
            <span>Дія.AI</span>
        </div>
        <div class="nav-item" onclick="switchScreen('docs', this)">
            <svg viewBox="0 0 24 24">
                <rect x="3" y="3" width="18" height="18" rx="4" />
                <path d="M13 7l-4 7h4l-2 4 4-7h-4z" />
            </svg>
            <span>Сервіси</span>
        </div>
        <div class="nav-item" onclick="switchScreen('menu', this)">
            <svg viewBox="0 0 24 24">
                <circle cx="12" cy="8" r="4" />
                <path d="M4 20c0-4 4-6 8-6s8 2 8 6" />
            </svg>
            <span>Меню</span>
            <div class="notification-dot"></div>
        </div>
    </div>

    <script>
        // --- ДЕФОЛТНІ ЗОБРАЖЕННЯ ---
        const defaultAvatar = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 110 145'><rect width='110' height='145' fill='%23e0d6c8'/><rect x='0' y='90' width='110' height='55' fill='%23b3231b'/><circle cx='55' cy='55' r='30' fill='%23fff'/><path d='M 30 40 Q 55 20 80 40' stroke='%23333' stroke-width='10' fill='none'/></svg>";
        const defaultQr = "data:image/png;base64,/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=";

        // --- ПЕРЕМИКАННЯ ЕКРАНІВ ---
        function switchScreen(screenName, element) {
            document.querySelectorAll('.screen').forEach(s => s.classList.remove('active'));
            if(screenName === 'menu') {
                document.getElementById('screen-menu').classList.add('active');
            } else {
                document.getElementById('screen-docs').classList.add('active');
            }
            document.querySelectorAll('.nav-item').forEach(item => item.classList.remove('active'));
            element.classList.add('active');
        }

        // --- ІНІЦІАЛІЗАЦІЯ ---
        const profileImg = document.getElementById('profileImg');
        const qrDisplay = document.getElementById('qrDisplay');
        const flipper = document.getElementById('flipper');

        window.onload = () => {
            const savedAvatar = localStorage.getItem('diia_avatar');
            profileImg.src = savedAvatar ? savedAvatar : defaultAvatar;

            const savedQr = localStorage.getItem('diia_qr');
            qrDisplay.src = savedQr ? savedQr : defaultQr;

            autoResize(document.getElementById('nameInput'));
            document.addEventListener('dblclick', function(e) { e.preventDefault(); }, { passive: false });
        };

        // --- ЗБЕРЕЖЕННЯ АВАТАРУ ---
        function loadImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const base64Str = e.target.result;
                    profileImg.src = base64Str;
                    localStorage.setItem('diia_avatar', base64Str);
                }
                reader.readAsDataURL(file);
            }
        }

        // --- ЗБЕРЕЖЕННЯ QR ---
        function loadCustomQr(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const base64Str = e.target.result;
                    qrDisplay.src = base64Str;
                    localStorage.setItem('diia_qr', base64Str);
                }
                reader.readAsDataURL(file);
            }
        }

        // --- РЕЖИМ РЕДАГУВАННЯ ---
        let isEditing = false;
        function toggleEdit(e) {
            e.stopPropagation();
            isEditing = !isEditing;
            const front = document.querySelector('.front');
            const btn = document.getElementById('editBtn');
            
            if (isEditing) {
                front.classList.add('editing');
                btn.innerText = 'Готово';
                btn.classList.add('active');
            } else {
                front.classList.remove('editing');
                btn.innerText = 'Редагувати';
                btn.classList.remove('active');
            }
        }

        // --- ПЕРЕВОРОТ КАРТКИ ---
        flipper.addEventListener('click', function(e) {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'BUTTON' || e.target.closest('.qr-image-wrap')) {
                return;
            }
            this.classList.toggle('flipped');
        });

        function autoResize(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
        }
    </script>
</body>
</html>
