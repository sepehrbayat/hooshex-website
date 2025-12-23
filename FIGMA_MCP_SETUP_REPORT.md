# گزارش تلاش‌های اتصال به Figma MCP

## خلاصه
تلاش‌های متعددی برای اتصال Cursor به Figma از طریق Model Context Protocol (MCP) انجام شد، اما به دلیل مشکل در ثبت ابزارهای MCP در Cursor، موفقیت‌آمیز نبود.

---

## مراحل انجام شده

### 1. نصب و راه‌اندازی اولیه

#### 1.1 نصب Bun
```bash
# Bun برای اجرای MCP server و socket server مورد نیاز بود
curl -fsSL https://bun.sh/install | bash
```

#### 1.2 کلون کردن پروژه Figma-Cursor-MCP
```bash
cd ~
git clone <repository-url>
cd Figma-Cursor-MCP
bun setup
```

#### 1.3 نصب از طریق npm (روش جایگزین)
```bash
# تلاش برای استفاده از نسخه npm
bunx cursor-talk-to-figma-mcp@latest
```

---

### 2. راه‌اندازی Socket Server

#### 2.1 اجرای Socket Server
```bash
# Socket server باید روی پورت 3055 اجرا شود
bunx cursor-talk-to-figma-socket
# یا
cd ~/Figma-Cursor-MCP
bun run src/socket.ts
```

**وضعیت**: Socket server با موفقیت روی پورت 3055 راه‌اندازی شد و در حال اجرا بود.

**تایید اتصال**:
- Chrome (Figma PWA) به socket server متصل بود
- MCP server instances به socket server متصل بودند
- WebSocket connections برقرار بودند

---

### 3. پیکربندی Cursor MCP

#### 3.1 فایل `~/.cursor/mcp.json`

**نسخه 1 (مسیر محلی)**:
```json
{
  "mcpServers": {
    "TalkToFigma": {
      "command": "bun",
      "args": [
        "/home/sepehr/Figma-Cursor-MCP/src/talk_to_figma_mcp/server.ts"
      ]
    }
  }
}
```

**نسخه 2 (npm package)**:
```json
{
  "mcpServers": {
    "TalkToFigma": {
      "command": "bunx",
      "args": [
        "cursor-talk-to-figma-mcp@latest"
      ]
    }
  }
}
```

**نسخه 3 (مسیر کامل bunx)**:
```json
{
  "mcpServers": {
    "TalkToFigma": {
      "command": "/home/sepehr/.bun/bin/bunx",
      "args": [
        "cursor-talk-to-figma-mcp@latest"
      ]
    }
  }
}
```

**نکته**: از آنجایی که `~/.cursor/mcp.json` در globalignore قرار داشت، از `sed` command برای ویرایش استفاده شد:
```bash
cat ~/.cursor/mcp.json | jq '.mcpServers.TalkToFigma = {...}' > temp && mv temp ~/.cursor/mcp.json
```

---

### 4. نصب و راه‌اندازی Figma Plugin

#### 4.1 نصب Figma Desktop برای Linux
**تلاش اول**: نصب `figma-linux` (نسخه غیررسمی)
```bash
snap install figma-linux
```

**مشکل**: نسخه غیررسمی Figma به درستی کار نمی‌کرد و نمی‌توانست به socket server متصل شود.

#### 4.2 راه‌حل: استفاده از Figma PWA در Chrome
- نصب Figma به عنوان Progressive Web App (PWA) از طریق Chrome
- ایجاد desktop shortcut برای دسترسی آسان
- ایجاد shell script: `~/.local/bin/figma-hooshex`

**وضعیت**: Figma PWA با موفقیت به socket server متصل شد.

**تایید اتصال از Terminal**:
```
✓ Client joined channel "rnxc3gdn" (1 total clients)
✓ Client joined channel "3mzwzyir" (1 total clients)
```

---

### 5. بررسی وضعیت سیستم

#### 5.1 بررسی Process ها
```bash
ps aux | grep cursor-talk-to-figma
```

**نتیجه**: 
- Socket server در حال اجرا (PID: 136503)
- MCP server instances در حال اجرا (چندین instance)

