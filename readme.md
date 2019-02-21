# forum [![Build Status](https://travis-ci.org/Mayvis/forum.svg?branch=master)](https://travis-ci.org/Mayvis/forum)
這是一個使用laravel配合vue所建置的一個論壇，於laracasts學習時建置的專案

# 使用套件
### 僅列出部分使用套件
1. broadcasting (pusher) 
2. mail (mailtrap fake mail)
3. redis
4. algolia
5. vue
6. google reCAPTCHA
7. trix
8. highlight 

# 使用方式
## STEP 1
```bash
git clone https://github.com/Mayvis/forum.git
cd forum && composer install && npm install
npm run dev
```

## STEP 2
1. 將.env連接資料庫的部分寫好，並先創建好forum的資料庫
2. 執行`php artisan migrate`
3. 創建一個帳號並手動將資料庫的 users.confirmed 調成1
4. 至 app/Providers/AuthServiceProvider.php 內將管理者改建成你創建帳號的名稱
5. 至admin頁面創建channel或著使用`php artisan tinker`使用faker匯入假資料
