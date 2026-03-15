# Sklad.pro — Омбор бошқаруви тизими

PHP ва Bootstrap асосида ишлаб чиқилган складни бошқариш тизими.

---

## 📦 Лойиҳани ишга тушириш

1. `db.sql` файлини MySQL базасига импорт қилинг
2. `includes/db.php` файлида маълумотлар базаси созламаларини ўзгартиринг
3. Дефолт кириш: `admin` / `admin123`

---

# 🔧 GIT — Тўлиқ Қўлланма (Ўзбекча)

## Git нима?

**Git** — бу код ўзгаришларини кузатиб борувчи версиялар бошқаруви тизими.  
Ҳар бир сақлаш (commit) — лойиҳанинг "фотосурати" ҳисобланади.  
Хоҳлаган вақтда орқага қайтиш, шохлар (branch) яратиш, жамоа билан ишлаш мумкин.

---

## ⚙️ Биринчи марта созлаш

```bash
git config --global user.name "Исмингиз"
git config --global user.email "email@example.com"
```

---

## 🏁 Янги репозиторий яратиш

```bash
git init                  # Мавжуд папкани git репозиторийга айлантириш
```

**ёки** GitHub дан кўчириб олиш:

```bash
git clone https://github.com/username/repo.git
```

---

## 📋 Асосий амалlar (Workflow)

### 1. Ҳолатни кўриш
```bash
git status                # Қайси файллар ўзгарган?
git log --oneline         # Commitlar тарихи
```

### 2. Ўзгаришларни тайёрлаш (Stage)
```bash
git add .                 # Барча файлларни тайёрлаш
git add filename.php      # Фақат битта файлни тайёрлаш
git add assets/           # Бир папкани тайёрлаш
```

### 3. Сақлаш (Commit)
```bash
git commit -m "Ўзгариш тавсифи"
```

> 💡 Яхши commit хабари: `"Товарлар жадвалига филтр қўшилди"`

### 4. GitHub га юклаш (Push)
```bash
git push                  # Охирги commit ларни юклаш
git push origin main      # main шохига юклаш
```

### 5. GitHub дан юклаб олиш (Pull)
```bash
git pull                  # Охирги ўзгаришларни олиш
```

---

## 🌿 Шохлар (Branch) билан ишлаш

```bash
git branch                      # Мавжуд шохларни кўрish
git branch yangi-funksiya        # Янги шох яратиш
git checkout yangi-funksiya      # Шохга ўтиш
git checkout -b yangi-funksiya   # Яратиш + ўтиш (бир командада)

git merge yangi-funksiya         # Шохни asosiy ga qo'shish
git branch -d yangi-funksiya     # Шохни ўчириш
```

---

## ↩️ Хатони тузатиш

```bash
# Oxirgi commit xabarini o'zgartirish
git commit --amend -m "To'g'ri xabar"

# Bitta faylni oxirgi holga qaytarish
git checkout -- filename.php

# Barcha stagingni bekor qilish
git reset HEAD .

# Oxirgi N ta commitni bekor qilish (o'zgarishlar saqlanadi)
git reset --soft HEAD~1
```

---

## 🔗 Remote (GitHub) bilan ishlash

```bash
git remote -v                                        # Ulangan remote lar
git remote add origin https://github.com/user/repo  # Remote qo'shish
git remote set-url origin <yangi-url>                # URL o'zgartirish
```

---

## 🏷️ Versiyalar (Tag)

```bash
git tag v1.0.0                    # Tag qo'shish
git tag v1.0.0 -m "Birinchi vers" # Xabarli tag
git push origin v1.0.0            # Tag ni push qilish
git push origin --tags            # Barcha taglarni push qilish
```

---

## 📁 .gitignore — Keraksiz fayllarni e'tiborsiz qoldirish

`.gitignore` fayli orqali git kuzatmasin degan fayllarni ko'rsatasiz:

```
# Konfiguratsiya fayllari
config.local.php
.env

# Log fayllar
*.log

# Backup
*.sql.bak

# OS fayllar
.DS_Store
Thumbs.db
```

---

## 📊 Foydali buyruqlar

```bash
git diff                  # Ko'rilmagan o'zgarishlarni ko'rish
git diff --staged         # Stage qilingan o'zgarishlarni ko'rish
git log --graph --oneline # Grafik tarihini ko'rish
git stash                 # O'zgarishlarni vaqtincha saqlash
git stash pop             # Saqlangan o'zgarishlarni qaytarish
git show HEAD             # Oxirgi commitni ko'rish
```

---

## 🔁 Bu loyiha uchun oddiy jarayon

```bash
# 1. O'zgarish qil (kodni yoz)

# 2. Tayyorla
git add .

# 3. Saqlа
git commit -m "Nima o'zgardi?"

# 4. GitHub ga yuborish
git push
```

---

## 🔗 Foydali havolalar

- [Git rasmiy sayt](https://git-scm.com)
- [GitHub](https://github.com)
- [Bu loyiha](https://github.com/sherzodshef19/sklad_pro)