#### 5.2 بررسی Port و Connections
```bash
lsof -i :3055
netstat -tlnp | grep 3055
```

**نتیجه**:
- Port 3055 LISTEN بود
- Chrome (Figma) به socket server متصل بود
- MCP server instances به socket server متصل بودند

---

### 6. مشکل اصلی: عدم ثبت ابزارهای MCP در Cursor

#### 6.1 تلاش برای استفاده از ابزارها

**ابزارهای مورد انتظار**:
- `mcp_TalkToFigma_get_document_info`
- `mcp_TalkToFigma_get_selection`
- `mcp_TalkToFigma_join_channel`

**خطای دریافت شده**:
```
Error: Tool mcp_TalkToFigma_get_document_info not found in available tools
```

**ابزارهای موجود در Cursor** (نمونه):
- `codebase_search`
- `mcp_filesystem_*`
- `mcp_git_*`
- `mcp_firecrawl_*`
- `mcp_cursor-ide-browser_*`
- اما **هیچ ابزار `TalkToFigma` وجود نداشت**

#### 6.2 بررسی کد MCP Server

**فایل**: `~/Figma-Cursor-MCP/src/talk_to_figma_mcp/server.ts`

**ابزارهای تعریف شده در کد**:
```typescript
- get_document_info
- get_selection
- join_channel
```

**نتیجه**: ابزارها در کد تعریف شده بودند، اما در Cursor ثبت نشده بودند.

---

## دلایل احتمالی عدم موفقیت

### 1. مشکل در بارگذاری MCP Server
- Cursor ممکن است MCP server را به درستی load نکرده باشد
- ممکن است نیاز به restart کامل Cursor باشد
- ممکن است مشکل در تشخیص `bunx` command باشد

### 2. مشکل در ثبت ابزارها
- MCP server ممکن است به درستی initialize نشده باشد
- ممکن است protocol handshake کامل نشده باشد
- ممکن است مشکل در JSON-RPC communication باشد

### 3. مشکل در Channel Connection
- ابزار `join_channel` که برای اتصال به channel خاص لازم است، در دسترس نبود
- بدون join کردن channel، نمی‌توانستیم با Figma plugin ارتباط برقرار کنیم

### 4. مشکل در Plugin Communication
- اگرچه Figma plugin به socket server متصل بود، ممکن است MCP server نتواند به درستی با plugin ارتباط برقرار کند

---

## کارهای انجام شده که موفق بودند

✅ **Socket Server**: با موفقیت راه‌اندازی شد و در حال اجرا بود  
✅ **Figma PWA**: با موفقیت به socket server متصل شد  
✅ **MCP Server Process**: در حال اجرا بود  
✅ **WebSocket Connections**: برقرار بودند  
✅ **Configuration**: فایل `mcp.json` به درستی پیکربندی شد  

---

## کارهایی که موفق نبودند

❌ **ابزارهای MCP در Cursor**: هیچ ابزار `TalkToFigma` در Cursor ثبت نشده بود  
❌ **اتصال به Figma**: به دلیل عدم دسترسی به ابزارها، امکان خواندن طراحی‌ها وجود نداشت  
❌ **Channel Join**: بدون ابزار `join_channel`، نمی‌توانستیم به channel خاص متصل شویم  

---

## راه‌حل‌های پیشنهادی

### 1. بررسی Log ها
```bash
# بررسی log های MCP server
tail -f ~/.cursor/logs/mcp.log
# یا
journalctl -u cursor-mcp
```

### 2. استفاده از Browser MCP
- استفاده از `mcp_cursor-ide-browser_*` tools برای دسترسی به Figma از طریق browser
- **محدودیت**: Browser MCP نمی‌تواند داده‌های ساختاریافته از Figma API بخواند

### 3. استفاده از Figma REST API
- استفاده مستقیم از Figma REST API برای دریافت design data
- نیاز به API token

### 4. بررسی مجدد Setup
- اطمینان از نصب صحیح تمام dependencies
- بررسی version compatibility بین Cursor و MCP server
- بررسی documentation به‌روزرسانی شده

### 5. استفاده از Alternative Tools
- بررسی سایر ابزارهای MCP برای Figma
- استفاده از Figma CLI tools

---

## نتیجه‌گیری

با وجود راه‌اندازی موفق socket server و اتصال Figma plugin، مشکل اصلی در **عدم ثبت ابزارهای MCP در Cursor** است. این ممکن است به دلیل:

1. مشکل در بارگذاری MCP server توسط Cursor
2. نیاز به restart کامل Cursor یا سیستم
3. مشکل در compatibility بین Cursor version و MCP server
4. نیاز به تنظیمات اضافی در Cursor configuration

**راه‌حل موقت**: استفاده از Browser MCP برای مشاهده Figma و پیاده‌سازی دستی بر اساس CSS files و تصاویر موجود.

---

## فایل‌های مرتبط

- `~/.cursor/mcp.json` - پیکربندی MCP servers
- `~/Figma-Cursor-MCP/` - کلون شده repository
- `~/.local/bin/figma-hooshex` - Shell script برای باز کردن Figma PWA
- Socket server running on port 3055

---

**تاریخ**: 2025-01-10  
**وضعیت**: Unresolved - نیاز به بررسی بیشتر

---

## نقشه راه کامل (Roadmap)

### معماری سیستم پیشنهادی

#### 1. معماری سه لایه

**مشکل**: پلاگین‌های فیگما در یک محیط ایزوله (Sandbox) اجرا می‌شوند و نمی‌توانند مستقیماً به پروسس‌های داخلی سیستم عامل یا Cursor دسترسی داشته باشند.

**راه‌حل**: معماری سه لایه

```
┌─────────────────────────────────────────────────────────┐
│  Layer 1: Figma Plugin (Frontend)                      │
│  - رابط کاربری در Figma                                │
│  - ارسال درخواست HTTP به Localhost                      │
└──────────────────┬──────────────────────────────────────┘
                   │ HTTP Request
                   ▼
┌─────────────────────────────────────────────────────────┐
│  Layer 2: Local Bridge Server                          │
│  - Node.js/Express Server روی localhost                │
│  - دریافت درخواست از Plugin                            │
│  - ارتباط با MCP Server                                │
└──────────────────┬──────────────────────────────────────┘
                   │ MCP Protocol
                   ▼
┌─────────────────────────────────────────────────────────┐
│  Layer 3: MCP Implementation                           │
│  - منطق ابزارهای MCP                                   │
│  - تبدیل داده‌های Figma به فرمت MCP                    │
│  - ارتباط با Cursor                                    │
└─────────────────────────────────────────────────────────┘
```

**جریان داده**:
1. فیگما Plugin → درخواست HTTP به `http://localhost:PORT`
2. Local Bridge Server → دریافت و پردازش درخواست
3. MCP Implementation → اجرای منطق MCP
4. بازگشت نتیجه به Plugin ← پاسخ HTTP

---

#### 2. حل مشکل لینوکس

**مشکل**: 
- فیگما نسخه رسمی لینوکس ندارد
- نسخه مرورگر (Web) به دلایل امنیتی (Mixed Content) اجازه نمی‌دهد پلاگین‌ها به localhost وصل شوند

**راه‌حل**: استفاده از Figma-Linux

**Figma-Linux** یک نسخه Electron Wrapper متن‌باز است که:
- رفتار اپلیکیشن دسکتاپ را شبیه‌سازی می‌کند
- محدودیت‌های مرورگر را برای درخواست‌های لوکال ندارد
- از پلاگین‌ها پشتیبانی می‌کند

**نصب**:
```bash
# روش 1: از طریق Snap
sudo snap install figma-linux

# روش 2: از طریق AppImage
# دانلود از: https://github.com/Figma-Linux/figma-linux/releases
```

---

### مراحل پیاده‌سازی

#### مرحله 1: نصب و راه‌اندازی Figma-Linux
- [ ] نصب Figma-Linux
- [ ] تست اتصال پلاگین به localhost

#### مرحله 2: ایجاد Local Bridge Server
- [ ] ایجاد Node.js/Express server
- [ ] راه‌اندازی HTTP endpoint برای دریافت درخواست‌های Plugin
- [ ] راه‌اندازی WebSocket برای real-time communication (اختیاری)

#### مرحله 3: پیاده‌سازی MCP Integration
- [ ] ایجاد MCP server منطبق با استاندارد
- [ ] پیاده‌سازی ابزارهای MCP:
  - `get_document_info`: دریافت اطلاعات سند
  - `get_selection`: دریافت انتخاب فعلی
  - `get_frames`: دریافت لیست frames
  - `get_components`: دریافت لیست components
  - و سایر ابزارهای مورد نیاز

#### مرحله 4: توسعه Figma Plugin
- [ ] ایجاد Plugin با Figma Plugin API
- [ ] پیاده‌سازی UI برای Plugin
- [ ] اتصال به Local Bridge Server
- [ ] نمایش نتایج در Plugin

#### مرحله 5: اتصال به Cursor
- [ ] پیکربندی Cursor برای استفاده از MCP Server
- [ ] تست اتصال و ابزارها
- [ ] پیاده‌سازی مثال‌های کاربردی

---

### تکنولوژی‌های مورد نیاز

1. **Backend**:
   - Node.js / Express
   - WebSocket (برای real-time communication)
   - MCP Protocol implementation

2. **Figma Plugin**:
   - Figma Plugin API
   - TypeScript
   - HTML/CSS برای UI

3. **MCP Server**:
   - JSON-RPC 2.0
   - MCP Protocol specification

---

### نکات مهم

1. **امنیت**: Local Bridge Server باید فقط به localhost listen کند
2. **Error Handling**: باید error handling مناسب برای تمام لایه‌ها پیاده‌سازی شود
3. **Logging**: سیستم logging برای debugging
4. **Documentation**: مستندسازی کامل API و ابزارها

---

### منابع

- [Figma Plugin API Documentation](https://www.figma.com/plugin-docs/)
- [Figma-Linux GitHub](https://github.com/Figma-Linux/figma-linux)
- [MCP Specification](https://modelcontextprotocol.io/)
- [JSON-RPC 2.0 Specification](https://www.jsonrpc.org/specification)

---

**تاریخ به‌روزرسانی**: 2025-01-10  
**وضعیت**: Roadmap آماده - در حال پیاده‌سازی

---

## پیشرفت پیاده‌سازی

### ✅ مراحل انجام شده

#### 1. نصب Figma-Linux
```bash
sudo snap install figma-linux
```
**وضعیت**: ✅ نصب شد (نسخه 0.11.4)

#### 2. ایجاد Local Bridge Server
**مسیر**: `~/figma-mcp-bridge/`

**ساختار**:
```
~/figma-mcp-bridge/
├── package.json
├── server.js
└── README.md
```

**قابلیت‌ها**:
- HTTP Server روی پورت 3056
- اتصال به MCP WebSocket Server (پورت 3055)
- API Endpoints برای ارتباط با Figma Plugin
- Error handling و reconnection logic

**API Endpoints**:
- `GET /health` - بررسی وضعیت
- `POST /api/figma/document-info` - دریافت اطلاعات سند
- `POST /api/figma/selection` - دریافت انتخاب فعلی
- `POST /api/figma/join-channel` - اتصال به channel
- `POST /api/figma/frames` - دریافت لیست frames

**وضعیت**: ✅ راه‌اندازی شد و به MCP Server متصل است

**تست**:
```bash
curl http://localhost:3056/health
# Response: {"status":"ok","mcpConnected":true}
```

---

### 📋 مراحل باقی‌مانده

- [ ] ایجاد Figma Plugin با UI
- [ ] پیاده‌سازی MCP Tools کامل در Bridge Server
- [ ] پیکربندی Cursor برای استفاده از Bridge Server
- [ ] تست کامل end-to-end

---

**تاریخ آخرین به‌روزرسانی**: 2025-01-10  
**وضعیت فعلی**: Bridge Server فعال - آماده برای توسعه Plugin

